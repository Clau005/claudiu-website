# Dashboard Registry Guide

The Visual Editor package includes a Dashboard Registry that allows any package to register dashboard components (widgets/lenses) that will be displayed on the admin dashboard.

## How It Works

The Dashboard Registry is a singleton service that stores dashboard components. All packages can register their components, and they'll automatically appear on the admin dashboard with automatic layout management.

## Registering Dashboard Components

### In Your Service Provider

```php
<?php

namespace YourVendor\YourPackage;

use Illuminate\Support\ServiceProvider;

class YourPackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Get the dashboard registry
        $dashboard = app('visual-editor.dashboard');

        // Register a single component
        $dashboard->register('recent-orders', [
            'view' => 'your-package::admin.widgets.recent-orders',
            'data' => function () {
                return [
                    'orders' => \App\Models\Order::latest()->take(5)->get(),
                ];
            },
            'width' => 'half',
            'order' => 20,
            'title' => 'Recent Orders',
        ]);
    }
}
```

### Using the Facade (Alternative)

```php
use ElevateCommerce\VisualEditor\Facades\Dashboard;

public function boot(): void
{
    Dashboard::register('sales-chart', [
        'view' => 'your-package::admin.widgets.sales-chart',
        'data' => ['sales' => $this->getSalesData()],
        'width' => 'full',
        'order' => 10,
    ]);
}
```

## Component Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `view` | string | Yes | Blade view to render |
| `data` | array\|callable | No | Data to pass to the view. Can be an array or closure |
| `width` | string | No | Component width: `full`, `half`, `third`, `quarter`. Default: `full` |
| `order` | int | No | Sort order (lower = higher on page). Default: 100 |
| `permission` | string\|null | No | Permission required to view this component |
| `title` | string\|null | No | Component title |
| `refreshable` | bool | No | Whether component can be refreshed. Default: false |

## Width Options

Components are automatically laid out based on their width:

- **`full`**: Takes full width of the dashboard
- **`half`**: Takes 50% width (2 columns on desktop)
- **`third`**: Takes 33.33% width (3 columns on desktop)
- **`quarter`**: Takes 25% width (4 columns on desktop)

All components stack to full width on mobile devices.

## Examples

### Basic Stat Card

```php
$dashboard->register('total-users', [
    'view' => 'visual-editor::admin.components.stat-card',
    'data' => [
        'label' => 'Total Users',
        'value' => \App\Models\User::count(),
        'icon' => '👥',
        'color' => 'blue',
    ],
    'width' => 'third',
    'order' => 10,
]);
```

### Component with Dynamic Data

```php
$dashboard->register('revenue-chart', [
    'view' => 'your-package::admin.widgets.revenue-chart',
    'data' => function () {
        return [
            'revenue' => \App\Models\Order::sum('total'),
            'chartData' => $this->getChartData(),
            'period' => 'Last 30 days',
        ];
    },
    'width' => 'full',
    'order' => 5,
    'title' => 'Revenue Overview',
]);
```

### Component with Permission

```php
$dashboard->register('admin-stats', [
    'view' => 'your-package::admin.widgets.admin-stats',
    'data' => ['stats' => $this->getAdminStats()],
    'width' => 'half',
    'order' => 15,
    'permission' => 'view-admin-stats',
    'title' => 'Admin Statistics',
]);
```

### Multiple Components at Once

```php
$dashboard->registerMany([
    'users-stat' => [
        'view' => 'visual-editor::admin.components.stat-card',
        'data' => [
            'label' => 'Total Users',
            'value' => \App\Models\User::count(),
            'icon' => '👥',
            'color' => 'blue',
        ],
        'width' => 'quarter',
        'order' => 10,
    ],
    'orders-stat' => [
        'view' => 'visual-editor::admin.components.stat-card',
        'data' => [
            'label' => 'Total Orders',
            'value' => \App\Models\Order::count(),
            'icon' => '🛒',
            'color' => 'green',
        ],
        'width' => 'quarter',
        'order' => 11,
    ],
    'revenue-stat' => [
        'view' => 'visual-editor::admin.components.stat-card',
        'data' => [
            'label' => 'Revenue',
            'value' => '$' . number_format(\App\Models\Order::sum('total'), 2),
            'icon' => '💰',
            'color' => 'yellow',
        ],
        'width' => 'quarter',
        'order' => 12,
    ],
]);
```

## Creating Custom Component Views

### Simple Widget

```blade
{{-- resources/views/admin/widgets/recent-activity.blade.php --}}
<div class="bg-white rounded-lg shadow p-6">
    @if(isset($_component['title']))
        <h3 class="font-semibold text-gray-800 mb-4">{{ $_component['title'] }}</h3>
    @endif
    
    <div class="space-y-3">
        @foreach($activities as $activity)
            <div class="flex items-center text-sm">
                <span class="text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                <span class="mx-2">•</span>
                <span class="text-gray-700">{{ $activity->description }}</span>
            </div>
        @endforeach
    </div>
</div>
```

### Chart Widget

```blade
{{-- resources/views/admin/widgets/sales-chart.blade.php --}}
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold text-gray-800">{{ $_component['title'] ?? 'Sales Chart' }}</h3>
        @if($_component['refreshable'])
            <button class="text-sm text-blue-600 hover:text-blue-800">Refresh</button>
        @endif
    </div>
    
    <canvas id="sales-chart" width="400" height="200"></canvas>
    
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            new Chart(document.getElementById('sales-chart'), {
                type: 'line',
                data: @json($chartData),
            });
        </script>
    @endpush
</div>
```

### List Widget

```blade
{{-- resources/views/admin/widgets/recent-orders.blade.php --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800">{{ $_component['title'] ?? 'Recent Orders' }}</h3>
    </div>
    
    <div class="divide-y">
        @forelse($orders as $order)
            <div class="px-6 py-3 flex justify-between items-center hover:bg-gray-50">
                <div>
                    <p class="font-medium text-gray-900">#{{ $order->id }}</p>
                    <p class="text-sm text-gray-500">{{ $order->customer_name }}</p>
                </div>
                <div class="text-right">
                    <p class="font-medium text-gray-900">${{ number_format($order->total, 2) }}</p>
                    <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-gray-500">
                No orders yet
            </div>
        @endforelse
    </div>
    
    <div class="px-6 py-3 bg-gray-50 border-t">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
            View all orders →
        </a>
    </div>
</div>
```

## Registry Methods

### `register(string $key, array $component)`
Register a single dashboard component.

```php
$dashboard->register('my-widget', [
    'view' => 'my-package::widgets.my-widget',
    'data' => ['foo' => 'bar'],
]);
```

### `registerMany(array $components)`
Register multiple components at once.

```php
$dashboard->registerMany([
    'widget1' => [...],
    'widget2' => [...],
]);
```

### `all()`
Get all registered components (sorted by order).

```php
$components = $dashboard->all();
```

### `get(string $key)`
Get a specific component.

```php
$component = $dashboard->get('my-widget');
```

### `has(string $key)`
Check if a component exists.

```php
if ($dashboard->has('my-widget')) {
    // Component exists
}
```

### `remove(string $key)`
Remove a component.

```php
$dashboard->remove('unwanted-widget');
```

### `filter(callable $callback)`
Get filtered components.

```php
$filtered = $dashboard->filter(function($component) {
    return $component['width'] === 'full';
});
```

### `byWidth(string $width)`
Get components by width.

```php
$fullWidth = $dashboard->byWidth('full');
$halfWidth = $dashboard->byWidth('half');
```

### `forUser($user = null)`
Get components that a user has permission to view.

```php
$components = $dashboard->forUser(auth()->guard('admin')->user());
```

### `render(string $key)`
Render a specific component.

```php
$html = $dashboard->render('my-widget');
```

### `renderAll()`
Render all components.

```php
$rendered = $dashboard->renderAll();
```

## Complete Package Example

```php
<?php

namespace ElevateCommerce\Products;

use Illuminate\Support\ServiceProvider;
use App\Models\Product;

class ProductsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'products');

        // Register dashboard components
        $this->registerDashboardComponents();
    }

    protected function registerDashboardComponents(): void
    {
        $dashboard = app('visual-editor.dashboard');

        // Product stats
        $dashboard->registerMany([
            'total-products' => [
                'view' => 'visual-editor::admin.components.stat-card',
                'data' => function () {
                    return [
                        'label' => 'Total Products',
                        'value' => Product::count(),
                        'icon' => '📦',
                        'color' => 'blue',
                        'change' => $this->getProductGrowth(),
                    ];
                },
                'width' => 'third',
                'order' => 20,
            ],
            'low-stock' => [
                'view' => 'visual-editor::admin.components.stat-card',
                'data' => [
                    'label' => 'Low Stock Items',
                    'value' => Product::where('stock', '<', 10)->count(),
                    'icon' => '⚠️',
                    'color' => 'red',
                ],
                'width' => 'third',
                'order' => 21,
            ],
        ]);

        // Recent products
        $dashboard->register('recent-products', [
            'view' => 'products::admin.widgets.recent-products',
            'data' => function () {
                return [
                    'products' => Product::latest()->take(5)->get(),
                ];
            },
            'width' => 'half',
            'order' => 30,
            'title' => 'Recently Added Products',
        ]);
    }

    protected function getProductGrowth(): float
    {
        // Calculate growth percentage
        $thisMonth = Product::whereMonth('created_at', now()->month)->count();
        $lastMonth = Product::whereMonth('created_at', now()->subMonth()->month)->count();
        
        if ($lastMonth === 0) return 0;
        
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }
}
```

## Built-in Component Views

The Visual Editor package provides two built-in component views:

### Stat Card
`visual-editor::admin.components.stat-card`

```php
'data' => [
    'label' => 'Label',
    'value' => 'Value',
    'icon' => '📊',
    'color' => 'blue', // blue, green, red, yellow, purple
    'change' => 5.2, // Optional: percentage change
]
```

### Welcome Card
`visual-editor::admin.components.welcome-card`

```php
'data' => [
    'message' => 'Welcome message',
    'actions' => [ // Optional
        ['label' => 'Get Started', 'url' => '/admin/setup'],
    ],
]
```

## Tips

1. **Order Numbers**: Use increments of 10 (10, 20, 30) to leave room for components to be inserted between
2. **Dynamic Data**: Use closures for data that needs to be calculated on each request
3. **Permissions**: Always set permissions for sensitive data
4. **Width**: Choose appropriate widths based on content - stats work well as `third` or `quarter`, charts as `full` or `half`
5. **Performance**: Cache expensive queries in your data closures
6. **Refreshable**: Set to `true` for components that benefit from real-time updates

## Removing Default Components

If you want to remove the default Visual Editor components:

```php
public function boot(): void
{
    $dashboard = app('visual-editor.dashboard');
    
    // Remove specific components
    $dashboard->remove('welcome');
    $dashboard->remove('stat-users');
    
    // Or register your own with the same keys to override them
    $dashboard->register('welcome', [
        'view' => 'your-package::custom-welcome',
        // ...
    ]);
}
```
