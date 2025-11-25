@extends('visual-editor::admin.layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    @if(isset($components) && count($components) > 0)
        {{-- Full Width Components --}}
        @php
            $fullWidth = collect($components)->filter(fn($c) => ($c['width'] ?? 'full') === 'full');
        @endphp
        @foreach($fullWidth as $key => $component)
            <div class="mb-6">
                {!! app('visual-editor.dashboard')->render($key) !!}
            </div>
        @endforeach

        {{-- Half Width Components --}}
        @php
            $halfWidth = collect($components)->filter(fn($c) => ($c['width'] ?? 'full') === 'half');
        @endphp
        @if($halfWidth->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @foreach($halfWidth as $key => $component)
                    <div>
                        {!! app('visual-editor.dashboard')->render($key) !!}
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Third Width Components --}}
        @php
            $thirdWidth = collect($components)->filter(fn($c) => ($c['width'] ?? 'full') === 'third');
        @endphp
        @if($thirdWidth->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                @foreach($thirdWidth as $key => $component)
                    <div>
                        {!! app('visual-editor.dashboard')->render($key) !!}
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Quarter Width Components --}}
        @php
            $quarterWidth = collect($components)->filter(fn($c) => ($c['width'] ?? 'full') === 'quarter');
        @endphp
        @if($quarterWidth->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                @foreach($quarterWidth as $key => $component)
                    <div>
                        {!! app('visual-editor.dashboard')->render($key) !!}
                    </div>
                @endforeach
            </div>
        @endif
    @else
        {{-- Default empty state --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Welcome to Visual Editor</h3>
            <p class="text-gray-600 text-sm">
                This is your admin dashboard. Use the navigation on the left to manage your content.
                Other packages can register dashboard components using the Dashboard registry.
            </p>
        </div>
    @endif
@endsection
