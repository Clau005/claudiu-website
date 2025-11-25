@extends('visual-editor::admin.layouts.dashboard')

@section('title', 'Pages')

@section('content')
<x-visual-editor::data-table
    title="Pages"
    :icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z\'/>'"
    :items="$pages"
    :columns="[
        ['key' => 'name', 'label' => 'Title', 'sortable' => true],
        ['key' => 'theme', 'label' => 'Theme', 'sortable' => false],
        ['key' => 'type', 'label' => 'Type', 'sortable' => true],
        ['key' => 'is_published', 'label' => 'Status', 'sortable' => true],
        ['key' => 'updated_at', 'label' => 'Updated', 'sortable' => true],
        ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-32 text-right'],
    ]"
    :filters="[
        ['label' => 'All themes', 'url' => route('admin.pages.index'), 'active' => !request('theme')],
    ]"
    :bulk-actions="[
        ['action' => 'delete', 'label' => 'Delete', 'class' => 'bg-red-600 text-white hover:bg-red-700'],
        ['action' => 'publish', 'label' => 'Publish'],
        ['action' => 'unpublish', 'label' => 'Unpublish'],
    ]"
    :bulk-action-url="route('admin.pages.bulk-action')"
    empty-title="No pages found"
    empty-description="Create your first page to get started"
    :empty-icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z\'/>'"
>
    <x-slot:actions>
        <div class="flex items-center gap-2">
            <select name="theme" onchange="window.location.href='{{ route('admin.pages.index') }}?theme=' + this.value" class="px-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-200">
                <option value="">All themes</option>
                @foreach($themes as $theme)
                    <option value="{{ $theme->slug }}" {{ $selectedTheme === $theme->slug ? 'selected' : '' }}>
                        {{ $theme->name }}
                        @if($theme->is_active)
                            (Active)
                        @endif
                    </option>
                @endforeach
            </select>
            
            <a href="{{ route('admin.pages.create') }}" class="px-4 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-700 flex items-center gap-2">
                + Add Page
            </a>
        </div>
    </x-slot:actions>

    @forelse($pages as $page)
        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.pages.edit', $page->id) }}'">
            <td class="px-4 py-3" onclick="event.stopPropagation()">
                <input type="checkbox" 
                       class="rounded"
                       x-bind:checked="selectedItems.includes({{ $page->id }})"
                       @change="toggleItem({{ $page->id }})">
            </td>
            <td class="px-4 py-3">
                <div class="font-medium text-gray-900">{{ $page->name }}</div>
                <div class="text-sm text-gray-500">/{{ $page->slug }}</div>
            </td>
            <td class="px-4 py-3">
                <div class="text-sm text-gray-900">{{ $page->theme->name }}</div>
                @if($page->theme->is_active)
                    <span class="text-xs text-green-600">Active theme</span>
                @endif
            </td>
            <td class="px-4 py-3">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    {{ $page->type === 'static' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $page->type === 'dynamic' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $page->type === 'template' ? 'bg-purple-100 text-purple-800' : '' }}">
                    {{ ucfirst($page->type) }}
                </span>
            </td>
            <td class="px-4 py-3">
                @if($page->is_published)
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
                {{ $page->updated_at->diffForHumans() }}
            </td>
            <td class="px-4 py-3" onclick="event.stopPropagation()">
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="tooltip p-1.5 hover:bg-gray-100 rounded text-gray-600 hover:text-gray-900" data-tooltip="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    
                    @if($page->is_published)
                        <form action="{{ route('admin.pages.unpublish', $page->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="tooltip p-1.5 hover:bg-gray-100 rounded text-yellow-600 hover:text-yellow-900" data-tooltip="Unpublish">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.pages.publish', $page->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="tooltip p-1.5 hover:bg-gray-100 rounded text-green-600 hover:text-green-900" data-tooltip="Publish">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page?');">
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
        <tr>
            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p class="font-medium mb-1">No pages found</p>
                <p class="text-sm">Create your first page to get started</p>
            </td>
        </tr>
    @endforelse
</x-visual-editor::data-table>
@endsection
