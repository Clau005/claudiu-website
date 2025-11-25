# Theme Creation Guide

Complete guide to creating your first theme for the Visual Editor.

## Quick Start

### 1. Create Theme Directory

```bash
mkdir -p resources/views/themes/dawn
mkdir -p resources/views/themes/dawn/sections
mkdir -p resources/views/themes/dawn/section-configs
```

### 2. Create `theme.json`

Create `resources/views/themes/dawn/theme.json`:

```json
{
  "name": "Dawn",
  "slug": "dawn",
  "version": "1.0.0",
  "author": "Your Name",
  "description": "A modern, clean theme perfect for any store",
  "preview_image": "/themes/dawn/preview.jpg",
  "settings": {
    "primary_color": "#3B82F6",
    "secondary_color": "#10B981",
    "font_family": "Inter"
  }
}
```

### 3. Create Your First Section

**Section Config:** `resources/views/themes/dawn/section-configs/hero.json`

```json
{
  "label": "Hero Banner",
  "category": "marketing",
  "icon": "🎨",
  "preview_image": "/themes/dawn/previews/hero.jpg",
  "schema": {
    "title": {
      "type": "text",
      "label": "Title",
      "required": true,
      "default": "Welcome to Our Store"
    },
    "subtitle": {
      "type": "textarea",
      "label": "Subtitle",
      "maxlength": 200
    },
    "background_image": {
      "type": "image",
      "label": "Background Image"
    },
    "button_text": {
      "type": "text",
      "label": "Button Text"
    },
    "button_url": {
      "type": "url",
      "label": "Button URL"
    },
    "text_color": {
      "type": "color",
      "label": "Text Color",
      "default": "#ffffff"
    },
    "overlay_opacity": {
      "type": "range",
      "label": "Overlay Opacity",
      "min": 0,
      "max": 100,
      "default": 40
    }
  },
  "defaults": {
    "title": "Welcome to Our Store",
    "subtitle": "Discover amazing products",
    "button_text": "Shop Now",
    "text_color": "#ffffff",
    "overlay_opacity": 40
  }
}
```

**Section Blade:** `resources/views/themes/dawn/sections/hero.blade.php`

```blade
<section class="relative h-screen flex items-center justify-center overflow-hidden"
         style="background-image: url('{{ $settings->background_image ?? '' }}'); background-size: cover; background-position: center;">
    
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black" style="opacity: {{ ($settings->overlay_opacity ?? 40) / 100 }}"></div>
    
    {{-- Content --}}
    <div class="relative z-10 container mx-auto px-4 text-center">
        <h1 class="text-6xl font-bold mb-6" style="color: {{ $settings->text_color ?? '#ffffff' }}">
            {{ $settings->title }}
        </h1>
        
        @if($settings->subtitle ?? null)
            <p class="text-2xl mb-8" style="color: {{ $settings->text_color ?? '#ffffff' }}">
                {{ $settings->subtitle }}
            </p>
        @endif
        
        @if(($settings->button_text ?? null) && ($settings->button_url ?? null))
            <a href="{{ $settings->button_url }}" 
               class="inline-block px-8 py-4 bg-white text-gray-900 rounded-lg font-semibold hover:bg-gray-100 transition">
                {{ $settings->button_text }}
            </a>
        @endif
    </div>
</section>
```

### 4. Sync Theme to Database

```bash
php artisan visual-editor:sync-themes
```

Output:
```
Syncing themes from filesystem to database...

  ✓ Created theme: Dawn (v1.0.0)

✓ Successfully synced 1 theme(s)
```

### 5. Activate Your Theme

```php
use ElevateCommerce\VisualEditor\Models\Theme;

$theme = Theme::where('slug', 'dawn')->first();
$theme->activate();
```

## Complete Example: Product Page Sections

### Product Info Section

**Config:** `section-configs/product-info.json`

```json
{
  "label": "Product Information",
  "category": "product",
  "icon": "📦",
  "contexts": ["product"],
  "schema": {
    "show_sku": {
      "type": "boolean",
      "label": "Show SKU",
      "default": true
    },
    "show_vendor": {
      "type": "boolean",
      "label": "Show Vendor",
      "default": true
    },
    "show_share_buttons": {
      "type": "boolean",
      "label": "Show Share Buttons",
      "default": true
    },
    "layout": {
      "type": "select",
      "label": "Layout",
      "options": {
        "stacked": "Stacked",
        "side-by-side": "Side by Side"
      },
      "default": "side-by-side"
    }
  }
}
```

**Blade:** `sections/product-info.blade.php`

```blade
<section class="container mx-auto px-4 py-12">
    @if($context)
        <div class="grid grid-cols-1 {{ $settings->layout === 'side-by-side' ? 'md:grid-cols-2' : '' }} gap-12">
            
            {{-- Product Images --}}
            <div>
                @if($context->images && count($context->images) > 0)
                    <img src="{{ $context->images[0]->url }}" 
                         alt="{{ $context->name }}" 
                         class="w-full rounded-lg shadow-lg">
                    
                    @if(count($context->images) > 1)
                        <div class="grid grid-cols-4 gap-4 mt-4">
                            @foreach($context->images->slice(1, 4) as $image)
                                <img src="{{ $image->url }}" 
                                     alt="{{ $context->name }}" 
                                     class="w-full rounded cursor-pointer hover:opacity-75">
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

            {{-- Product Details --}}
            <div>
                <h1 class="text-4xl font-bold mb-4">{{ $context->name }}</h1>
                
                @if($settings->show_vendor && $context->vendor)
                    <p class="text-gray-600 mb-2">by {{ $context->vendor }}</p>
                @endif

                @if($settings->show_sku && $context->sku)
                    <p class="text-sm text-gray-500 mb-4">SKU: {{ $context->sku }}</p>
                @endif

                <p class="text-3xl font-bold text-blue-600 mb-6">
                    ${{ number_format($context->price, 2) }}
                </p>

                <div class="prose mb-8">
                    {!! $context->description !!}
                </div>

                {{-- Variants --}}
                @if($context->variants && count($context->variants) > 0)
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Select Variant</label>
                        <select class="w-full border rounded-lg px-4 py-2">
                            @foreach($context->variants as $variant)
                                <option value="{{ $variant->id }}">
                                    {{ $variant->name }} - ${{ number_format($variant->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button class="w-full bg-blue-600 text-white py-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Add to Cart
                </button>

                @if($settings->show_share_buttons)
                    <div class="mt-6 flex gap-4">
                        <button class="text-gray-600 hover:text-blue-600">📘 Share on Facebook</button>
                        <button class="text-gray-600 hover:text-blue-600">🐦 Share on Twitter</button>
                        <button class="text-gray-600 hover:text-blue-600">📧 Email</button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</section>
```

### Product Reviews Section

**Config:** `section-configs/product-reviews.json`

```json
{
  "label": "Product Reviews",
  "category": "product",
  "icon": "⭐",
  "contexts": ["product"],
  "schema": {
    "reviews_per_page": {
      "type": "number",
      "label": "Reviews Per Page",
      "default": 10,
      "min": 5,
      "max": 50
    },
    "allow_images": {
      "type": "boolean",
      "label": "Allow Image Uploads in Reviews",
      "default": true
    },
    "show_verified_badge": {
      "type": "boolean",
      "label": "Show Verified Purchase Badge",
      "default": true
    }
  }
}
```

**Blade:** `sections/product-reviews.blade.php`

```blade
<section class="container mx-auto px-4 py-12">
    @if($context && $context->reviews)
        <h2 class="text-3xl font-bold mb-8">Customer Reviews</h2>
        
        {{-- Reviews Summary --}}
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="text-5xl font-bold">{{ number_format($context->average_rating, 1) }}</div>
                <div>
                    <div class="flex text-yellow-400 text-2xl mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($context->average_rating))
                                ⭐
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                    <p class="text-gray-600">Based on {{ $context->reviews->count() }} reviews</p>
                </div>
            </div>
        </div>

        {{-- Individual Reviews --}}
        <div class="space-y-6">
            @foreach($context->reviews->take($settings->reviews_per_page ?? 10) as $review)
                <div class="border-b pb-6">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold">{{ $review->user->name }}</span>
                                @if($settings->show_verified_badge && $review->verified_purchase)
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                        ✓ Verified Purchase
                                    </span>
                                @endif
                            </div>
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{ $review->created_at->diffForHumans() }}
                        </span>
                    </div>
                    
                    <p class="text-gray-700 mb-3">{{ $review->comment }}</p>
                    
                    @if($settings->allow_images && $review->images && count($review->images) > 0)
                        <div class="flex gap-2">
                            @foreach($review->images as $image)
                                <img src="{{ $image->url }}" 
                                     alt="Review image" 
                                     class="w-20 h-20 object-cover rounded">
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>
```

## File Structure Reference

```
resources/views/themes/dawn/
├── theme.json                          # Theme metadata
├── sections/                           # Blade templates
│   ├── hero.blade.php
│   ├── product-info.blade.php
│   ├── product-reviews.blade.php
│   ├── collection-grid.blade.php
│   ├── featured-products.blade.php
│   └── footer.blade.php
└── section-configs/                    # Section configurations
    ├── hero.json
    ├── product-info.json
    ├── product-reviews.json
    ├── collection-grid.json
    ├── featured-products.json
    └── footer.json
```

## Available Variables in Sections

### $settings
Editor-configured values (what the merchant sets):
```blade
{{ $settings->title }}
{{ $settings->subtitle }}
{{ $settings->show_sku }}
{{ $settings->text_color }}
```

### $context
Dynamic data from database (product, collection, etc.):
```blade
{{ $context->name }}
{{ $context->price }}
{{ $context->images }}
{{ $context->variants }}
```

### $_section
Section metadata:
```blade
{{ $_section['key'] }}    // e.g., "dawn-hero"
{{ $_section['name'] }}   // e.g., "dawn-hero"
```

## Schema Field Types

```json
{
  "text": {
    "type": "text",
    "label": "Title",
    "required": true,
    "maxlength": 100
  },
  "textarea": {
    "type": "textarea",
    "label": "Description",
    "rows": 5
  },
  "number": {
    "type": "number",
    "label": "Count",
    "min": 1,
    "max": 100,
    "default": 10
  },
  "boolean": {
    "type": "boolean",
    "label": "Show Feature",
    "default": true
  },
  "select": {
    "type": "select",
    "label": "Layout",
    "options": {
      "grid": "Grid",
      "list": "List"
    }
  },
  "color": {
    "type": "color",
    "label": "Background Color",
    "default": "#ffffff"
  },
  "image": {
    "type": "image",
    "label": "Background Image",
    "accept": "image/*"
  },
  "url": {
    "type": "url",
    "label": "Link URL"
  },
  "range": {
    "type": "range",
    "label": "Opacity",
    "min": 0,
    "max": 100,
    "default": 50
  }
}
```

## Next Steps

1. Create more sections for your theme
2. Register contexts in your app (product, collection, etc.)
3. Build the visual editor UI
4. Create page templates

Happy theming! ��
