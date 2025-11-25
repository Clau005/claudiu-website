{{-- CTA Banner Section --}}
@php
    $bgClass = match($settings->style ?? 'gradient') {
        'gradient' => 'bg-gradient-to-r from-blue-600 to-cyan-600',
        'solid' => 'bg-slate-900',
        'minimal' => 'bg-transparent',
        default => 'bg-gradient-to-r from-blue-600 to-cyan-600'
    };
    
    $paddingClass = match($settings->size ?? 'medium') {
        'small' => 'py-12',
        'medium' => 'py-20',
        'large' => 'py-32',
        default => 'py-20'
    };
    
    $borderClass = ($settings->style ?? 'gradient') === 'minimal' ? 'border-2 border-slate-700' : '';
@endphp

<section class="relative {{ $paddingClass }} overflow-hidden">
    <div class="absolute inset-0 {{ $bgClass }}"></div>
    
    @if(($settings->style ?? 'gradient') !== 'minimal')
        {{-- Background Pattern --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff12_1px,transparent_1px),linear-gradient(to_bottom,#ffffff12_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
    @endif
    
    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        <div class="max-w-4xl mx-auto text-center {{ $borderClass }} {{ ($settings->style ?? 'gradient') === 'minimal' ? 'p-12 rounded-2xl' : '' }}">
            
            {{-- Eyebrow --}}
            @if(!empty($settings->eyebrow_text))
                <p class="text-sm md:text-base font-semibold {{ ($settings->style ?? 'gradient') === 'minimal' ? 'text-blue-400' : 'text-white/80' }} uppercase tracking-wider mb-4">
                    {{ $settings->eyebrow_text }}
                </p>
            @endif
            
            {{-- Title --}}
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold {{ ($settings->style ?? 'gradient') === 'minimal' ? 'text-white' : 'text-white' }} mb-6">
                {{ $settings->title }}
            </h2>
            
            {{-- Description --}}
            @if(!empty($settings->description))
                <p class="text-lg md:text-xl {{ ($settings->style ?? 'gradient') === 'minimal' ? 'text-slate-300' : 'text-white/90' }} mb-8 max-w-2xl mx-auto">
                    {{ $settings->description }}
                </p>
            @endif
            
            {{-- CTAs --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @if(!empty($settings->cta_text) && !empty($settings->cta_url))
                    <a href="{{ $settings->cta_url }}" 
                       class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold {{ ($settings->style ?? 'gradient') === 'minimal' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-white text-blue-600 hover:bg-slate-100' }} rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        {{ $settings->cta_text }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                @endif
                
                @if(($settings->show_secondary_cta ?? false) && !empty($settings->secondary_cta_text) && !empty($settings->secondary_cta_url))
                    <a href="{{ $settings->secondary_cta_url }}" 
                       class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold {{ ($settings->style ?? 'gradient') === 'minimal' ? 'text-white border-2 border-slate-600 hover:border-blue-500 hover:bg-slate-800/50' : 'text-white border-2 border-white/30 hover:bg-white/10' }} rounded-lg transition-all duration-300">
                        {{ $settings->secondary_cta_text }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

