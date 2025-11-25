<template>
  <div class="media-library-picker">
    <!-- Image Preview with Replace Button -->
    <div 
      class="relative group cursor-pointer rounded-lg overflow-hidden border-2 border-gray-200 hover:border-gray-400 transition-colors"
      :style="{ width: width, height: height }"
      @click="openModal"
    >
      <img 
        v-if="modelValue" 
        :src="modelValue" 
        :alt="alt"
        class="w-full h-full object-cover"
      />
      <div 
        v-else
        class="w-full h-full bg-gray-100 flex items-center justify-center"
      >
        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
      
      <!-- Replace Button Overlay -->
      <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-center justify-center">
        <button 
          class="px-4 py-2 bg-white text-gray-900 text-sm font-medium rounded opacity-0 group-hover:opacity-100 transition-opacity transform group-hover:scale-100 scale-95"
        >
          {{ modelValue ? 'Replace' : 'Add image' }}
        </button>
      </div>
    </div>

    <!-- Modal -->
    <teleport to="body">
      <div 
        v-if="isModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
        @click.self="closeModal"
      >
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
          <!-- Modal Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ title }}</h2>
            <button 
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Search and Filters -->
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
              <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search files"
                  class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                </svg>
                Sort
              </button>
              <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                View
              </button>
            </div>
          </div>

          <!-- Upload Area + Media Grid -->
          <div class="flex-1 overflow-y-auto px-6 py-4">
            <!-- Upload Area -->
            <div 
              class="border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6 text-center hover:border-gray-400 transition-colors"
              @dragover.prevent
              @drop.prevent="handleDrop"
            >
              <div class="flex items-center justify-center gap-4">
                <button 
                  @click="triggerFileInput"
                  class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-800"
                >
                  + Add files
                </button>
                <button 
                  class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50"
                >
                  🎨 Generate Image
                </button>
              </div>
              <p class="mt-4 text-sm text-gray-500">Drag and drop images</p>
              <input 
                ref="fileInput"
                type="file"
                accept="image/*"
                multiple
                class="hidden"
                @change="handleFileSelect"
              />
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-12">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
              <p class="mt-2 text-sm text-gray-600">Loading media...</p>
            </div>

            <!-- Media Grid -->
            <div v-else class="grid grid-cols-6 gap-4">
              <div 
                v-for="media in filteredMedia" 
                :key="media.id"
                class="relative group cursor-pointer"
                @click="selectMedia(media)"
              >
                <div 
                  :class="[
                    'aspect-square rounded-lg overflow-hidden border-2 transition-all',
                    selectedMediaId === media.id ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-400'
                  ]"
                >
                  <img 
                    :src="media.url" 
                    :alt="media.alt_text || media.filename"
                    class="w-full h-full object-cover"
                  />
                  
                  <!-- Checkbox -->
                  <div 
                    v-if="selectedMediaId === media.id"
                    class="absolute top-2 right-2 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center"
                  >
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                </div>
                <p class="mt-1 text-xs text-gray-600 truncate">{{ media.filename }}</p>
                <p class="text-xs text-gray-400">{{ media.extension?.toUpperCase() }}</p>
              </div>

              <!-- Empty State -->
              <div v-if="filteredMedia.length === 0" class="col-span-6 text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium text-gray-900">No media found</p>
                <p class="text-sm text-gray-500">Upload your first image to get started</p>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
            <button 
              @click="closeModal"
              class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900"
            >
              Cancel
            </button>
            <button 
              @click="confirmSelection"
              :disabled="!selectedMediaId"
              class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Done
            </button>
          </div>
        </div>
      </div>
    </teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  title: {
    type: String,
    default: 'Select Image'
  },
  alt: {
    type: String,
    default: ''
  },
  width: {
    type: String,
    default: '200px'
  },
  height: {
    type: String,
    default: '200px'
  }
})

const emit = defineEmits(['update:modelValue'])

const isModalOpen = ref(false)
const loading = ref(false)
const mediaItems = ref([])
const selectedMediaId = ref(null)
const searchQuery = ref('')
const fileInput = ref(null)

const filteredMedia = computed(() => {
  if (!searchQuery.value) return mediaItems.value
  
  const query = searchQuery.value.toLowerCase()
  return mediaItems.value.filter(media => 
    media.filename.toLowerCase().includes(query) ||
    media.alt_text?.toLowerCase().includes(query)
  )
})

async function openModal() {
  isModalOpen.value = true
  await loadMedia()
}

function closeModal() {
  isModalOpen.value = false
  selectedMediaId.value = null
  searchQuery.value = ''
}

async function loadMedia() {
  loading.value = true
  try {
    const response = await axios.get('/admin/api/media')
    mediaItems.value = response.data.media || []
  } catch (error) {
    console.error('Failed to load media:', error)
    alert('Failed to load media library')
  } finally {
    loading.value = false
  }
}

function selectMedia(media) {
  selectedMediaId.value = media.id
}

function confirmSelection() {
  const selected = mediaItems.value.find(m => m.id === selectedMediaId.value)
  if (selected) {
    emit('update:modelValue', selected.url)
    closeModal()
  }
}

function triggerFileInput() {
  fileInput.value?.click()
}

async function handleFileSelect(event) {
  const files = Array.from(event.target.files)
  await uploadFiles(files)
}

async function handleDrop(event) {
  const files = Array.from(event.dataTransfer.files).filter(file => 
    file.type.startsWith('image/')
  )
  await uploadFiles(files)
}

async function uploadFiles(files) {
  if (files.length === 0) return
  
  loading.value = true
  
  try {
    const formData = new FormData()
    
    // Append all files to the same FormData
    for (const file of files) {
      formData.append('files[]', file)
    }
    
    await axios.post('/admin/media/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    // Reload media after upload
    await loadMedia()
  } catch (error) {
    console.error('Failed to upload files:', error)
    alert('Failed to upload files')
  } finally {
    loading.value = false
  }
}
</script>
