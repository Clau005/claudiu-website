<?php

namespace ElevateCommerce\VisualEditor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $key, array $config)
 * @method static void registerMany(array $contexts)
 * @method static array|null get(string $key)
 * @method static bool has(string $key)
 * @method static array all()
 * @method static mixed fetch(string $key, \Illuminate\Http\Request $request, array $params = [])
 * @method static void clearCache(string $key, mixed $identifier = null)
 * @method static void remove(string $key)
 *
 * @see \ElevateCommerce\VisualEditor\Support\ContextRegistry
 */
class Context extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'visual-editor.context';
    }
}
