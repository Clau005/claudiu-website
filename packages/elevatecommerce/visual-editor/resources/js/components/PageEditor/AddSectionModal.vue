<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="$emit('close')">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[80vh] flex flex-col">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Add Section</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Search -->
      <div class="px-6 py-3 border-b border-gray-200">
        <input 
          v-model="searchQuery"
          type="text"
          placeholder="Search sections..."
          class="w-full px-3 py-2 text-sm border border-gray-300 rounded"
        >
      </div>

      <!-- Sections Grid -->
      <div class="flex-1 overflow-y-auto p-6">
        <div class="grid grid-cols-2 gap-4">
          <div 
            v-for="section in filteredSections" 
            :key="section.key"
            @click="$emit('add', section.key)"
            class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:shadow-md transition"
          >
            <div class="flex items-start gap-3">
              <span class="text-2xl">{{ section.icon }}</span>
              <div class="flex-1">
                <div class="font-medium text-sm mb-1">{{ section.label }}</div>
                <div class="text-xs text-gray-500">{{ section.category }}</div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="filteredSections.length === 0" class="text-center text-gray-500 py-12">
          No sections found
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  availableSections: {
    type: Array,
    required: true
  },
  group: {
    type: String,
    default: 'template' // 'header', 'template', or 'footer'
  }
})

defineEmits(['add', 'close'])

const searchQuery = ref('')

const filteredSections = computed(() => {
  let sections = props.availableSections
  
  // Filter by group
  if (props.group === 'header' || props.group === 'footer') {
    // For header/footer, only show layout category sections
    sections = sections.filter(s => s.category === 'layout')
  } else {
    // For template, exclude layout category sections
    sections = sections.filter(s => s.category !== 'layout')
  }
  
  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    sections = sections.filter(section => 
      section.label.toLowerCase().includes(query) ||
      section.category.toLowerCase().includes(query)
    )
  }
  
  return sections
})
</script>
