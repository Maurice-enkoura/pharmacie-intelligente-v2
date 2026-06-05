import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        pharmacy: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role === 'admin',
        isPharmacien: (state) => state.user?.role === 'pharmacien',
        isCaissier: (state) => state.user?.role === 'caissier',
        isSuperAdmin: (state) => state.user?.role === 'super_admin',
        userRole: (state) => state.user?.role,
        userRoleLabel: (state) => {
            const roles = {
                super_admin: '👑 Super Administrateur',
                admin: '👑 Administrateur',
                pharmacien: '💊 Pharmacien',
                caissier: '🏪 Caissier'
            }
            return roles[state.user?.role] || '👤 Utilisateur'
        },
        userName: (state) => state.user?.name || 'Utilisateur',
        userInitial: (state) => (state.user?.name || 'U').charAt(0).toUpperCase(),
        pharmacyName: (state) => state.pharmacy?.name || 'Aucune pharmacie',
    },

    actions: {
        async login(credentials) {
            try {
                console.log('Tentative de connexion...')
                const response = await axios.post('/api/v1/login', credentials)
                console.log('Réponse login:', response.data)
                
                this.token = response.data.token
                this.user = response.data.user
                this.pharmacy = response.data.pharmacy
                
                localStorage.setItem('token', this.token)
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
                
                return { success: true, data: response.data }
            } catch (error) {
                console.error('Login error:', error.response?.data)
                return { 
                    success: false, 
                    message: error.response?.data?.message || 'Erreur de connexion' 
                }
            }
        },

        async logout() {
            try {
                if (this.token) {
                    await axios.post('/api/v1/logout')
                }
            } catch (error) {
                console.error('Logout error:', error)
            } finally {
                this.token = null
                this.user = null
                this.pharmacy = null
                localStorage.removeItem('token')
                delete axios.defaults.headers.common['Authorization']
            }
        },

        async fetchUser() {
            if (!this.token) {
                console.log('fetchUser: pas de token')
                return null
            }
            
            try {
                console.log('fetchUser: récupération utilisateur...')
                const response = await axios.get('/api/v1/user')
                console.log('fetchUser: réponse reçue', response.data)
                this.user = response.data
                this.pharmacy = response.data.pharmacy
                return response.data
            } catch (error) {
                console.error('fetchUser error:', error.response?.status, error.response?.data)
                this.logout()
                throw error
            }
        }
    }
})