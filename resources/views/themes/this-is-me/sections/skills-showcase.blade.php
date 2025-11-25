{{-- Skills Showcase Section --}}
@php
    $parseSkills = function($skillsString) {
        $skills = [];
        foreach (array_filter(explode("\n", $skillsString ?? '')) as $line) {
            $parts = explode('|', $line);
            $skills[] = [
                'name' => trim($parts[0] ?? ''),
                'level' => (int) trim($parts[1] ?? 80)
            ];
        }
        return $skills;
    };
    
    $frontendSkills = $parseSkills($settings->frontend_skills);
    $backendSkills = $parseSkills($settings->backend_skills);
    $toolsSkills = $parseSkills($settings->tools_skills);
    $otherSkills = $parseSkills($settings->other_skills);
    
    $allSkills = [
        ['title' => 'Frontend', 'icon' => '🎨', 'skills' => $frontendSkills, 'color' => 'indigo'],
        ['title' => 'Backend', 'icon' => '⚙️', 'skills' => $backendSkills, 'color' => 'pink'],
        ['title' => 'Tools & DevOps', 'icon' => '🛠️', 'skills' => $toolsSkills, 'color' => 'teal'],
        ['title' => 'Other', 'icon' => '💡', 'skills' => $otherSkills, 'color' => 'purple'],
    ];
@endphp

<section id="skills" class="py-20 lg:py-32 bg-slate-950 relative overflow-hidden">
    {{-- Background Elements --}}
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-900/20 via-transparent to-transparent"></div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#4f4f4f12_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f12_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
    </div>

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

        @if(($settings->layout_style ?? 'categories') === 'categories')
            {{-- Categorized Layout --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($allSkills as $category)
                    @if(count($category['skills']) > 0)
                        <div class="bg-slate-900/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-800 hover:border-{{ $category['color'] }}-500/50 transition-colors">
                            <div class="flex items-center gap-3 mb-6">
                                <span class="text-4xl">{{ $category['icon'] }}</span>
                                <h3 class="text-2xl font-bold text-white">{{ $category['title'] }}</h3>
                            </div>
                            
                            <div class="space-y-4">
                                @foreach($category['skills'] as $skill)
                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-slate-300 font-medium">{{ $skill['name'] }}</span>
                                            @if($settings->show_proficiency ?? true)
                                                <span class="text-{{ $category['color'] }}-400 text-sm font-semibold">{{ $skill['level'] }}%</span>
                                            @endif
                                        </div>
                                        @if($settings->show_proficiency ?? true)
                                            <div class="h-2 bg-slate-800 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-{{ $category['color'] }}-500 to-{{ $category['color'] }}-400 rounded-full transition-all duration-1000 ease-out"
                                                     style="width: {{ $skill['level'] }}%"
                                                     x-data
                                                     x-intersect="$el.style.width = '{{ $skill['level'] }}%'">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @elseif(($settings->layout_style ?? 'categories') === 'grid')
            {{-- Grid Layout --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($allSkills as $category)
                    @foreach($category['skills'] as $skill)
                        <div class="group relative bg-slate-900/50 backdrop-blur-sm rounded-xl p-6 border border-slate-800 hover:border-{{ $category['color'] }}-500 transition-all duration-300 hover:transform hover:scale-105">
                            <div class="text-center">
                                <div class="text-3xl mb-3">{{ $category['icon'] }}</div>
                                <h4 class="text-white font-semibold mb-2">{{ $skill['name'] }}</h4>
                                @if($settings->show_proficiency ?? true)
                                    <div class="text-{{ $category['color'] }}-400 text-sm font-semibold">{{ $skill['level'] }}%</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        @else
            {{-- Progress Bars Layout --}}
            <div class="max-w-4xl mx-auto space-y-12">
                @foreach($allSkills as $category)
                    @if(count($category['skills']) > 0)
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <span class="text-3xl">{{ $category['icon'] }}</span>
                                <h3 class="text-2xl font-bold text-white">{{ $category['title'] }}</h3>
                            </div>
                            
                            <div class="space-y-6">
                                @foreach($category['skills'] as $skill)
                                    <div>
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="text-lg text-slate-300 font-medium">{{ $skill['name'] }}</span>
                                            @if($settings->show_proficiency ?? true)
                                                <span class="text-{{ $category['color'] }}-400 font-bold">{{ $skill['level'] }}%</span>
                                            @endif
                                        </div>
                                        @if($settings->show_proficiency ?? true)
                                            <div class="h-3 bg-slate-800 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-{{ $category['color'] }}-500 to-{{ $category['color'] }}-400 rounded-full transition-all duration-1000 ease-out"
                                                     style="width: 0%"
                                                     x-data
                                                     x-intersect="$el.style.width = '{{ $skill['level'] }}%'">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>

