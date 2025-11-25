<?php

namespace ElevateCommerce\VisualEditor\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ElevateCommerce\VisualEditor\Models\Theme;

class ThemeLoader
{
    /**
     * Load sections for a specific theme.
     *
     * @param string $themeSlug
     * @return void
     */
    public function loadTheme(string $themeSlug): void
    {
        $themePath = resource_path("views/themes/{$themeSlug}");

        if (!File::isDirectory($themePath)) {
            return;
        }

        // Register Laravel view namespace
        view()->addNamespace($themeSlug, $themePath);

        // Load and register sections
        $this->registerThemeSections($themeSlug, $themePath);
    }

    /**
     * Load sections for active theme only.
     *
     * @return void
     */
    public function loadActiveTheme(): void
    {
        // Check if themes table exists before querying
        if (!$this->themesTableExists()) {
            return;
        }

        // Cache active theme for 1 hour
        $activeTheme = \Illuminate\Support\Facades\Cache::remember('active_theme', 3600, function () {
            return Theme::where('is_active', true)->first();
        });

        if ($activeTheme) {
            $this->loadTheme($activeTheme->slug);
        }
    }

    /**
     * Load theme being edited in admin.
     *
     * @param string $themeSlug
     * @return void
     */
    public function loadThemeForEditing(string $themeSlug): void
    {
        $this->loadTheme($themeSlug);
    }

    /**
     * Register sections from section-configs directory.
     *
     * @param string $themeSlug
     * @param string $themePath
     * @return void
     */
    protected function registerThemeSections(string $themeSlug, string $themePath): void
    {
        $configsPath = $themePath . '/section-configs';

        if (!File::isDirectory($configsPath)) {
            return;
        }

        $configFiles = File::files($configsPath);
        $sectionRegistry = app('visual-editor.section');

        foreach ($configFiles as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $sectionName = $file->getFilenameWithoutExtension();
            $sectionKey = "{$themeSlug}-{$sectionName}";

            // Load config from JSON file
            $config = json_decode(File::get($file->getPathname()), true);

            if (!$config) {
                continue;
            }

            // Register the section
            $sectionRegistry->register($sectionKey, [
                'name' => $sectionKey,
                'label' => $config['label'] ?? $this->generateLabel($sectionName),
                'view' => "{$themeSlug}::sections.{$sectionName}",
                'category' => $config['category'] ?? 'general',
                'icon' => $config['icon'] ?? '📦',
                'preview_image' => $config['preview_image'] ?? null,
                'schema' => $config['schema'] ?? [],
                'defaults' => $config['defaults'] ?? [],
                'contexts' => $config['contexts'] ?? [],
            ]);
        }
    }

    /**
     * Generate a human-readable label from section name.
     *
     * @param string $name
     * @return string
     */
    protected function generateLabel(string $name): string
    {
        return Str::title(str_replace(['-', '_'], ' ', $name));
    }

    /**
     * Get all available themes from resources/views/themes.
     *
     * @return array
     */
    public function getAvailableThemes(): array
    {
        $themesPath = resource_path('views/themes');

        if (!File::isDirectory($themesPath)) {
            return [];
        }

        $themes = [];
        $directories = File::directories($themesPath);

        foreach ($directories as $themePath) {
            $themeSlug = basename($themePath);
            $config = $this->loadThemeConfig($themePath);

            $themes[] = [
                'slug' => $themeSlug,
                'name' => $config['name'] ?? Str::title($themeSlug),
                'description' => $config['description'] ?? null,
                'version' => $config['version'] ?? '1.0.0',
                'author' => $config['author'] ?? null,
                'preview_image' => $config['preview_image'] ?? null,
                'path' => $themePath,
            ];
        }

        return $themes;
    }

    /**
     * Load theme configuration from theme.json.
     *
     * @param string $themePath
     * @return array
     */
    protected function loadThemeConfig(string $themePath): array
    {
        $configPath = $themePath . '/theme.json';

        if (!File::exists($configPath)) {
            return [];
        }

        return json_decode(File::get($configPath), true) ?? [];
    }

    /**
     * Sync themes from filesystem to database.
     *
     * @return array
     */
    public function syncThemesToDatabase(): array
    {
        $availableThemes = $this->getAvailableThemes();
        $synced = [];

        foreach ($availableThemes as $themeData) {
            $theme = Theme::updateOrCreate(
                ['slug' => $themeData['slug']],
                [
                    'name' => $themeData['name'],
                    'description' => $themeData['description'],
                    'version' => $themeData['version'],
                    'author' => $themeData['author'],
                ]
            );

            $synced[] = [
                'theme' => $theme,
                'was_created' => $theme->wasRecentlyCreated,
            ];
        }

        return $synced;
    }

    /**
     * Get theme metadata only (without loading sections).
     *
     * @return array
     */
    public function getThemeMetadata(): array
    {
        return $this->getAvailableThemes();
    }

    /**
     * Check if themes table exists in database.
     *
     * @return bool
     */
    protected function themesTableExists(): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('themes');
        } catch (\Exception $e) {
            return false;
        }
    }
}
