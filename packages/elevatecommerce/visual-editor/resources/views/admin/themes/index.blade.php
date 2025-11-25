@extends('visual-editor::admin.layouts.dashboard')

@section('title', 'Themes')

@section('content')
<div class="max-w-4xl mx-auto">
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm">
        {{ session('error') }}
    </div>
@endif

@php
    $activeTheme = $themes->where('is_active', true)->first();
    // dd($activeTheme->pages()->where('slug', 'home')->get());
    $activeHomepage = $activeTheme ? $activeTheme->pages()->where('slug', 'home')->first() : null;
    if (!$activeHomepage && $activeTheme) {
        $activeHomepage = $activeTheme->pages()->orderBy('created_at', 'asc')->first();
    }
    // dd($activeHomepage);
@endphp

{{-- Active Theme --}}
@if($activeTheme)
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6">
    <!-- Theme Preview -->
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="w-full aspect-video overflow-hidden relative bg-white">
            @if($activeHomepage)
                <iframe 
                    src="{{ route('admin.pages.preview', $activeHomepage->id) }}" 
                    class="absolute top-0 left-0 border-0 w-full h-full" 
                    frameborder="0" 
                    style="width: 1400px; height: 787px; transform-origin: top left;"
                    onload="this.style.transform = 'scale(' + (this.parentElement.offsetWidth / 1400) + ')'"
                ></iframe>
            @else
                <div class="flex items-center justify-center h-full text-gray-400">
                    <p>No homepage found</p>
                </div>
            @endif
        </div>
    </div>    
    <!-- Theme Info -->
    <div class="p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-sm font-semibold text-gray-900">{{ $activeTheme->name }}</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                        Current theme
                    </span>
                </div>
                <p class="text-xs text-gray-600 mb-2">Last saved: {{ $activeTheme->updated_at->format('l \a\t g:i a') }}</p>
                @if($activeTheme->version)
                    <button class="text-xs text-blue-600 hover:text-blue-700 font-medium inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        Version {{ $activeTheme->version }} available
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                @endif
            </div>
            
            <div class="flex items-center gap-2">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 hover:bg-gray-100 rounded text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </button>
                    <div x-show="open" 
                         @click.away="open = false"
                         x-cloak
                         x-transition
                         class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                        <form action="{{ route('admin.themes.duplicate', $activeTheme->slug) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 rounded-t-lg">
                                Duplicate
                            </button>
                        </form>
                    </div>
                </div>
                @if($activeHomepage)
                    <a href="{{ route('admin.pages.edit', $activeHomepage->id) }}" 
                       class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-800 transition-colors">
                        Customize
                    </a>
                @else
                    <a href="{{ route('admin.pages.index', ['theme' => $activeTheme->slug]) }}" 
                       class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-800 transition-colors">
                        Customize
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

{{-- Theme Library --}}
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900">Theme library</h3>
        <p class="text-sm text-gray-600 mt-1">
            These themes are only visible to you. Publishing a theme from your library will switch it to your current theme.
        </p>
    </div>

    @if($themes->where('is_active', false)->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($themes->where('is_active', false) as $theme)
                @php
                    $homepage = $theme->pages()->where('slug', 'home')->first();
                    if (!$homepage) {
                        $homepage = $theme->pages()->orderBy('created_at', 'asc')->first();
                    }
                @endphp
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-4">
                        <!-- Theme Thumbnail -->
                        <div class="shrink-0 w-32 h-24 bg-gray-100 rounded border border-gray-200 overflow-hidden">
                            @if($homepage)
                                <iframe src="{{ route('admin.pages.preview', $homepage->id) }}" class="w-full h-full pointer-events-none" frameborder="0" style="transform: scale(0.25); transform-origin: 0 0; width: 400%; height: 400%;"></iframe>
                            @else
                                <div class="flex items-center justify-center h-full text-gray-400 text-xs">
                                    No preview
                                </div>
                            @endif
                        </div>
                        
                        <!-- Theme Info -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-gray-900">{{ $theme->name }}</h4>
                            <p class="text-xs text-gray-600 mt-1">Added: {{ $theme->created_at->format('M j \a\t g:i a') }}</p>
                            @if($theme->version)
                                <button class="text-xs text-blue-600 hover:text-blue-700 font-medium inline-flex items-center mt-1">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <circle cx="10" cy="10" r="3"/>
                                    </svg>
                                    Version {{ $theme->version }} available
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-2 hover:bg-gray-100 rounded text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-cloak
                                     x-transition
                                     class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                    <form action="{{ route('admin.themes.duplicate', $theme->slug) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 rounded-t-lg">
                                            Duplicate
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.themes.destroy', $theme->slug) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this theme?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-red-50 rounded-b-lg border-t border-gray-200">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <form action="{{ route('admin.themes.activate', $theme->slug) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-medium rounded hover:bg-gray-50 transition-colors">
                                    Publish
                                </button>
                            </form>
                            @if($homepage)
                                <a href="{{ route('admin.pages.edit', $homepage->id) }}" 
                                   class="px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-medium rounded hover:bg-gray-50 transition-colors">
                                    Customize
                                </a>
                            @else
                                <a href="{{ route('admin.pages.index', ['theme' => $theme->slug]) }}" 
                                   class="px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-medium rounded hover:bg-gray-50 transition-colors">
                                    Customize
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
            </svg>
            <p class="text-xs font-medium text-gray-900 mb-1">No themes in library</p>
            <p class="text-xs text-gray-600">Duplicate your current theme to create a backup</p>
        </div>
    @endif
</div>

{{-- Available Themes from Filesystem --}}
@if(count($availableThemes) > 0)
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            <h3 class="text-sm font-semibold text-blue-900 mb-1">Themes found in filesystem</h3>
            <p class="text-sm text-blue-800 mb-3">
                Run <code class="bg-blue-100 px-2 py-0.5 rounded font-mono text-xs">php artisan visual-editor:sync-themes</code> to sync these themes to the database.
            </p>
            <div class="space-y-2">
                @foreach($availableThemes as $theme)
                    <div class="bg-white border border-blue-200 rounded p-3 text-sm">
                        <div class="font-medium text-gray-900">{{ $theme['name'] }}</div>
                        <div class="text-gray-600 text-xs mt-0.5">{{ $theme['slug'] }} • Version {{ $theme['version'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

</div>
@endsection
