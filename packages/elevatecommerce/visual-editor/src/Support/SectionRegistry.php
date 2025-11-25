<?php

namespace ElevateCommerce\VisualEditor\Support;

class SectionRegistry
{
    /**
     * Registered sections.
     *
     * @var array
     */
    protected array $sections = [];

    /**
     * Register a section.
     *
     * @param string $key Unique identifier for the section
     * @param array $config Section configuration
     * @return void
     */
    public function register(string $key, array $config): void
    {
        $this->sections[$key] = array_merge([
            'name' => $key,
            'label' => ucfirst(str_replace(['-', '_'], ' ', $key)),
            'view' => null,
            'category' => 'general',
            'icon' => '📦',
            'preview_image' => null,
            'schema' => [],
            'defaults' => [],
            'max_instances' => null,
            'contexts' => [], // Which contexts this section can use
        ], $config);
    }

    /**
     * Register multiple sections.
     *
     * @param array $sections
     * @return void
     */
    public function registerMany(array $sections): void
    {
        foreach ($sections as $key => $config) {
            $this->register($key, $config);
        }
    }

    /**
     * Get a section configuration.
     *
     * @param string $key
     * @return array|null
     */
    public function get(string $key): ?array
    {
        return $this->sections[$key] ?? null;
    }

    /**
     * Check if a section exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->sections[$key]);
    }

    /**
     * Get all registered sections.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->sections;
    }

    /**
     * Get sections by category.
     *
     * @param string $category
     * @return array
     */
    public function byCategory(string $category): array
    {
        return array_filter($this->sections, function ($section) use ($category) {
            return ($section['category'] ?? 'general') === $category;
        });
    }

    /**
     * Get sections that support a specific context.
     *
     * @param string $contextKey
     * @return array
     */
    public function forContext(string $contextKey): array
    {
        return array_filter($this->sections, function ($section) use ($contextKey) {
            $contexts = $section['contexts'] ?? [];
            return empty($contexts) || in_array($contextKey, $contexts);
        });
    }

    /**
     * Get all categories.
     *
     * @return array
     */
    public function categories(): array
    {
        $categories = [];
        
        foreach ($this->sections as $section) {
            $category = $section['category'] ?? 'general';
            if (!in_array($category, $categories)) {
                $categories[] = $category;
            }
        }

        return $categories;
    }

    /**
     * Render a section with data.
     *
     * @param string $key Section key
     * @param array $settings Section settings from page config
     * @param mixed $contextData Data from context
     * @return string|null
     */
    public function render(string $key, array $settings = [], mixed $contextData = null): ?string
    {
        $section = $this->get($key);

        if (!$section || !$section['view']) {
            return null;
        }

        // Merge defaults with settings
        $mergedSettings = array_merge($section['defaults'] ?? [], $settings);

        // Create settings object for clean API
        $settingsObject = (object) $mergedSettings;

        // Create context object
        $contextObject = is_array($contextData) ? (object) $contextData : $contextData;

        // Prepare view data
        $viewData = [
            'settings' => $settingsObject,  // Clean object API
            'context' => $contextObject,    // Clean object API
            '_section' => [
                'key' => $key,
                'name' => $section['name'],
            ],
            // Also spread settings for backward compatibility
            ...$mergedSettings,
        ];

        return view($section['view'], $viewData)->render();
    }

    /**
     * Get section schema for editor.
     *
     * @param string $key
     * @return array
     */
    public function getSchema(string $key): array
    {
        $section = $this->get($key);
        return $section['schema'] ?? [];
    }

    /**
     * Validate section settings against schema.
     *
     * @param string $key
     * @param array $settings
     * @return array Validation errors
     */
    public function validate(string $key, array $settings): array
    {
        $schema = $this->getSchema($key);
        $errors = [];

        foreach ($schema as $field => $rules) {
            $required = $rules['required'] ?? false;
            $type = $rules['type'] ?? 'text';

            if ($required && !isset($settings[$field])) {
                $errors[$field] = "Field {$field} is required";
                continue;
            }

            if (isset($settings[$field])) {
                // Basic type validation
                $value = $settings[$field];
                
                switch ($type) {
                    case 'number':
                        if (!is_numeric($value)) {
                            $errors[$field] = "Field {$field} must be a number";
                        }
                        break;
                    case 'boolean':
                        if (!is_bool($value)) {
                            $errors[$field] = "Field {$field} must be a boolean";
                        }
                        break;
                    case 'array':
                        if (!is_array($value)) {
                            $errors[$field] = "Field {$field} must be an array";
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Remove a section.
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        unset($this->sections[$key]);
    }
}
