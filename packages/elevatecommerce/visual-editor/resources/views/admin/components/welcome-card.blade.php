<div class="bg-white rounded-lg shadow p-6">
    @if(isset($_component['title']))
        <h3 class="font-semibold text-gray-800 mb-4">{{ $_component['title'] }}</h3>
    @endif
    
    <p class="text-gray-600 text-sm">
        {{ $message ?? 'Welcome to your admin dashboard.' }}
    </p>
    
    @if(isset($actions) && count($actions) > 0)
        <div class="mt-4 flex gap-2">
            @foreach($actions as $action)
                <a href="{{ $action['url'] }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                    {{ $action['label'] }}
                </a>
            @endforeach
        </div>
    @endif
</div>
