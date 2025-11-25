@extends('visual-editor::admin.layouts.dashboard')

@section('title', 'Add collection')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.collections.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to collections
        </a>
    </div>

    <form action="{{ route('admin.collections.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="col-span-2 space-y-6">
                <!-- Title -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200"
                               placeholder="e.g., Summer collection, Under $100, Staff picks" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ 
                        quill: null,
                        initEditor() {
                            this.quill = new Quill('#quill-editor-create', {
                                theme: 'snow',
                                modules: {
                                    toolbar: [
                                        ['bold', 'italic', 'underline'],
                                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                        ['link'],
                                        ['clean']
                                    ]
                                }
                            });
                            
                            // Set initial content
                            const content = document.getElementById('description').value;
                            if (content) {
                                this.quill.root.innerHTML = content;
                            }
                            
                            // Update hidden field on change
                            this.quill.on('text-change', () => {
                                document.getElementById('description').value = this.quill.root.innerHTML;
                            });
                        }
                    }" x-init="setTimeout(() => initEditor(), 100)">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <div id="quill-editor-create" style="height: 200px;"></div>
                        <textarea name="description" id="description" class="hidden">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Collection type -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Collection type</h3>
                    
                    <div class="space-y-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="type" value="manual" checked class="mt-1">
                            <div>
                                <div class="font-medium text-gray-900">Manual</div>
                                <div class="text-sm text-gray-500">Add items to this collection one by one. <a href="#" class="text-blue-600">Learn more about manual collections</a></div>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer opacity-50">
                            <input type="radio" name="type" value="smart" disabled class="mt-1">
                            <div>
                                <div class="font-medium text-gray-900">Smart</div>
                                <div class="text-sm text-gray-500">Existing and future items that match the conditions you set will automatically be added to this collection. <a href="#" class="text-blue-600">Learn more about smart collections</a></div>
                                <div class="text-xs text-yellow-600 mt-1">Coming soon</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publishing -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Publishing</h3>
                    <div class="text-sm text-gray-600">
                        <p>This collection will be saved as a draft.</p>
                        <p class="mt-2">You can publish it after adding items.</p>
                    </div>
                </div>

                <!-- Image -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Image</h3>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Add image</p>
                        <p class="text-xs text-gray-500">or drop an image to upload</p>
                    </div>
                    <input type="hidden" name="image" id="image" value="{{ old('image') }}">
                </div>

                <!-- Theme template -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Theme template</h3>
                    <select name="page_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <option value="">Default collection</option>
                        @foreach($pages as $page)
                            <option value="{{ $page->id }}" {{ old('page_id') == $page->id ? 'selected' : '' }}>
                                {{ $page->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500">Choose a custom page template for this collection</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('admin.collections.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-700">
                Save collection
            </button>
        </div>
    </form>
</div>
@endsection
