<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6"> Dashboard Administrateur</h1>
    
    <!-- ========== ALERTES STOCK ET PÉREMPTION ========== -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <!-- Alerte Stock Bas -->
      <div class="rounded-lg shadow p-4" :class="stockBas.length > 0 ? 'bg-red-50 border-l-4 border-red-500' : 'bg-green-50 border-l-4 border-green-500'">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="stockBas.length > 0 ? 'bg-red-200' : 'bg-green-200'">
              <span class="text-xl">{{ stockBas.length > 0 ? '⚠️' : '✅' }}</span>
            </div>
            <div>
              <p class="text-sm font-medium" :class="stockBas.length > 0 ? 'text-red-700' : 'text-green-700'">
                Alertes Stock Bas
              </p>
              <p class="text-2xl font-bold" :class="stockBas.length > 0 ? 'text-red-600' : 'text-green-600'">
                {{ stockBas.length }}
              </p>
            </div>
          </div>
          <button 
            v-if="stockBas.length > 0" 
            @click="goToStockAlertes" 
            class="text-sm bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700"
          >
            Voir détails →
          </button>
        </div>
        <div v-if="stockBas.length > 0" class="mt-3 text-sm text-red-600">
          ⚡ {{ stockBas.length }} médicament(s) en stock critique
        </div>
      </div>
      
      <!-- Alerte Péremption Proche -->
      <div class="rounded-lg shadow p-4" :class="peremptionProche.length > 0 ? 'bg-orange-50 border-l-4 border-orange-500' : 'bg-green-50 border-l-4 border-green-500'">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="peremptionProche.length > 0 ? 'bg-orange-200' : 'bg-green-200'">
              <span class="text-xl">{{ peremptionProche.length > 0 ? '📅' : '✅' }}</span>
            </div>
            <div>
              <p class="text-sm font-medium" :class="peremptionProche.length > 0 ? 'text-orange-700' : 'text-green-700'">
                Péremption Proche
              </p>
              <p class="text-2xl font-bold" :class="peremptionProche.length > 0 ? 'text-orange-600' : 'text-green-600'">
                {{ peremptionProche.length }}
              </p>
            </div>
          </div>
          <button 
            v-if="peremptionProche.length > 0" 
            @click="goToStockAlertes" 
            class="text-sm bg-orange-600 text-white px-3 py-1 rounded-lg hover:bg-orange-700"
          >
            Voir détails →
          </button>
        </div>
        <div v-if="peremptionProche.length > 0" class="mt-3">
          <div v-for="lot in peremptionProche.slice(0, 3)" :key="lot.id" class="text-sm text-orange-600 flex justify-between items-center">
            <span>{{ lot.medicament?.nom }}</span>
            <span class="text-xs">Expire: {{ formatDate(lot.date_peremption) }}</span>
          </div>
          <div v-if="peremptionProche.length > 3" class="text-xs text-orange-500 mt-1">
            + {{ peremptionProche.length - 3 }} autre(s)
          </div>
        </div>
      </div>
    </div>
    
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Chiffre d'affaires</p>
            <p class="text-2xl font-bold text-green-600">{{ formatMoney(stats.chiffre_affaires) }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <span class="text-xl">💰</span>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Ventes</p>
            <p class="text-2xl font-bold">{{ stats.nombre_ventes }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
            <span class="text-xl">📦</span>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Clients</p>
            <p class="text-2xl font-bold">{{ stats.nombre_clients }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
            <span class="text-xl">👥</span>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Médicaments</p>
            <p class="text-2xl font-bold">{{ stats.medicaments_count || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
            <span class="text-xl">💊</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Top médicaments -->
    <div class="bg-white rounded-lg shadow mb-8">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="font-semibold"> Top 5 médicaments vendus</h2>
      </div>
      <div class="p-6">
        <div v-for="(med, index) in stats.top_medicaments" :key="index" class="flex justify-between items-center py-2 border-b last:border-0">
          <div class="flex items-center gap-3">
            <span class="w-6 text-gray-500">{{ index + 1 }}</span>
            <span class="font-medium">{{ med.nom }}</span>
          </div>
          <span class="text-green-600 font-semibold">{{ med.total_vendus }} vendus</span>
        </div>
        <div v-if="stats.top_medicaments?.length === 0" class="text-center text-gray-500 py-4">
          Aucune vente pour le moment
        </div>
      </div>
    </div>
    
    <!-- Ventes par mode de paiement -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="font-semibold"> Ventes par mode de paiement</h2>
      </div>
      <div class="p-6">
        <div v-for="paiement in stats.paiements" :key="paiement.mode_paiement" class="flex justify-between items-center py-2">
          <span>{{ getModePaiementLabel(paiement.mode_paiement) }}</span>
          <span class="font-semibold">{{ formatMoney(paiement.total) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const stats = ref({
  chiffre_affaires: 0,
  nombre_ventes: 0,
  nombre_clients: 0,
  stock_alerte: 0,
  medicaments_count: 0,
  top_medicaments: [],
  paiements: []
})

const stockBas = ref([])
const peremptionProche = ref([])

const formatMoney = (value) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(value || 0)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const getModePaiementLabel = (mode) => {
  const labels = {
    especes: ' Espèces',
    orange_money: ' Orange Money',
    wave: ' Wave',
    carte: ' Carte bancaire'
  }
  return labels[mode] || mode
}

const goToStockAlertes = () => {
  router.push('/stock/alertes')
}

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/v1/dashboard')
    stats.value = response.data
  } catch (error) {
    console.error('Erreur chargement stats:', error)
  }
}

const fetchAlertes = async () => {
  try {
    const response = await axios.get('/api/v1/stock/alertes')
    stockBas.value = response.data.stock_bas || []
    peremptionProche.value = response.data.peremption_proche || []
    stats.value.stock_alerte = stockBas.value.length
  } catch (error) {
    console.error('Erreur chargement alertes:', error)
  }
}

const fetchMedicamentsCount = async () => {
  try {
    const response = await axios.get('/api/v1/medicaments')
    stats.value.medicaments_count = response.data.total || 0
  } catch (error) {
    console.error('Erreur chargement médicaments:', error)
  }
}

onMounted(() => {
  fetchStats()
  fetchAlertes()
  fetchMedicamentsCount()
})
</script>