<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
        <div class="flex-1">
            <p class="text-gray-500 text-sm">{{ $label }}</p>
            <p class="text-lg font-bold text-gray-900">{{ $value }}</p>
            @if(isset($change))
                <p class="text-xs mt-1 {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $change >= 0 ? '↑' : '↓' }} {{ abs($change) }}% from last period
                </p>
            @endif
        </div>
        @if(isset($icon))
            <div class="bg-{{ $color ?? 'blue' }}-100 rounded-full p-3">
                <span class="text-2xl">{{ $icon }}</span>
            </div>
        @endif
    </div>
</div>
