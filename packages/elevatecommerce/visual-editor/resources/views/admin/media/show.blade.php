@extends('visual-editor::admin.layouts.media')

@section('title', $media->original_filename)

@section('content')
<div class="h-screen flex flex-col bg-black">
    <!-- Header -->
    <div class="bg-black border-b border-gray-800 px-4 py-3 flex items-center justify-between text-white">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.media.index') }}" class="hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
            <h1 class="text-lg font-medium">{{ $media->original_filename }}</h1>
        </div>

        <div class="flex items-center gap-2">
            <!-- Replace Button -->
            <button 
                onclick="document.getElementById('replaceInput').click()"
                class="px-3 py-1.5 text-sm border border-gray-600 rounded hover:bg-gray-800 flex items-center gap-2"
            >
                Replace
            </button>
            
            <form id="replaceForm" action="{{ route('admin.media.replace', $media->id) }}" method="POST" enctype="multipart/form-data" class="hidden">
                @csrf
                @method('PUT')
                <input 
                    type="file" 
                    id="replaceInput" 
                    name="file"
                    onchange="document.getElementById('replaceForm').submit()"
                >
            </form>

            <!-- Copy Link Button -->
            <button 
                onclick="copyToClipboard('{{ $media->url }}')"
                class="px-3 py-1.5 text-sm border border-gray-600 rounded hover:bg-gray-800 flex items-center gap-2"
            >
                Copy link
            </button>

            <!-- Delete Button -->
            <form action="{{ route('admin.media.destroy', $media->id) }}" method="POST" onsubmit="return confirm('Delete this file?')">
                @csrf
                @method('DELETE')
                <button 
                    type="submit"
                    class="px-3 py-1.5 text-sm border border-gray-600 rounded hover:bg-gray-800 text-red-400"
                >
                    Delete
                </button>
            </form>
        </div>
    </div>
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

    <div class="flex-1 flex overflow-hidden">
        <!-- Preview Area -->
        <div class="flex-1 flex items-center justify-center p-8">
        
            @if($media->isImage())
                <img 
                    src="{{ $media->url }}" 
                    alt="{{ $media->alt_text }}"
                    class="max-w-full max-h-full object-contain rounded"
                >
            @elseif($media->isVideo())
                <video 
                    src="{{ $media->url }}" 
                    controls
                    class="max-w-full max-h-full rounded"
                ></video>
            @else
                <div class="text-center text-white">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gray-800 rounded-lg flex items-center justify-center text-2xl font-bold">
                        {{ strtoupper($media->extension) }}
                    </div>
                    <p class="text-lg mb-2">{{ $media->original_filename }}</p>
                    <a href="{{ $media->url }}" download class="text-blue-400 hover:text-blue-300">
                        Download file
                    </a>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="w-80 bg-gray-900 text-white p-6 overflow-y-auto border-l border-gray-800">
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="font-medium">Information</h2>
                </div>

                <form action="{{ route('admin.media.update', $media->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Name</label>
                            <input 
                                type="text" 
                                value="{{ $media->original_filename }}"
                                readonly
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Alt text</label>
                            <input 
                                type="text" 
                                name="alt_text"
                                value="{{ $media->alt_text }}"
                                placeholder="Add alt text..."
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded text-sm"
                            >
                        </div>

                        <button 
                            type="submit"
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>

            <div class="pt-6 border-t border-gray-800">
                <h3 class="font-medium mb-4">Details</h3>
                <dl class="space-y-3 text-sm">
                    @if($media->width && $media->height)
                        <div>
                            <dt class="text-gray-400">Dimensions</dt>
                            <dd>{{ $media->width }} × {{ $media->height }} px</dd>
                        </div>
                    @endif
                    
                    <div>
                        <dt class="text-gray-400">File size</dt>
                        <dd>{{ $media->formatted_size }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-gray-400">Type</dt>
                        <dd>{{ strtoupper($media->extension) }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-gray-400">Added</dt>
                        <dd>{{ $media->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="pt-6 border-t border-gray-800">
                <h3 class="font-medium mb-4">Used in</h3>
                <p class="text-sm text-gray-400">—</p>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Link copied to clipboard!');
    });
}
</script>
@endsection
