# Page Builder System Guide

A Shopify-style page builder with themes, contexts, and sections.

## Architecture Overview

```
Theme (Active/Inactive)
  ├── Pages (Home, Product, Collection, etc.)
  │   ├── Type (static, dynamic, template)
  │   ├── Context (product, collection, etc.)
  │   ├── Draft Configuration
  │   └── Published Configuration
  └── Available Sections
```

## Core Concepts

### 1. Themes

Themes are containers for pages and sections. Only one theme can be active at a time.

- **Active Theme**: The theme currently being used on the frontend
- **Theme Library**: Inactive themes that can be activated or edited
- **Duplicate**: Create copies of themes for testing

### 2. Contexts

Contexts solve the N+1 query problem by centralizing data fetching.

**Problem**: If multiple sections need product data, each would query the database separately.

**Solution**: Context fetches data ONCE and passes it to all sections.

```php
Context::register('product', [
    'fetcher' => function ($params) {
        return Product::with(['images', 'variants', 'reviews'])
            ->where('slug', $params['identifier'])
            ->firstOrFail();
    },
    'identifier' => 'slug',
    'eager_load' => ['images', 'variants', 'reviews'],
    'cacheable' => true,
    'cache_ttl' => 3600,
]);
```

### 3. Sections

Sections are reusable Blade components that can be added to pages.

```php
Section::register('hero-banner', [
    'label' => 'Hero Banner',
    'view' => 'your-theme::sections.hero-banner',
    'category' => 'marketing',
    'icon' => '🎨',
    'schema' => [
        'title' => ['type' => 'text', 'required' => true],
        'subtitle' => ['type' => 'text'],
        'image' => ['type' => 'image'],
        'button_text' => ['type' => 'text'],
        'button_url' => ['type' => 'url'],
    ],
    'defaults' => [
        'title' => 'Welcome',
        'button_text' => 'Shop Now',
    ],
    'contexts' => [], // Empty = available for all contexts
]);
```

### 4. Pages

Pages belong to themes and have:
- **Draft Configuration**: Work-in-progress sections
- **Published Configuration**: Live sections on the frontend
- **Context**: Optional data source (product, collection, etc.)

## Registration Examples

### Register a Context

```php
// In your package's service provider
public function boot(): void
{
    $context = app('visual-editor.context');

    // Product context
    $context->register('product', [
        'fetcher' => function ($params) {
            return \App\Models\Product::with([
                'images',
                'variants',
                'reviews',
                'related_products'
            ])
            ->where($params['identifier'], $params['params']['identifier'])
            ->firstOrFail();
        },
        'identifier' => 'slug',
        'eager_load' => ['images', 'variants', 'reviews'],
        'cacheable' => true,
        'cache_ttl' => 3600,
        'filters' => ['category', 'brand', 'price_range'],
        'sorts' => ['price', 'name', 'created_at'],
    ]);

    // Collection context with pagination
    $context->register('collection', [
        'fetcher' => function ($params) {
            $collection = \App\Models\Collection::where('slug', $params['identifier'])
                ->firstOrFail();

            $query = $collection->products()->with(['images']);

            // Apply filters
            foreach ($params['filters'] as $key => $value) {
                if ($key === 'price_range') {
                    $query->whereBetween('price', explode(',', $value));
                } else {
                    $query->where($key, $value);
                }
            }

            // Apply sorts
            foreach ($params['sorts'] as $field => $direction) {
                $query->orderBy($field, $direction);
            }

            // Paginate if enabled
            if ($params['pagination']) {
                $products = $query->paginate($params['per_page']);
            } else {
                $products = $query->get();
            }

            return [
                'collection' => $collection,
                'products' => $products,
            ];
        },
        'identifier' => 'slug',
        'pagination' => true,
        'per_page' => 24,
        'filters' => ['category', 'brand', 'price_range', 'color', 'size'],
        'sorts' => ['price', 'name', 'popularity', 'created_at'],
    ]);
}
```

### Register Sections

```php
public function boot(): void
{
    $section = app('visual-editor.section');

    // Hero section (works on any page)
    $section->register('hero', [
        'label' => 'Hero Banner',
        'view' => 'your-theme::sections.hero',
        'category' => 'marketing',
        'icon' => '🎨',
        'schema' => [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'required' => true,
            ],
            'subtitle' => [
                'type' => 'textarea',
                'label' => 'Subtitle',
            ],
            'background_image' => [
                'type' => 'image',
                'label' => 'Background Image',
            ],
            'button_text' => [
                'type' => 'text',
                'label' => 'Button Text',
            ],
            'button_url' => [
                'type' => 'url',
                'label' => 'Button URL',
            ],
            'text_color' => [
                'type' => 'color',
                'label' => 'Text Color',
                'default' => '#ffffff',
            ],
        ],
        'defaults' => [
            'title' => 'Welcome to Our Store',
            'subtitle' => 'Discover amazing products',
            'button_text' => 'Shop Now',
            'text_color' => '#ffffff',
        ],
    ]);

    // Product info section (only for product pages)
    $section->register('product-info', [
        'label' => 'Product Information',
        'view' => 'your-theme::sections.product-info',
        'category' => 'product',
        'icon' => '📦',
        'contexts' => ['product'], // Only available on product pages
        'schema' => [
            'show_sku' => [
                'type' => 'boolean',
                'label' => 'Show SKU',
                'default' => true,
            ],
            'show_vendor' => [
                'type' => 'boolean',
                'label' => 'Show Vendor',
                'default' => true,
            ],
            'show_share_buttons' => [
                'type' => 'boolean',
                'label' => 'Show Share Buttons',
                'default' => true,
            ],
        ],
        'defaults' => [
            'show_sku' => true,
            'show_vendor' => true,
            'show_share_buttons' => true,
        ],
    ]);

    // Product reviews section
    $section->register('product-reviews', [
        'label' => 'Product Reviews',
        'view' => 'your-theme::sections.product-reviews',
        'category' => 'product',
        'icon' => '⭐',
        'contexts' => ['product'],
        'schema' => [
            'reviews_per_page' => [
                'type' => 'number',
                'label' => 'Reviews Per Page',
                'default' => 10,
            ],
            'allow_images' => [
                'type' => 'boolean',
                'label' => 'Allow Image Uploads',
                'default' => true,
            ],
        ],
    ]);

    // Collection grid section
    $section->register('collection-grid', [
        'label' => 'Product Grid',
        'view' => 'your-theme::sections.collection-grid',
        'category' => 'collection',
        'icon' => '📱',
        'contexts' => ['collection'],
        'schema' => [
            'columns' => [
                'type' => 'select',
                'label' => 'Columns',
                'options' => [2, 3, 4, 5],
                'default' => 4,
            ],
            'show_filters' => [
                'type' => 'boolean',
                'label' => 'Show Filters',
                'default' => true,
            ],
            'show_sort' => [
                'type' => 'boolean',
                'label' => 'Show Sort Options',
                'default' => true,
            ],
        ],
        'defaults' => [
            'columns' => 4,
            'show_filters' => true,
            'show_sort' => true,
        ],
    ]);
}
```

### Create Section Views

```blade
{{-- resources/views/sections/hero.blade.php --}}
<section class="relative h-screen flex items-center justify-center"
         style="background-image: url('{{ $background_image }}'); background-size: cover;">
    <div class="absolute inset-0 bg-black opacity-40"></div>
    <div class="relative z-10 text-center" style="color: {{ $text_color }};">
        <h1 class="text-6xl font-bold mb-4">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-2xl mb-8">{{ $subtitle }}</p>
        @endif
        @if($button_text && $button_url)
            <a href="{{ $button_url }}" class="inline-block px-8 py-4 bg-white text-gray-900 rounded-lg font-semibold hover:bg-gray-100">
                {{ $button_text }}
            </a>
        @endif
    </div>
</section>
```

```blade
{{-- resources/views/sections/product-info.blade.php --}}
<section class="container mx-auto px-4 py-8">
    @if($_context)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Product Images --}}
            <div>
                <img src="{{ $_context->featured_image }}" alt="{{ $_context->name }}" class="w-full rounded-lg">
            </div>

            {{-- Product Details --}}
            <div>
                <h1 class="text-4xl font-bold mb-4">{{ $_context->name }}</h1>
                
                @if($show_vendor && $_context->vendor)
                    <p class="text-gray-600 mb-2">by {{ $_context->vendor }}</p>
                @endif

                @if($show_sku && $_context->sku)
                    <p class="text-sm text-gray-500 mb-4">SKU: {{ $_context->sku }}</p>
                @endif

                <p class="text-3xl font-bold text-blue-600 mb-6">${{ number_format($_context->price, 2) }}</p>

                <div class="prose mb-6">
                    {!! $_context->description !!}
                </div>

                <button class="w-full bg-blue-600 text-white py-4 rounded-lg font-semibold hover:bg-blue-700">
                    Add to Cart
                </button>

                @if($show_share_buttons)
                    <div class="mt-6 flex gap-4">
                        <button class="text-gray-600 hover:text-blue-600">Share on Facebook</button>
                        <button class="text-gray-600 hover:text-blue-600">Share on Twitter</button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</section>
```

```blade
{{-- resources/views/sections/collection-grid.blade.php --}}
<section class="container mx-auto px-4 py-8">
    @if($_context)
        <h1 class="text-4xl font-bold mb-8">{{ $_context['collection']->name }}</h1>

        <div class="flex gap-8">
            {{-- Filters Sidebar --}}
            @if($show_filters)
                <aside class="w-64">
                    <h3 class="font-semibold mb-4">Filters</h3>
                    {{-- Filter UI here --}}
                </aside>
            @endif

            {{-- Products Grid --}}
            <div class="flex-1">
                @if($show_sort)
                    <div class="mb-6 flex justify-end">
                        <select class="border rounded px-4 py-2">
                            <option>Sort by: Featured</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Newest</option>
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-{{ $columns }} gap-6">
                    @foreach($_context['products'] as $product)
                        <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                            <img src="{{ $product->featured_image }}" alt="{{ $product->name }}" class="w-full h-64 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold mb-2">{{ $product->name }}</h3>
                                <p class="text-blue-600 font-bold">${{ number_format($product->price, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if(method_exists($_context['products'], 'links'))
                    <div class="mt-8">
                        {{ $_context['products']->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</section>
```

## Theme Management

### Create a Theme

```php
use ElevateCommerce\VisualEditor\Models\Theme;

$theme = Theme::create([
    'name' => 'My Theme',
    'slug' => 'my-theme',
    'description' => 'A beautiful theme',
    'version' => '1.0.0',
    'author' => 'Your Name',
    'settings' => [
        'primary_color' => '#3B82F6',
        'secondary_color' => '#10B981',
        'font_family' => 'Inter',
    ],
]);
```

### Activate a Theme

```php
$theme->activate();
```

### Duplicate a Theme

```php
$newTheme = $theme->duplicate('My Theme Copy');
```

## Page Management

### Create a Page

```php
use ElevateCommerce\VisualEditor\Models\Page;

// Static page (home, about, contact)
$page = Page::create([
    'theme_id' => $theme->id,
    'name' => 'Home',
    'slug' => 'home',
    'type' => 'static',
    'draft_config' => [],
]);

// Dynamic page (product, collection)
$page = Page::create([
    'theme_id' => $theme->id,
    'name' => 'Product',
    'slug' => 'product',
    'type' => 'dynamic',
    'context_key' => 'product',
    'route_pattern' => '/products/{slug}',
    'draft_config' => [],
]);
```

### Add Sections to a Page

```php
$page->addSection('hero', [
    'title' => 'Welcome to Our Store',
    'subtitle' => 'Shop the latest products',
    'button_text' => 'Shop Now',
    'button_url' => '/collections/all',
]);

$page->addSection('product-grid', [
    'columns' => 4,
    'show_filters' => true,
]);
```

### Publish a Page

```php
$page->publish();
```

## Frontend Rendering

The `PageRendererController` handles all rendering:

1. **Identifies the active theme**
2. **Loads the requested page**
3. **Fetches context data** (if page has a context)
4. **Renders all sections** with context data
5. **Returns the complete page**

### Routes

```php
// Static pages
Route::get('/', [PageRendererController::class, 'render'])->defaults('pageSlug', 'home');
Route::get('/about', [PageRendererController::class, 'render'])->defaults('pageSlug', 'about');

// Dynamic pages
Route::get('/products/{slug}', [PageRendererController::class, 'renderDynamic'])
    ->defaults('pageSlug', 'product');

Route::get('/collections/{slug}', [PageRendererController::class, 'renderDynamic'])
    ->defaults('pageSlug', 'collection');

// Preview (for editor)
Route::get('/admin/pages/{id}/preview', [PageRendererController::class, 'preview'])
    ->middleware('auth:admin');
```

## Benefits of This Architecture

1. **No N+1 Queries**: Context fetches data once
2. **Theme Isolation**: Each theme has its own pages and sections
3. **Draft/Publish Workflow**: Test changes before going live
4. **Extensible**: Packages can register contexts and sections
5. **Clean Controller**: Renderer just orchestrates, doesn't fetch data
6. **Reusable Sections**: Build once, use everywhere
7. **Type Safety**: Schemas validate section settings

## Next Steps

1. Build the visual editor UI (drag & drop interface)
2. Create API endpoints for editor operations
3. Add section preview thumbnails
4. Implement undo/redo functionality
5. Add theme import/export
