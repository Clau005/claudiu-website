{{-- Heaven Home - About Section --}}
<div class="min-h-screen">
    {{-- Hero Section --}}
    <section class="relative h-[400px] flex items-center">
        @if(!empty($settings->hero_image))
            <div class="absolute inset-0">
                <x-visual-editor::responsive-image
                    :src="$settings->hero_image"
                    :alt="$settings->page_title ?? 'About Us'"
                    class="w-full h-full object-cover"
                    sizes="100vw"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/30"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900 to-gray-800"></div>
        @endif
        
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                @if(!empty($settings->page_title))
                    <h1 class="font-serif text-5xl md:text-6xl font-bold text-white mb-4">
                        {{ $settings->page_title }}
                    </h1>
                @endif
                
                @if(!empty($settings->page_subtitle))
                    <p class="text-xl text-white/90">
                        {{ $settings->page_subtitle }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- Story Section --}}
    @if(!empty($settings->story_title) || !empty($settings->story_content))
        <section class="container mx-auto px-4 lg:px-8 py-20">
            <div class="max-w-4xl mx-auto">
                @if(!empty($settings->story_title))
                    <h2 class="font-serif text-4xl font-bold mb-6 text-center">
                        {{ $settings->story_title }}
                    </h2>
                @endif
                
                @if(!empty($settings->story_content))
                    <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                        {!! nl2br(e($settings->story_content)) !!}
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Image + Content Section --}}
    @if(!empty($settings->section_image) || !empty($settings->section_content))
        <section class="bg-muted py-20">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    @if(!empty($settings->section_image))
                        <div class="order-2 lg:order-1">
                            <x-visual-editor::responsive-image
                                :src="$settings->section_image"
                                alt="About"
                                class="w-full h-[500px] object-cover rounded-lg shadow-lg"
                                sizes="(min-width: 1024px) 50vw, 100vw"
                            />
                        </div>
                    @endif
                    
                    <div class="order-1 lg:order-2">
                        @if(!empty($settings->section_title))
                            <h2 class="font-serif text-4xl font-bold mb-6">
                                {{ $settings->section_title }}
                            </h2>
                        @endif
                        
                        @if(!empty($settings->section_content))
                            <div class="prose prose-lg text-gray-700 leading-relaxed">
                                {!! nl2br(e($settings->section_content)) !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Values/Features Section --}}
    @if(!empty($settings->value_1_title) || !empty($settings->value_2_title) || !empty($settings->value_3_title))
        <section class="container mx-auto px-4 lg:px-8 py-20">
            @if(!empty($settings->values_title))
                <h2 class="font-serif text-4xl font-bold mb-12 text-center">
                    {{ $settings->values_title }}
                </h2>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                @foreach([
                    ['title' => $settings->value_1_title ?? null, 'description' => $settings->value_1_description ?? null, 'icon' => $settings->value_1_icon ?? null],
                    ['title' => $settings->value_2_title ?? null, 'description' => $settings->value_2_description ?? null, 'icon' => $settings->value_2_icon ?? null],
                    ['title' => $settings->value_3_title ?? null, 'description' => $settings->value_3_description ?? null, 'icon' => $settings->value_3_icon ?? null],
                ] as $value)
                    @if(!empty($value['title']))
                        <div class="text-center">
                            @if(!empty($value['icon']))
                                <div class="text-5xl mb-4">{{ $value['icon'] }}</div>
                            @endif
                            <h3 class="font-serif text-2xl font-bold mb-4">{{ $value['title'] }}</h3>
                            @if(!empty($value['description']))
                                <p class="text-gray-600 leading-relaxed">
                                    {{ $value['description'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    {{-- Team Section (Optional) --}}
    @if(!empty($settings->show_team) && !empty($settings->team_title))
        <section class="bg-muted py-20">
            <div class="container mx-auto px-4 lg:px-8">
                <h2 class="font-serif text-4xl font-bold mb-12 text-center">
                    {{ $settings->team_title }}
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach(['1', '2', '3'] as $num)
                        @if(!empty($settings->{"team_{$num}_name"}))
                            <div class="text-center">
                                @if(!empty($settings->{"team_{$num}_image"}))
                                    <div class="mb-4">
                                        <x-visual-editor::responsive-image
                                            :src="$settings->{'team_' . $num . '_image'}"
                                            :alt="$settings->{'team_' . $num . '_name'}"
                                            class="w-48 h-48 object-cover rounded-full mx-auto"
                                            sizes="200px"
                                        />
                                    </div>
                                @endif
                                <h3 class="font-serif text-xl font-bold mb-1">
                                    {{ $settings->{"team_{$num}_name"} }}
                                </h3>
                                @if(!empty($settings->{"team_{$num}_role"}))
                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ $settings->{"team_{$num}_role"} }}
                                    </p>
                                @endif
                                @if(!empty($settings->{"team_{$num}_bio"}))
                                    <p class="text-sm text-gray-700">
                                        {{ $settings->{"team_{$num}_bio"} }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
