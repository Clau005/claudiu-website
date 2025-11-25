<?php

namespace ElevateCommerce\VisualEditor\Support;

class DashboardRegistry
{
    /**
     * Registered dashboard components.
     *
     * @var array
     */
    protected array $components = [];

    /**
     * Register a dashboard component.
     *
     * @param string $key Unique identifier for the component
     * @param array $component Component configuration
     * @return void
     */
    public function register(string $key, array $component): void
    {
        $this->components[$key] = array_merge([
            'view' => null,
            'data' => null,
            'width' => 'full', // full, half, third, quarter
            'order' => 100,
            'permission' => null,
            'title' => null,
            'refreshable' => false,
        ], $component);
    }

    /**
     * Register multiple dashboard components.
     *
     * @param array $components
     * @return void
     */
    public function registerMany(array $components): void
    {
        foreach ($components as $key => $component) {
            $this->register($key, $component);
        }
    }

    /**
     * Get all registered components, sorted by order.
     *
     * @return array
     */
    public function all(): array
    {
        $components = $this->components;

        uasort($components, function ($a, $b) {
            return ($a['order'] ?? 100) <=> ($b['order'] ?? 100);
        });

        return $components;
    }

    /**
     * Get a specific component.
     *
     * @param string $key
     * @return array|null
     */
    public function get(string $key): ?array
    {
        return $this->components[$key] ?? null;
    }

    /**
     * Check if a component exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->components[$key]);
    }

    /**
     * Remove a component.
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        unset($this->components[$key]);
    }

    /**
     * Get filtered components.
     *
     * @param callable $callback
     * @return array
     */
    public function filter(callable $callback): array
    {
        return array_filter($this->all(), $callback);
    }

    /**
     * Get components by width.
     *
     * @param string $width
     * @return array
     */
    public function byWidth(string $width): array
    {
        return $this->filter(function ($component) use ($width) {
            return ($component['width'] ?? 'full') === $width;
        });
    }

    /**
     * Get components that the current user has permission to view.
     *
     * @param mixed $user
     * @return array
     */
    public function forUser($user = null): array
    {
        return $this->filter(function ($component) use ($user) {
            if (!isset($component['permission'])) {
                return true;
            }

            $user = $user ?? auth()->guard('admin')->user();

            if (!$user) {
                return false;
            }

            // If the user has a 'can' method (like with policies/gates)
            if (method_exists($user, 'can')) {
                return $user->can($component['permission']);
            }

            // If user is super admin, allow everything
            if (isset($user->is_super_admin) && $user->is_super_admin) {
                return true;
            }

            return false;
        });
    }

    /**
     * Render a component.
     *
     * @param string $key
     * @return string|null
     */
    public function render(string $key): ?string
    {
        $component = $this->get($key);

        if (!$component || !$component['view']) {
            return null;
        }

        $data = [];

        // If data is a closure, execute it
        if (is_callable($component['data'])) {
            $data = call_user_func($component['data']);
        } elseif (is_array($component['data'])) {
            $data = $component['data'];
        }

        // Add component metadata to data
        $data['_component'] = [
            'key' => $key,
            'title' => $component['title'] ?? null,
            'refreshable' => $component['refreshable'] ?? false,
        ];

        return view($component['view'], $data)->render();
    }

    /**
     * Render all components.
     *
     * @return array
     */
    public function renderAll(): array
    {
        $rendered = [];

        foreach ($this->all() as $key => $component) {
            $html = $this->render($key);
            if ($html) {
                $rendered[$key] = [
                    'html' => $html,
                    'width' => $component['width'] ?? 'full',
                    'order' => $component['order'] ?? 100,
                ];
            }
        }

        return $rendered;
    }
}
