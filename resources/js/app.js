import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Import Font Awesome core and icons
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faCoffee, faArrowLeft, faRetweet, faPaperPlane } from '@fortawesome/free-solid-svg-icons'; // Import specific icons
import { faTwitter } from '@fortawesome/free-brands-svg-icons'; // Import brand icons

// Import the Markdown editor
import VMdEditor from '@kangc/v-md-editor';
import '@kangc/v-md-editor/lib/style/base-editor.css';
import githubTheme from '@kangc/v-md-editor/lib/theme/github.js';
import '@kangc/v-md-editor/lib/theme/style/github.css';

// Highlight.js for syntax highlighting
import hljs from 'highlight.js';

// Add icons to the library
library.add(faCoffee, faArrowLeft, faRetweet, faPaperPlane, faTwitter);

// Use Markdown editor
VMdEditor.use(githubTheme, {
  Hljs: hljs,
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .component('font-awesome-icon', FontAwesomeIcon) // Register FontAwesomeIcon globally
            .use(plugin)
            .use(ZiggyVue)
            .use(VMdEditor) // Use Markdown editor globally
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
