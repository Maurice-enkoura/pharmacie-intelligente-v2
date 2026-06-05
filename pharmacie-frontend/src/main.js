import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'
import axios from 'axios'

// Configuration axios AVANT tout
axios.defaults.baseURL = 'http://127.0.0.1:8000'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// Intercepteur pour ajouter le token à CHAQUE requête
axios.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
      console.log('Token ajouté à la requête:', config.url)
    } else {
      console.log('Aucun token pour:', config.url)
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

// Intercepteur pour gérer les erreurs 401
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      console.log('Erreur 401, déconnexion...')
      localStorage.removeItem('token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Initialiser l'auth store APRÈS pinia
const authStore = useAuthStore()
const token = localStorage.getItem('token')

console.log('Token présent dans localStorage:', !!token)

if (token) {
  authStore.token = token
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  // Ne pas appeler fetchUser immédiatement, laisser le router le faire
}

app.mount('#app')