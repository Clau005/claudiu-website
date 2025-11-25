<section class="relative bg-blue-600 text-white py-20 overflow-hidden">
    @if($settings->background_image ?? null)
        <div class="absolute inset-0">
            <x-visual-editor::responsive-image 
                :src="$settings->background_image" 
                alt="Hero background" 
                class="w-full h-full object-cover"
                fetchpriority="high"
                loading="eager"
                sizes="100vw"
            />
            <div class="absolute inset-0 bg-black/40"></div>
        </div>
    @endif
    
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-5xl font-bold mb-4">{{ $settings->title }}</h1>
        
        @if($settings->subtitle ?? null)
            <p class="text-xl mb-8">{{ $settings->subtitle }}</p>
        @endif
        
        @if(($settings->button_text ?? null) && ($settings->button_url ?? null))
            <a href="{{ $settings->button_url }}" 
               class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100">
                {{ $settings->button_text }}
            </a>
        @endif
    </div>
</section>
