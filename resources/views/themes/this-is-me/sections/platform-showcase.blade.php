{{-- Platform Showcase - The Kicker! --}}
@php
    $techStack = array_filter(explode("\n", $settings->tech_stack ?? ''));
    $bgClass = match($settings->background_style ?? 'gradient') {
        'gradient' => 'bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900',
        'solid' => 'bg-slate-950',
        'pattern' => 'bg-slate-900',
        default => 'bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900'
    };
@endphp

<section class="relative py-20 lg:py-32 {{ $bgClass }} overflow-hidden">
    {{-- Animated Background Elements --}}
    <div class="absolute inset-0">
        @if(($settings->background_style ?? 'gradient') === 'pattern')
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#4f4f4f12_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f12_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
        @endif
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl"></div>
    </div>

    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            
            {{-- Content Side --}}
            <div class="space-y-8">
                {{-- Eyebrow --}}
                @if(!empty($settings->eyebrow_text))
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                        </span>
                        <span class="text-sm font-medium text-white">{{ $settings->eyebrow_text }}</span>
                    </div>
                @endif

                {{-- Main Title --}}
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight">
                    {{ $settings->main_title }}
                </h2>

                {{-- Subtitle --}}
                @if(!empty($settings->subtitle))
                    <p class="text-xl text-slate-300 leading-relaxed">
                        {{ $settings->subtitle }}
                    </p>
                @endif

                {{-- Platform Badge --}}
                <div class="inline-block p-6 bg-white/5 backdrop-blur-lg rounded-2xl border border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center text-3xl">
                            ⚡
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">{{ $settings->platform_name }}</h3>
                            <p class="text-slate-400">{{ $settings->platform_tagline }}</p>
                        </div>
                    </div>
                </div>

                {{-- Features Grid --}}
                @if($settings->show_features ?? true)
                    <div class="grid grid-cols-2 gap-4">
                        @if(!empty($settings->feature_1_title))
                            <div class="p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 hover:border-indigo-500/50 transition-colors">
                                <div class="text-3xl mb-2">{{ $settings->feature_1_icon }}</div>
                                <h4 class="text-white font-semibold mb-1">{{ $settings->feature_1_title }}</h4>
                                <p class="text-sm text-slate-400">{{ $settings->feature_1_description }}</p>
                            </div>
                        @endif
                        
                        @if(!empty($settings->feature_2_title))
                            <div class="p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 hover:border-pink-500/50 transition-colors">
                                <div class="text-3xl mb-2">{{ $settings->feature_2_icon }}</div>
                                <h4 class="text-white font-semibold mb-1">{{ $settings->feature_2_title }}</h4>
                                <p class="text-sm text-slate-400">{{ $settings->feature_2_description }}</p>
                            </div>
                        @endif
                        
                        @if(!empty($settings->feature_3_title))
                            <div class="p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 hover:border-purple-500/50 transition-colors">
                                <div class="text-3xl mb-2">{{ $settings->feature_3_icon }}</div>
                                <h4 class="text-white font-semibold mb-1">{{ $settings->feature_3_title }}</h4>
                                <p class="text-sm text-slate-400">{{ $settings->feature_3_description }}</p>
                            </div>
                        @endif
                        
                        @if(!empty($settings->feature_4_title))
                            <div class="p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 hover:border-teal-500/50 transition-colors">
                                <div class="text-3xl mb-2">{{ $settings->feature_4_icon }}</div>
                                <h4 class="text-white font-semibold mb-1">{{ $settings->feature_4_title }}</h4>
                                <p class="text-sm text-slate-400">{{ $settings->feature_4_description }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Tech Stack --}}
                @if(($settings->show_tech_stack ?? true) && !empty($techStack))
                    <div>
                        <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-3">Tech Stack</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($techStack as $tech)
                                <span class="px-4 py-2 bg-white/10 backdrop-blur-sm text-white rounded-lg border border-white/20 font-medium">
                                    {{ trim($tech) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CTA --}}
                @if(!empty($settings->cta_text) && !empty($settings->cta_url))
                    <div>
                        <a href="{{ $settings->cta_url }}" 
                           class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all duration-300 hover:shadow-lg">
                            {{ $settings->cta_text }}
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Visual Side - Code Preview / Screenshot --}}
            <div class="relative">
                @if($settings->show_code_snippet ?? true)
                    {{-- Code Editor Mockup --}}
                    <div class="relative bg-slate-950 rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
                        {{-- Window Controls --}}
                        <div class="flex items-center gap-2 px-4 py-3 bg-slate-900 border-b border-slate-700">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="ml-4 text-sm text-slate-400 font-mono">theme.json</span>
                        </div>
                        
                        {{-- Code Content --}}
                        <div class="p-6 font-mono text-sm overflow-x-auto">
                            <pre class="text-slate-300"><code><span class="text-slate-500">{</span>
  <span class="text-pink-400">"name"</span><span class="text-slate-500">:</span> <span class="text-green-400">"This Is Me"</span><span class="text-slate-500">,</span>
  <span class="text-pink-400">"slug"</span><span class="text-slate-500">:</span> <span class="text-green-400">"this-is-me"</span><span class="text-slate-500">,</span>
  <span class="text-pink-400">"sections"</span><span class="text-slate-500">:</span> <span class="text-slate-500">[</span>
    <span class="text-slate-500">{</span>
      <span class="text-pink-400">"key"</span><span class="text-slate-500">:</span> <span class="text-green-400">"hero"</span><span class="text-slate-500">,</span>
      <span class="text-pink-400">"label"</span><span class="text-slate-500">:</span> <span class="text-green-400">"Hero Section"</span><span class="text-slate-500">,</span>
      <span class="text-pink-400">"category"</span><span class="text-slate-500">:</span> <span class="text-green-400">"hero"</span>
    <span class="text-slate-500">},</span>
    <span class="text-slate-500">{</span>
      <span class="text-pink-400">"key"</span><span class="text-slate-500">:</span> <span class="text-green-400">"portfolio-grid"</span><span class="text-slate-500">,</span>
      <span class="text-pink-400">"label"</span><span class="text-slate-500">:</span> <span class="text-green-400">"Portfolio"</span><span class="text-slate-500">,</span>
      <span class="text-pink-400">"category"</span><span class="text-slate-500">:</span> <span class="text-green-400">"portfolio"</span>
    <span class="text-slate-500">}</span>
  <span class="text-slate-500">]</span>
<span class="text-slate-500">}</span></code></pre>
                        </div>

                        {{-- Glowing Border Effect --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 via-pink-500 to-purple-500 opacity-20 blur-xl -z-10"></div>
                    </div>

                    {{-- Floating Elements --}}
                    <div class="absolute -top-6 -right-6 w-32 h-32 bg-gradient-to-br from-indigo-500 to-pink-500 rounded-2xl opacity-20 blur-2xl animate-pulse"></div>
                    <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-gradient-to-br from-purple-500 to-teal-500 rounded-2xl opacity-20 blur-2xl animate-pulse animation-delay-1000"></div>
                @else
                    {{-- Placeholder for Screenshot --}}
                    <div class="relative aspect-square bg-slate-800 rounded-2xl border border-slate-700 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-6xl mb-4">⚡</div>
                            <p class="text-slate-400">Platform Screenshot</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Bottom Stats/Metrics (Optional) --}}
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-4xl font-bold text-white mb-2">100%</div>
                <div class="text-slate-400">Custom Built</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-white mb-2">Laravel</div>
                <div class="text-slate-400">Framework</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-white mb-2">Vue.js</div>
                <div class="text-slate-400">Frontend</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-white mb-2">∞</div>
                <div class="text-slate-400">Possibilities</div>
            </div>
        </div>
    </div>
</section>

<style>
    .animation-delay-1000 {
        animation-delay: 1s;
    }
</style>

