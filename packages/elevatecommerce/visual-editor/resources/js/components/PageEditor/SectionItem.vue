<template>
  <div 
    @click="$emit('select')"
    :class="[
      'group relative bg-white border-2 rounded-lg p-4 cursor-pointer transition',
      isSelected ? 'border-blue-500 shadow-lg' : 'border-gray-200 hover:border-gray-300'
    ]"
  >
    <!-- Drag Handle -->
    <div class="drag-handle absolute left-2 top-2 opacity-0 group-hover:opacity-100 cursor-move">
      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
      </svg>
    </div>

    <!-- Section Content Preview -->
    <div class="ml-6">
      <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
          <span class="text-xl">{{ sectionMeta?.icon || '📦' }}</span>
          <span class="font-medium">{{ sectionMeta?.label || section.key }}</span>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100">
          <button 
            @click.stop="$emit('edit')"
            class="p-1 hover:bg-gray-100 rounded"
            title="Edit settings"
          >
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </button>
          
          <button 
            @click.stop="$emit('remove')"
            class="p-1 hover:bg-red-100 rounded"
            title="Remove section"
          >
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Settings Preview -->
      <div class="text-sm text-gray-600 space-y-1">
        <div v-for="(value, key) in displaySettings" :key="key" class="truncate">
          <span class="font-medium">{{ key }}:</span> {{ value }}
        </div>
      </div>
    </div>

    <!-- Selected Indicator -->
    <div v-if="isSelected" class="absolute inset-0 border-2 border-blue-500 rounded-lg pointer-events-none"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePageEditorStore } from '../../stores/pageEditor'
import { storeToRefs } from 'pinia'

const props = defineProps({
  section: {
    type: Object,
    required: true
  },
  index: {
    type: Number,
    required: true
  },
  isSelected: {
    type: Boolean,
    default: false
  }
})

defineEmits(['select', 'remove', 'edit'])

const store = usePageEditorStore()
const { availableSections } = storeToRefs(store)

const sectionMeta = computed(() => {
  return availableSections.value.find(s => s.key === props.section.key)
})

const displaySettings = computed(() => {
  const settings = props.section.settings || {}
  const display = {}
  
  // Show first 3 settings
  Object.keys(settings).slice(0, 3).forEach(key => {
    let value = settings[key]
    if (typeof value === 'string' && value.length > 50) {
      value = value.substring(0, 50) + '...'
    }
    display[key] = value
  })
  
  return display
})
</script>
