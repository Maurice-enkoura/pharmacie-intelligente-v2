<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6"> Dashboard Pharmacien</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Stock total</p>
            <p class="text-2xl font-bold">{{ stats.stock_total }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
            <span class="text-xl"></span>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Médicaments</p>
            <p class="text-2xl font-bold">{{ stats.medicaments_count }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <span class="text-xl"></span>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Alertes stock</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.stock_alerte }}</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
            <span class="text-xl"></span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Stock bas -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
          <h2 class="font-semibold text-yellow-800"> Stock critique</h2>
        </div>
        <div class="p-4">
          <div v-for="med in stockBas" :key="med.id" class="flex justify-between items-center py-2 border-b">
            <div>
              <span class="font-medium">{{ med.nom }}</span>
              <span class="text-sm text-gray-500 ml-2">{{ med.dosage }}</span>
            </div>
            <span class="text-red-600 font-semibold">{{ med.stock_actuel }} restants</span>
          </div>
          <div v-if="stockBas.length === 0" class="text-center text-gray-500 py-4">
             Aucun stock critique
          </div>
        </div>
      </div>
      
      <!-- Péremptions proches -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-orange-50">
          <h2 class="font-semibold text-orange-800"> Péremptions proches</h2>
        </div>
        <div class="p-4">
          <div v-for="lot in peremptionProche" :key="lot.id" class="flex justify-between items-center py-2 border-b">
            <div>
              <span class="font-medium">{{ lot.medicament?.nom }}</span>
              <span class="text-sm text-gray-500 ml-2">Lot: {{ lot.lot_number }}</span>
            </div>
            <div class="text-right">
              <div class="text-orange-600 text-sm">{{ formatDate(lot.date_peremption) }}</div>
              <div class="text-xs text-gray-500">{{ lot.quantite_restante }} restants</div>
            </div>
          </div>
          <div v-if="peremptionProche.length === 0" class="text-center text-gray-500 py-4">
            Aucune péremption proche
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({
  stock_total: 0,
  medicaments_count: 0,
  stock_alerte: 0
})

const stockBas = ref([])
const peremptionProche = ref([])

const formatDate = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleDateString('fr-FR')
}

const fetchData = async () => {
  try {
    // Récupérer les stats
    const statsResponse = await axios.get('/api/v1/medicaments?per_page=1')
    const medicamentsResponse = await axios.get('/api/v1/medicaments')
    stats.value.medicaments_count = medicamentsResponse.data.total || 0
    
    // Récupérer les alertes
    const alertesResponse = await axios.get('/api/v1/stock/alertes')
    stockBas.value = alertesResponse.data.stock_bas || []
    peremptionProche.value = alertesResponse.data.peremption_proche || []
    stats.value.stock_alerte = stockBas.value.length
  } catch (error) {
    console.error('Erreur chargement:', error)
  }
}

onMounted(() => {
  fetchData()
})
</script>