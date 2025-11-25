<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $page->name }} - Visual Editor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div id="app">
        <page-editor page-id="{{ $page->id }}"></page-editor>
    </div>

    @php
        $manifestPath = public_path('vendor/visual-editor/.vite/manifest.json');
        $hasManifest = file_exists($manifestPath);
        $appJs = null;
        
        if ($hasManifest) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $appJs = $manifest['resources/js/app.js']['file'] ?? null;
        }
    @endphp

    @if($hasManifest && $appJs)
        <script type="module" src="{{ asset('vendor/visual-editor/' . $appJs) }}"></script>
    @else
        <!-- Development mode: use Vite -->
        @vite(['resources/js/app.js'], 'packages/elevatecommerce/visual-editor')
    @endif
</body>
</html>
