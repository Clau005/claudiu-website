<template>
    <aside class="w-56 bg-[#2F2F31] text-gray-200 shrink-0 flex flex-col h-screen border-r border-gray-900">
        <div class="px-6 py-4 shrink-0 border-b border-gray-800 text-gray-100">
            <h1 class="text-lg font-semibold tracking-tight">ElevateCommerce</h1>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-3 text-sm">
            <template v-for="(item, key) in navigation" :key="key">
                <!-- Standalone item (no children) -->
                <a v-if="!hasChildren(item)"
                   :href="item.url"
                   class="flex items-center px-4 py-1.5 text-gray-300 hover:bg-[#454547] hover:text-white transition-colors"
                   :class="{ 'bg-[#454547] text-white rounded-md': isActive(item.url) }">
                    <span v-if="item.icon" class="mr-2 text-gray-300 text-base">{{ item.icon }}</span>
                    <span class="font-medium">{{ item.label }}</span>
                    <span v-if="item.badge" class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                        {{ item.badge }}
                    </span>
                </a>

                <!-- Group with children -->
                <div v-else class="px-4 py-1">
                    <button type="button"
                            class="flex items-center w-full mb-0.5 focus:outline-none"
                            @click="toggleGroup(key)">
                        <span v-if="item.icon" class="mr-2 text-gray-300 text-base">{{ item.icon }}</span>
                        <span class="leading-none font-semibold tracking-wide text-gray-300 text-left flex-1">
                            {{ item.label }}
                        </span>
                    </button>

                    <transition
                        enter-active-class="transition ease-out duration-150"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-screen"
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100 max-h-screen"
                        leave-to-class="opacity-0 max-h-0">
                        <div v-show="openGroups[key]"
                             class="mt-0.5 pl-4 border-l border-gray-700 space-y-0.5 overflow-hidden">
                            <a v-for="(child, childKey) in item.children"
                               :key="childKey"
                               :href="child.url"
                               class="relative flex items-center pl-6 pr-3 py-1.5 text-sm text-gray-400 hover:text-white hover:bg-[#454547] rounded-r-full transition-colors"
                               :class="{ 'bg-[#454547] text-white font-medium rounded-full': isActive(child.url) }">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">↳</span>
                                <span>{{ child.label }}</span>
                            </a>
                        </div>
                    </transition>
                </div>
            </template>
        </nav>
    </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    navigation: {
        type: Object,
        required: true
    },
    currentPath: {
        type: String,
        default: ''
    }
});

const openGroups = ref({});

// Initialize open state for groups with active children
onMounted(() => {
    Object.keys(props.navigation).forEach(key => {
        const item = props.navigation[key];
        if (hasChildren(item)) {
            openGroups.value[key] = hasActiveChild(item);
        }
    });
});

function hasChildren(item) {
    return item.children && Object.keys(item.children).length > 0;
}

function hasActiveChild(item) {
    if (!item.children) return false;
    return Object.values(item.children).some(child => isActive(child.url));
}

function isActive(url) {
    if (!url) return false;
    const cleanUrl = url.replace(/^\//, '');
    const cleanCurrent = props.currentPath.replace(/^\//, '');
    return cleanCurrent === cleanUrl;
}

function toggleGroup(key) {
    openGroups.value[key] = !openGroups.value[key];
}
</script>
