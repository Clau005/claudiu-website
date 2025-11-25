# Package Structure & Asset Management

## Overview

The Visual Editor package is now fully self-contained with all Vue.js components, stores, and assets inside the package directory. This makes it ready for distribution via Packagist.

## Directory Structure

```
packages/elevatecommerce/visual-editor/
├── config/                          # Package configuration
├── database/
│   ├── migrations/                  # Database migrations
│   └── seeders/                     # Database seeders
├── resources/
│   ├── js/                          # Vue.js application (NEW!)
│   │   ├── app.js                   # Main Vue app entry point
│   │   ├── components/
│   │   │   └── PageEditor/          # Page editor Vue components
│   │   │       ├── PageEditor.vue
│   │   │       ├── SettingsPanel.vue
│   │   │       ├── SectionItem.vue
│   │   │       └── AddSectionModal.vue
│   │   └── stores/
│   │       └── pageEditor.js        # Pinia store for page editor
│   └── views/                       # Blade templates
│       └── admin/
├── routes/                          # Package routes
├── src/                             # PHP source code
├── public/                          # Built assets (gitignored)
│   └── build/                       # Vite build output
├── package.json                     # NPM dependencies
├── vite.config.js                   # Vite configuration
└── README.md                        # Installation instructions
```

## Asset Build Process

### Development

When developing the package locally:

```bash
cd packages/elevatecommerce/visual-editor
npm install
npm run dev
```

The page editor will use Vite's dev server for hot module replacement.

### Production Build

Before publishing to Packagist or deploying:

```bash
cd packages/elevatecommerce/visual-editor
npm install
npm run build
```

This creates optimized assets in `public/build/` directory.

### Publishing Assets

After installing the package via Composer, users must publish the assets:

```bash
php artisan vendor:publish --tag=visual-editor-assets
```

This copies the built assets from `packages/elevatecommerce/visual-editor/public/build/` to `public/vendor/visual-editor/`.

## How It Works

### Asset Loading

The `edit.blade.php` view intelligently loads assets:

1. **Production Mode**: Loads from `public/vendor/visual-editor/` (published assets)
2. **Development Mode**: Falls back to Vite dev server

```blade
@if(file_exists(public_path('vendor/visual-editor/manifest.json')))
    {{-- Load published production assets --}}
    <script type="module" src="{{ asset('vendor/visual-editor/' . $appJs) }}"></script>
@else
    {{-- Development mode: use Vite --}}
    @vite(['resources/js/app.js'], 'packages/elevatecommerce/visual-editor')
@endif
```

## Installation for End Users

When someone installs your package via Composer:

```bash
composer require elevatecommerce/visual-editor
```

They need to:

1. Publish migrations:
   ```bash
   php artisan vendor:publish --tag=visual-editor-migrations
   php artisan migrate
   ```

2. Publish assets:
   ```bash
   php artisan vendor:publish --tag=visual-editor-assets
   ```

3. (Optional) Publish config:
   ```bash
   php artisan vendor:publish --tag=visual-editor-config
   ```

## Benefits

✅ **Self-Contained**: All Vue components are in the package
✅ **Distributable**: Ready for Packagist
✅ **No Main App Pollution**: Doesn't clutter the main Laravel app
✅ **Version Control**: Vue components are versioned with the package
✅ **Easy Updates**: `composer update` updates everything including frontend

## Development Workflow

### Adding New Vue Components

1. Create component in `packages/elevatecommerce/visual-editor/resources/js/components/`
2. Import in `app.js` if needed
3. Build: `npm run build`
4. Publish: `php artisan vendor:publish --tag=visual-editor-assets --force`

### Updating Existing Components

1. Edit component in package directory
2. Rebuild: `npm run build`
3. Republish: `php artisan vendor:publish --tag=visual-editor-assets --force`

## Notes

- The `public/build/` directory is gitignored
- Built assets should be included in the package release/tag
- Users don't need Node.js installed - they just use the pre-built assets
- For development, you can run `npm run dev` in the package directory
