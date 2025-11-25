@props([
    'src',
    'alt' => '',
    'class' => '',
    'loading' => 'lazy',
    'fetchpriority' => null,
    'sizes' => '100vw'
])

@php
    // Parse URL to get path
    $parsedUrl = parse_url($src);
    $path = $parsedUrl['path'] ?? $src;
    
    // Extract path info
    $pathInfo = pathinfo($path);
    $directory = $pathInfo['dirname'];
    $filename = $pathInfo['filename'];
    
    // Build WebP URLs
    $webpOriginal = "{$directory}/{$filename}.webp";
    $webpThumbnail = "{$directory}/{$filename}-thumbnail.webp";
    $webpMedium = "{$directory}/{$filename}-medium.webp";
    $webpLarge = "{$directory}/{$filename}-large.webp";
    
    // Build srcset with available sizes
    // Check storage path (convert URL path to storage path)
    $storagePath = str_replace('/storage/', '', $path);
    $srcsetParts = [];
    
    if (Storage::disk('public')->exists(str_replace('/storage/', '', $webpThumbnail))) {
        $srcsetParts[] = "{$webpThumbnail} 400w";
    }
    if (Storage::disk('public')->exists(str_replace('/storage/', '', $webpMedium))) {
        $srcsetParts[] = "{$webpMedium} 800w";
    }
    if (Storage::disk('public')->exists(str_replace('/storage/', '', $webpLarge))) {
        $srcsetParts[] = "{$webpLarge} 1600w";
    }
    
    // Always include original WebP as fallback
    $srcsetParts[] = "{$webpOriginal} 2000w";
    
    $srcset = implode(', ', $srcsetParts);
@endphp

<picture>
    {{-- WebP sources with responsive sizes --}}
    <source 
        type="image/webp"
        srcset="{{ $srcset }}"
        sizes="{{ $sizes }}"
    >
    
    {{-- Fallback to original image for browsers that don't support WebP --}}
    <img 
        src="{{ $src }}" 
        alt="{{ $alt }}"
        class="{{ $class }}"
        loading="{{ $loading }}"
        @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
    >
</picture>
