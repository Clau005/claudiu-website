{{-- Shop All Products Section --}}
@php
    use App\Models\Product;
    
    // Fetch products with pagination
    $perPage = $settings->products_per_page ?? 12;
    $category = request('category');
    $sort = request('sort', 'newest');
    
    // Build query
    $query = Product::query();
    
    // Filter by category if selected
    if ($category && $category !== 'All') {
        $query->where('category', $category);
    }
    
    // Filter out of stock if needed
    if (!($settings->show_out_of_stock ?? true)) {
        $query->where('stock_status', 'in_stock');
    }
    
    // Apply sorting
    switch ($sort) {
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        case 'price_asc':
            $query->orderBy('price', 'asc');
            break;
        case 'price_desc':
            $query->orderBy('price', 'desc');
            break;
        case 'newest':
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }
    
    // Paginate
    $products = $query->paginate($perPage)->withQueryString();


    
    // Get all categories for filter
    $categories = Product::select('category')
        ->distinct()
        ->whereNotNull('category')
        ->pluck('category')
        ->filter()
        ->sort()
        ->values();
@endphp

<div class="min-h-screen bg-background">
    {{-- Header Section --}}
    <div class="py-16 px-4 lg:px-8" style="background-color: {{ $settings->background_color ?? '#f7f4f0' }}">
        <div class="container mx-auto">
            <h1 class="font-serif text-5xl font-bold mb-4">{{ $settings->title ?? 'Shop All' }}</h1>
            
            @if(isset($settings->subtitle))
                <p class="text-muted-foreground text-lg max-w-2xl">
                    {{ $settings->subtitle }}
                </p>
            @endif
        </div>
    </div>

    {{-- Filters & Products Section --}}
    <div class="container mx-auto px-4 lg:px-8 py-12">
        
        {{-- Category Filter Buttons --}}
        @if(($settings->show_category_filter ?? true) && $categories->count() > 0)
            <div class="flex flex-wrap gap-3 mb-8">
                {{-- All Button --}}
                <a href="{{ request()->fullUrlWithQuery(['category' => 'All', 'page' => null]) }}" 
                   class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-10 px-4 py-2 {{ (!$category || $category === 'All') ? 'bg-primary text-primary-foreground' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground' }}">
                    All
                </a>
                
                {{-- Category Buttons --}}
                @foreach($categories as $cat)
                    <a href="{{ request()->fullUrlWithQuery(['category' => $cat, 'page' => null]) }}" 
                       class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-10 px-4 py-2 {{ $category === $cat ? 'bg-primary text-primary-foreground' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Sort Options --}}
        @if($settings->sort_options ?? true)
            <div class="flex justify-end mb-6">
                <select 
                    onchange="window.location.href = this.value"
                    class="rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ $sort === 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </div>
        @endif

        {{-- Products Grid --}}
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $settings->products_per_row ?? 4 }} gap-8 mb-12">
                @foreach($products as $product)
                    <div class="group">
                        <a href="{{ $product->url ?? '/products/' . $product->slug }}" class="block">
                            {{-- Product Image --}}
                            <div class="relative aspect-square mb-4 overflow-hidden rounded-lg bg-muted">
                                @if($product->preview)
                                    <x-visual-editor::responsive-image
                                        :src="$product->preview"
                                        :alt="$product->name ?? 'Product image'"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                        sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
                                    />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-muted">
                                        <span class="text-muted-foreground">No image</span>
                                    </div>
                                @endif
                                
                                {{-- Out of Stock Badge --}}
                                @if(isset($product->stock_status) && $product->stock_status === 'out_of_stock')
                                    <div class="absolute top-3 right-3 bg-background/90 backdrop-blur-sm px-3 py-1 rounded-md text-sm font-medium">
                                        Out of Stock
                                    </div>
                                @endif
                            </div>

                            {{-- Product Info --}}
                            <div class="space-y-2">
                                <h3 class="font-medium text-lg group-hover:text-primary transition-colors">
                                    {{ $product->name }}
                                </h3>
                                
                                @if(isset($product->category))
                                    <p class="text-sm text-muted-foreground">{{ $product->category }}</p>
                                @endif
                                
                                <p class="font-semibold">
                                    ${{ number_format($product->price / 100, 2) }}
                                </p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center">
                {{ $products->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-20">
                <p class="text-muted-foreground text-lg mb-2">No products found</p>
                @if($category && $category !== 'All')
                    <a href="{{ request()->fullUrlWithQuery(['category' => 'All', 'page' => null]) }}" 
                       class="text-primary hover:underline">
                        View all products
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
