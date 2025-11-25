@props([
    'model' => null,
    'name' => 'tags',
    'label' => 'Tags',
    'placeholder' => 'Add tags...',
    'help' => 'Press Enter or comma to add a tag',
])

@php
    $existingTags = $model?->tags ?? collect();
    $allTags = \ElevateCommerce\VisualEditor\Models\Tag::orderBy('name')->get();
@endphp

<div class="bg-white rounded-lg shadow-sm p-6" x-data="tagsInput({{ json_encode($existingTags->pluck('name')->toArray()) }})">
    <h3 class="text-sm font-medium text-gray-900 mb-4">{{ $label }}</h3>
    
    <!-- Tag Input -->
    <div class="mb-3">
        <div class="flex flex-wrap gap-2 p-3 border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-gray-200 min-h-[42px]">
            <!-- Display existing tags -->
            <template x-for="(tag, index) in tags" :key="tag">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                    <span x-text="tag"></span>
                    <button type="button" @click="removeTag(index)" class="hover:text-red-600">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </span>
            </template>
            
            <!-- Input for new tags -->
            <input 
                type="text" 
                x-model="currentInput"
                @keydown.enter.prevent="addTag()"
                @keydown.comma.prevent="addTag()"
                @keydown.backspace="handleBackspace()"
                placeholder="{{ $placeholder }}"
                class="flex-1 min-w-[120px] border-0 px-1 py-1 focus:ring-0 text-sm"
            >
        </div>
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    </div>

    <!-- Suggested Tags -->
    @if($allTags->count() > 0)
        <div class="mb-3">
            <p class="text-xs font-medium text-gray-700 mb-2">Suggested tags:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($allTags->take(10) as $tag)
                    <button 
                        type="button"
                        @click="addTagByName('{{ $tag->name }}')"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs border border-gray-300 hover:bg-gray-50"
                        :class="{ 'opacity-50 cursor-not-allowed': tags.includes('{{ $tag->name }}') }"
                        :disabled="tags.includes('{{ $tag->name }}')">
                        @if($tag->color)
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $tag->color }}"></span>
                        @endif
                        <span>{{ $tag->name }}</span>
                        <span class="text-gray-400">({{ $tag->usage_count }})</span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Hidden inputs for form submission -->
    <template x-for="(tag, index) in tags" :key="tag">
        <input type="hidden" :name="'{{ $name }}[]'" :value="tag">
    </template>
</div>

@push('scripts')
<script>
function tagsInput(initialTags = []) {
    return {
        tags: initialTags,
        currentInput: '',
        
        addTag() {
            const tag = this.currentInput.trim();
            
            if (tag && !this.tags.includes(tag)) {
                this.tags.push(tag);
                this.currentInput = '';
            }
        },
        
        addTagByName(name) {
            if (!this.tags.includes(name)) {
                this.tags.push(name);
            }
        },
        
        removeTag(index) {
            this.tags.splice(index, 1);
        },
        
        handleBackspace() {
            if (this.currentInput === '' && this.tags.length > 0) {
                this.tags.pop();
            }
        }
    }
}
</script>
@endpush
