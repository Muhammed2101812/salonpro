import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router/index';
import App from './App.vue';

// Import global styles
import '../css/app.css';

console.log('App.ts is loading...');
console.log('Router object:', router);
console.log('Router routes:', router.getRoutes());

// Create Pinia store
const pinia = createPinia();

// Create Vue app
const app = createApp(App);

console.log('Vue app created');

// Use plugins
app.use(pinia);
app.use(router);

console.log('About to mount app');
console.log('Current route:', router.currentRoute.value);

// Mount app
app.mount('#app');

console.log('App mounted');
console.log('After mount, current route:', router.currentRoute.value);
