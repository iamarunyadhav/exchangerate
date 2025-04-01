import { createApp } from 'vue'
import App from './App.vue'
import axios from 'axios';
import router from './router'
import store from './store'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

const app = createApp(App)

app.use(router)
app.use(store)

// Initialize auth but don't wait for it to mount
store.dispatch('initializeAuth')
  .catch(error => console.error('Auth initialization failed:', error))

app.config.errorHandler = (err) => {
  console.error('Vue error:', err)
}
app.config.globalProperties.$http = axios;

app.mount('#app')