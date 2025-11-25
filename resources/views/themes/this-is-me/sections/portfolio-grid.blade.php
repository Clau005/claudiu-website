{{-- Portfolio Grid Section --}}
@php
    // use App\Models\Project;
    
    // Fetch projects from database (using Products tagged with 'portfolio')
    $projects = collect();
    // if (($settings->projects_source ?? 'database') === 'database') {
    //     $projects = Product::withTag('portfolio')
    //         ->where('is_active', true)
    //         ->orderBy('created_at', 'desc')
    //         ->limit($settings->projects_limit ?? 6)
    //         ->get();
    // }
    
    $filterCategories = array_filter(explode("\n", $settings->filter_categories ?? 'All'));
    
    $gridClass = match($settings->layout ?? '3-col') {
        '2-col' => 'grid-cols-1 md:grid-cols-2',
        '3-col' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        'masonry' => 'columns-1 md:columns-2 lg:columns-3',
        default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
    };
@endphp

<section id="portfolio" class="py-20 lg:py-32 bg-slate-900 relative overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#4f4f4f12_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f12_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
    
    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                {{ $settings->section_title }}
            </h2>
            @if(!empty($settings->section_subtitle))
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    {{ $settings->section_subtitle }}
                </p>
            @endif
        </div>

        {{-- Category Filters --}}
        @if(($settings->show_filters ?? true) && !empty($filterCategories))
            <div class="flex flex-wrap justify-center gap-3 mb-12" 
                 x-data="{ activeFilter: 'All' }">
                @foreach($filterCategories as $category)
                    <button @click="activeFilter = '{{ trim($category) }}'"
                            :class="activeFilter === '{{ trim($category) }}' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                            class="px-6 py-2 rounded-full font-medium transition-all duration-300">
                        {{ trim($category) }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Projects Grid --}}
        @if($projects->count() > 0)
            <div class="{{ ($settings->layout ?? '3-col') === 'masonry' ? '' : 'grid' }} {{ $gridClass }} gap-8">
                @foreach($projects as $project)
                    <div class="{{ ($settings->layout ?? '3-col') === 'masonry' ? 'mb-8 break-inside-avoid' : '' }}"
                         x-data="{ hover: false }"
                         @mouseenter="hover = true"
                         @mouseleave="hover = false">
                        
                        @if(($settings->card_style ?? 'modern') === 'glassmorphism')
                            {{-- Glassmorphism Card --}}
                            <div class="group relative bg-white/5 backdrop-blur-lg rounded-2xl overflow-hidden border border-white/10 hover:border-indigo-500/50 transition-all duration-500 hover:transform hover:scale-105">
                                @if($project->preview)
                                    <div class="aspect-video overflow-hidden">
                                        <x-visual-editor::responsive-image
                                            :src="$project->preview"
                                            :alt="$project->name"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                            loading="lazy"
                                            sizes="(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw"
                                        />
                                    </div>
                                @endif
                                
                                <div class="p-6">
                                    @if($project->category)
                                        <span class="inline-block px-3 py-1 text-xs font-semibold text-indigo-400 bg-indigo-500/10 rounded-full mb-3">
                                            {{ $project->category }}
                                        </span>
                                    @endif
                                    
                                    <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-indigo-400 transition-colors">
                                        {{ $project->name }}
                                    </h3>
                                    
                                    @if($project->excerpt)
                                        <p class="text-slate-400 mb-4 line-clamp-3">
                                            {{ $project->excerpt }}
                                        </p>
                                    @endif
                                    
                                    <a href="/portfolio/{{ $project->slug }}" 
                                       class="inline-flex items-center text-indigo-400 hover:text-indigo-300 font-medium group/link">
                                        View Project
                                        <svg class="w-4 h-4 ml-2 transition-transform group-hover/link:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @elseif(($settings->card_style ?? 'modern') === 'minimal')
                            {{-- Minimal Card --}}
                            <a href="/portfolio/{{ $project->slug }}" class="group block">
                                @if($project->preview)
                                    <div class="aspect-video overflow-hidden rounded-lg mb-4 bg-slate-800">
                                        <x-visual-editor::responsive-image
                                            :src="$project->preview"
                                            :alt="$project->name"
                                            class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105 group-hover:opacity-90"
                                            loading="lazy"
                                            sizes="(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw"
                                        />
                                    </div>
                                @endif
                                
                                @if($project->category)
                                    <span class="text-sm text-indigo-400 font-medium">{{ $project->category }}</span>
                                @endif
                                
                                <h3 class="text-xl font-bold text-white mt-2 mb-2 group-hover:text-indigo-400 transition-colors">
                                    {{ $project->name }}
                                </h3>
                                
                                @if($project->excerpt)
                                    <p class="text-slate-400 line-clamp-2">
                                        {{ $project->excerpt }}
                                    </p>
                                @endif
                            </a>
                        @else
                            {{-- Modern Card (Default) --}}
                            <div class="group relative bg-slate-800/50 rounded-2xl overflow-hidden border border-slate-700 hover:border-indigo-500 transition-all duration-500 hover:transform hover:scale-105 hover:shadow-2xl hover:shadow-indigo-500/20">
                                @if($project->preview)
                                    <div class="relative aspect-video overflow-hidden">
                                        <x-visual-editor::responsive-image
                                            :src="$project->preview"
                                            :alt="$project->name"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                            loading="lazy"
                                            sizes="(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw"
                                        />
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-60"></div>
                                    </div>
                                @endif
                                
                                <div class="p-6">
                                    @if($project->category)
                                        <span class="inline-block px-3 py-1 text-xs font-semibold text-indigo-400 bg-indigo-500/10 rounded-full mb-3">
                                            {{ $project->category }}
                                        </span>
                                    @endif
                                    
                                    <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-indigo-400 transition-colors">
                                        {{ $project->name }}
                                    </h3>
                                    
                                    @if($project->excerpt)
                                        <p class="text-slate-400 mb-4 line-clamp-3">
                                            {{ $project->excerpt }}
                                        </p>
                                    @endif
                                    
                                    {{-- Tech Stack Tags (if stored in tags) --}}
                                    @if($project->tags && $project->tags->count() > 0)
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            @foreach($project->tags->take(4) as $tag)
                                                <span class="text-xs px-2 py-1 bg-slate-700 text-slate-300 rounded">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <a href="/portfolio/{{ $project->slug }}" 
                                       class="inline-flex items-center text-indigo-400 hover:text-indigo-300 font-medium group/link">
                                        View Project
                                        <svg class="w-4 h-4 ml-2 transition-transform group-hover/link:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- View All Button --}}
            @if(($settings->show_view_all ?? true) && !empty($settings->view_all_url))
                <div class="text-center mt-16">
                    <a href="{{ $settings->view_all_url }}" 
                       class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-indigo-500 rounded-lg hover:bg-indigo-500 transition-all duration-300 group">
                        View All Projects
                        <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="text-center py-20">
                <div class="text-6xl mb-4">💼</div>
                <h3 class="text-2xl font-bold text-white mb-2">No Projects Yet</h3>
                <p class="text-slate-400">Tag some products with 'portfolio' to display them here.</p>
            </div>
        @endif
    </div>
</section>

