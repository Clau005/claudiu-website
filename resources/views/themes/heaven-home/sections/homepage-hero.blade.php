{{-- Heaven Home - Homepage Hero Section --}}
<div class="min-h-screen">
    {{-- Hero Section --}}
    <section class="relative h-[600px] flex items-center">
        @if(!empty($settings->hero_image))
            <div class="absolute inset-0">
                <x-visual-editor::responsive-image
                    :src="$settings->hero_image"
                    :alt="$settings->hero_title ?? 'Hero image'"
                    class="w-full h-full object-cover"
                    fetchpriority="high"
                    loading="eager"
                    sizes="100vw"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-white/80 to-white/20"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-r from-gray-100 to-gray-50"></div>
        @endif
        
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="max-w-xl">
                @if(!empty($settings->hero_title))
                    <h1 class="font-serif text-5xl md:text-6xl font-bold mb-4">
                        {{ $settings->hero_title }}
                    </h1>
                @endif
                
                @if(!empty($settings->hero_subtitle))
                    <p class="text-lg text-muted-foreground mb-8">
                        {{ $settings->hero_subtitle }}
                    </p>
                @endif
                
                @if(!empty($settings->hero_button_text) && !empty($settings->hero_button_url))
                    <a href="{{ $settings->hero_button_url }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-primary text-primary-foreground hover:bg-primary/90 h-11 px-8">
                        {{ $settings->hero_button_text }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Featured Collections --}}
    <section class="container mx-auto px-4 lg:px-8 py-20">
        <div class="text-center mb-12">
            @if(!empty($settings->collections_title))
                <h2 class="font-serif text-4xl font-bold mb-4">{{ $settings->collections_title }}</h2>
            @endif
            
            @if(!empty($settings->collections_subtitle))
                <p class="text-muted-foreground max-w-2xl mx-auto">
                    {{ $settings->collections_subtitle }}
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Collection 1 --}}
            @if(!empty($settings->collection_1_title))
                <a href="{{ $settings->collection_1_url ?? '#' }}" class="group relative overflow-hidden rounded-lg h-[400px] block">
                    @if(!empty($settings->collection_1_image))
                        <x-visual-editor::responsive-image
                            :src="$settings->collection_1_image"
                            :alt="$settings->collection_1_title"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                            sizes="(min-width: 768px) 50vw, 100vw"
                        />
                    @else
                        <div class="w-full h-full bg-muted"></div>
                    @endif
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-background/80 to-transparent flex items-end">
                        <div class="p-8">
                            <h3 class="font-serif text-3xl font-bold mb-2">{{ $settings->collection_1_title }}</h3>
                            @if(!empty($settings->collection_1_description))
                                <p class="text-muted-foreground mb-4">{{ $settings->collection_1_description }}</p>
                            @endif
                            <span class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-secondary text-secondary-foreground hover:bg-secondary/80 h-10 px-4 py-2">
                                {{ $settings->collection_1_button_text ?? 'Explore Collection' }}
                            </span>
                        </div>
                    </div>
                </a>
            @endif

            {{-- Collection 2 --}}
            @if(!empty($settings->collection_2_title))
                <a href="{{ $settings->collection_2_url ?? '#' }}" class="group relative overflow-hidden rounded-lg h-[400px] block">
                    @if(!empty($settings->collection_2_image))
                        <x-visual-editor::responsive-image
                            :src="$settings->collection_2_image"
                            :alt="$settings->collection_2_title"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                            sizes="(min-width: 768px) 50vw, 100vw"
                        />
                    @else
                        <div class="w-full h-full bg-muted"></div>
                    @endif
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-background/80 to-transparent flex items-end">
                        <div class="p-8">
                            <h3 class="font-serif text-3xl font-bold mb-2">{{ $settings->collection_2_title }}</h3>
                            @if(!empty($settings->collection_2_description))
                                <p class="text-muted-foreground mb-4">{{ $settings->collection_2_description }}</p>
                            @endif
                            <span class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-secondary text-secondary-foreground hover:bg-secondary/80 h-10 px-4 py-2">
                                {{ $settings->collection_2_button_text ?? 'Explore Collection' }}
                            </span>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </section>

    {{-- Featured Products --}}
    <section class="bg-muted py-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-12">
                @if(!empty($settings->featured_title))
                    <h2 class="font-serif text-4xl font-bold mb-4">{{ $settings->featured_title }}</h2>
                @endif
                
                @if(!empty($settings->featured_subtitle))
                    <p class="text-muted-foreground max-w-2xl mx-auto">
                        {{ $settings->featured_subtitle }}
                    </p>
                @endif
            </div>

            @php
                // Get limit from settings or default to 4
                $limit = $settings->featured_products_limit ?? 4;
                
                // Fetch products tagged with 'featured'
                $featuredProducts = \App\Models\Product::withTag('featured')
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->limit($limit)
                    ->get();
            @endphp

            @if($featuredProducts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($featuredProducts as $product)
                        <div class="group bg-background rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <a href="/products/{{ $product->slug }}" class="block">
                                @if($product->preview)
                                    <div class="aspect-square overflow-hidden">
                                        <x-visual-editor::responsive-image
                                            :src="$product->preview"
                                            :alt="$product->name"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
                                        />
                                    </div>
                                @else
                                    <div class="aspect-square bg-muted flex items-center justify-center">
                                        <span class="text-muted-foreground">No image</span>
                                    </div>
                                @endif
                                
                                <div class="p-4">
                                    <h3 class="font-medium mb-2 group-hover:text-accent transition-colors">
                                        {{ $product->name }}
                                    </h3>
                                    
                                    @if($product->excerpt)
                                        <p class="text-sm text-muted-foreground mb-2 line-clamp-2">
                                            {{ $product->excerpt }}
                                        </p>
                                    @endif
                                    
                                    <p class="text-lg font-semibold">
                                        ${{ number_format($product->price, 2) }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-12">
                    @if(!empty($settings->featured_button_text) && !empty($settings->featured_button_url))
                        <a href="{{ $settings->featured_button_url }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-11 px-8">
                            {{ $settings->featured_button_text }}
                        </a>
                    @endif
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-muted-foreground">No featured products available.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Values Section --}}
    @if(!empty($settings->value_1_title) || !empty($settings->value_2_title) || !empty($settings->value_3_title))
        <section class="container mx-auto px-4 lg:px-8 py-20">
            <div class="grid grid-cols-1 md:grid-cols-{{ collect([$settings->value_1_title, $settings->value_2_title, $settings->value_3_title])->filter()->count() ?: 1 }} gap-12">
                @foreach([[
                    'title' => $settings->value_1_title ?? null,
                    'description' => $settings->value_1_description ?? null,
                ], [
                    'title' => $settings->value_2_title ?? null,
                    'description' => $settings->value_2_description ?? null,
                ], [
                    'title' => $settings->value_3_title ?? null,
                    'description' => $settings->value_3_description ?? null,
                ]] as $value)
                    @if(!empty($value['title']))
                        <div class="text-center">
                            <h3 class="font-serif text-2xl font-bold mb-4">{{ $value['title'] }}</h3>
                            @if(!empty($value['description']))
                                <p class="text-muted-foreground">
                                    {{ $value['description'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
</div>
