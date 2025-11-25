<template>
  <div class="page-editor h-screen flex flex-col bg-gray-50">
    <!-- Compact Header -->
    <div class="bg-white border-b border-gray-200 px-4 py-2 flex items-center justify-between text-sm">
      <!-- Left: Back + Theme Name -->
      <div class="flex items-center gap-3 flex-1">
        <a href="/admin/themes" class="text-gray-600 hover:text-gray-900 p-1">
          ←
        </a>
        <span class="text-gray-500">{{ page?.theme?.name }}</span>
      </div>

      <!-- Center: Page Dropdown + Preview Modes -->
      <div class="flex items-center gap-3 flex-1 justify-center">
        <!-- Page Dropdown -->
        <div class="relative">
          <button 
            @click="showPageDropdown = !showPageDropdown"
            class="flex items-center gap-2 px-3 py-1.5 border border-gray-300 rounded hover:bg-gray-50"
          >
            <span class="font-medium">{{ page?.name || 'Loading...' }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          
          <!-- Mega Menu Dropdown -->
          <div v-if="showPageDropdown" class="absolute top-full left-0 mt-1 w-96 bg-white border border-gray-200 rounded shadow-lg z-50">
            <input 
              v-model="pageSearchQuery"
              type="text" 
              placeholder="Search pages..."
              class="w-full px-3 py-2 text-sm border-b border-gray-200"
            >
            <div class="max-h-96 overflow-y-auto p-2">
              <!-- No Context Pages (Top Level) -->
              <div v-if="groupedPages.noContext.length > 0" class="mb-3">
                <a 
                  v-for="themePage in groupedPages.noContext" 
                  :key="themePage.id"
                  :href="`/admin/pages/${themePage.id}/edit`"
                  class="block px-3 py-2 text-sm hover:bg-gray-50 rounded"
                >
                  {{ themePage.name }}
                </a>
              </div>

              <!-- Grouped by Context -->
              <div v-for="(pages, contextKey) in groupedPages.byContext" :key="contextKey" class="mb-3">
                <div class="px-3 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                  {{ contextKey }}
                </div>
                <a 
                  v-for="themePage in pages" 
                  :key="themePage.id"
                  :href="`/admin/pages/${themePage.id}/edit`"
                  class="block px-3 py-2 text-sm hover:bg-gray-50 rounded ml-2"
                >
                  {{ themePage.name }}
                </a>
              </div>

              <div v-if="filteredThemePages.length === 0" class="px-3 py-2 text-sm text-gray-500">
                No pages found
              </div>
            </div>
          </div>
        </div>

        <!-- Preview Mode Buttons -->
        <div class="flex items-center gap-1 border border-gray-300 rounded">
          <button 
            @click="previewMode = 'mobile'"
            :class="[
              'px-3 py-1 text-xs',
              previewMode === 'mobile' ? 'bg-gray-900 text-white' : 'hover:bg-gray-100'
            ]"
          >
            📱
          </button>
          <button 
            @click="previewMode = 'tablet'"
            :class="[
              'px-3 py-1 text-xs',
              previewMode === 'tablet' ? 'bg-gray-900 text-white' : 'hover:bg-gray-100'
            ]"
          >
            📱
          </button>
          <button 
            @click="previewMode = 'desktop'"
            :class="[
              'px-3 py-1 text-xs',
              previewMode === 'desktop' ? 'bg-gray-900 text-white' : 'hover:bg-gray-100'
            ]"
          >
            🖥️
          </button>
        </div>
      </div>

      <!-- Right: Actions -->
      <div class="flex items-center gap-2 flex-1 justify-end">
        <span v-if="hasChanges" class="text-xs text-orange-600">
          Unsaved
        </span>
        
        <button 
          @click="handleSave"
          :disabled="saving || !hasChanges"
          class="px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50"
        >
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
        
        <button 
          @click="handlePublish"
          :disabled="saving"
          class="px-3 py-1.5 text-sm bg-black text-white rounded hover:bg-gray-800 disabled:opacity-50"
        >
          Publish
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
      <!-- Left Sidebar - Sections Tree -->
      <div class="w-64 bg-white border-r border-gray-200 flex flex-col text-sm">
        <div class="flex-1 overflow-y-auto">
          <!-- Header Group -->
          <div class="border-b border-gray-200">
            <div class="px-3 py-2 font-medium text-xs text-gray-500">Header</div>
            <div v-for="section in headerSections" :key="section.id" class="group">
              <div 
                @click="selectSection(section.id)"
                :class="[
                  'px-3 py-1.5 flex items-center gap-2 cursor-pointer',
                  selectedSection === section.id ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50'
                ]"
              >
                <span class="text-xs">{{ getSectionIcon(section.key) }}</span>
                <span class="flex-1 text-xs">{{ getSectionLabel(section.key) }}</span>
                <button 
                  @click.stop="removeSection(section.id)"
                  class="opacity-0 group-hover:opacity-100 text-red-600 hover:text-red-800"
                >
                  ×
                </button>
              </div>
            </div>
            <button 
              @click="showAddSection('header')"
              class="w-full px-3 py-1.5 text-xs text-blue-600 hover:bg-blue-50 flex items-center gap-1"
            >
              <span>⊕</span> Add section
            </button>
          </div>

          <!-- Template Group -->
          <div class="border-b border-gray-200">
            <div class="px-3 py-2 font-medium text-xs text-gray-500">Template</div>
            <draggable 
              v-model="templateSections"
              item-key="id"
              handle=".drag-handle"
              @end="onDragEnd"
            >
              <template #item="{ element }">
                <div class="group">
                  <div 
                    @click="selectSection(element.id)"
                    :class="[
                      'px-3 py-1.5 flex items-center gap-2 cursor-pointer',
                      selectedSection === element.id ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50'
                    ]"
                  >
                    <span class="drag-handle cursor-move opacity-0 group-hover:opacity-100">⋮⋮</span>
                    <span class="text-xs">{{ getSectionIcon(element.key) }}</span>
                    <span class="flex-1 text-xs">{{ getSectionLabel(element.key) }}</span>
                    <button 
                      @click.stop="removeSection(element.id)"
                      class="opacity-0 group-hover:opacity-100 text-red-600 hover:text-red-800"
                    >
                      ×
                    </button>
                  </div>
                </div>
              </template>
            </draggable>
            <button 
              @click="showAddSection('template')"
              class="w-full px-3 py-1.5 text-xs text-blue-600 hover:bg-blue-50 flex items-center gap-1"
            >
              <span>⊕</span> Add section
            </button>
          </div>

          <!-- Footer Group -->
          <div>
            <div class="px-3 py-2 font-medium text-xs text-gray-500">Footer</div>
            <div v-for="section in footerSections" :key="section.id" class="group">
              <div 
                @click="selectSection(section.id)"
                :class="[
                  'px-3 py-1.5 flex items-center gap-2 cursor-pointer',
                  selectedSection === section.id ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50'
                ]"
              >
                <span class="text-xs">{{ getSectionIcon(section.key) }}</span>
                <span class="flex-1 text-xs">{{ getSectionLabel(section.key) }}</span>
                <button 
                  @click.stop="removeSection(section.id)"
                  class="opacity-0 group-hover:opacity-100 text-red-600 hover:text-red-800"
                >
                  ×
                </button>
              </div>
            </div>
            <button 
              @click="showAddSection('footer')"
              class="w-full px-3 py-1.5 text-xs text-blue-600 hover:bg-blue-50 flex items-center gap-1"
            >
              <span>⊕</span> Add section
            </button>
          </div>
        </div>
      </div>

      <!-- Center - Preview -->
      <div class="flex-1 overflow-hidden bg-gray-100 flex items-start justify-center p-4">
        <div 
          :class="[
            'bg-white rounded shadow-lg overflow-hidden transition-all duration-300 h-full',
            previewMode === 'mobile' ? 'w-[375px]' : '',
            previewMode === 'tablet' ? 'w-[768px]' : '',
            previewMode === 'desktop' ? 'w-full ' : ''
          ]"
        >
          <iframe 
            ref="previewFrame"
            :src="previewUrl"
            class="w-full h-full border-0"
          ></iframe>
        </div>
      </div>

      <!-- Right Sidebar - Settings (when section selected) -->
      <settings-panel 
        v-if="selectedSection"
        :section="getSelectedSection()"
        @update="updateSectionSettings"
        @close="selectedSection = null"
      />
    </div>

    <!-- Add Section Modal -->
    <add-section-modal
      v-if="showAddModal"
      :available-sections="availableSections"
      :group="currentAddGroup"
      @add="addNewSection"
      @close="showAddModal = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { usePageEditorStore } from '../../stores/pageEditor'
import draggable from 'vuedraggable'
import axios from 'axios'
import SettingsPanel from './SettingsPanel.vue'
import AddSectionModal from './AddSectionModal.vue'

const props = defineProps({
  pageId: {
    type: [String, Number],
    required: true
  }
})

const store = usePageEditorStore()
const { page, theme, headerSections, sections, footerSections, availableSections, selectedSection, loading, saving, hasChanges } = storeToRefs(store)

const showPageDropdown = ref(false)
const showAddModal = ref(false)
const previewFrame = ref(null)
const previewMode = ref('desktop')
const pageSearchQuery = ref('')
const themePages = ref([])
const autoSaveTimeout = ref(null)

// Watch for changes and auto-save to show in preview
watch([headerSections, sections, footerSections], () => {
  // Auto-save after 1 second of no changes
  if (autoSaveTimeout.value) {
    clearTimeout(autoSaveTimeout.value)
  }
  autoSaveTimeout.value = setTimeout(async () => {
    if (hasChanges.value) {
      await handleSave()
    }
  }, 1000)
}, { deep: true })

const previewUrl = computed(() => {
  if (!page.value) return ''
  // Add timestamp to prevent caching
  return `/admin/pages/${page.value.id}/preview?t=${Date.now()}`
})

const filteredThemePages = computed(() => {
  if (!pageSearchQuery.value) return themePages.value
  
  const query = pageSearchQuery.value.toLowerCase()
  return themePages.value.filter(p => 
    p.name.toLowerCase().includes(query)
  )
})

const groupedPages = computed(() => {
  const pages = pageSearchQuery.value ? filteredThemePages.value : themePages.value
  
  const noContext = []
  const byContext = {}
  
  pages.forEach(page => {
    if (!page.context_key) {
      noContext.push(page)
    } else {
      if (!byContext[page.context_key]) {
        byContext[page.context_key] = []
      }
      byContext[page.context_key].push(page)
    }
  })
  
  return { noContext, byContext }
})

const templateSections = computed({
  get: () => sections.value,
  set: (value) => {
    sections.value = value
  }
})

function getSectionLabel(key) {
  if (!key) return 'Unknown'
  const section = availableSections.value.find(s => s?.key === key)
  return section?.label || key
}

function getSectionIcon(key) {
  if (!key) return '📦'
  const section = availableSections.value.find(s => s?.key === key)
  return section?.icon || '📦'
}

function selectSection(sectionId) {
  store.selectSection(sectionId)
}

function removeSection(sectionId) {
  store.removeSection(sectionId)
}

const currentAddGroup = ref('template')

function showAddSection(group) {
  currentAddGroup.value = group
  showAddModal.value = true
}

function addNewSection(sectionKey) {
  store.addSection(sectionKey, currentAddGroup.value)
  showAddModal.value = false
}

function onDragEnd() {
  // Sections updated via v-model
}

function getSelectedSection() {
  if (!selectedSection.value) return null
  return headerSections.value.find(s => s?.id === selectedSection.value) ||
         sections.value.find(s => s?.id === selectedSection.value) ||
         footerSections.value.find(s => s?.id === selectedSection.value) ||
         null
}

function updateSectionSettings(settings) {
  if (selectedSection.value) {
    store.updateSection(selectedSection.value, settings)
    // Don't auto-save - let user click Save button
    // This keeps the "Unsaved" indicator visible
  }
}

function refreshPreview() {
  if (previewFrame.value) {
    previewFrame.value.contentWindow.location.reload()
  }
}

async function handleSave() {
  const success = await store.saveDraft()
  if (success) {
    // Refresh preview to show saved changes
    refreshPreview()
  } else {
    alert('Failed to save draft')
  }
}

async function handlePublish() {
  if (!confirm('Publish this page?')) return
  
  const success = await store.publish()
  if (success) {
    // Refresh preview to show published changes
    refreshPreview()
  } else {
    alert('Failed to publish page')
  }
}

onMounted(async () => {
  console.log('PageEditor: Component mounted with pageId:', props.pageId)
  console.log('PageEditor: pageId type:', typeof props.pageId)
  
  if (!props.pageId) {
    console.error('PageEditor: No pageId provided!')
    return
  }
  
  await store.loadPage(props.pageId)
  
  // Load theme pages for dropdown
  if (page.value?.theme_id) {
    try {
      const response = await axios.get(`/admin/api/themes/${page.value.theme_id}/pages`)
      themePages.value = response.data.pages || []
    } catch (error) {
      console.error('Failed to load theme pages:', error)
    }
  }
})
</script>
