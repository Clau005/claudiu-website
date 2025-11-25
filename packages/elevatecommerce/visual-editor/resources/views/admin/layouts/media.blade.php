<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Visual Editor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-100 h-screen overflow-hidden">
    <!-- Content -->
    <main class="flex-1 overflow-y-auto">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
