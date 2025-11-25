<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->name }} - {{ $theme->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $page->name }}</h1>
            <p class="text-gray-600 mb-8">This page has no sections yet. Add sections to get started.</p>
            <a href="{{ route('admin.dashboard') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">
                Go to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
