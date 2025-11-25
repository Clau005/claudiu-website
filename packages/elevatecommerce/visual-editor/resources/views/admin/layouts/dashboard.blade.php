<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Visual Editor</title>

      @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/24/outline/index.js"></script>
    
    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 h-screen overflow-hidden">
    <div class="h-screen flex">
        <!-- Sidebar -->
        <aside class="w-56 bg-[#2F2F31] text-gray-200 shrink-0 flex flex-col h-screen">
            <div class="px-6 py-4 shrink-0 text-gray-100">
                <h1 class="text-lg font-semibold tracking-tight">ElevateCommerce</h1>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-3 text-sm space-y-2">
                @foreach($navigation as $key => $item)
                    @php
                        $hasChildren = !empty($item['children']);
                        $itemUrl = $item['url'] ?? '';
                        $isActiveItem = $itemUrl ? request()->is(trim($itemUrl, '/')) : false;

                        $isActiveChild = false;
                        if ($hasChildren) {
                            foreach ($item['children'] as $child) {
                                $childUrl = $child['url'] ?? '';
                                if ($childUrl && request()->is(trim($childUrl, '/'))) {
                                    $isActiveChild = true;
                                    break;
                                }
                            }
                        }
                    @endphp

                    @if(!$hasChildren)
                        <a href="{{ $itemUrl }}" 
                           class="flex items-center px-4 py-1.5 text-gray-300 hover:bg-[#454547] hover:text-white transition-colors {{ $isActiveItem ? 'bg-[#454547] text-white rounded-md' : '' }}">
                            @if(!empty($item['icon']))
                                <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $item['icon'] !!}
                                </svg>
                            @endif
                            <span class="font-medium">{{ $item['label'] }}</span>
                            @if(!empty($item['badge']))
                                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    @else
                        <div class="px-4 py-1" x-data="{ open: {{ $isActiveChild ? 'true' : 'false' }} }">
                            <button type="button"
                                    class="flex items-center w-full mb-0.5 focus:outline-none"
                                    @click="open = !open">
                                @if(!empty($item['icon']))
                                    <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $item['icon'] !!}
                                    </svg>
                                @endif
                                <span class="leading-none font-semibold tracking-wide text-gray-300 text-left flex-1">{{ $item['label'] }}</span>
                            </button>

                            <div class="mt-0.5 pl-4 border-l border-gray-700 space-y-0.5 overflow-hidden"
                                 x-show="open"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 max-h-0"
                                 x-transition:enter-end="opacity-100 max-h-40"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 max-h-40"
                                 x-transition:leave-end="opacity-0 max-h-0">
                                @foreach($item['children'] as $childKey => $child)
                                    @php
                                        $childUrl = $child['url'] ?? '';
                                        $childActive = $childUrl ? request()->is(trim($childUrl, '/')) : false;
                                    @endphp
                                    <a href="{{ $childUrl }}"   
                                       class="relative flex items-center pl-6 pr-3 py-1.5 text-sm text-gray-400 hover:text-white hover:bg-[#454547] rounded-full transition-colors {{ $childActive ? 'bg-[#454547] text-white font-medium' : '' }}">
                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">↳</span>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 h-screen">
            <!-- Header -->
            <header class="bg-gray-50 shadow flex-shrink-0 z-10">
                <div class="flex justify-between items-center px-8 py-4">
                    <h2 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    
                    <div class="flex items-center space-x-4">
                        @if(Auth::guard('admin')->check())
                            <span class="text-gray-700">{{ Auth::guard('admin')->user()->full_name }}</span>
                            @if(Auth::guard('admin')->user()->is_super_admin)
                                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">Super Admin</span>
                            @endif
                        @endif
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
