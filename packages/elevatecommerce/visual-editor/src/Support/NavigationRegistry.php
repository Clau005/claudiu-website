<?php

namespace ElevateCommerce\VisualEditor\Support;

class NavigationRegistry
{
    /**
     * Registered navigation items.
     *
     * @var array
     */
    protected array $items = [];

    /**
     * Register a navigation item.
     *
     * @param string $key Unique identifier for the nav item
     * @param array $item Navigation item data
     * @return self
     */
    public function register(string $key, array $item): self
    {
        $this->items[$key] = array_merge([
            'label' => '',
            'url' => '',
            'icon' => null,
            'order' => 100,
            'permission' => null,
            'badge' => null,
            'children' => [],
        ], $item);

        return $this;
    }

    /**
     * Register multiple navigation items.
     *
     * @param array $items
     * @return self
     */
    public function registerMany(array $items): self
    {
        foreach ($items as $key => $item) {
            $this->register($key, $item);
        }

        return $this;
    }

    /**
     * Get all registered navigation items.
     *
     * @return array
     */
    public function all(): array
    {
        return collect($this->items)
            ->sortBy('order')
            ->toArray();
    }

    /**
     * Get a specific navigation item.
     *
     * @param string $key
     * @return array|null
     */
    public function get(string $key): ?array
    {
        return $this->items[$key] ?? null;
    }

    /**
     * Remove a navigation item.
     *
     * @param string $key
     * @return self
     */
    public function remove(string $key): self
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * Check if a navigation item exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Get filtered navigation items (e.g., by permission).
     *
     * @param callable|null $callback
     * @return array
     */
    public function filter(?callable $callback = null): array
    {
        $items = collect($this->items);

        if ($callback) {
            $items = $items->filter($callback);
        }

        return $items->sortBy('order')->toArray();
    }
}
