# Visual Editor Package

A powerful Laravel package for building visual page builders with themes, collections, and automatic image optimization.

## Features

- ✅ **Visual Page Builder**: Drag-and-drop section-based page builder
- ✅ **Theme System**: Multi-theme support with live preview
- ✅ **Collections**: Polymorphic collections (manual & smart - coming soon)
- ✅ **Image Optimization**: Automatic WebP conversion & responsive images
- ✅ **Media Library**: Full-featured media management
- ✅ **Admin Authentication**: Complete admin auth system
- ✅ **Navigation Registry**: Extensible navigation system
- ✅ **Context Registry**: Dynamic page contexts (products, collections, etc.)
- ✅ **Performance**: 100/100 Lighthouse scores with optimized routing
- ✅ **Modular Structure**: Clean, extensible architecture

## Installation

### Via Composer (Production)

```bash
composer require elevatecommerce/visual-editor
```

### Local Development

1. Add the package to your main `composer.json`:

```json
{
    "autoload": {
        "psr-4": {
            "ElevateCommerce\\VisualEditor\\": "packages/elevatecommerce/visual-editor/src/"
        }
    }
}
```

2. Run composer autoload:

```bash
composer dump-autoload
```

3. Build the Vue assets:

```bash
cd packages/elevatecommerce/visual-editor
npm install
npm run build
cd ../../..
```

4. Publish package assets:

```bash
php artisan vendor:publish --tag=visual-editor-assets
php artisan vendor:publish --tag=visual-editor-migrations
```

5. Run migrations:

```bash
php artisan migrate
```

6. Create an admin user (you can create a seeder or use tinker):

```bash
php artisan tinker
```

```php
ElevateCommerce\VisualEditor\Models\Admin::create([
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'is_super_admin' => true,
]);
```

## Usage

### Accessing the Admin Panel

Navigate to `/admin/login` to access the admin panel.

### Registering Navigation Items

From any service provider or package, you can register navigation items:

```php
use ElevateCommerce\VisualEditor\Facades\Navigation;

  $navigation = app('visual-editor.navigation'); //or use the facade Navigation::register() or Navigation::registerMany()

        // Top-level: Dashboard
        $navigation->register('dashboard', [
            'label' => 'Dashboard',
            'url' => '/admin/dashboard',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
            'order' => 1,
        ]);

        // Group: Online store (Themes + Pages + Collections)
        $navigation->register('online-store', [
            'label' => 'Online store',
            'icon' => '  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />',
            'order' => 2,
            'children' => [
                'themes' => [
                    'label' => 'Themes',
                    'url' => '/admin/themes',
                    'order' => 1,
                ],
                'pages' => [
                    'label' => 'Pages',
                    'url' => '/admin/pages',
                    'order' => 2,
                ],
                'collections' => [
                    'label' => 'Collections',
                    'url' => '/admin/collections',
                    'order' => 3,
                ],
            ],
        ]);
```

### Navigation Item Options

- `label` (string): Display text
- `url` (string): Navigation URL
- `icon` (string|null): Icon (emoji or HTML)
- `order` (int): Sort order (lower = higher in list)
- `permission` (string|null): Permission required to view
- `badge` (string|null): Badge text (e.g., notification count)
- `children` (array): Nested navigation items

### Using the Navigation Facade

```php
// Get all navigation items
$items = Navigation::all();

// Get a specific item
$item = Navigation::get('products');

// Check if item exists
if (Navigation::has('products')) {
    // ...
}

// Remove an item
Navigation::remove('products');

// Filter items (e.g., by permission)
$filtered = Navigation::filter(function($item) {
    return auth()->user()->can($item['permission']);
});
```

## Package Structure

```
packages/elevatecommerce/visual-editor/
├── config/
│   ├── auth.php              # Admin auth configuration
│   └── visual-editor.php     # Package configuration
├── database/
│   └── migrations/           # Database migrations
├── resources/
│   └── views/
│       └── admin/            # Admin views
├── routes/
│   └── web.php              # Package routes
└── src/
    ├── Console/             # Artisan commands
    ├── Http/
    │   ├── Controllers/     # Controllers
    │   └── Middleware/      # Middleware
    ├── Models/              # Eloquent models
    ├── Notifications/       # Notifications
    ├── Services/            # Business logic
    ├── Support/             # Support classes (registries, helpers)
    └── Facades/             # Facades
```

## Creating Additional Packages

Other packages can follow the same structure and register their own navigation items, routes, and functionality.

Example from another package's service provider:

```php
namespace YourVendor\YourPackage;

use Illuminate\Support\ServiceProvider;
use ElevateCommerce\VisualEditor\Facades\Navigation;

class YourPackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register your navigation items
        Navigation::register('your-feature', [
            'label' => 'Your Feature',
            'url' => '/admin/your-feature',
            'icon' => '⚡',
            'order' => 50,
        ]);
    }
}
```

## Image Optimization

The package includes automatic image optimization with WebP conversion and responsive sizing.

### Setup

1. Install Intervention Image:
```bash
composer require intervention/image
```

2. Optimize existing images:
```bash
php artisan images:optimize
```

### Usage

Use the responsive image component in your themes:

```blade
{{-- Hero/LCP images (above the fold) --}}
<x-visual-editor::responsive-image 
    :src="$settings->background_image" 
    alt="Hero background"
    fetchpriority="high"
    loading="eager"
/>

{{-- Regular images (below the fold) --}}
<x-visual-editor::responsive-image 
    :src="$image->url" 
    :alt="$image->alt_text"
    loading="lazy"
/>
```

### What It Does

- ✅ Auto-converts to WebP (85% smaller, same quality)
- ✅ Generates responsive sizes (400px, 800px, 1600px)
- ✅ Serves optimal size based on device
- ✅ Falls back to original for old browsers
- ✅ Achieves 100/100 Lighthouse scores

## Collections

Create manual or smart collections of any content type.

### Creating Collections

```php
use ElevateCommerce\VisualEditor\Models\Collection;

$collection = Collection::create([
    'title' => 'Summer Collection',
    'slug' => 'summer-collection',
    'description' => 'Hot summer items',
    'type' => 'manual', // or 'smart'
]);
```

### Adding Items

```php
// Add a product to collection
$collection->addItem($product);

// Add multiple items
$collection->addItems([$product1, $product2]);

// Remove item
$collection->removeItem($product);
```

### Registering Collectable Types

In your `AppServiceProvider`:

```php
app('visual-editor.context')->register('products', [
    'model' => Product::class,
    'collectable' => true,
    'title_field' => 'name',
]);
```

### Public Routes

Collections are automatically available at:
- `/collections` - List all collections
- `/collections/{slug}` - View single collection

## Context Registry

Register dynamic page contexts for products, collections, etc.

```php
app('visual-editor.context')->register('products', [
    'model' => Product::class,
    'route_pattern' => '/products/{slug}',
    'title_field' => 'name',
    'collectable' => true,
    'fetcher' => function($request, $params) {
        return Product::where('slug', $params['identifier'])->firstOrFail();
    },
]);
```

## Performance

The package is optimized for maximum performance:

- ✅ Single catch-all route (minimal overhead)
- ✅ Automatic image optimization
- ✅ Response caching
- ✅ 100/100 Lighthouse scores (desktop & mobile)

## License

MIT
