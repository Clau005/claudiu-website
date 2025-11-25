{{-- This Is Me - Hero Section --}}
@php
    $roles = array_filter(explode("\n", $settings->animated_roles ?? ''));
    $bgClass = match($settings->background_gradient ?? 'gradient-3') {
        'gradient-1' => 'bg-gradient-to-br from-purple-900 via-indigo-900 to-pink-900',
        'gradient-2' => 'bg-gradient-to-br from-blue-900 via-indigo-900 to-teal-900',
        'gradient-3' => 'bg-slate-950',
        'solid' => 'bg-slate-900',
        default => 'bg-slate-950'
    };
@endphp

<section class="relative h-screen flex items-center justify-center overflow-hidden {{ $bgClass }}">
    {{-- Animated Background Elements --}}
    <div class="absolute inset-0 overflow-hidden">
        {{-- Gradient Mesh --}}
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-indigo-500/20 via-transparent to-transparent"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_var(--tw-gradient-stops))] from-pink-500/20 via-transparent to-transparent"></div>
        
        {{-- Animated Grid --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#4f4f4f12_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f12_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,#000_70%,transparent_110%)]"></div>
    </div>

    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            
            {{-- Text Content --}}
            <div class="text-center lg:text-left space-y-6">
                {{-- Greeting --}}
                @if(!empty($settings->greeting))
                    <p class="text-lg md:text-xl text-indigo-400 font-medium animate-fade-in">
                        {{ $settings->greeting }}
                    </p>
                @endif

                {{-- Name --}}
                <h1 class="text-5xl md:text-7xl font-bold text-white leading-tight animate-fade-in-up">
                    {{ $settings->name }}
                </h1>

                {{-- Tagline --}}
                @if(!empty($settings->tagline))
                    <p class="text-xl md:text-2xl text-slate-300 animate-fade-in-up animation-delay-200">
                        {{ $settings->tagline }}
                    </p>
                @endif

                {{-- Animated Roles --}}
                @if(!empty($roles))
                    <div class="flex items-center justify-center lg:justify-start gap-3 text-lg md:text-xl animate-fade-in-up animation-delay-300">
                        <span class="text-slate-400">I'm a</span>
                        <span class="font-semibold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400" 
                              x-data="{ 
                                  roles: {{ json_encode($roles) }}, 
                                  currentIndex: 0,
                                  currentRole: '{{ $roles[0] ?? '' }}'
                              }"
                              x-init="setInterval(() => { 
                                  currentIndex = (currentIndex + 1) % roles.length; 
                                  currentRole = roles[currentIndex]; 
                              }, 3000)"
                              x-text="currentRole"
                              x-transition:enter="transition ease-out duration-300"
                              x-transition:enter-start="opacity-0 transform translate-y-2"
                              x-transition:enter-end="opacity-100 transform translate-y-0">
                        </span>
                    </div>
                @endif

                {{-- Description --}}
                @if(!empty($settings->description))
                    <p class="text-lg text-slate-400 max-w-2xl mx-auto lg:mx-0 leading-relaxed animate-fade-in-up animation-delay-400">
                        {{ $settings->description }}
                    </p>
                @endif

                {{-- CTAs --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-fade-in-up animation-delay-500">
                    @if(!empty($settings->cta_primary_text) && !empty($settings->cta_primary_url))
                        <a href="{{ $settings->cta_primary_url }}" 
                           class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-blue-600 rounded-lg transition-all duration-300 hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/50">
                            <span>{{ $settings->cta_primary_text }}</span>
                            <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @endif

                    @if(!empty($settings->cta_secondary_text) && !empty($settings->cta_secondary_url))
                        <a href="{{ $settings->cta_secondary_url }}" 
                           class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-slate-600 rounded-lg hover:border-blue-500 hover:bg-slate-800/50 transition-all duration-300">
                            {{ $settings->cta_secondary_text }}
                        </a>
                    @endif
                </div>

                {{-- Social Links (optional - can be added later) --}}
                <div class="flex gap-4 justify-center lg:justify-start animate-fade-in-up animation-delay-600">
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Profile Image --}}
            <div class="flex justify-center lg:justify-end animate-fade-in-up animation-delay-300">
                @if(!empty($settings->profile_image))
                    <div class="relative">
                        {{-- Glow Effect --}}
                        <div class="absolute inset-0 bg-blue-500/20 rounded-full blur-3xl"></div>
                        
                        {{-- Image Container --}}
                        <div class="relative w-64 h-64 md:w-80 md:h-80 lg:w-96 lg:h-96">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full animate-spin-slow"></div>
                            <div class="absolute inset-1 rounded-full overflow-hidden bg-slate-900">
                                <x-visual-editor::responsive-image
                                    :src="$settings->profile_image"
                                    :alt="$settings->name"
                                    class="w-full h-full object-cover"
                                    loading="eager"
                                    fetchpriority="high"
                                    sizes="(min-width: 1024px) 384px, (min-width: 768px) 320px, 256px"
                                />
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Placeholder Avatar --}}
                    <div class="relative w-64 h-64 md:w-80 md:h-80 lg:w-96 lg:h-96">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full animate-spin-slow"></div>
                        <div class="absolute inset-1 rounded-full bg-slate-800 flex items-center justify-center">
                            <span class="text-8xl">👨‍💻</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Scroll Indicator --}}
    @if($settings->show_scroll_indicator ?? true)
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#about" class="flex flex-col items-center gap-2 text-slate-400 hover:text-white transition-colors">
                <span class="text-sm">Scroll to explore</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </a>
        </div>
    @endif
</section>

<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animate-fade-in {
        animation: fade-in 1s ease-out;
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 1s ease-out;
    }
    
    .animate-spin-slow {
        animation: spin-slow 8s linear infinite;
    }
    
    .animation-delay-200 {
        animation-delay: 0.2s;
        opacity: 0;
        animation-fill-mode: forwards;
    }
    
    .animation-delay-300 {
        animation-delay: 0.3s;
        opacity: 0;
        animation-fill-mode: forwards;
    }
    
    .animation-delay-400 {
        animation-delay: 0.4s;
        opacity: 0;
        animation-fill-mode: forwards;
    }
    
    .animation-delay-500 {
        animation-delay: 0.5s;
        opacity: 0;
        animation-fill-mode: forwards;
    }
    
    .animation-delay-600 {
        animation-delay: 0.6s;
        opacity: 0;
        animation-fill-mode: forwards;
    }
</style>

