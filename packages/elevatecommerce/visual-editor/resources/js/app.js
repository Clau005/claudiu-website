import { createApp } from 'vue';
import { createPinia } from 'pinia';
import PageEditor from './components/PageEditor/PageEditor.vue';
import Sidebar from './components/Sidebar.vue';

console.log('Visual Editor: Loading Vue app...');
console.log('Visual Editor: PageEditor component:', PageEditor);

// Only mount Vue if #app exists (not on preview pages)
const appElement = document.getElementById('app');

if (appElement) {
    console.log('Visual Editor: App element found, creating Vue instance...');
    console.log('Visual Editor: App element HTML:', appElement.innerHTML);
    
    const app = createApp({
        components: {
            'page-editor': PageEditor
        }
    });
    const pinia = createPinia();

    app.use(pinia);

    // Global error handler
    app.config.errorHandler = (err, instance, info) => {
        console.error('Vue Error:', err);
        console.error('Component:', instance);
        console.error('Info:', info);
    };

    // Warn handler for development
    app.config.warnHandler = (msg, instance, trace) => {
        console.warn('Vue Warning:', msg);
        console.warn('Trace:', trace);
    };

    app.mount('#app');
    console.log('Visual Editor: Vue app mounted successfully!');
    console.log('Visual Editor: App HTML after mount:', appElement.innerHTML);
} else {
    console.warn('Visual Editor: #app element not found');
}

// Mount Sidebar component separately if #sidebar exists
const sidebarElement = document.getElementById('sidebar');

if (sidebarElement) {
    console.log('Visual Editor: Sidebar element found, creating Vue instance...');
    
    const sidebarApp = createApp(Sidebar, {
        navigation: window.navigationData || {},
        currentPath: window.location.pathname
    });

    sidebarApp.mount('#sidebar');
    console.log('Visual Editor: Sidebar mounted successfully!');
} else {
    console.log('Visual Editor: #sidebar element not found (normal for non-admin pages)');
}
