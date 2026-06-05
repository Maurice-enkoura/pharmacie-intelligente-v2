<template>
  <div class="min-h-screen flex">
    
    <!-- ==================== PARTIE GAUCHE - IMAGE ==================== -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
      <div 
        class="absolute inset-0 bg-cover bg-center bg-no-repeat"
        style="background-image: url('https://t3.ftcdn.net/jpg/00/83/75/36/360_F_83753610_9HpC3dJZBbxHlYNKrug52QNzy4U4qf6p.jpg')"
      >
        <div class="absolute inset-0 bg-gradient-to-br from-green-900/90 to-green-800/80"></div>
      </div>
      
      <div class="relative z-10 flex flex-col justify-between h-full p-10 text-white">
        <div>
          <div class="flex items-center gap-3 mb-8">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
              <span class="text-2xl">💊</span>
            </div>
            <div>
              <h2 class="font-bold text-xl">PharmaCare</h2>
              <p class="text-white/70 text-xs">Système de gestion intelligent</p>
            </div>
          </div>
        </div>
        
        <div class="text-center">
          <div class="mb-6">
            <div class="w-32 h-32 bg-white/10 rounded-full flex items-center justify-center mx-auto backdrop-blur-sm">
              <span class="text-5xl">🏥</span>
            </div>
          </div>
          <h1 class="text-3xl font-bold mb-2">Rejoignez PharmaCare</h1>
          <p class="text-white/80 text-sm max-w-sm mx-auto">
            Créez votre espace pharmacie et bénéficiez de notre solution complète de gestion
          </p>
        </div>
        
        <div class="text-center text-white/50 text-xs">
          <p>© 2025 PharmaCare - Tous droits réservés</p>
        </div>
      </div>
    </div>
    
    <!-- ==================== PARTIE DROITE - FORMULAIRE D'INSCRIPTION ==================== -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-gray-50">
      <div class="w-full max-w-md">
        <div class="text-center mb-8 lg:hidden">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-3xl">💊</span>
          </div>
          <h1 class="text-2xl font-bold text-gray-800">Pharmacie Intelligente</h1>
          <p class="text-gray-500 mt-1">Créez votre espace pharmacie</p>
        </div>
        
        <div class="mb-8 text-center lg:text-left">
          <h2 class="text-2xl font-bold text-gray-800">Inscription</h2>
          <p class="text-gray-500 text-sm mt-1">Créez votre espace pharmacie en quelques secondes</p>
        </div>
        
        <form @submit.prevent="handleRegister" class="space-y-4">
          <!-- Section Pharmacie -->
          <div class="border-b border-gray-200 pb-2 mb-2">
            <p class="text-sm font-semibold text-gray-700">🏢 Informations de la pharmacie</p>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              Nom de la pharmacie *
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
              </span>
              <input 
                type="text" 
                v-model="form.pharmacy_name"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                placeholder="ex: Pharmacie Centrale"
                required
              >
            </div>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              Email de la pharmacie *
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </span>
              <input 
                type="email" 
                v-model="form.pharmacy_email"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                placeholder="contact@pharmacie.com"
                required
              >
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-gray-700 text-sm font-semibold mb-2">
                Téléphone
              </label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                </span>
                <input 
                  type="tel" 
                  v-model="form.pharmacy_phone"
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                  placeholder="77 000 00 00"
                >
              </div>
            </div>
            <div>
              <label class="block text-gray-700 text-sm font-semibold mb-2">
                SIRET (optionnel)
              </label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                  </svg>
                </span>
                <input 
                  type="text" 
                  v-model="form.pharmacy_siret"
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                  placeholder="123 456 789 00012"
                >
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              Adresse
            </label>
            <div class="relative">
              <span class="absolute left-3 top-4 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </span>
              <textarea 
                v-model="form.pharmacy_address"
                rows="2"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                placeholder="Adresse complète de la pharmacie"
              ></textarea>
            </div>
          </div>
          
          <!-- Section Administrateur -->
          <div class="border-b border-gray-200 pb-2 mb-2 mt-4">
            <p class="text-sm font-semibold text-gray-700">👨‍💼 Informations de l'administrateur</p>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              Nom complet *
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </span>
              <input 
                type="text" 
                v-model="form.admin_name"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                placeholder="Jean Dupont"
                required
              >
            </div>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              Email de l'administrateur *
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </span>
              <input 
                type="email" 
                v-model="form.admin_email"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                placeholder="jean.dupont@pharmacie.com"
                required
              >
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-gray-700 text-sm font-semibold mb-2">
                Mot de passe *
              </label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </span>
                <input 
                  :type="showPassword ? 'text' : 'password'"
                  v-model="form.admin_password"
                  class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                  placeholder="••••••••"
                  required
                >
                <button 
                  type="button" 
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                  </svg>
                </button>
              </div>
            </div>
            <div>
              <label class="block text-gray-700 text-sm font-semibold mb-2">
                Confirmer mot de passe *
              </label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                  </svg>
                </span>
                <input 
                  :type="showConfirmPassword ? 'text' : 'password'"
                  v-model="form.admin_password_confirmation"
                  class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                  placeholder="••••••••"
                  required
                >
                <button 
                  type="button" 
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                  <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          <div class="flex items-center mt-4">
            <input type="checkbox" v-model="acceptTerms" id="terms" class="mr-2 rounded" required>
            <label for="terms" class="text-sm text-gray-600">
              J'accepte les <a href="#" class="text-green-600 hover:underline">conditions générales d'utilisation</a>
            </label>
          </div>
          
          <button 
            type="submit" 
            :disabled="loading"
            class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed mt-6"
          >
            <span v-if="loading" class="flex items-center justify-center gap-2">
              <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
              Création en cours...
            </span>
            <span v-else class="flex items-center justify-center gap-2">
              Créer mon espace pharmacie
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </span>
          </button>
        </form>
        
        <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm flex items-start gap-2">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>{{ error }}</span>
        </div>
        
        <div v-if="success" class="mt-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-start gap-2">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>{{ success }}</span>
        </div>
        
        <!-- Lien vers la connexion -->
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            Déjà inscrit ? 
            <router-link to="/login" class="text-green-600 font-semibold hover:underline">
              Se connecter
            </router-link>
          </p>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const loading = ref(false)
const error = ref('')
const success = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const acceptTerms = ref(false)

const form = ref({
  // Pharmacie
  pharmacy_name: '',
  pharmacy_email: '',
  pharmacy_phone: '',
  pharmacy_address: '',
  pharmacy_siret: '',
  // Admin
  admin_name: '',
  admin_email: '',
  admin_password: '',
  admin_password_confirmation: ''
})

const handleRegister = async () => {
  loading.value = true
  error.value = ''
  success.value = ''

  // Vérifier les mots de passe
  if (form.value.admin_password !== form.value.admin_password_confirmation) {
    error.value = 'Les mots de passe ne correspondent pas'
    loading.value = false
    return
  }

  // Vérifier les conditions
  if (!acceptTerms.value) {
    error.value = 'Vous devez accepter les conditions générales'
    loading.value = false
    return
  }

  // Vérifier la longueur du mot de passe
  if (form.value.admin_password.length < 6) {
    error.value = 'Le mot de passe doit contenir au moins 6 caractères'
    loading.value = false
    return
  }

  try {
    const response = await axios.post('/api/v1/register-pharmacy', {
      pharmacy: {
        name: form.value.pharmacy_name,
        email: form.value.pharmacy_email,
        phone: form.value.pharmacy_phone,
        address: form.value.pharmacy_address,
        siret: form.value.pharmacy_siret
      },
      admin: {
        name: form.value.admin_name,
        email: form.value.admin_email,
        password: form.value.admin_password
      }
    })

    if (response.data.success) {
      success.value = ' Votre pharmacie a été créée avec succès ! En attente de validation par l\'administrateur. Vous allez être redirigé...'
      setTimeout(() => {
        router.push('/login')
      }, 3000)
    }
  } catch (err) {
    console.error('Erreur inscription:', err)
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors
      error.value = Object.values(errors).flat().join(', ')
    } else {
      error.value = err.response?.data?.message || 'Erreur lors de l\'inscription'
    }
  } finally {
    loading.value = false
  }
}
</script>