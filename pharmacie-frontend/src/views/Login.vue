<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
    email: '',
    password: ''
})

const errorMessage = ref('')
const loading = ref(false)

const handleLogin = async () => {
    loading.value = true
    errorMessage.value = ''
    
    const result = await authStore.login(form.value)
    
    if (result.success) {
        router.push('/')
    } else {
        // Gestion des erreurs (pharmacie inactive, etc.)
        errorMessage.value = result.message
    }
    
    loading.value = false
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold text-center mb-6">Pharmacie Intelligente</h1>
            
            <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                ⚠️ {{ errorMessage }}
            </div>
            
            <form @submit.prevent="handleLogin">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input 
                        v-model="form.email" 
                        type="email" 
                        class="w-full border rounded-lg px-3 py-2"
                        required
                    />
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Mot de passe</label>
                    <input 
                        v-model="form.password" 
                        type="password" 
                        class="w-full border rounded-lg px-3 py-2"
                        required
                    />
                </div>
                
                <button 
                    type="submit" 
                    :disabled="loading"
                    class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 disabled:opacity-50"
                >
                    {{ loading ? 'Connexion...' : 'Se connecter' }}
                </button>
            </form>
        </div>
    </div>
</template>