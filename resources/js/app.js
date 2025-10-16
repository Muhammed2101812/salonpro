import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createI18n } from 'vue-i18n';
import router from './router';

// Import global styles
import '../css/app.css';

// Create Pinia store
const pinia = createPinia();

// Create i18n instance
const i18n = createI18n({
    legacy: false,
    locale: 'tr',
    fallbackLocale: 'en',
    messages: {
        tr: {},
        en: {},
    },
});

// Create Vue app
const app = createApp({});

// Use plugins
app.use(pinia);
app.use(router);
app.use(i18n);

// Mount app
app.mount('#app');
