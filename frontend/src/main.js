import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import apiClient from './api/axios'
import './assets/styles/main.css'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Prime the CSRF cookie before the app becomes interactive. Now that the SPA is a
// stateful Sanctum client, every POST/PUT/DELETE (login, register, chatbot, …)
// needs the XSRF-TOKEN. We mount regardless so the UI still loads if the API is down.
apiClient.get('/sanctum/csrf-cookie')
  .catch(() => {})
  .finally(() => app.mount('#app'))
