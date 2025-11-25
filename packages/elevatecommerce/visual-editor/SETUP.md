# Visual Editor Setup Guide

## Quick Start

### 1. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `admins` - Admin users
- `admin_password_reset_tokens` - Password reset tokens
- `admin_sessions` - Admin sessions

### 2. Create an Admin User

**Option A: Using the Seeder**

```bash
php artisan db:seed --class="ElevateCommerce\VisualEditor\Database\Seeders\AdminSeeder"
```

Default credentials:
- Email: `admin@example.com`
- Password: `password`

**Option B: Using Tinker**

```bash
php artisan tinker
```

```php
ElevateCommerce\VisualEditor\Models\Admin::create([
    'first_name' => 'Your',
    'last_name' => 'Name',
    'email' => 'your@email.com',
    'password' => bcrypt('your-password'),
    'is_super_admin' => true,
]);
```

### 3. Access the Admin Panel

Navigate to: `http://your-domain.com/admin/login`

## Configuration

### Publishing Config Files

```bash
# Publish all config files
php artisan vendor:publish --tag=visual-editor-config

# Publish views (optional, for customization)
php artisan vendor:publish --tag=visual-editor-views
```

### Config Options

Edit `config/visual-editor.php`:

```php
return [
    // Change the admin URL prefix
    'route_prefix' => 'admin', // Change to 'dashboard', 'backend', etc.

    // Middleware applied to admin routes
    'middleware' => ['web', 'auth:admin'],

    // Auth configuration
    'auth' => [
        'guard' => 'admin',
        'passwords' => 'admins',
    ],

    // Default navigation items
    'navigation' => [
        'default_items' => [
            'dashboard' => [
                'label' => 'Dashboard',
                'url' => '/admin/dashboard',
                'icon' => 'home',
                'order' => 1,
            ],
        ],
    ],
];
```

## Registering Navigation Items

### In Your Service Provider

```php
use ElevateCommerce\VisualEditor\Facades\Navigation;

public function boot()
{
    // Single item
    Navigation::register('my-feature', [
        'label' => 'My Feature',
        'url' => '/admin/my-feature',
        'icon' => '⚡',
        'order' => 50,
    ]);

    // Multiple items
    Navigation::registerMany([
        'products' => [
            'label' => 'Products',
            'url' => '/admin/products',
            'icon' => '📦',
            'order' => 10,
        ],
        'orders' => [
            'label' => 'Orders',
            'url' => '/admin/orders',
            'icon' => '🛒',
            'order' => 20,
        ],
    ]);
}
```

### In Routes File

```php
// In routes/web.php or your package's routes
use ElevateCommerce\VisualEditor\Facades\Navigation;

Navigation::register('custom-page', [
    'label' => 'Custom Page',
    'url' => route('admin.custom'),
    'icon' => '🎨',
    'order' => 30,
]);
```

## Customizing Views

### Extending the Dashboard Layout

Create your own layout that extends the visual-editor layout:

```blade
{{-- resources/views/admin/layout.blade.php --}}
@extends('visual-editor::admin.dashboard')

@section('content')
    <div class="p-8">
        <h1>My Custom Admin Page</h1>
        @yield('page-content')
    </div>
@endsection
```

### Creating Custom Admin Pages

```php
// In your controller
public function index()
{
    $navigation = app('visual-editor.navigation')->all();
    
    return view('your-package::admin.index', [
        'admin' => auth()->guard('admin')->user(),
        'navigation' => $navigation,
    ]);
}
```

## Authentication

### Protecting Routes

```php
Route::middleware(['web', 'auth:admin'])->group(function () {
    Route::get('/admin/my-page', [MyController::class, 'index']);
});
```

### Checking Admin Authentication

```php
// Check if admin is authenticated
if (auth()->guard('admin')->check()) {
    // Admin is logged in
}

// Get current admin
$admin = auth()->guard('admin')->user();

// Check if super admin
if ($admin->is_super_admin) {
    // Super admin logic
}
```

## Troubleshooting

### Package Not Discovered

```bash
composer dump-autoload
php artisan package:discover
php artisan config:clear
```

### Routes Not Working

```bash
php artisan route:clear
php artisan route:list --name=admin
```

### Views Not Found

```bash
php artisan view:clear
```

### Migration Issues

```bash
# Rollback and re-run
php artisan migrate:rollback
php artisan migrate
```

## Development Tips

1. **Clear caches during development:**
   ```bash
   php artisan optimize:clear
   ```

2. **Check registered navigation:**
   ```bash
   php artisan tinker
   ```
   ```php
   app('visual-editor.navigation')->all();
   ```

3. **Test authentication:**
   Navigate to `/admin/login` and use your credentials

4. **View all admin routes:**
   ```bash
   php artisan route:list --name=admin
   ```
