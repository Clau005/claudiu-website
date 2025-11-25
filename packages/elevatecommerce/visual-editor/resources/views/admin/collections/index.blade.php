@extends('visual-editor::admin.layouts.dashboard')

@section('title', 'Collections')

@section('content')
<x-visual-editor::data-table
    title="Collections"
    :icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10\'/>'"
    :items="$collections"
    :columns="[
        ['key' => 'title', 'label' => 'Collection', 'sortable' => true],
        ['key' => 'type', 'label' => 'Type', 'sortable' => true],
        ['key' => 'page', 'label' => 'Template', 'sortable' => false],
        ['key' => 'is_published', 'label' => 'Status', 'sortable' => true],
        ['key' => 'updated_at', 'label' => 'Updated', 'sortable' => true],
        ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-32 text-right'],
    ]"
    :filters="[
        ['label' => 'All collections', 'url' => route('admin.collections.index'), 'active' => true],
    ]"
    :bulk-actions="[
        ['action' => 'delete', 'label' => 'Delete', 'class' => 'bg-red-600 text-white hover:bg-red-700'],
        ['action' => 'publish', 'label' => 'Publish'],
        ['action' => 'unpublish', 'label' => 'Unpublish'],
    ]"
    :bulk-action-url="route('admin.collections.bulk-action')"
    empty-title="No collections found"
    empty-description="Create your first collection to get started"
    :empty-icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10\'/>'"
>
    <x-slot:actions>
        <a href="{{ route('admin.collections.create') }}" class="px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-700 flex items-center gap-2">
            + Add collection
        </a>
    </x-slot:actions>

    @forelse($collections as $collection)
        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.collections.edit', $collection->id) }}'">
            <td class="px-4 py-3" onclick="event.stopPropagation()">
                <input type="checkbox" 
                       class="rounded"
                       x-bind:checked="selectedItems.includes({{ $collection->id }})"
                       @change="toggleItem({{ $collection->id }})">
            </td>
            <td class="px-4 py-3">
                <div class="flex items-center">
                    @if($collection->image)
                        <div class="shrink-0 h-10 w-10 mr-3">
                            <img class="h-10 w-10 rounded object-cover" src="{{ $collection->image }}" alt="{{ $collection->title }}">
                        </div>
                    @else
                        <div class="shrink-0 h-10 w-10 bg-gray-100 rounded flex items-center justify-center mr-3">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    @endif
                    <div>
                        <div class="font-medium text-gray-900">{{ $collection->title }}</div>
                        <div class="text-sm text-gray-500">/collections/{{ $collection->slug }}</div>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $collection->type === 'manual' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ ucfirst($collection->type) }}
                </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-600">
                {{ $collection->page ? $collection->page->name : 'Default' }}
            </td>
            <td class="px-4 py-3">
                @if($collection->is_published)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Published
                    </span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Draft
                    </span>
                @endif
            </td>
            <td class="px-4 py-3 text-sm text-gray-600">
                {{ $collection->updated_at->diffForHumans() }}
            </td>
            <td class="px-4 py-3" onclick="event.stopPropagation()">
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.collections.edit', $collection->id) }}" class="tooltip p-1.5 hover:bg-gray-100 rounded text-gray-600 hover:text-gray-900" data-tooltip="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    
                    @if($collection->is_published)
                        <form action="{{ route('admin.collections.unpublish', $collection->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="tooltip p-1.5 hover:bg-gray-100 rounded text-yellow-600 hover:text-yellow-900" data-tooltip="Unpublish">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.collections.publish', $collection->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="tooltip p-1.5 hover:bg-gray-100 rounded text-green-600 hover:text-green-900" data-tooltip="Publish">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('admin.collections.destroy', $collection->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this collection?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="tooltip p-1.5 hover:bg-gray-100 rounded text-red-600 hover:text-red-900" data-tooltip="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
    @endforelse
</x-visual-editor::data-table>
@endsection
