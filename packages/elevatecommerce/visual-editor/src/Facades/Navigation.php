<?php

namespace ElevateCommerce\VisualEditor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \ElevateCommerce\VisualEditor\Support\NavigationRegistry register(string $key, array $item)
 * @method static \ElevateCommerce\VisualEditor\Support\NavigationRegistry registerMany(array $items)
 * @method static array all()
 * @method static array|null get(string $key)
 * @method static \ElevateCommerce\VisualEditor\Support\NavigationRegistry remove(string $key)
 * @method static bool has(string $key)
 * @method static array filter(callable|null $callback = null)
 *
 * @see \ElevateCommerce\VisualEditor\Support\NavigationRegistry
 */
class Navigation extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'visual-editor.navigation';
    }
}
