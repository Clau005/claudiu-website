{{-- Collection Products Section --}}
@php
    // Get collection from context (passed from CollectionPublicController)
    $collection = $context ?? null;
    
    if (!$collection) {
        abort(404, 'Collection not found');
    }
    
    // Get products from collection's collectables
    $collectables = $collection->collectables ?? collect();
    
    // Extract actual products and prepare for filtering/sorting
    $allProducts = $collectables->map(function($collectable) {
        return $collectable->collectable; // The actual Product model
    })->filter(); // Remove any null values
    
    // Get filter and sort parameters
    $category = request('category');
    $sort = request('sort', 'position');
    $page = request('page', 1);
    $perPage = $settings->products_per_page ?? 12;
    
    // Filter by category if selected
    if ($category && $category !== 'All') {
        $allProducts = $allProducts->filter(function($product) use ($category) {
            return $product->category === $category;
        });
    }
    
    // Apply sorting
    $allProducts = match($sort) {
        'name_asc' => $allProducts->sortBy('name'),
        'name_desc' => $allProducts->sortByDesc('name'),
        'price_asc' => $allProducts->sortBy('price'),
        'price_desc' => $allProducts->sortByDesc('price'),
        'newest' => $allProducts->sortByDesc('created_at'),
        'position' => $allProducts->sortBy(function($product) use ($collectables) {
            // Sort by position in collection
            $collectable = $collectables->firstWhere('collectable_id', $product->id);
            return $collectable ? $collectable->position : 999;
        }),
        default => $allProducts,
    };
    
    // Get all unique categories from products
    $categories = $allProducts->pluck('category')
        ->filter()
        ->unique()
        ->sort()
        ->values();
    
    // Manual pagination
    $total = $allProducts->count();
    $products = $allProducts->forPage($page, $perPage);
    $lastPage = ceil($total / $perPage);
@endphp

<div class="min-h-screen bg-background">
    {{-- Header Section with Collection Info --}}
    <div class="py-16 px-4 lg:px-8" style="background-color: {{ $settings->header_background_color ?? '#f7f4f0' }}">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 {{ ($settings->show_collection_image ?? true) && $collection->image ? 'lg:grid-cols-2' : '' }} gap-8 items-center">
                
                {{-- Collection Text --}}
                <div>
                    <h1 class="font-serif text-5xl font-bold mb-4">{{ $collection->title }}</h1>
                    
                    @if($collection->description)
                        <p class="text-muted-foreground text-lg max-w-2xl mb-4">
                            {!! $collection->description !!}
                        </p>
                    @endif
                    
                    @if($settings->show_product_count ?? true)
                        <p class="text-sm text-muted-foreground">
                            {{ $total }} {{ Str::plural('product', $total) }}
                        </p>
                    @endif
                </div>
                
                {{-- Collection Image --}}
                @if(($settings->show_collection_image ?? true) && $collection->image)
                    <div class="aspect-square rounded-lg overflow-hidden bg-muted">
                        <x-visual-editor::responsive-image
                            :src="$collection->image"
                            :alt="$collection->title"
                            class="w-full h-full object-cover"
                            loading="eager"
                            fetchpriority="high"
                            sizes="(min-width: 1024px) 50vw, 100vw"
                        />
                    </div>
                @endif
            </div>
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
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'position']) }}" {{ $sort === 'position' ? 'selected' : '' }}>Collection Order</option>
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
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        loading="lazy"
                                        sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
                                    />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-muted">
                                        <span class="text-muted-foreground">No image</span>
                                    </div>
                                @endif
                                
                                {{-- Out of Stock Badge --}}
                                @if(isset($product->stock_quantity) && $product->stock_quantity <= 0)
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

            {{-- Manual Pagination --}}
            @if($lastPage > 1)
                <div class="flex justify-center items-center gap-2">
                    {{-- Previous --}}
                    @if($page > 1)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" 
                           class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-10 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground">
                            Previous
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @for($i = 1; $i <= $lastPage; $i++)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" 
                           class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-10 w-10 {{ $i == $page ? 'bg-primary text-primary-foreground' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    {{-- Next --}}
                    @if($page < $lastPage)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" 
                           class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-10 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground">
                            Next
                        </a>
                    @endif
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="text-center py-20">
                <p class="text-muted-foreground text-lg mb-2">No products found</p>
                @if($category && $category !== 'All')
                    <a href="{{ request()->fullUrlWithQuery(['category' => 'All', 'page' => null]) }}" 
                       class="text-primary hover:underline">
                        View all products in this collection
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
