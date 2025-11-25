@extends('visual-editor::admin.layouts.dashboard')

@section('title', 'Inquiries')

@section('content')
<x-visual-editor::data-table
    title="Inquiries"
    :icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z\'/>'"
    :items="$inquiries"
    :columns="[
        ['key' => 'name', 'label' => 'Contact', 'sortable' => false],
        ['key' => 'subject', 'label' => 'Subject', 'sortable' => false],
        ['key' => 'type', 'label' => 'Type', 'sortable' => true],
        ['key' => 'status', 'label' => 'Status', 'sortable' => true],
        ['key' => 'priority', 'label' => 'Priority', 'sortable' => true],
        ['key' => 'created_at', 'label' => 'Received', 'sortable' => true],
        ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-32 text-right'],
    ]"
    :bulk-actions="[
        ['action' => 'mark_read', 'label' => 'Mark as Read'],
        ['action' => 'mark_replied', 'label' => 'Mark as Replied'],
        ['action' => 'mark_closed', 'label' => 'Mark as Closed'],
        ['action' => 'delete', 'label' => 'Delete', 'class' => 'bg-red-600 text-white hover:bg-red-700'],
    ]"
    :bulk-action-url="route('admin.inquiries.bulk-action')"
    empty-title="No inquiries found"
    empty-description="Inquiries from your contact forms will appear here"
    :empty-icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z\'/>'"
>
    <x-slot:actions>
        <div class="flex items-center gap-2">
            <select name="status" onchange="window.location.href='{{ route('admin.inquiries.index') }}?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), status: this.value}).toString()" class="px-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-200">
                <option value="">All Status</option>
                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New ({{ $counts['new'] }})</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read ({{ $counts['read'] }})</option>
                <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied ({{ $counts['replied'] }})</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed ({{ $counts['closed'] }})</option>
            </select>
            
            <select name="type" onchange="window.location.href='{{ route('admin.inquiries.index') }}?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), type: this.value}).toString()" class="px-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-200">
                <option value="">All Types</option>
                <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>General</option>
                <option value="support" {{ request('type') === 'support' ? 'selected' : '' }}>Support</option>
                <option value="sales" {{ request('type') === 'sales' ? 'selected' : '' }}>Sales</option>
                <option value="partnership" {{ request('type') === 'partnership' ? 'selected' : '' }}>Partnership</option>
            </select>
        </div>
    </x-slot:actions>

    @forelse($inquiries as $inquiry)
        <tr class="hover:bg-gray-50 cursor-pointer {{ $inquiry->isNew() ? 'bg-blue-50' : '' }}" onclick="window.location='{{ route('admin.inquiries.show', $inquiry) }}'">
            <td class="px-4 py-3" onclick="event.stopPropagation()">
                <input type="checkbox" 
                       class="rounded"
                       x-bind:checked="selectedItems.includes({{ $inquiry->id }})"
                       @change="toggleItem({{ $inquiry->id }})">
            </td>
            <td class="px-4 py-3">
                <div class="font-medium text-gray-900">{{ $inquiry->name ?: 'Anonymous' }}</div>
                <div class="text-sm text-gray-500">
                    @if($inquiry->email)
                        {{ $inquiry->email }}
                    @elseif($inquiry->phone)
                        {{ $inquiry->phone }}
                    @endif
                </div>
                @if($inquiry->company)
                    <div class="text-xs text-gray-400">{{ $inquiry->company }}</div>
                @endif
            </td>
            <td class="px-4 py-3">
                <div class="font-medium text-gray-900">{{ $inquiry->subject ?: '—' }}</div>
                @if($inquiry->message)
                    <div class="text-sm text-gray-500 line-clamp-1">{{ Str::limit($inquiry->message, 60) }}</div>
                @endif
            </td>
            <td class="px-4 py-3">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize
                    {{ $inquiry->type === 'general' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $inquiry->type === 'support' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $inquiry->type === 'sales' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $inquiry->type === 'partnership' ? 'bg-purple-100 text-purple-800' : '' }}">
                    {{ $inquiry->type }}
                </span>
            </td>
            <td class="px-4 py-3">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize
                    {{ $inquiry->status === 'new' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $inquiry->status === 'read' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $inquiry->status === 'replied' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $inquiry->status === 'closed' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ $inquiry->status }}
                </span>
            </td>
            <td class="px-4 py-3">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize
                    {{ $inquiry->priority === 'low' ? 'bg-gray-100 text-gray-600' : '' }}
                    {{ $inquiry->priority === 'normal' ? 'bg-blue-100 text-blue-600' : '' }}
                    {{ $inquiry->priority === 'high' ? 'bg-orange-100 text-orange-600' : '' }}
                    {{ $inquiry->priority === 'urgent' ? 'bg-red-100 text-red-600' : '' }}">
                    {{ $inquiry->priority }}
                </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-600">
                {{ $inquiry->created_at->diffForHumans() }}
            </td>
            <td class="px-4 py-3" onclick="event.stopPropagation()">
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="tooltip p-1.5 hover:bg-gray-100 rounded text-gray-600 hover:text-gray-900" data-tooltip="View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    
                    <form action="{{ route('admin.inquiries.destroy', $inquiry) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
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
            <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <p class="font-medium mb-1">No inquiries found</p>
                <p class="text-sm">Inquiries from your contact forms will appear here</p>
            </td>
        </tr>
    @endforelse
</x-visual-editor::data-table>
@endsection
