# Example: Creating Another Package That Uses Visual Editor

This example shows how to create a new package that integrates with the Visual Editor navigation system.

## Example Package Structure

```
packages/elevatecommerce/products/
├── src/
│   ├── Http/
│   │   └── Controllers/
│   │       └── ProductController.php
│   ├── Models/
│   │   └── Product.php
│   └── ProductsServiceProvider.php
├── routes/
│   └── web.php
├── resources/
│   └── views/
│       └── products/
│           └── index.blade.php
└── composer.json
```

## Example Service Provider

```php
<?php

namespace ElevateCommerce\Products;

use Illuminate\Support\ServiceProvider;
use ElevateCommerce\VisualEditor\Facades\Navigation;

class ProductsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register navigation items
        Navigation::registerMany([
            'products' => [
                'label' => 'Products',
                'url' => route('admin.products.index'),
                'icon' => '📦',
                'order' => 10,
                'children' => [
                    'products.all' => [
                        'label' => 'All Products',
                        'url' => route('admin.products.index'),
                        'order' => 1,
                    ],
                    'products.create' => [
                        'label' => 'Add New',
                        'url' => route('admin.products.create'),
                        'order' => 2,
                    ],
                    'products.categories' => [
                        'label' => 'Categories',
                        'url' => route('admin.products.categories'),
                        'order' => 3,
                    ],
                ],
            ],
        ]);

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'products');
    }
}
```

## Example Routes

```php
<?php

use Illuminate\Support\Facades\Route;
use ElevateCommerce\Products\Http\Controllers\ProductController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'auth:admin'])
    ->group(function () {
        Route::resource('products', ProductController::class);
        Route::get('products/categories', [ProductController::class, 'categories'])
            ->name('products.categories');
    });
```

## Example Controller

```php
<?php

namespace ElevateCommerce\Products\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function index()
    {
        return view('products::products.index', [
            'products' => [],
        ]);
    }

    public function create()
    {
        return view('products::products.create');
    }

    // ... other methods
}
```

## Registering the Package

Add to main `composer.json`:

```json
{
    "autoload": {
        "psr-4": {
            "ElevateCommerce\\Products\\": "packages/elevatecommerce/products/src/"
        }
    }
}
```

Add to `config/app.php` providers array:

```php
'providers' => [
    // ...
    ElevateCommerce\Products\ProductsServiceProvider::class,
],
```

Or use auto-discovery in the package's `composer.json`:

```json
{
    "extra": {
        "laravel": {
            "providers": [
                "ElevateCommerce\\Products\\ProductsServiceProvider"
            ]
        }
    }
}
```

## Dynamic Navigation with Badges

```php
// In your service provider's boot method
Navigation::register('orders', [
    'label' => 'Orders',
    'url' => route('admin.orders.index'),
    'icon' => '🛒',
    'order' => 20,
    'badge' => function() {
        return \App\Models\Order::where('status', 'pending')->count();
    },
]);
```

## Permission-Based Navigation

```php
Navigation::register('settings', [
    'label' => 'Settings',
    'url' => route('admin.settings'),
    'icon' => '⚙️',
    'order' => 100,
    'permission' => 'manage-settings',
]);

// Then in your view, filter by permission:
$navigation = Navigation::filter(function($item) {
    return !$item['permission'] || auth()->user('admin')->can($item['permission']);
});
```
