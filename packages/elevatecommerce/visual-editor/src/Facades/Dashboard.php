<?php

namespace ElevateCommerce\VisualEditor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $key, array $component)
 * @method static void registerMany(array $components)
 * @method static array all()
 * @method static array|null get(string $key)
 * @method static bool has(string $key)
 * @method static void remove(string $key)
 * @method static array filter(callable $callback)
 * @method static array byWidth(string $width)
 * @method static array forUser($user = null)
 * @method static string|null render(string $key)
 * @method static array renderAll()
 *
 * @see \ElevateCommerce\VisualEditor\Support\DashboardRegistry
 */
class Dashboard extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'visual-editor.dashboard';
    }
}
