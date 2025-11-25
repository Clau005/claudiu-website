<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Dynamic SEO Meta Tags --}}
    @php
        // Priority: Context SEO > Page SEO > Theme defaults
        $metaTitle = $seo['title'] ?? $page->meta_title ?? $page->name . ' - ' . $theme->name;
        $metaDescription = $seo['description'] ?? $page->meta_description ?? $page->description ?? $theme->description ?? 'Welcome to our store';
        $metaImage = $seo['image'] ?? $page->featured_image ?? null;
        $metaKeywords = $seo['keywords'] ?? $page->meta_keywords ?? null;
        $canonical = $seo['canonical'] ?? url()->current();
    @endphp
    
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    @if($metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    
    {{-- Robots Meta Tag --}}
    <meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $canonical }}">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="{{ $seo['og_type'] ?? 'website' }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    @if($metaImage)
        <meta property="og:image" content="{{ $metaImage }}">
    @endif
    
    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $canonical }}">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if($metaImage)
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif
    
    {{-- Preload critical resources --}}
    @if(isset($page->featured_image))
        <link rel="preload" as="image" href="{{ $page->featured_image }}" fetchpriority="high">
    @endif
    
    {{-- Vite Assets (Tailwind CSS + JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Alpine.js for interactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @if(isset($theme->settings['custom_css']))
        <style>{!! $theme->settings['custom_css'] !!}</style>
    @endif
    
    @stack('styles')
</head>
<body>
    @if(isset($preview) && $preview)
        <div class="bg-yellow-100 border-b border-yellow-300 px-4 py-2 text-sm text-yellow-800">
            <strong>Preview Mode</strong> - This is how your page will look when published
        </div>
    @endif
    {{-- Header Sections (Fixed/Absolute positioning) --}}
    @foreach($headerSections as $section)
        <div data-section-id="{{ $section['id'] }}" data-section-key="{{ $section['key'] }}" data-section-group="header">
            {!! $section['html'] !!}
        </div>
    @endforeach

    <div class="flex flex-col min-h-screen">
        <main class="flex-1">
            {{-- Template Sections --}}
            @foreach($templateSections as $section)
                <div data-section-id="{{ $section['id'] }}" data-section-key="{{ $section['key'] }}" data-section-group="template">
                    {!! $section['html'] !!}
                </div>
            @endforeach
        </main>

        {{-- Footer Sections --}}
        @foreach($footerSections as $section)
            <div data-section-id="{{ $section['id'] }}" data-section-key="{{ $section['key'] }}" data-section-group="footer">
                {!! $section['html'] !!}
            </div>
        @endforeach
    </div>

    @if(isset($theme->settings['custom_js']))
        <script>{!! $theme->settings['custom_js'] !!}</script>
    @endif
    
    @stack('scripts')
</body>
</html>
