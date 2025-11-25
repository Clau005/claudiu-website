@extends('visual-editor::admin.layouts.dashboard')

@section('title', 'Files')

@section('content')
<x-visual-editor::data-table
    title="Files"
    :icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z\'/>'"
    :items="$media"
    :columns="[
        ['key' => 'filename', 'label' => 'File name', 'sortable' => true],
        ['key' => 'alt_text', 'label' => 'Alt text', 'sortable' => false],
        ['key' => 'created_at', 'label' => 'Date added', 'sortable' => true],
        ['key' => 'size', 'label' => 'Size', 'sortable' => true],
        ['key' => 'references', 'label' => 'References', 'sortable' => false],
        ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-32 text-right'],
    ]"
    :filters="[
        ['label' => 'All', 'url' => route('admin.media.index'), 'active' => !request('type')],
        ['label' => 'Images', 'url' => route('admin.media.index', ['type' => 'images']), 'active' => request('type') === 'images'],
        ['label' => 'Videos', 'url' => route('admin.media.index', ['type' => 'videos']), 'active' => request('type') === 'videos'],
    ]"
    :bulk-actions="[
        ['action' => 'delete', 'label' => 'Delete', 'class' => 'bg-red-600 text-white hover:bg-red-700'],
        ['action' => 'download', 'label' => 'Download'],
    ]"
    :bulk-action-url="route('admin.media.bulk-action')"
    empty-title="No files found"
    empty-description="Upload your first file to get started"
    :empty-icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z\'/>'"
>
    <x-slot:actions>
        <button 
            onclick="document.getElementById('fileInput').click()"
            class="px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-700 flex items-center gap-2"
        >
            Upload files
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <form id="uploadForm" action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
            <input 
                type="file" 
                id="fileInput" 
                name="files[]" 
                multiple
                onchange="handleFileUpload()"
                accept="image/*,video/*"
            >
        </form>
    </x-slot:actions>

    @scope('item')
    <td class="px-4 py-2">
        <div class="flex items-center gap-3">
            @if($item->isImage())
                <img src="{{ $item->thumbnail }}" alt="{{ $item->original_filename }}" class="w-10 h-10 object-cover rounded">
            @else
                <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center text-xs font-medium">
                    {{ strtoupper($item->extension) }}
                </div>
            @endif
            <div>
                <div class="font-medium">{{ $item->original_filename }}</div>
                <div class="text-xs text-gray-500">{{ strtoupper($item->extension) }}</div>
            </div>
        </div>
    </td>
    <td class="px-4 py-3 text-gray-600">
        {{ $item->alt_text ?: '—' }}
    </td>
    <td class="px-4 py-3 text-gray-600">
        {{ $item->created_at->format('M d') }}
    </td>
    <td class="px-4 py-3 text-gray-600">
        {{ $item->formatted_size }}
    </td>
    <td class="px-4 py-3 text-gray-600">
        —
    </td>
    <td class="px-4 py-3" onclick="event.stopPropagation()">
        <div class="flex items-center justify-end gap-2">
            <!-- Copy Link Button -->
            <button 
                onclick="copyToClipboard('{{ $item->url }}', this)"
                class="tooltip p-1.5 hover:bg-gray-100 rounded text-gray-600 hover:text-gray-900"
                data-tooltip="Copy link"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </button>

            <!-- Replace Button -->
            <button 
                onclick="openReplaceModal({{ $item->id }})"
                class="tooltip p-1.5 hover:bg-gray-100 rounded text-gray-600 hover:text-gray-900"
                data-tooltip="Replace file"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </button>
        </div>
    </td>
    @endscope
</x-visual-editor::data-table>

<script>
function handleFileUpload() {
    const fileInput = document.getElementById('fileInput');
    const files = fileInput.files;
    
    if (files.length === 0) return;
    
    // Show loading indicator
    const button = event.target.closest('form').previousElementSibling;
    button.disabled = true;
    button.innerHTML = `Uploading ${files.length} file(s)...`;
    
    // Submit the form
    document.getElementById('uploadForm').submit();
}

function copyToClipboard(url, button) {
    navigator.clipboard.writeText(url).then(() => {
        // Show success feedback
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
    }).catch(err => {
        alert('Failed to copy link');
    });
}

function openReplaceModal(mediaId) {
    // Create a temporary file input
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*,video/*';
    input.onchange = function(e) {
        if (e.target.files.length > 0) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/media/${mediaId}/replace`;
            form.enctype = 'multipart/form-data';
            
            // Add CSRF token
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            // Add method spoofing
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';
            form.appendChild(method);
            
            // Add file
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.name = 'file';
            fileInput.files = e.target.files;
            form.appendChild(fileInput);
            
            // Submit
            document.body.appendChild(form);
            form.submit();
        }
    };
    input.click();
}
</script>
@endsection
