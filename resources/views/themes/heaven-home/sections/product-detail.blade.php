{{-- Product Detail Section --}}
@php
    use App\Models\Product;
    
    // Get product from context (passed from controller)
    $product = $context ?? null;
    
    if (!$product) {
        // Fallback: try to get from route parameter
        $slug = request()->route('slug');
        $product = Product::where('slug', $slug)->firstOrFail();
    }
    
    // Get related products (same category, exclude current)
    $relatedProducts = collect();
    if (($settings->show_related_products ?? true) && $product->category) {
        $relatedProducts = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit($settings->related_products_count ?? 4)
            ->get();
    }
    
    // Calculate savings if compare_at_price exists
    $hasSale = $product->compare_at_price && $product->compare_at_price > $product->price;
    $savings = $hasSale ? $product->compare_at_price - $product->price : 0;
    $savingsPercent = $hasSale ? round(($savings / $product->compare_at_price) * 100) : 0;
    
    // Stock status
    $inStock = !isset($product->stock_quantity) || $product->stock_quantity > 0;
    $lowStock = isset($product->stock_quantity) && $product->stock_quantity > 0 && $product->stock_quantity <= 5;
@endphp

<div class="min-h-screen bg-background py-8 lg:py-12">
    <div class="container mx-auto px-4 lg:px-8">
        
        {{-- Breadcrumbs --}}
        @if($settings->show_breadcrumbs ?? true)
            <nav class="flex items-center space-x-2 text-sm text-muted-foreground mb-8">
                <a href="/" class="hover:text-foreground transition-colors">Home</a>
                <span>/</span>
                <a href="/shop" class="hover:text-foreground transition-colors">Shop</a>
                @if($product->category && ($settings->show_category ?? true))
                    <span>/</span>
                    <a href="/shop?category={{ urlencode($product->category) }}" class="hover:text-foreground transition-colors">
                        {{ $product->category }}
                    </a>
                @endif
                <span>/</span>
                <span class="text-foreground">{{ $product->name }}</span>
            </nav>
        @endif

        {{-- Product Detail Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 mb-16">
            
            {{-- Product Image --}}
            <div class="order-1 {{ ($settings->image_position ?? 'left') === 'right' ? 'lg:order-2' : '' }}">
                <div class="sticky top-8">
                    <div class="aspect-square rounded-lg overflow-hidden bg-muted">
                        @if($product->preview)
                            <x-visual-editor::responsive-image
                                :src="$product->preview"
                                :alt="$product->name"
                                class="w-full h-full object-cover"
                                loading="eager"
                                fetchpriority="high"
                                sizes="(min-width: 1024px) 50vw, 100vw"
                            />
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-muted-foreground">No image available</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Product Info --}}
            <div class="order-2 {{ ($settings->image_position ?? 'left') === 'right' ? 'lg:order-1' : '' }}">
                
                {{-- Category --}}
                @if($product->category && ($settings->show_category ?? true))
                    <div class="mb-4">
                        <a href="/shop?category={{ urlencode($product->category) }}" 
                           class="inline-block text-sm font-medium text-primary hover:underline">
                            {{ $product->category }}
                        </a>
                    </div>
                @endif

                {{-- Product Name --}}
                <h1 class="font-serif text-4xl lg:text-5xl font-bold mb-4">
                    {{ $product->name }}
                </h1>

                {{-- SKU --}}
                @if($product->sku && ($settings->show_sku ?? true))
                    <p class="text-sm text-muted-foreground mb-6">
                        SKU: {{ $product->sku }}
                    </p>
                @endif

                {{-- Price --}}
                <div class="mb-6">
                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl font-bold">
                            ${{ number_format($product->price / 100, 2) }}
                        </span>
                        
                        @if($hasSale)
                            <span class="text-xl text-muted-foreground line-through">
                                ${{ number_format($product->compare_at_price / 100, 2) }}
                            </span>
                            <span class="inline-flex items-center rounded-full bg-destructive/10 px-3 py-1 text-sm font-medium text-destructive">
                                Save {{ $savingsPercent }}%
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Stock Status --}}
                @if($settings->show_stock_status ?? true)
                    <div class="mb-6">
                        @if($inStock)
                            @if($lowStock)
                                <div class="flex items-center gap-2 text-amber-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span class="font-medium">Only {{ $product->stock_quantity }} left in stock</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-green-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="font-medium">In Stock</span>
                                </div>
                            @endif
                        @else
                            <div class="flex items-center gap-2 text-destructive">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="font-medium">Out of Stock</span>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Excerpt --}}
                @if($product->excerpt)
                    <p class="text-lg text-muted-foreground mb-8 leading-relaxed">
                        {{ $product->excerpt }}
                    </p>
                @endif

                {{-- Add to Cart Form --}}
                <form class="mb-8" x-data="{ quantity: 1 }">
                    @if($settings->enable_quantity_selector ?? true)
                        <div class="flex items-center gap-4 mb-4">
                            <label class="text-sm font-medium">Quantity:</label>
                            <div class="flex items-center border rounded-md">
                                <button type="button" 
                                        @click="quantity = Math.max(1, quantity - 1)"
                                        class="px-4 py-2 hover:bg-muted transition-colors">
                                    -
                                </button>
                                <input type="number" 
                                       x-model="quantity" 
                                       min="1" 
                                       class="w-16 text-center border-x py-2 focus:outline-none"
                                       readonly>
                                <button type="button" 
                                        @click="quantity++"
                                        class="px-4 py-2 hover:bg-muted transition-colors">
                                    +
                                </button>
                            </div>
                        </div>
                    @endif

                    <button type="submit" 
                            {{ !$inStock ? 'disabled' : '' }}
                            class="w-full bg-primary text-primary-foreground hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed font-medium py-4 px-8 rounded-md transition-colors">
                        {{ $inStock ? 'Add to Cart' : 'Out of Stock' }}
                    </button>
                </form>

                {{-- Shipping & Returns Info --}}
                <div class="space-y-4 border-t pt-8">
                    @if($settings->show_shipping_info ?? true)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-muted-foreground mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            <div>
                                <p class="font-medium">
                                    @if($product->free_shipping)
                                        Free Shipping
                                    @else
                                        Standard Shipping
                                    @endif
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Estimated delivery: {{ $product->shipping_days ?? '5-7' }} business days
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($settings->show_return_policy ?? true)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-muted-foreground mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            <div>
                                <p class="font-medium">Easy Returns</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ $product->return_days ?? 30 }}-day return policy
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Full Description --}}
                @if($product->description)
                    <div class="mt-8 border-t pt-8">
                        <h2 class="text-xl font-semibold mb-4">Description</h2>
                        <div class="prose prose-neutral max-w-none text-muted-foreground">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Related Products --}}
        @if(($settings->show_related_products ?? true) && $relatedProducts->count() > 0)
            <div class="border-t pt-16">
                <h2 class="font-serif text-3xl font-bold mb-8">
                    {{ $settings->related_products_title ?? 'You May Also Like' }}
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="group">
                            <a href="/products/{{ $relatedProduct->slug }}" class="block">
                                {{-- Product Image --}}
                                <div class="relative aspect-square mb-4 overflow-hidden rounded-lg bg-muted">
                                    @if($relatedProduct->preview)
                                        <x-visual-editor::responsive-image
                                            :src="$relatedProduct->preview"
                                            :alt="$relatedProduct->name"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            loading="lazy"
                                            sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
                                        />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-muted">
                                            <span class="text-muted-foreground">No image</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Info --}}
                                <div class="space-y-2">
                                    <h3 class="font-medium text-lg group-hover:text-primary transition-colors">
                                        {{ $relatedProduct->name }}
                                    </h3>
                                    
                                    @if($relatedProduct->category)
                                        <p class="text-sm text-muted-foreground">{{ $relatedProduct->category }}</p>
                                    @endif
                                    
                                    <p class="font-semibold">
                                        ${{ number_format($relatedProduct->price / 100, 2) }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
