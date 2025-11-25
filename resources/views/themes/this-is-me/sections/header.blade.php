{{-- Header Navigation --}}
@php
    $navClass = match($settings->nav_style ?? 'blur') {
        'transparent' => 'bg-transparent',
        'solid' => 'bg-slate-900',
        'blur' => 'bg-slate-900/80 backdrop-blur-lg',
        default => 'bg-slate-900/80 backdrop-blur-lg'
    };
@endphp

<header class="fixed top-0 left-0 right-0 z-50 {{ $navClass }} border-b border-slate-800"
        x-data="{ mobileMenuOpen: false, scrolled: false }"
        x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
        :class="scrolled ? 'shadow-lg' : ''">
    
    <nav class="container mx-auto px-4 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3 group">
                @if(!empty($settings->logo_image))
                    <img src="{{ $settings->logo_image }}" alt="Logo" class="h-10 w-auto">
                @else
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg flex items-center justify-center font-bold text-white text-xl group-hover:scale-110 transition-transform">
                        {{ $settings->logo_text ?? 'CB' }}
                    </div>
                @endif
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="#about" class="text-slate-300 hover:text-white transition-colors font-medium">About</a>
                <a href="#portfolio" class="text-slate-300 hover:text-white transition-colors font-medium">Portfolio</a>
                <a href="#skills" class="text-slate-300 hover:text-white transition-colors font-medium">Skills</a>
                <a href="#contact" class="text-slate-300 hover:text-white transition-colors font-medium">Contact</a>
                
                @if(($settings->show_cta ?? true) && !empty($settings->cta_text) && !empty($settings->cta_url))
                    <a href="{{ $settings->cta_url }}" 
                       class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all duration-300">
                        {{ $settings->cta_text }}
                    </a>
                @endif
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="md:hidden text-white p-2">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-4"
             class="md:hidden py-4 border-t border-slate-800">
            <div class="flex flex-col gap-4">
                <a href="#about" @click="mobileMenuOpen = false" class="text-slate-300 hover:text-white transition-colors font-medium py-2">About</a>
                <a href="#portfolio" @click="mobileMenuOpen = false" class="text-slate-300 hover:text-white transition-colors font-medium py-2">Portfolio</a>
                <a href="#skills" @click="mobileMenuOpen = false" class="text-slate-300 hover:text-white transition-colors font-medium py-2">Skills</a>
                <a href="#contact" @click="mobileMenuOpen = false" class="text-slate-300 hover:text-white transition-colors font-medium py-2">Contact</a>
                
                @if(($settings->show_cta ?? true) && !empty($settings->cta_text) && !empty($settings->cta_url))
                    <a href="{{ $settings->cta_url }}" 
                       @click="mobileMenuOpen = false"
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold text-center hover:bg-blue-700 transition-colors">
                        {{ $settings->cta_text }}
                    </a>
                @endif
            </div>
        </div>
    </nav>
</header>

