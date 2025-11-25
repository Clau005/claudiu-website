@props([
    'title' => '',
    'icon' => null,
    'items' => null,
    'columns' => [],
    'actions' => null,
    'bulkActions' => [],
    'bulkActionUrl' => null,
    'filters' => [],
    'searchable' => true,
    'sortable' => true,
    'emptyTitle' => 'No items found',
    'emptyDescription' => 'Get started by creating your first item',
    'emptyIcon' => null,
])

<div x-data="dataTable()" x-init="init()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            @if($icon)
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $icon !!}
                </svg>
            @endif
            <h1 class="text-xl font-semibold">{{ $title }}</h1>
        </div>

        @if($actions)
            <div class="flex items-center gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>

    <!-- Filters & Search -->
    <div class="flex items-center gap-4 mb-4">
        @if(!empty($filters))
            <div class="flex items-center gap-2 text-sm">
                @foreach($filters as $filter)
                    <a href="{{ $filter['url'] }}" 
                       class="px-3 py-1 rounded {{ $filter['active'] ? 'bg-gray-200' : 'hover:bg-gray-100' }}">
                        {{ $filter['label'] }}
                    </a>
                @endforeach
            </div>
        @endif

        @if($searchable)
            <div class="flex-1 max-w-md">
                <form method="GET" class="relative">
                    @foreach(request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Search..." 
                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-200"
                        x-on:input.debounce.300ms="$el.form.submit()"
                    >
                </form>
            </div>
        @endif

        <!-- View Options -->
        <div class="flex items-center gap-1 text-sm">
            @if($sortable)
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="tooltip p-2 hover:bg-gray-100 rounded" 
                            data-tooltip="Sort">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         x-cloak
                         x-transition
                         class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                        @foreach($columns as $column)
                            @if($column['sortable'] ?? false)
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $column['key'], 'direction' => request('sort') === $column['key'] && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                                   class="block px-4 py-2 text-sm hover:bg-gray-50 {{ request('sort') === $column['key'] ? 'bg-gray-50 font-medium' : '' }}">
                                    {{ $column['label'] }}
                                    @if(request('sort') === $column['key'])
                                        <span class="float-right">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Actions Bar (shown when items selected) -->
    @if(!empty($bulkActions))
        <div x-show="selectedItems.length > 0" 
             x-cloak
             x-transition
             class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-between">
            <span class="text-sm font-medium" x-text="`${selectedItems.length} item(s) selected`"></span>
            <div class="flex items-center gap-2">
                @foreach($bulkActions as $action)
                    <button @click="handleBulkAction('{{ $action['action'] }}')"
                            class="px-3 py-1.5 text-sm {{ $action['class'] ?? 'bg-gray-800 text-white hover:bg-gray-700' }} rounded">
                        {{ $action['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    @if(!empty($bulkActions))
                        <th class="w-10 px-4 py-3 text-left">
                            <input type="checkbox" 
                                   class="rounded"
                                   @change="toggleAll($event.target.checked)"
                                   :checked="selectedItems.length === {{ $items->count() }} && {{ $items->count() }} > 0">
                        </th>
                    @endif
                    @foreach($columns as $column)
                        <th class="px-4 py-3 text-left font-medium text-gray-700 {{ $column['class'] ?? '' }}">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($items && $items->hasPages())
        <div class="mt-4 flex items-center justify-between text-sm">
            <div class="text-gray-600">
                Showing {{ $items->firstItem() }}-{{ $items->lastItem() }} of {{ $items->total() }}
            </div>
            <div>
                {{ $items->links() }}
            </div>
        </div>
    @endif

    <!-- Hidden form for bulk actions -->
    @if($bulkActionUrl)
        <form id="bulkActionForm" action="{{ $bulkActionUrl }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="action" x-model="bulkAction">
            <input type="hidden" name="ids" x-model="selectedItems.join(',')">
        </form>
    @endif
</div>

<style>
    .tooltip {
        position: relative;
    }
    .tooltip::before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-4px);
        padding: 4px 8px;
        background: #1f2937;
        color: white;
        font-size: 12px;
        white-space: nowrap;
        border-radius: 4px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
        z-index: 10;
    }
    .tooltip::after {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 4px solid transparent;
        border-top-color: #1f2937;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
    }
    .tooltip:hover::before,
    .tooltip:hover::after {
        opacity: 1;
    }
</style>

<script>
function dataTable() {
    return {
        selectedItems: [],
        bulkAction: '',
        
        init() {
            // Initialize
        },
        
        toggleAll(checked) {
            if (checked) {
                this.selectedItems = @json($items->pluck('id')->toArray());
            } else {
                this.selectedItems = [];
            }
        },
        
        toggleItem(id) {
            const index = this.selectedItems.indexOf(id);
            if (index > -1) {
                this.selectedItems.splice(index, 1);
            } else {
                this.selectedItems.push(id);
            }
        },
        
        handleBulkAction(action) {
            if (this.selectedItems.length === 0) {
                alert('Please select at least one item');
                return;
            }
            
            if (confirm(`Are you sure you want to ${action} ${this.selectedItems.length} item(s)?`)) {
                this.bulkAction = action;
                this.$nextTick(() => {
                    document.getElementById('bulkActionForm').submit();
                });
            }
        }
    }
}
</script>
