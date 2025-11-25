<tr class="hover:bg-gray-50 cursor-pointer">
    @if(!empty($bulkActions))
        <td class="px-4 py-3" onclick="event.stopPropagation()">
            <input type="checkbox" 
                   class="rounded"
                   x-bind:checked="selectedItems.includes({{ $item->id }})"
                   @change="toggleItem({{ $item->id }})">
        </td>
    @endif
    
    {{ $slot }}
</tr>
