# Quick Start: Build Your First Theme in 5 Minutes

## Step 1: Create Theme Structure (1 min)

```bash
# Create theme directories
mkdir -p resources/views/themes/dawn/sections
mkdir -p resources/views/themes/dawn/section-configs
```

## Step 2: Create theme.json (1 min)

Create `resources/views/themes/dawn/theme.json`:

```json
{
  "name": "Dawn",
  "slug": "dawn",
  "version": "1.0.0",
  "author": "Your Name",
  "description": "My first theme"
}
```

## Step 3: Create Your First Section (2 min)

**Config:** `resources/views/themes/dawn/section-configs/hero.json`

```json
{
  "label": "Hero Banner",
  "category": "marketing",
  "icon": "🎨",
  "schema": {
    "title": {
      "type": "text",
      "label": "Title",
      "default": "Welcome!"
    },
    "subtitle": {
      "type": "text",
      "label": "Subtitle"
    }
  }
}
```

**Blade:** `resources/views/themes/dawn/sections/hero.blade.php`

```blade
<section class="bg-blue-600 text-white py-20 text-center">
    <div class="container mx-auto">
        <h1 class="text-5xl font-bold mb-4">{{ $settings->title }}</h1>
        @if($settings->subtitle ?? null)
            <p class="text-xl">{{ $settings->subtitle }}</p>
        @endif
    </div>
</section>
```

## Step 4: Sync and Activate (1 min)

```bash
# Sync theme to database
php artisan visual-editor:sync-themes

# Activate theme (in tinker or controller)
php artisan tinker
```

```php
$theme = \ElevateCommerce\VisualEditor\Models\Theme::where('slug', 'dawn')->first();
$theme->activate();
```

## Step 5: Test It! (30 sec)

The theme is now active! The `dawn-hero` section is registered and ready to use in the page builder.

## What You Just Built

✅ A complete theme structure  
✅ Auto-discovered by the system  
✅ Section with configurable settings  
✅ Clean `$settings` object API  
✅ Ready for the visual editor  

## Next Steps

1. **Add more sections** - Copy the hero pattern for other sections
2. **Create a product section** - Use `$context` for dynamic data
3. **Build pages** - Combine sections into pages
4. **Style it** - Add Tailwind classes or custom CSS

## File Structure You Created

```
resources/views/themes/dawn/
├── theme.json
├── sections/
│   └── hero.blade.php
└── section-configs/
    └── hero.json
```

That's it! You now have a working theme. 🎉

See `THEME_CREATION_GUIDE.md` for complete examples with product pages, reviews, and more.
