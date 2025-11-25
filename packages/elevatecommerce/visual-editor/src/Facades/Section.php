<?php

namespace ElevateCommerce\VisualEditor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $key, array $config)
 * @method static void registerMany(array $sections)
 * @method static array|null get(string $key)
 * @method static bool has(string $key)
 * @method static array all()
 * @method static array byCategory(string $category)
 * @method static array forContext(string $contextKey)
 * @method static array categories()
 * @method static string|null render(string $key, array $settings = [], mixed $contextData = null)
 * @method static array getSchema(string $key)
 * @method static array validate(string $key, array $settings)
 * @method static void remove(string $key)
 *
 * @see \ElevateCommerce\VisualEditor\Support\SectionRegistry
 */
class Section extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'visual-editor.section';
    }
}
