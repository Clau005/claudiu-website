# Navigation Registry Guide

The Visual Editor package includes a Navigation Registry that allows any package to register menu items in the admin sidebar.

## How It Works

The Navigation Registry is a singleton service that stores navigation items. All packages can register their menu items, and they'll automatically appear in the admin sidebar.

## Registering Navigation in Your Package

### In Your Service Provider

```php
<?php

namespace YourVendor\YourPackage;

use Illuminate\Support\ServiceProvider;

class YourPackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Get the navigation registry
        $navigation = app('visual-editor.navigation');

        // Register a single navigation item
        $navigation->register('products', [
            'label' => 'Products',
            'url' => '/admin/products',
            'icon' => '📦',
            'order' => 10,
        ]);

        // Register multiple items at once
        $navigation->registerMany([
            'orders' => [
                'label' => 'Orders',
                'url' => '/admin/orders',
                'icon' => '🛒',
                'order' => 20,
            ],
            'customers' => [
                'label' => 'Customers',
                'url' => '/admin/customers',
                'icon' => '👥',
                'order' => 30,
            ],
        ]);
    }
}
```

### Using the Facade (Alternative)

```php
use ElevateCommerce\VisualEditor\Facades\Navigation;

public function boot(): void
{
    Navigation::register('settings', [
        'label' => 'Settings',
        'url' => '/admin/settings',
        'icon' => '⚙️',
        'order' => 100,
    ]);
}
```

## Navigation Item Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `label` | string | Yes | Display text for the menu item |
| `url` | string | Yes | URL to navigate to |
| `icon` | string\|null | No | Icon (emoji or HTML) |
| `order` | int | No | Sort order (lower = higher in list). Default: 100 |
| `permission` | string\|null | No | Permission required to view this item |
| `badge` | string\|null | No | Badge text (e.g., notification count) |
| `children` | array | No | Nested navigation items |

## Examples

### Basic Item

```php
$navigation->register('dashboard', [
    'label' => 'Dashboard',
    'url' => '/admin/dashboard',
    'icon' => '🏠',
    'order' => 1,
]);
```

### Item with Badge

```php
$navigation->register('notifications', [
    'label' => 'Notifications',
    'url' => '/admin/notifications',
    'icon' => '🔔',
    'order' => 50,
    'badge' => '5', // Or use a closure for dynamic badges
]);
```

### Item with Dynamic Badge

```php
$navigation->register('orders', [
    'label' => 'Orders',
    'url' => '/admin/orders',
    'icon' => '🛒',
    'order' => 20,
    'badge' => function() {
        return \App\Models\Order::where('status', 'pending')->count();
    },
]);
```

### Item with Nested Children

```php
$navigation->register('products', [
    'label' => 'Products',
    'url' => '/admin/products',
    'icon' => '📦',
    'order' => 10,
    'children' => [
        'all-products' => [
            'label' => 'All Products',
            'url' => '/admin/products',
            'order' => 1,
        ],
        'add-product' => [
            'label' => 'Add New',
            'url' => '/admin/products/create',
            'order' => 2,
        ],
        'categories' => [
            'label' => 'Categories',
            'url' => '/admin/products/categories',
            'order' => 3,
        ],
    ],
]);
```

### Item with Permission

```php
$navigation->register('settings', [
    'label' => 'Settings',
    'url' => '/admin/settings',
    'icon' => '⚙️',
    'order' => 100,
    'permission' => 'manage-settings',
]);
```

## Registry Methods

### `register(string $key, array $item)`
Register a single navigation item.

```php
$navigation->register('my-page', [
    'label' => 'My Page',
    'url' => '/admin/my-page',
]);
```

### `registerMany(array $items)`
Register multiple navigation items at once.

```php
$navigation->registerMany([
    'page1' => [...],
    'page2' => [...],
]);
```

### `all()`
Get all registered navigation items (sorted by order).

```php
$items = $navigation->all();
```

### `get(string $key)`
Get a specific navigation item.

```php
$item = $navigation->get('dashboard');
```

### `has(string $key)`
Check if a navigation item exists.

```php
if ($navigation->has('products')) {
    // Item exists
}
```

### `remove(string $key)`
Remove a navigation item.

```php
$navigation->remove('unwanted-item');
```

### `filter(callable $callback)`
Get filtered navigation items.

```php
$filtered = $navigation->filter(function($item) {
    return auth()->user('admin')->can($item['permission'] ?? null);
});
```

## Complete Package Example

```php
<?php

namespace ElevateCommerce\Products;

use Illuminate\Support\ServiceProvider;

class ProductsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'products');

        // Register navigation
        $this->registerNavigation();
    }

    protected function registerNavigation(): void
    {
        $navigation = app('visual-editor.navigation');

        $navigation->register('products', [
            'label' => 'Products',
            'url' => route('admin.products.index'),
            'icon' => '📦',
            'order' => 10,
            'children' => [
                'all' => [
                    'label' => 'All Products',
                    'url' => route('admin.products.index'),
                    'order' => 1,
                ],
                'create' => [
                    'label' => 'Add New',
                    'url' => route('admin.products.create'),
                    'order' => 2,
                ],
                'categories' => [
                    'label' => 'Categories',
                    'url' => route('admin.products.categories'),
                    'order' => 3,
                ],
            ],
        ]);
    }
}
```

## Tips

1. **Order Numbers**: Use increments of 10 (10, 20, 30) to leave room for items to be inserted between
2. **Icons**: Use emojis for simplicity, or HTML/SVG for more control
3. **Dynamic Badges**: Use closures for badges that need to be calculated on each request
4. **Permissions**: Always set permissions for sensitive areas
5. **Children**: Keep nesting to one level for better UX

## Visual Editor's Default Navigation

The Visual Editor package registers the Dashboard item by default:

```php
protected function registerNavigation(): void
{
    $navigation = app('visual-editor.navigation');

    $navigation->register('dashboard', [
        'label' => 'Dashboard',
        'url' => '/admin/dashboard',
        'icon' => '🏠',
        'order' => 1,
    ]);
}
```

You can remove or modify this in your own package if needed.




php
// Navigation
'icon' => '🏠',  // Home/Dashboard
'icon' => '📊',  // Analytics/Reports
'icon' => '⚙️',  // Settings
'icon' => '👥',  // Users/Customers
'icon' => '📦',  // Products/Packages
'icon' => '🛒',  // Orders/Cart
'icon' => '💳',  // Payments
'icon' => '📝',  // Content/Posts
'icon' => '📄',  // Pages/Documents
'icon' => '🖼️',  // Media/Images
'icon' => '🔔',  // Notifications
'icon' => '💬',  // Messages/Chat
'icon' => '📧',  // Email
'icon' => '🔐',  // Security
'icon' => '🎨',  // Design/Themes
'icon' => '🔌',  // Plugins/Extensions
'icon' => '📈',  // Growth/Statistics
'icon' => '🏷️',  // Tags/Categories
'icon' => '🔍',  // Search
'icon' => '📁',  // Files/Folders
'icon' => '⭐',  // Featured/Favorites
'icon' => '🌐',  // Website/Global
'icon' => '🚀',  // Launch/Deploy
'icon' => '<i class="fas fa-home"></i>',
'icon' => '<i class="fas fa-users"></i>',
'icon' => '<i class="fas fa-cog"></i>',
'icon' => '<svg class="w-5 h-5">...</svg>',