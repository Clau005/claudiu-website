<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        @if($settings->heading ?? null)
            <h2 class="text-3xl font-bold mb-6">{{ $settings->heading }}</h2>
        @endif
        
        @if($settings->content ?? null)
            <div class="prose max-w-none">
                <p class="text-gray-700 leading-relaxed">{{ $settings->content }}</p>
            </div>
        @endif
    </div>
</section>
