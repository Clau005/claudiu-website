@extends('visual-editor::admin.layouts.dashboard')

@section('title', 'Edit collection')

@push('scripts')
<script>
// Global function to handle media selection from Vue component
window.handleMediaSelection = function(url, targetInputId) {
    const input = document.getElementById(targetInputId);
    if (input) {
        input.value = url;
        input.dispatchEvent(new Event('input'));
    }
};
</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="collectionEditor()">
    <div class="mb-6">
        <a href="{{ route('admin.collections.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to collections
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.collections.update', $collection) }}" method="POST" id="collection-form">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="col-span-2 space-y-6">
                <!-- Title -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $collection->title) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200"
                               placeholder="e.g., Summer collection, Under $100, Staff picks" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $collection->slug) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200"
                               placeholder="e.g., summer-collection" required>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ 
                        quill: null,
                        initEditor() {
                            this.quill = new Quill('#quill-editor', {
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
                        <div id="quill-editor" style="height: 200px;"></div>
                        <textarea name="description" id="description" class="hidden">{{ old('description', $collection->description) }}</textarea>
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
                            <input type="radio" name="type" value="manual" {{ $collection->type === 'manual' ? 'checked' : '' }} class="mt-1">
                            <div>
                                <div class="font-medium text-gray-900">Manual</div>
                                <div class="text-sm text-gray-500">Add items to this collection one by one.</div>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer opacity-50">
                            <input type="radio" name="type" value="smart" {{ $collection->type === 'smart' ? 'checked' : '' }} disabled class="mt-1">
                            <div>
                                <div class="font-medium text-gray-900">Smart</div>
                                <div class="text-sm text-gray-500">Existing and future items that match the conditions you set will automatically be added to this collection.</div>
                                <div class="text-xs text-yellow-600 mt-1">Coming soon</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Products/Items (Manual only) -->
                @if($collection->type === 'manual')
                    <div class="bg-white rounded-lg shadow-sm p-6" x-data="{
                        showBrowseModal: false,
                        selectedType: null,
                        items: [],
                        loading: false,
                        selectedItems: [],
                        
                        selectType(type) {
                            this.selectedType = type;
                            this.fetchItems(type.key);
                        },
                        
                        async fetchItems(typeKey) {
                            this.loading = true;
                            try {
                                const response = await fetch(`/admin/api/collections/{{ $collection->id }}/available-items?context_type=${typeKey}`);
                                const data = await response.json();
                                this.items = data.items || [];
                            } catch (error) {
                                console.error('Failed to fetch items:', error);
                                this.items = [];
                            }
                            this.loading = false;
                        },
                        
                        toggleItem(itemId) {
                            const index = this.selectedItems.indexOf(itemId);
                            if (index > -1) {
                                this.selectedItems.splice(index, 1);
                            } else {
                                this.selectedItems.push(itemId);
                            }
                        },
                        
                        selectAll() {
                            this.selectedItems = this.items.map(item => item.id);
                        },
                        
                        deselectAll() {
                            this.selectedItems = [];
                        },
                        
                        async addSelectedItems() {
                            if (this.selectedItems.length === 0) return;
                            
                            console.log('Adding items:', {
                                selectedItems: this.selectedItems,
                                selectedType: this.selectedType
                            });
                            
                            try {
                                // Add each item individually
                                for (const itemId of this.selectedItems) {
                                    const formData = new FormData();
                                    formData.append('collectable_type', this.selectedType.model);
                                    formData.append('collectable_id', itemId);
                                    
                                    console.log('Sending request for item:', itemId, {
                                        collectable_type: this.selectedType.model,
                                        collectable_id: itemId
                                    });
                                    
                                    const response = await fetch(`/admin/collections/{{ $collection->id }}/items`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'Accept': 'application/json'
                                        },
                                        body: formData
                                    });
                                    
                                    const responseText = await response.text();
                                    console.log('Response:', response.status, response.ok, responseText);
                                    
                                    if (!response.ok) {
                                        throw new Error(`HTTP ${response.status}: ${responseText}`);
                                    }
                                }
                                
                                console.log('All items added, reloading...');
                                window.location.reload();
                            } catch (error) {
                                console.error('Failed to add items:', error);
                                alert('Failed to add items. Please try again.');
                            }
                        },
                        
                        resetModal() {
                            this.selectedType = null;
                            this.items = [];
                            this.selectedItems = [];
                        }
                    }">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-900">Items</h3>
                            <button type="button" @click="showBrowseModal = true" class="px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-50">
                                Browse
                            </button>
                        </div>

                        @if($collection->collectables->count() > 0)
                            <div class="space-y-2">
                                @foreach($collection->collectables as $collectable)
                                    <div class="flex items-center gap-3 p-3 border border-gray-200 rounded">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $collectable->collectable->title ?? $collectable->collectable->name ?? 'Item #' . $collectable->collectable_id }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ class_basename($collectable->collectable_type) }}</div>
                                        </div>
                                        <button 
                                            @click="async () => {
                                                if (!confirm('Remove this item from the collection?')) return;
                                                
                                                try {
                                                    const response = await fetch('/admin/collections/{{ $collection->id }}/items/{{ $collectable->id }}', {
                                                        method: 'DELETE',
                                                        headers: {
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                            'Accept': 'application/json'
                                                        }
                                                    });
                                                    
                                                    if (response.ok) {
                                                        window.location.reload();
                                                    } else {
                                                        alert('Failed to remove item');
                                                    }
                                                } catch (error) {
                                                    console.error('Error:', error);
                                                    alert('Failed to remove item');
                                                }
                                            }"
                                            class="text-red-600 hover:text-red-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-sm">There are no items in this collection.</p>
                                <p class="text-sm">Search or browse to add items.</p>
                            </div>
                        @endif

                        <!-- Browse Items Modal -->
                        <div x-show="showBrowseModal" 
                             x-cloak
                             class="fixed inset-0 z-50 overflow-y-auto" 
                             @keydown.escape.window="showBrowseModal = false; resetModal()">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="fixed inset-0 bg-black opacity-50" @click="showBrowseModal = false; resetModal()"></div>
                                
                                <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] flex flex-col">
                                    <!-- Modal Header -->
                                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <button x-show="selectedType" @click="resetModal()" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                </svg>
                                            </button>
                                            <h3 class="text-lg font-medium text-gray-900" x-text="selectedType ? selectedType.label : 'Browse Items'"></h3>
                                        </div>
                                        <button @click="showBrowseModal = false; resetModal()" class="text-gray-400 hover:text-gray-500">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="flex-1 overflow-y-auto">
                                        <!-- Type Selection -->
                                        <div x-show="!selectedType" class="p-6">
                                            @if(empty($collectableTypes))
                                                <div class="text-center py-12">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                    </svg>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No item types available</h3>
                                                    <p class="text-sm text-gray-500 mb-4">
                                                        Collections can contain products, blog posts, or other content types.<br>
                                                        Register item types using the Context Registry to enable browsing.
                                                    </p>
                                                </div>
                                            @else
                                                <div class="space-y-3">
                                                    <p class="text-sm text-gray-600 mb-4">Select the type of items you want to add to this collection:</p>
                                                    @foreach($collectableTypes as $type)
                                                        <div @click="selectType({{ json_encode($type) }})" class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 hover:bg-gray-50 cursor-pointer transition">
                                                            <div class="flex items-center justify-between">
                                                                <div>
                                                                    <h4 class="text-sm font-medium text-gray-900">{{ $type['label'] }}</h4>
                                                                    <p class="text-xs text-gray-500 mt-1">{{ $type['model'] }}</p>
                                                                </div>
                                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Items Table -->
                                        <div x-show="selectedType" class="p-6">
                                            <div x-show="loading" class="text-center py-12">
                                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                                                <p class="mt-2 text-sm text-gray-600">Loading items...</p>
                                            </div>

                                            <div x-show="!loading && items.length === 0" class="text-center py-12">
                                                <p class="text-sm text-gray-600">No items found.</p>
                                            </div>

                                            <div x-show="!loading && items.length > 0">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="text-sm text-gray-600">
                                                        <span x-text="selectedItems.length"></span> of <span x-text="items.length"></span> selected
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <button @click="selectAll()" class="text-sm text-blue-600 hover:text-blue-800">Select all</button>
                                                        <span class="text-gray-300">|</span>
                                                        <button @click="deselectAll()" class="text-sm text-blue-600 hover:text-blue-800">Deselect all</button>
                                                    </div>
                                                </div>

                                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th class="w-12 px-4 py-3"></th>
                                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            <template x-for="item in items" :key="item.id">
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="px-4 py-3">
                                                                        <input type="checkbox" 
                                                                               :checked="selectedItems.includes(item.id)"
                                                                               @change="toggleItem(item.id)"
                                                                               class="rounded">
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm text-gray-900" x-text="item.name"></td>
                                                                    <td class="px-4 py-3 text-sm text-gray-600" x-text="item.sku"></td>
                                                                    <td class="px-4 py-3 text-sm text-gray-600" x-text="item.price ? '$' + parseFloat(item.price).toFixed(2) : '-'"></td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="px-6 py-4 border-t border-gray-200 flex justify-between">
                                        <button @click="showBrowseModal = false; resetModal()" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">
                                            Cancel
                                        </button>
                                        <button x-show="selectedType && selectedItems.length > 0" 
                                                @click="addSelectedItems()" 
                                                class="px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-700">
                                            Add <span x-text="selectedItems.length"></span> item(s)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Search engine listing -->
                <div class="bg-white rounded-lg shadow-sm p-6" x-data="{
                    metaTitle: '{{ old('meta_title', $collection->meta_title) }}',
                    metaDescription: '{{ old('meta_description', $collection->meta_description) }}',
                    get previewTitle() {
                        return this.metaTitle || '{{ $collection->title }}';
                    },
                    get previewDescription() {
                        return this.metaDescription || '{{ Str::limit(strip_tags($collection->description ?? ''), 160) }}';
                    }
                }">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Search engine listing</h3>
                    <p class="text-sm text-gray-600 mb-4">Add a title and description to see how this collection might appear in a search engine listing</p>
                    
                    <!-- SEO Preview -->
                    <div class="p-4 bg-gray-50 rounded border border-gray-200 mb-4">
                        <div class="text-blue-600 text-sm hover:underline" x-text="previewTitle"></div>
                        <div class="text-green-700 text-xs">{{ url('/collections/' . $collection->slug) }}</div>
                        <div class="text-gray-600 text-xs mt-1" x-text="previewDescription"></div>
                    </div>

                    <!-- Meta Title -->
                    <div class="mb-4">
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                            Meta title
                        </label>
                        <input 
                            type="text" 
                            name="meta_title" 
                            id="meta_title" 
                            x-model="metaTitle"
                            value="{{ old('meta_title', $collection->meta_title) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200 text-sm"
                            placeholder="{{ $collection->title }}"
                            maxlength="60">
                        <p class="mt-1 text-xs text-gray-500">
                            <span x-text="metaTitle.length"></span>/60 characters recommended
                        </p>
                        @error('meta_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meta Description -->
                    <div class="mb-4">
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Meta description
                        </label>
                        <textarea 
                            name="meta_description" 
                            id="meta_description" 
                            x-model="metaDescription"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200 text-sm"
                            placeholder="{{ Str::limit(strip_tags($collection->description ?? ''), 160) }}"
                            maxlength="160">{{ old('meta_description', $collection->meta_description) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            <span x-text="metaDescription.length"></span>/160 characters recommended
                        </p>
                        @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meta Keywords -->
                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">
                            Meta keywords
                        </label>
                        <input 
                            type="text" 
                            name="meta_keywords" 
                            id="meta_keywords" 
                            value="{{ old('meta_keywords', $collection->meta_keywords) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200 text-sm"
                            placeholder="furniture, home decor, modern">
                        <p class="mt-1 text-xs text-gray-500">Comma-separated keywords (optional)</p>
                        @error('meta_keywords')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tags -->
                <x-tags-input :model="$collection" name="tags" label="Tags" />
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publishing -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Publishing</h3>
                    
                    @if($collection->is_published)
                        <div class="mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Published
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Published {{ $collection->published_at->diffForHumans() }}</p>
                        <button 
                            type="button"
                            @click="unpublishCollection()"
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded hover:bg-gray-50">
                            Unpublish
                        </button>
                    @else
                        <div class="mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Draft
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">This collection is not published yet</p>
                        <button 
                            type="button"
                            @click="publishCollection()"
                            class="w-full px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-700">
                            Publish
                        </button>
                    @endif
                </div>

                <!-- Image -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Image</h3>
                    <div x-show="imageUrl" class="mb-3">
                        <img :src="imageUrl" alt="{{ $collection->title }}" class="w-full rounded mb-2">
                        <button type="button" @click="removeImage()" class="text-sm text-red-600 hover:text-red-800">Remove image</button>
                    </div>
                    <div x-show="!imageUrl" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-gray-400" @click="openMediaLibrary()">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Add image</p>
                        <p class="text-xs text-gray-500">Click to select from media library</p>
                    </div>
                    <input type="hidden" name="image" id="image" x-model="imageUrl" value="{{ old('image', $collection->image) }}">
                </div>

                <!-- Theme template -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Theme template</h3>
                    <select name="page_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <option value="">Default collection</option>
                        @foreach($pages as $page)
                            <option value="{{ $page->id }}" {{ old('page_id', $collection->page_id) == $page->id ? 'selected' : '' }}>
                                {{ $page->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500">Choose a custom page template for this collection</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex items-center justify-between">
            <button 
                type="button"
                @click="deleteCollection()"
                class="px-4 py-2 text-sm text-red-600 hover:text-red-800">
                Delete collection
            </button>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.collections.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-700">
                    Save changes
                </button>
            </div>
        </div>
    </form>
    
    <!-- Media Library Modal Portal -->
    <div x-show="showMediaLibrary" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         @keydown.escape.window="showMediaLibrary = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="showMediaLibrary = false"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Select Image</h3>
                    <button @click="showMediaLibrary = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 140px);">
                    <div id="media-library-mount"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function collectionEditor() {
    return {
        imageUrl: '{{ old('image', $collection->image) }}',
        showMediaLibrary: false,
        
        init() {
            // Listen for media selection events
            window.addEventListener('media-selected', (event) => {
                this.selectImage(event.detail.url);
            });
        },
        
        openMediaLibrary() {
            this.showMediaLibrary = true;
            this.$nextTick(() => {
                this.mountMediaLibrary();
            });
        },
        
        async mountMediaLibrary() {
            const mountPoint = document.getElementById('media-library-mount');
            if (!mountPoint) return;
            
            // Load media from API
            try {
                const response = await fetch('/admin/api/media');
                const data = await response.json();
                const mediaItems = data.media || [];
                
                // Render media grid
                this.renderMediaGrid(mountPoint, mediaItems);
            } catch (error) {
                console.error('Failed to load media:', error);
                mountPoint.innerHTML = `
                    <div class="text-center py-12">
                        <p class="text-red-600 mb-4">Failed to load media library</p>
                        <button onclick="location.reload()" class="text-blue-600 hover:text-blue-800">
                            Retry
                        </button>
                    </div>
                `;
            }
        },
        
        renderMediaGrid(mountPoint, mediaItems) {
            if (mediaItems.length === 0) {
                mountPoint.innerHTML = `
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">No media found</p>
                        <p class="text-sm text-gray-500 mb-4">Upload your first image to get started</p>
                        <a href="{{ route('admin.media.index') }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            Open Media Manager
                        </a>
                    </div>
                `;
                return;
            }
            
            const gridHtml = mediaItems.map(media => `
                <div class="relative group cursor-pointer" onclick="window.dispatchEvent(new CustomEvent('media-selected', { detail: { url: '${media.url}' } }))">
                    <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition-all">
                        <img src="${media.url}" alt="${media.alt_text || media.filename}" class="w-full h-full object-cover">
                    </div>
                    <p class="mt-1 text-xs text-gray-600 truncate">${media.filename}</p>
                </div>
            `).join('');
            
            mountPoint.innerHTML = `
                <div class="mb-4">
                    <input 
                        type="text" 
                        placeholder="Search media..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        oninput="this.closest('[id=media-library-mount]').querySelectorAll('[data-filename]').forEach(el => {
                            const filename = el.getAttribute('data-filename').toLowerCase();
                            el.style.display = filename.includes(this.value.toLowerCase()) ? 'block' : 'none';
                        })"
                    />
                </div>
                <div class="grid grid-cols-4 gap-4 max-h-96 overflow-y-auto">
                    ${mediaItems.map(media => `
                        <div data-filename="${media.filename}" class="relative group cursor-pointer" onclick="window.dispatchEvent(new CustomEvent('media-selected', { detail: { url: '${media.url}' } }))">
                            <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition-all">
                                <img src="${media.url}" alt="${media.alt_text || media.filename}" class="w-full h-full object-cover">
                            </div>
                            <p class="mt-1 text-xs text-gray-600 truncate">${media.filename}</p>
                        </div>
                    `).join('')}
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                    <a href="{{ route('admin.media.index') }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">
                        Manage all media →
                    </a>
                </div>
            `;
        },
        
        selectImage(url) {
            this.imageUrl = url;
            this.showMediaLibrary = false;
            // Clear the mount point
            const mountPoint = document.getElementById('media-library-mount');
            if (mountPoint) {
                mountPoint.innerHTML = '';
            }
        },
        
        removeImage() {
            this.imageUrl = '';
        },
        
        async publishCollection() {
            if (!confirm('Publish this collection?')) return;
            
            try {
                const response = await fetch('{{ route('admin.collections.publish', $collection) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to publish collection');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to publish collection');
            }
        },
        
        async unpublishCollection() {
            if (!confirm('Unpublish this collection?')) return;
            
            try {
                const response = await fetch('{{ route('admin.collections.unpublish', $collection) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to unpublish collection');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to unpublish collection');
            }
        },
        
        async deleteCollection() {
            if (!confirm('Are you sure you want to delete this collection? This action cannot be undone.')) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch('{{ route('admin.collections.destroy', $collection) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                if (response.ok) {
                    window.location.href = '{{ route('admin.collections.index') }}';
                } else {
                    alert('Failed to delete collection');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to delete collection');
            }
        }
    };
}
</script>
@endsection
