import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const usePageEditorStore = defineStore('pageEditor', () => {
    // State
    const page = ref(null)
    const theme = ref(null)
    const headerSections = ref([])
    const sections = ref([]) // Template sections
    const footerSections = ref([])
    const availableSections = ref([])
    const selectedSection = ref(null)
    const selectedGroup = ref('template') // 'header', 'template', or 'footer'
    const loading = ref(false)
    const saving = ref(false)

    // Computed
    const hasChanges = computed(() => {
        if (!page.value || !theme.value) return false
        
        const originalPageConfig = page.value.draft_config || []
        const originalHeaderConfig = theme.value.header_config_draft || theme.value.header_config || []
        const originalFooterConfig = theme.value.footer_config_draft || theme.value.footer_config || []
        
        const pageChanged = JSON.stringify(sections.value) !== JSON.stringify(originalPageConfig)
        const headerChanged = JSON.stringify(headerSections.value) !== JSON.stringify(originalHeaderConfig)
        const footerChanged = JSON.stringify(footerSections.value) !== JSON.stringify(originalFooterConfig)
        
        console.log('Has changes check:', {
            pageChanged,
            headerChanged,
            footerChanged,
            total: pageChanged || headerChanged || footerChanged
        })
        
        return pageChanged || headerChanged || footerChanged
    })

    // Actions
    async function loadPage(pageId) {
        console.log('PageEditor Store: Loading page', pageId)
        loading.value = true
        try {
            const response = await axios.get(`/admin/api/pages/${pageId}`)
            console.log('PageEditor Store: Page loaded', response.data)
            page.value = response.data.page
            theme.value = response.data.theme
            
            // Deep clone to prevent reference sharing
            // Use draft configs for editing
            headerSections.value = JSON.parse(JSON.stringify(response.data.theme?.header_config_draft || response.data.theme?.header_config || []))
            sections.value = JSON.parse(JSON.stringify(response.data.page.draft_config || []))
            footerSections.value = JSON.parse(JSON.stringify(response.data.theme?.footer_config_draft || response.data.theme?.footer_config || []))
            
            availableSections.value = response.data.availableSections || []
            console.log('PageEditor Store: Sections loaded', {
                header: headerSections.value.length,
                template: sections.value.length,
                footer: footerSections.value.length
            })
        } catch (error) {
            console.error('PageEditor Store: Failed to load page:', error)
            console.error('Error details:', error.response?.data || error.message)
        } finally {
            loading.value = false
        }
    }

    async function saveDraft() {
        if (!page.value || !theme.value) return false
        
        saving.value = true
        try {
            // Save page template sections
            await axios.put(`/admin/api/pages/${page.value.id}`, {
                draft_config: sections.value
            })
            
            // Save theme header/footer sections to DRAFT
            await axios.put(`/admin/api/themes/${theme.value.id}`, {
                header_config_draft: headerSections.value,
                footer_config_draft: footerSections.value
            })
            
            // Update local state to match saved state
            page.value.draft_config = JSON.parse(JSON.stringify(sections.value))
            theme.value.header_config_draft = JSON.parse(JSON.stringify(headerSections.value))
            theme.value.footer_config_draft = JSON.parse(JSON.stringify(footerSections.value))
            
            return true
        } catch (error) {
            console.error('Failed to save draft:', error)
            return false
        } finally {
            saving.value = false
        }
    }

    async function publish() {
        if (!page.value || !theme.value) return false
        
        saving.value = true
        try {
            // Publish page
            await axios.post(`/admin/pages/${page.value.id}/publish`)
            
            // Publish theme header/footer
            await axios.post(`/admin/api/themes/${theme.value.id}/publish`)
            
            return true
        } catch (error) {
            console.error('Failed to publish:', error)
            return false
        } finally {
            saving.value = false
        }
    }

    function addSection(sectionKey, group = 'template', position = null) {
        const newSection = {
            id: crypto.randomUUID(),
            key: sectionKey,
            group: group,
            settings: getDefaultSettings(sectionKey)
        }

        const targetArray = group === 'header' ? headerSections : 
                           group === 'footer' ? footerSections : sections

        if (position !== null) {
            targetArray.value.splice(position, 0, newSection)
        } else {
            targetArray.value.push(newSection)
        }

        return newSection
    }

    function removeSection(sectionId) {
        // Try to find and remove from all groups
        let index = headerSections.value.findIndex(s => s.id === sectionId)
        if (index !== -1) {
            headerSections.value.splice(index, 1)
            return
        }
        
        index = sections.value.findIndex(s => s.id === sectionId)
        if (index !== -1) {
            sections.value.splice(index, 1)
            return
        }
        
        index = footerSections.value.findIndex(s => s.id === sectionId)
        if (index !== -1) {
            footerSections.value.splice(index, 1)
        }
    }

    function updateSection(sectionId, settings) {
        // Try to find and update in all groups
        let index = headerSections.value.findIndex(s => s.id === sectionId)
        if (index !== -1) {
            const newSections = [...headerSections.value]
            newSections[index] = {
                ...newSections[index],
                settings: { ...newSections[index].settings, ...settings }
            }
            headerSections.value = newSections
            return
        }
        
        index = sections.value.findIndex(s => s.id === sectionId)
        if (index !== -1) {
            const newSections = [...sections.value]
            newSections[index] = {
                ...newSections[index],
                settings: { ...newSections[index].settings, ...settings }
            }
            sections.value = newSections
            return
        }
        
        index = footerSections.value.findIndex(s => s.id === sectionId)
        if (index !== -1) {
            const newSections = [...footerSections.value]
            newSections[index] = {
                ...newSections[index],
                settings: { ...newSections[index].settings, ...settings }
            }
            footerSections.value = newSections
        }
    }

    function moveSection(oldIndex, newIndex) {
        const section = sections.value.splice(oldIndex, 1)[0]
        sections.value.splice(newIndex, 0, section)
    }

    function selectSection(sectionId) {
        selectedSection.value = sectionId
    }

    function getDefaultSettings(sectionKey) {
        const section = availableSections.value.find(s => s.key === sectionKey)
        return section?.defaults || {}
    }

    return {
        // State
        page,
        theme,
        headerSections,
        sections,
        footerSections,
        availableSections,
        selectedSection,
        selectedGroup,
        loading,
        saving,
        
        // Computed
        hasChanges,
        
        // Actions
        loadPage,
        saveDraft,
        publish,
        addSection,
        removeSection,
        updateSection,
        moveSection,
        selectSection
    }
})
