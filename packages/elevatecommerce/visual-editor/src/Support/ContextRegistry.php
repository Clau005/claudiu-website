<?php

namespace ElevateCommerce\VisualEditor\Support;

use Illuminate\Http\Request;

class ContextRegistry
{
    /**
     * Registered contexts.
     *
     * @var array
     */
    protected array $contexts = [];

    /**
     * Register a context.
     *
     * @param string $key Unique identifier for the context
     * @param array $config Context configuration
     * @return void
     */
    public function register(string $key, array $config): void
    {
        $this->contexts[$key] = array_merge([
            'fetcher' => null,
            'identifier' => 'id',
            'cacheable' => true,
            'cache_ttl' => 3600,
            'eager_load' => [],
            'filters' => [],
            'sorts' => [],
            'pagination' => false,
            'per_page' => 15,
        ], $config);
    }

    /**
     * Register multiple contexts.
     *
     * @param array $contexts
     * @return void
     */
    public function registerMany(array $contexts): void
    {
        foreach ($contexts as $key => $config) {
            $this->register($key, $config);
        }
    }

    /**
     * Get a context configuration.
     *
     * @param string $key
     * @return array|null
     */
    public function get(string $key): ?array
    {
        return $this->contexts[$key] ?? null;
    }

    /**
     * Check if a context exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->contexts[$key]);
    }

    /**
     * Get all registered contexts.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->contexts;
    }

    /**
     * Fetch data for a context.
     *
     * @param string $key Context key
     * @param Request $request Current request
     * @param array $params Additional parameters (route params, filters, etc.)
     * @return mixed
     */
    public function fetch(string $key, Request $request, array $params = []): mixed
    {
        $context = $this->get($key);

        if (!$context || !$context['fetcher']) {
            return null;
        }

        $fetcher = $context['fetcher'];

        // If fetcher is a closure, execute it
        if (is_callable($fetcher)) {
            return $this->executeFetcher($fetcher, $request, $params, $context);
        }

        // If fetcher is a class name, instantiate and call
        if (is_string($fetcher) && class_exists($fetcher)) {
            $instance = app($fetcher);
            return $this->executeFetcher([$instance, 'fetch'], $request, $params, $context);
        }

        return null;
    }

    /**
     * Execute a fetcher with context configuration.
     *
     * @param callable $fetcher
     * @param Request $request
     * @param array $params
     * @param array $context
     * @return mixed
     */
    protected function executeFetcher(callable $fetcher, Request $request, array $params, array $context): mixed
    {
        // Build fetcher parameters
        $fetcherParams = [
            'request' => $request,
            'params' => $params,
            'identifier' => $params[$context['identifier']] ?? null,
            'filters' => $this->buildFilters($request, $context),
            'sorts' => $this->buildSorts($request, $context),
            'pagination' => $context['pagination'],
            'per_page' => $request->get('per_page', $context['per_page']),
            'eager_load' => $context['eager_load'],
        ];

        // Execute fetcher
        return call_user_func($fetcher, $fetcherParams);
    }

    /**
     * Build filters from request and context config.
     *
     * @param Request $request
     * @param array $context
     * @return array
     */
    protected function buildFilters(Request $request, array $context): array
    {
        $filters = [];

        // Get allowed filters from context
        $allowedFilters = $context['filters'] ?? [];

        foreach ($allowedFilters as $filter) {
            if ($request->has($filter)) {
                $filters[$filter] = $request->get($filter);
            }
        }

        return $filters;
    }

    /**
     * Build sorts from request and context config.
     *
     * @param Request $request
     * @param array $context
     * @return array
     */
    protected function buildSorts(Request $request, array $context): array
    {
        $sorts = [];

        // Get allowed sorts from context
        $allowedSorts = $context['sorts'] ?? [];

        $sortBy = $request->get('sort_by');
        $sortDirection = $request->get('sort_direction', 'asc');

        if ($sortBy && in_array($sortBy, $allowedSorts)) {
            $sorts[$sortBy] = $sortDirection;
        }

        return $sorts;
    }

    /**
     * Get cache key for a context.
     *
     * @param string $key
     * @param mixed $identifier
     * @return string
     */
    protected function getCacheKey(string $key, mixed $identifier): string
    {
        return "page_context:{$key}:{$identifier}";
    }

    /**
     * Clear cache for a context.
     *
     * @param string $key
     * @param mixed $identifier
     * @return void
     */
    public function clearCache(string $key, mixed $identifier = null): void
    {
        if ($identifier) {
            cache()->forget($this->getCacheKey($key, $identifier));
        } else {
            // Clear all cache for this context
            cache()->flush(); // In production, use tags or more specific clearing
        }
    }

    /**
     * Remove a context.
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        unset($this->contexts[$key]);
    }

    /**
     * Get all registered context keys.
     * These are used to exclude pages with these context_keys from static routing.
     *
     * @return array
     */
    public function getRegisteredKeys(): array
    {
        return array_keys($this->contexts);
    }

    /**
     * Check if a context key is registered.
     *
     * @param string $key
     * @return bool
     */
    public function isRegistered(string $key): bool
    {
        return isset($this->contexts[$key]);
    }
}
