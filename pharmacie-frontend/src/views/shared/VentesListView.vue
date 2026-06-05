<template>
  <div class="ventes-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800"> Ventes</h1>
      <router-link to="/ventes/create" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvelle vente
      </router-link>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <input type="date" v-model="filters.date_debut" @change="search" class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
        </div>
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <input type="date" v-model="filters.date_fin" @change="search" class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
        </div>
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <select v-model="filters.client_id" @change="search" class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            <option :value="null">Tous les clients</option>
            <option v-for="client in clients" :key="client.id" :value="client.id">
              {{ client.prenom }} {{ client.nom }}
            </option>
          </select>
        </div>
        <button @click="search" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          Filtrer
        </button>
      </div>
    </div>
    
    <!-- Filtre par vendeur (Admin et Pharmacien seulement) -->
    <div v-if="isAdmin || isPharmacien" class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <select v-model="filters.user_id" @change="search" class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            <option :value="null">Tous les vendeurs</option>
            <option v-for="user in utilisateurs" :key="user.id" :value="user.id">
              👤 {{ user.name }} ({{ getRoleLabel(user.role) }})
            </option>
          </select>
        </div>
      </div>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
    </div>
    
    <!-- Tableau -->
    <div v-else class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Facture</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendu par</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Paiement</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ordonnance</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Réf. Ordonnance</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="vente in ventes" :key="vente.id" class="border-t hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ vente.numero_facture }}</td>
            <td class="px-4 py-3 text-sm">{{ vente.client?.prenom }} {{ vente.client?.nom }}</td>
            <td class="px-4 py-3 text-sm">
              <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold">
                  {{ getUserInitial(vente.user) }}
                </span>
                <div>
                  <span class="font-medium">{{ vente.user?.name }}</span>
                  <span class="text-xs text-gray-400 block">{{ getRoleLabel(vente.user?.role) }}</span>
                </div>
              </div>
            </td>
            <td class="px-4 py-3 text-sm">{{ formatDate(vente.date_vente) }}</td>
            <td class="px-4 py-3 text-right font-medium text-green-600">{{ formatPrice(vente.montant_total) }}</td>
            <td class="px-4 py-3 text-center">
              <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getPaiementClass(vente.mode_paiement)">
                {{ getPaiementLabel(vente.mode_paiement) }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <span v-if="vente.type_vente === 'avec_ordonnance'" class="bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-xs font-medium">
                Avec
              </span>
              <span v-else class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">
                Sans
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <span v-if="vente.ordonnance_ref" class="text-blue-600 text-xs font-mono">
                {{ vente.ordonnance_ref }}
              </span>
              <span v-else class="text-gray-400 text-xs">—</span>
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center space-x-2">
                <router-link :to="`/ventes/${vente.id}`" class="text-blue-600 hover:text-blue-800" title="Voir détails">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </router-link>
                <button 
                  v-if="isAdmin || isPharmacien" 
                  @click="cancelVente(vente)" 
                  class="text-red-600 hover:text-red-800" 
                  title="Annuler la vente"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="ventes.length === 0">
            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
              Aucune vente trouvée
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        {{ (pagination.current_page - 1) * 15 + 1 }} - {{ Math.min(pagination.current_page * 15, pagination.total) }} sur {{ pagination.total }} ventes
      </div>
      <div class="flex space-x-2">
        <button 
          @click="goToPage(pagination.current_page - 1)" 
          :disabled="pagination.current_page <= 1" 
          class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100 transition-colors flex items-center gap-1"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Précédent
        </button>
        <button 
          @click="goToPage(pagination.current_page + 1)" 
          :disabled="pagination.current_page >= pagination.last_page" 
          class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100 transition-colors flex items-center gap-1"
        >
          Suivant
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'
import axios from 'axios'

const authStore = useAuthStore()
const { isAdmin, isPharmacien } = storeToRefs(authStore)

const ventes = ref([])
const clients = ref([])
const utilisateurs = ref([])
const loading = ref(false)
const pagination = ref(null)

const filters = ref({
  date_debut: '',
  date_fin: '',
  client_id: null,
  user_id: null,
  page: 1
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price || 0)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const getPaiementLabel = (mode) => {
  const labels = {
    especes: ' Espèces',
    orange_money: ' Orange Money',
    wave: ' Wave',
    carte: ' Carte'
  }
  return labels[mode] || mode
}

const getPaiementClass = (mode) => {
  const classes = {
    especes: 'bg-gray-100 text-gray-700',
    orange_money: 'bg-orange-100 text-orange-700',
    wave: 'bg-blue-100 text-blue-700',
    carte: 'bg-purple-100 text-purple-700'
  }
  return classes[mode] || 'bg-gray-100'
}

const getRoleLabel = (role) => {
  const labels = {
    super_admin: 'Super Admin',
    admin: 'Admin',
    pharmacien: 'Pharmacien',
    caissier: 'Caissier'
  }
  return labels[role] || role
}

const getUserInitial = (user) => {
  if (!user || !user.name) return '?'
  return user.name.charAt(0).toUpperCase()
}

const loadVentes = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.value.date_debut) params.append('date_debut', filters.value.date_debut)
    if (filters.value.date_fin) params.append('date_fin', filters.value.date_fin)
    if (filters.value.client_id) params.append('client_id', filters.value.client_id)
    if (filters.value.user_id) params.append('user_id', filters.value.user_id)
    params.append('page', filters.value.page)
    
    const response = await axios.get(`/api/v1/ventes?${params.toString()}`)
    ventes.value = response.data.data || []
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Erreur chargement ventes:', error)
  } finally {
    loading.value = false
  }
}

const loadClients = async () => {
  try {
    const response = await axios.get('/api/v1/clients')
    clients.value = response.data.data || []
  } catch (error) {
    console.error('Erreur chargement clients:', error)
  }
}

const loadUtilisateurs = async () => {
  if (!isAdmin.value && !isPharmacien.value) return
  try {
    const response = await axios.get('/api/v1/utilisateurs')
    utilisateurs.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement utilisateurs:', error)
  }
}

const search = () => {
  filters.value.page = 1
  loadVentes()
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    loadVentes()
  }
}

const cancelVente = async (vente) => {
  if (confirm(`Annuler la vente ${vente.numero_facture} ?\nCette action restaurera les stocks.`)) {
    try {
      await axios.delete(`/api/v1/ventes/${vente.id}/cancel`)
      alert(' Vente annulée avec succès')
      loadVentes()
    } catch (error) {
      console.error('Erreur:', error)
      alert(' Erreur lors de l\'annulation')
    }
  }
}

onMounted(() => {
  loadVentes()
  loadClients()
  loadUtilisateurs()
})
</script>