import './assets/main.css';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router/index'; // No .ts extension needed
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import axios from 'axios';

// Import PrimeVue theme and icons
import 'primevue/resources/themes/lara-light-indigo/theme.css'
import 'primevue/resources/primevue.min.css'
import 'primeicons/primeicons.css'

const app = createApp(App);
const token = localStorage.getItem('authToken');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

app.use(createPinia());
app.use(router);
app.use(PrimeVue)
app.use(ToastService)

app.mount('#app');