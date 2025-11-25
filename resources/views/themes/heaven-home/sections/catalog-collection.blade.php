{{-- Catalog Collection Section --}}
@php
    // Get products from collection
    $products = collect([]);
    $categories = collect(['All']);
    
    // Determine collection source:
    // 1. If collection_id is set in section data, use that
    // 2. Otherwise, if $collection variable exists (collection template context), use that
    
    if ($collection) {
        $products = $collection->collectables->map(function($collectable) {
            return $collectable->collectable;
        });

        
        // Apply sorting
        $sortBy = $data->sort_by ?? 'newest';
        switch ($sortBy) {
            case 'name_asc':
                $products = $products->sortBy('name');
                break;
            case 'name_desc':
                $products = $products->sortByDesc('name');
                break;
            case 'price_asc':
                $products = $products->sortBy('price');
                break;
            case 'price_desc':
                $products = $products->sortByDesc('price');
                break;
            case 'newest':
            default:
                $products = $products->sortByDesc('created_at');
                break;
        }

        $categories = $products->pluck('category')->filter()->unique()->values();
        
        // Apply limit
        if (isset($data->products_limit) && $data->products_limit > 0) {
            $products = $products->take($data->products_limit);
        }
    }
    
    // Prepare arrays for Alpine.js (keep raw data, just ensure proper structure)
    $productsArray = $products->values()->map(function($product) {
        // Convert model to array and ensure all needed fields exist
        $productArray = is_array($product) ? $product : $product->toArray();
        
        // Ensure URL exists
        if (!isset($productArray['url'])) {
            $productArray['url'] = "/products/{$productArray['slug']}";
        }
        
        return $productArray;
    })->toArray();
    
    // Add 'All' category for filtering
    $categoriesArray = $categories->prepend('All')->toArray();
@endphp

<div class="min-h-screen" style="padding-top: {{ $data->padding_top ?? 64 }}px; padding-bottom: {{ $data->padding_bottom ?? 64 }}px;">
    {{-- Header Section --}}
    <div class="{{ $data->header_bg_color ?? 'bg-muted' }} py-16">
        <div class="container mx-auto px-4 lg:px-8">
            @if(isset($data->title))
                <h1 class="font-serif text-5xl font-bold mb-4">{{ $data->title }}</h1>
            @endif
            
            @if(isset($data->description))
                <p class="text-muted-foreground max-w-2xl">
                    {{ $data->description }}
                </p>
            @endif
        </div>
    </div>

    {{-- Products Section with Alpine.js Filtering --}}
    <div class="container mx-auto px-4 lg:px-8 py-12"
         x-data="{
             selectedCategory: 'All',
             products: {{ json_encode($productsArray) }},
             categories: {{ json_encode($categoriesArray) }},
             get filteredProducts() {
                 if (this.selectedCategory === 'All') {
                     return this.products;
                 }
                 return this.products.filter(p => p.category === this.selectedCategory);
             }
         }">
        
        {{-- Category Filter Buttons --}}
        @if(($data->show_category_filter ?? true) && count($categoriesArray) > 1)
            <div class="flex flex-wrap gap-3 mb-12">
                <template x-for="category in categories" :key="category">
                    <button 
                        @click="selectedCategory = category"
                        :class="selectedCategory === category ? 'bg-primary text-primary-foreground' : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground'"
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2"
                        x-text="category">
                    </button>
                </template>
            </div>
        @endif

        {{-- Products Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $data->products_per_row ?? 4 }} gap-8">
            <template x-for="product in filteredProducts" :key="product.id">
                <div class="group">
                    <a :href="product.url" class="block">
                        {{-- Product Image --}}
                        <div class="relative aspect-square mb-4 overflow-hidden rounded-lg bg-muted">
                            <img 
                                :src="product.image_url" 
                                :alt="product.name"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                loading="lazy"
                            />
                        </div>

                        {{-- Product Info --}}
                        <div class="space-y-2">
                            <h3 class="font-medium text-lg group-hover:text-primary transition-colors" x-text="product.name"></h3>
                            
                            <p class="text-sm text-muted-foreground line-clamp-2" x-text="product.excerpt" x-show="product.excerpt"></p>
                            
                            <p class="font-semibold" x-text="'$' + (product.price / 100).toFixed(2)"></p>
                        </div>
                    </a>
                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="filteredProducts.length === 0" class="text-center py-20">
            <p class="text-muted-foreground">No products found in this category.</p>
        </div>
    </div>
</div>
