@extends('visual-editor::admin.layouts.dashboard')

@section('title', 'Create Page')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Create New Page</h1>
    <p class="text-gray-600 mt-2">Add a new page to your theme</p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
    <form action="{{ route('admin.pages.store') }}" method="POST">
        @csrf

        {{-- Theme Selection --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
            <select name="theme_id" required class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">Select a theme</option>
                @foreach($themes as $theme)
                    <option value="{{ $theme->id }}" {{ $selectedTheme === $theme->slug ? 'selected' : '' }}>
                        {{ $theme->name }}
                        @if($theme->is_active)
                            (Active)
                        @endif
                    </option>
                @endforeach
            </select>
            @error('theme_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Page Name --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Page Name</label>
            <input type="text" 
                   name="name" 
                   value="{{ old('name') }}"
                   required 
                   placeholder="e.g., Home, About Us, Contact"
                   class="w-full border border-gray-300 rounded px-3 py-2">
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Page Slug --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
            <input type="text" 
                   name="slug" 
                   value="{{ old('slug') }}"
                   required 
                   placeholder="e.g., home, about-us, contact"
                   class="w-full border border-gray-300 rounded px-3 py-2">
            <p class="text-sm text-gray-500 mt-1">This will be the URL path for your page</p>
            @error('slug')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Page Type --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Page Type</label>
            <select name="type" required class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="static" {{ old('type') === 'static' ? 'selected' : '' }}>
                    Static (e.g., Home, About, Contact)
                </option>
                <option value="dynamic" {{ old('type') === 'dynamic' ? 'selected' : '' }}>
                    Dynamic (e.g., Product, Collection)
                </option>
                <option value="template" {{ old('type') === 'template' ? 'selected' : '' }}>
                    Template
                </option>
            </select>
            @error('type')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Context Key (for dynamic pages) --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Context Key (optional)
            </label>
            <input type="text" 
                   name="context_key" 
                   value="{{ old('context_key') }}"
                   placeholder="e.g., product, collection"
                   class="w-full border border-gray-300 rounded px-3 py-2">
            <p class="text-sm text-gray-500 mt-1">
                For dynamic pages, specify which context to use (e.g., product, collection)
            </p>
            @error('context_key')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Route Pattern (for dynamic pages) --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Route Pattern (optional)
            </label>
            <input type="text" 
                   name="route_pattern" 
                   value="{{ old('route_pattern') }}"
                   placeholder="e.g., /products/{slug}, /collections/{slug}"
                   class="w-full border border-gray-300 rounded px-3 py-2">
            <p class="text-sm text-gray-500 mt-1">
                For dynamic pages, specify the URL pattern
            </p>
            @error('route_pattern')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Create Page
            </button>
            <a href="{{ route('admin.pages.index') }}" class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
