# Data Table Component

A reusable, feature-rich table component for admin interfaces with search, sorting, pagination, bulk actions, and filters.

## Features

✅ **Search** - Real-time search with debouncing  
✅ **Sorting** - Click-to-sort on any column  
✅ **Pagination** - Built-in Laravel pagination support  
✅ **Bulk Actions** - Select multiple items and perform actions  
✅ **Filters** - Tab-based filtering (e.g., All, Images, Videos)  
✅ **Empty States** - Customizable empty state with icon  
✅ **Responsive** - Clean, modern design  
✅ **Tooltips** - Built-in tooltip support  

## Usage

### Basic Example

```blade
<x-visual-editor::data-table
    title="Products"
    :items="$products"
    :columns="[
        ['key' => 'name', 'label' => 'Name', 'sortable' => true],
        ['key' => 'price', 'label' => 'Price', 'sortable' => true],
        ['key' => 'stock', 'label' => 'Stock', 'sortable' => false],
    ]"
>
    @foreach($products as $product)
        <td class="px-4 py-3">{{ $product->name }}</td>
        <td class="px-4 py-3">${{ $product->price }}</td>
        <td class="px-4 py-3">{{ $product->stock }}</td>
    @endforeach
</x-visual-editor::data-table>
```

### Full Example with All Features

```blade
<x-visual-editor::data-table
    title="Files"
    :icon="'<path ... />'"
    :items="$media"
    :columns="[
        ['key' => 'filename', 'label' => 'File name', 'sortable' => true],
        ['key' => 'size', 'label' => 'Size', 'sortable' => true],
        ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-32 text-right'],
    ]"
    :filters="[
        ['label' => 'All', 'url' => route('admin.media.index'), 'active' => !request('type')],
        ['label' => 'Images', 'url' => route('admin.media.index', ['type' => 'images']), 'active' => request('type') === 'images'],
    ]"
    :bulk-actions="[
        ['action' => 'delete', 'label' => 'Delete', 'class' => 'bg-red-600 text-white hover:bg-red-700'],
        ['action' => 'download', 'label' => 'Download'],
    ]"
    empty-title="No files found"
    empty-description="Upload your first file to get started"
>
    <x-slot:actions>
        <button class="px-4 py-2 bg-gray-800 text-white rounded">
            Create New
        </button>
    </x-slot:actions>

    @foreach($media as $item)
        <td class="px-4 py-3">{{ $item->filename }}</td>
        <td class="px-4 py-3">{{ $item->size }}</td>
        <td class="px-4 py-3">
            <button>Edit</button>
        </td>
    @endforeach
</x-visual-editor::data-table>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `''` | Table title |
| `icon` | string | `null` | Heroicon SVG path for title icon |
| `items` | Collection | `null` | Laravel paginated collection |
| `columns` | array | `[]` | Column definitions |
| `actions` | slot | `null` | Header action buttons |
| `bulkActions` | array | `[]` | Bulk action definitions |
| `filters` | array | `[]` | Filter tab definitions |
| `searchable` | boolean | `true` | Enable search |
| `sortable` | boolean | `true` | Enable sorting |
| `emptyTitle` | string | `'No items found'` | Empty state title |
| `emptyDescription` | string | `'Get started...'` | Empty state description |
| `emptyIcon` | string | `null` | Empty state icon SVG path |

## Column Definition

```php
[
    'key' => 'name',           // Column identifier (for sorting)
    'label' => 'Product Name', // Column header label
    'sortable' => true,        // Enable sorting on this column
    'class' => 'w-32',         // Optional: Additional CSS classes
]
```

## Filter Definition

```php
[
    'label' => 'All',                          // Filter tab label
    'url' => route('admin.products.index'),    // Filter URL
    'active' => !request('type'),              // Is this filter active?
]
```

## Bulk Action Definition

```php
[
    'action' => 'delete',                                    // Action identifier
    'label' => 'Delete',                                     // Button label
    'class' => 'bg-red-600 text-white hover:bg-red-700',   // Optional: Button classes
]
```

## Controller Setup

### Search

```php
public function index(Request $request)
{
    $query = Media::query();
    
    if ($request->filled('search')) {
        $query->where('original_filename', 'like', '%' . $request->search . '%');
    }
    
    $media = $query->paginate(20);
    
    return view('admin.media.index', compact('media'));
}
```

### Sorting

```php
public function index(Request $request)
{
    $query = Media::query();
    
    if ($request->filled('sort')) {
        $direction = $request->get('direction', 'asc');
        $query->orderBy($request->sort, $direction);
    }
    
    $media = $query->paginate(20);
    
    return view('admin.media.index', compact('media'));
}
```

### Bulk Actions

```php
public function bulkAction(Request $request)
{
    $ids = explode(',', $request->ids);
    $action = $request->action;
    
    switch ($action) {
        case 'delete':
            Media::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Items deleted');
            
        case 'download':
            // Handle download logic
            break;
    }
    
    return redirect()->back();
}
```

Add route:
```php
Route::post('/admin/media/bulk-action', [MediaController::class, 'bulkAction'])
    ->name('admin.media.bulk-action');
```

## Customization

### Custom Row Click Behavior

By default, rows are clickable. To disable or customize:

```blade
<x-visual-editor::data-table ...>
    @foreach($items as $item)
        <td onclick="event.stopPropagation()">
            <!-- This cell won't trigger row click -->
        </td>
    @endforeach
</x-visual-editor::data-table>
```

### Custom Empty State

```blade
<x-visual-editor::data-table
    empty-title="No products yet"
    empty-description="Create your first product to get started"
    :empty-icon="'<path ... />'"
>
```

### Tooltips

Add `tooltip` class and `data-tooltip` attribute:

```blade
<button class="tooltip" data-tooltip="Copy link">
    <svg>...</svg>
</button>
```

## Notes

- Requires Alpine.js (already included in dashboard layout)
- Uses Tailwind CSS for styling
- Pagination links preserve query parameters (search, filters, etc.)
- Search has 300ms debounce for better UX
- Bulk actions require a form submission endpoint
