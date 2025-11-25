@extends('visual-editor::admin.layouts.dashboard')

@section('title', 'Inquiry Details')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.inquiries.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to inquiries
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="col-span-2 space-y-6">
            <!-- Inquiry Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">
                            {{ $inquiry->subject ?: 'Inquiry #' . $inquiry->id }}
                        </h1>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 text-sm rounded-full bg-{{ $inquiry->status_color }}-100 text-{{ $inquiry->status_color }}-800">
                                {{ ucfirst($inquiry->status) }}
                            </span>
                            <span class="px-3 py-1 text-sm rounded-full bg-{{ $inquiry->priority_color }}-100 text-{{ $inquiry->priority_color }}-800">
                                {{ ucfirst($inquiry->priority) }} Priority
                            </span>
                            <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($inquiry->type) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Contact Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @if($inquiry->name)
                            <div>
                                <p class="text-xs text-gray-500">Name</p>
                                <p class="text-sm font-medium text-gray-900">{{ $inquiry->name }}</p>
                            </div>
                        @endif

                        @if($inquiry->email)
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <a href="mailto:{{ $inquiry->email }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                    {{ $inquiry->email }}
                                </a>
                            </div>
                        @endif

                        @if($inquiry->phone)
                            <div>
                                <p class="text-xs text-gray-500">Phone</p>
                                <a href="tel:{{ $inquiry->phone }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                    {{ $inquiry->phone }}
                                </a>
                            </div>
                        @endif

                        @if($inquiry->company)
                            <div>
                                <p class="text-xs text-gray-500">Company</p>
                                <p class="text-sm font-medium text-gray-900">{{ $inquiry->company }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Message -->
                @if($inquiry->message)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Message</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $inquiry->message }}</p>
                        </div>
                    </div>
                @endif

                <!-- Custom Fields -->
                @if($inquiry->custom_fields && count($inquiry->custom_fields) > 0)
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Additional Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($inquiry->custom_fields as $key => $value)
                                <div>
                                    <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $value }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Metadata -->
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Metadata</h3>
                    <div class="grid grid-cols-2 gap-4 text-xs text-gray-600">
                        <div>
                            <p class="text-gray-500">Submitted</p>
                            <p>{{ $inquiry->formatted_date }}</p>
                        </div>
                        @if($inquiry->read_at)
                            <div>
                                <p class="text-gray-500">Read At</p>
                                <p>{{ $inquiry->read_at->format('M d, Y g:i A') }}</p>
                            </div>
                        @endif
                        @if($inquiry->replied_at)
                            <div>
                                <p class="text-gray-500">Replied At</p>
                                <p>{{ $inquiry->replied_at->format('M d, Y g:i A') }}</p>
                            </div>
                        @endif
                        @if($inquiry->source)
                            <div>
                                <p class="text-gray-500">Source</p>
                                <p>{{ $inquiry->source }}</p>
                            </div>
                        @endif
                        @if($inquiry->ip_address)
                            <div>
                                <p class="text-gray-500">IP Address</p>
                                <p>{{ $inquiry->ip_address }}</p>
                            </div>
                        @endif
                        @if($inquiry->referrer)
                            <div class="col-span-2">
                                <p class="text-gray-500">Referrer</p>
                                <p class="truncate">{{ $inquiry->referrer }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Update Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Update Inquiry</h3>
                <form method="POST" action="{{ route('admin.inquiries.update', $inquiry) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <option value="new" {{ $inquiry->status === 'new' ? 'selected' : '' }}>New</option>
                                <option value="read" {{ $inquiry->status === 'read' ? 'selected' : '' }}>Read</option>
                                <option value="replied" {{ $inquiry->status === 'replied' ? 'selected' : '' }}>Replied</option>
                                <option value="closed" {{ $inquiry->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <option value="low" {{ $inquiry->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ $inquiry->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ $inquiry->priority === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $inquiry->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                            <textarea 
                                name="admin_notes" 
                                rows="4" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-200"
                                placeholder="Internal notes..."
                            >{{ old('admin_notes', $inquiry->admin_notes) }}</textarea>
                        </div>

                        <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                            Update Inquiry
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @if($inquiry->email)
                        <a href="mailto:{{ $inquiry->email }}" class="block w-full px-4 py-2 text-sm text-center bg-blue-600 text-white rounded hover:bg-blue-700">
                            Reply via Email
                        </a>
                    @endif

                    <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" onsubmit="return confirm('Are you sure you want to delete this inquiry?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 text-sm text-red-600 border border-red-300 rounded hover:bg-red-50">
                            Delete Inquiry
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
