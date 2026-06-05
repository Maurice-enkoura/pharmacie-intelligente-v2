<!-- src/views/pharmacien/StockAlertesView.vue -->
<template>
  <div class="stock-alertes p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800"> Alertes Stock</h1>
      <button 
        @click="refreshAlertes" 
        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center gap-2 text-sm"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Actualiser
      </button>
    </div>
    
    <!-- Stock bas -->
    <div class="bg-white rounded-lg shadow mb-6">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold flex items-center">
          <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          Stock bas 
          <span class="ml-2 text-sm text-gray-500">({{ stockBas.length }})</span>
        </h2>
      </div>
      
      <div v-if="stockBas.length === 0" class="text-gray-500 py-8 text-center">
        <svg class="w-12 h-12 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
         Aucun médicament en stock bas
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stock actuel</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Seuil alerte</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="med in stockBas" :key="med.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 font-medium text-gray-800">{{ med.nom }}</td>
              <td class="px-4 py-3 text-center">
                <span class="text-red-600 font-bold">{{ med.stock_actuel }}</span>
              </td>
              <td class="px-4 py-3 text-center text-gray-600">{{ med.seuil_alerte }}</td>
              <td class="px-4 py-3 text-center">
                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                   Stock critique
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <button 
                  @click="goToCommande(med)" 
                  class="text-green-600 hover:text-green-800 text-sm font-medium flex items-center gap-1 mx-auto"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                  </svg>
                  Commander
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Péremption proche -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold flex items-center">
          <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Péremption proche 
          <span class="ml-2 text-sm text-gray-500">({{ peremptionProche.length }})</span>
        </h2>
      </div>
      
      <div v-if="peremptionProche.length === 0" class="text-gray-500 py-8 text-center">
        <svg class="w-12 h-12 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
         Aucun médicament avec péremption proche
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lot</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantité</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date péremption</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jours restants</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="lot in peremptionProche" :key="lot.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 font-medium text-gray-800">{{ lot.medicament?.nom }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ lot.lot_number }}</td>
              <td class="px-4 py-3 text-center text-gray-600">{{ lot.quantite_restante }}</td>
              <td class="px-4 py-3 text-center text-gray-600">{{ formatDate(lot.date_peremption) }}</td>
              <td class="px-4 py-3 text-center" :class="getDaysClass(lot.date_peremption)">
                {{ getDaysLeft(lot.date_peremption) }} jours
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="getStatusClass(lot.date_peremption)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getStatus(lot.date_peremption) }}
                </span>
              </td>
            </tr>
              
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Dernière mise à jour -->
    <div class="mt-4 text-right text-xs text-gray-400">
      Dernière mise à jour : {{ lastUpdate }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const stockBas = ref([])
const peremptionProche = ref([])
const lastUpdate = ref('')
let refreshInterval = null

// ========== FORMATAGE DATES ==========
const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const getDaysLeft = (date) => {
  const diff = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24))
  return diff
}

const getDaysClass = (date) => {
  const days = getDaysLeft(date)
  if (days <= 7) return 'text-red-600 font-bold'
  if (days <= 15) return 'text-orange-600'
  return 'text-yellow-600'
}

const getStatus = (date) => {
  const days = getDaysLeft(date)
  if (days <= 7) return '⚠️ URGENT !'
  if (days <= 15) return '⚡ Attention'
  return '🔔 À surveiller'
}

const getStatusClass = (date) => {
  const days = getDaysLeft(date)
  if (days <= 7) return 'bg-red-100 text-red-700'
  if (days <= 15) return 'bg-orange-100 text-orange-700'
  return 'bg-yellow-100 text-yellow-700'
}

// ========== ACTIONS ==========
const goToCommande = (medicament) => {
  router.push(`/commandes/create?medicament_id=${medicament.id}`)
}

const refreshAlertes = () => {
  loadAlertes()
}

// ========== CHARGEMENT DES DONNÉES ==========
const loadAlertes = async () => {
  try {
    const response = await axios.get('/api/v1/stock/alertes')
    stockBas.value = response.data.stock_bas || []
    peremptionProche.value = response.data.peremption_proche || []
    lastUpdate.value = new Date().toLocaleTimeString('fr-FR')
    
    console.log(`Alertes chargées - Stock bas: ${stockBas.value.length}, Péremption: ${peremptionProche.value.length}`)
    
  } catch (error) {
    console.error('Erreur chargement alertes:', error)
  }
}

// ========== AUTO-REFRESH ==========
const startAutoRefresh = () => {
  if (refreshInterval) clearInterval(refreshInterval)
  refreshInterval = setInterval(() => {
    loadAlertes()
  }, 30000) // Rafraîchir toutes les 30 secondes
}

// ========== LIFECYCLE ==========
onMounted(() => {
  loadAlertes()
  startAutoRefresh()
})

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})
</script>