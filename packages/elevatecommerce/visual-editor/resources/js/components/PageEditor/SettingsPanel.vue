<template>
  <div class="w-80 bg-white border-l border-gray-200 flex flex-col text-sm">
    <!-- Header -->
    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-lg">{{ sectionMeta?.icon }}</span>
        <h3 class="font-medium text-sm">{{ sectionMeta?.label }}</h3>
      </div>
      <button @click="$emit('close')" class="p-1 hover:bg-gray-100 rounded">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Settings Form -->
    <div class="flex-1 overflow-y-auto p-4 space-y-3">
      <div v-for="(field, key) in schema" :key="key">
        <!-- Text Input -->
        <div v-if="field.type === 'text'">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
          </label>
          <input 
            v-model="localSettings[key]"
            type="text"
            :placeholder="field.default"
            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
        </div>

        <!-- Textarea -->
        <div v-else-if="field.type === 'textarea'">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ field.label }}
          </label>
          <textarea 
            v-model="localSettings[key]"
            :placeholder="field.default"
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          ></textarea>
        </div>

        <!-- Number Input -->
        <div v-else-if="field.type === 'number'">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ field.label }}
          </label>
          <input 
            v-model.number="localSettings[key]"
            type="number"
            :min="field.min"
            :max="field.max"
            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
        </div>

        <!-- Boolean/Checkbox -->
        <div v-else-if="field.type === 'boolean'">
          <label class="flex items-center gap-2 cursor-pointer">
            <input 
              v-model="localSettings[key]"
              type="checkbox"
              class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            >
            <span class="text-sm font-medium text-gray-700">{{ field.label }}</span>
          </label>
        </div>

        <!-- Select -->
        <div v-else-if="field.type === 'select'">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ field.label }}
          </label>
          <select 
            v-model="localSettings[key]"
            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option v-for="(label, value) in field.options" :key="value" :value="value">
              {{ label }}
            </option>
          </select>
        </div>

        <!-- Color Picker -->
        <div v-else-if="field.type === 'color'">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ field.label }}
          </label>
          <div class="flex gap-2">
            <input 
              v-model="localSettings[key]"
              type="color"
              class="w-12 h-10 border border-gray-300 rounded cursor-pointer"
            >
            <input 
              v-model="localSettings[key]"
              type="text"
              class="flex-1 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
          </div>
        </div>

        <!-- URL Input -->
        <div v-else-if="field.type === 'url'">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ field.label }}
          </label>
          <input 
            v-model="localSettings[key]"
            type="url"
            :placeholder="field.default"
            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
        </div>

        <!-- Image Upload -->
        <div v-else-if="field.type === 'image'">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ field.label }}
          </label>
          <media-library
            v-model="localSettings[key]"
            :title="field.label"
            width="100%"
            height="200px"
            :alt="field.label"
          />
        </div>

        <!-- Range Slider -->
        <div v-else-if="field.type === 'range'">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ field.label }}: {{ localSettings[key] }}
          </label>
          <input 
            v-model.number="localSettings[key]"
            type="range"
            :min="field.min || 0"
            :max="field.max || 100"
            class="w-full"
          >
        </div>

        <!-- Radio Buttons -->
        <div v-else-if="field.type === 'radio'">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ field.label }}
          </label>
          <div class="space-y-2">
            <label 
              v-for="(label, value) in field.options" 
              :key="value"
              class="flex items-center gap-2 cursor-pointer"
            >
              <input 
                v-model="localSettings[key]"
                type="radio"
                :value="value"
                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
              >
              <span class="text-sm">{{ label }}</span>
            </label>
          </div>
        </div>

        <!-- Switch/Toggle -->
        <div v-else-if="field.type === 'switch'">
          <label class="flex items-center justify-between cursor-pointer">
            <span class="text-sm font-medium text-gray-700">{{ field.label }}</span>
            <div class="relative">
              <input 
                v-model="localSettings[key]"
                type="checkbox"
                class="sr-only peer"
              >
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </div>
          </label>
        </div>

        <!-- Repeater -->
        <div v-else-if="field.type === 'repeater'">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ field.label }}
          </label>
          <div class="space-y-3">
            <div 
              v-for="(item, index) in (localSettings[key] || [])" 
              :key="index"
              class="border border-gray-200 rounded p-3"
            >
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500">Item {{ index + 1 }}</span>
                <button 
                  @click="removeRepeaterItem(key, index)"
                  type="button"
                  class="text-red-600 hover:text-red-800 text-xs"
                >
                  Remove
                </button>
              </div>
              <div class="space-y-2">
                <div v-for="(subField, subKey) in field.fields" :key="subKey">
                  <label class="block text-xs text-gray-600 mb-1">{{ subField.label }}</label>
                  <input 
                    v-if="subField.type === 'text' || subField.type === 'url'"
                    v-model="item[subKey]"
                    :type="subField.type === 'url' ? 'url' : 'text'"
                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded"
                  >
                  <textarea 
                    v-else-if="subField.type === 'textarea'"
                    v-model="item[subKey]"
                    rows="2"
                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded"
                  ></textarea>
                </div>
              </div>
            </div>
            <button 
              @click="addRepeaterItem(key, field)"
              type="button"
              class="w-full px-3 py-2 text-sm border-2 border-dashed border-gray-300 rounded hover:border-blue-500 hover:text-blue-600"
            >
              + Add {{ field.label }}
            </button>
          </div>
        </div>
      </div>

      <div v-if="Object.keys(schema).length === 0" class="text-center text-gray-500 py-8">
        No settings available for this section
      </div>
    </div>

    <!-- Footer -->
    <div class="p-4 border-t border-gray-200">
      <button 
        @click="applySettings"
        class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Apply Changes
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePageEditorStore } from '../../stores/pageEditor'
import { storeToRefs } from 'pinia'
import MediaLibrary from '../MediaLibrary/MediaLibrary.vue'

const props = defineProps({
  section: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update', 'close'])

const store = usePageEditorStore()
const { availableSections } = storeToRefs(store)

const localSettings = ref({ ...props.section.settings })

const sectionMeta = computed(() => {
  return availableSections.value.find(s => s.key === props.section.key)
})

const schema = computed(() => {
  return sectionMeta.value?.schema || {}
})

// Watch for section changes
watch(() => props.section, (newSection) => {
  localSettings.value = { ...newSection.settings }
}, { deep: true })

function addRepeaterItem(key, field) {
  if (!localSettings.value[key]) {
    localSettings.value[key] = []
  }
  
  // Create new item with default values from field schema
  const newItem = {}
  Object.keys(field.fields || {}).forEach(subKey => {
    newItem[subKey] = field.fields[subKey].default || ''
  })
  
  localSettings.value[key].push(newItem)
}

function removeRepeaterItem(key, index) {
  if (localSettings.value[key]) {
    localSettings.value[key].splice(index, 1)
  }
}

function applySettings() {
  emit('update', localSettings.value)
}
</script>
