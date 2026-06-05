<!-- src/views/pharmacien/RapportsView.vue -->
<template>
  <div class="rapports">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Rapports et statistiques</h1>
    
    <!-- Période selector -->
    <div class="card mb-6">
      <div class="flex flex-wrap gap-4 items-end">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Période</label>
          <select v-model="periode" @change="loadRapports" class="input-field">
            <option value="semaine">Cette semaine</option>
            <option value="mois">Ce mois</option>
            <option value="trimestre">Ce trimestre</option>
            <option value="annee">Cette année</option>
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Date début</label>
          <input type="date" v-model="dateDebut" class="input-field">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Date fin</label>
          <input type="date" v-model="dateFin" class="input-field">
        </div>
        <div>
          <button @click="loadRapports" class="btn-primary">Générer</button>
        </div>
        <div>
          <button @click="exportExcel" class="btn-success">Export Excel</button>
        </div>
        <div>
          <button @click="exportPDF" class="btn-secondary"> Export PDF</button>
        </div>
      </div>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
    
    <div v-else>
      <!-- Cartes KPI -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Chiffre d'affaires</p>
              <p class="text-2xl font-bold text-green-600">{{ formatPrice(rapports.chiffre_affaires || 0) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl"></div>
          </div>
        </div>
        
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Nombre de ventes</p>
              <p class="text-2xl font-bold text-blue-600">{{ rapports.nombre_ventes || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl"></div>
          </div>
        </div>
        
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Panier moyen</p>
              <p class="text-2xl font-bold text-purple-600">{{ formatPrice(rapports.panier_moyen || 0) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-2xl"></div>
          </div>
        </div>
        
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Clients actifs</p>
              <p class="text-2xl font-bold text-orange-600">{{ rapports.clients_actifs || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-2xl"></div>
          </div>
        </div>
      </div>
      
      <!-- Ventes par jour -->
      <div class="card mb-8">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
          </svg>
          Évolution des ventes
        </h3>
        <canvas ref="ventesChartCanvas" style="max-height: 350px;"></canvas>
        <div v-if="!rapports.ventes_par_jour?.length" class="text-center text-gray-500 py-8">
          Aucune donnée de vente sur cette période
        </div>
      </div>
      
      <!-- Top médicaments -->
      <div class="card mb-8">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Top 10 médicaments vendus
        </h3>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left">Médicament</th>
                <th class="px-4 py-2 text-center">Quantité vendue</th>
                <th class="px-4 py-2 text-right">Chiffre d'affaires</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="med in rapports.top_medicaments" :key="med.nom" class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ med.nom }}</td>
                <td class="px-4 py-3 text-center">{{ med.total_vendus }}</td>
                <td class="px-4 py-3 text-right">{{ formatPrice(med.ca || 0) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Ventes par mode de paiement et par caissier -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="card">
          <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Mode de paiement
          </h3>
          <canvas ref="paiementChartCanvas" style="max-height: 300px;"></canvas>
          <div v-if="!rapports.paiements?.length" class="text-center text-gray-500 py-8">
            Aucune donnée de paiement
          </div>
        </div>
        
        <div class="card">
          <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Ventes par caissier
          </h3>
          <div v-if="rapports.ventes_par_caissier?.length" class="space-y-3">
            <div v-for="user in rapports.ventes_par_caissier" :key="user.name" class="flex items-center">
              <span class="w-32 text-sm truncate">{{ user.name }}</span>
              <div class="flex-1 mx-2">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-primary-500 rounded-full" :style="{ width: getMaxVentesPercentage(user.total_ventes) + '%' }"></div>
                </div>
              </div>
              <span class="text-sm font-medium">{{ user.total_ventes }} ventes</span>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 py-8">
            Aucune donnée de vente
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import Chart from 'chart.js/auto'

const periode = ref('mois')
const dateDebut = ref('')
const dateFin = ref('')
const loading = ref(false)
const rapports = ref({
  chiffre_affaires: 0,
  nombre_ventes: 0,
  panier_moyen: 0,
  clients_actifs: 0,
  top_medicaments: [],
  ventes_par_jour: [],
  paiements: [],
  ventes_par_caissier: []
})

let ventesChart = null
let paiementChart = null
const ventesChartCanvas = ref(null)
const paiementChartCanvas = ref(null)

const formatPrice = (price) => {
  if (!price && price !== 0) return '0 FCFA'
  const numPrice = parseFloat(price)
  if (isNaN(numPrice)) return '0 FCFA'
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(numPrice)
}

const getMaxVentesPercentage = (value) => {
  const max = Math.max(...(rapports.value.ventes_par_caissier?.map(u => u.total_ventes) || [1]))
  return max > 0 ? (value / max) * 100 : 0
}

const getPaiementLabel = (mode) => {
  const labels = {
    especes: 'Espèces',
    orange_money: 'Orange Money',
    wave: 'Wave',
    carte: 'Carte'
  }
  return labels[mode] || mode
}

const loadRapports = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    params.append('periode', periode.value)
    if (dateDebut.value) params.append('date_debut', dateDebut.value)
    if (dateFin.value) params.append('date_fin', dateFin.value)
    
    const response = await fetch(`http://127.0.0.1:8000/api/v1/rapports?${params.toString()}`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    })
    
    if (!response.ok) throw new Error('Erreur API')
    
    const data = await response.json()
    rapports.value = data
    
    // Mettre à jour les graphiques après le rendu
    setTimeout(() => {
      updateCharts()
    }, 100)
  } catch (error) {
    console.error('Erreur chargement rapports:', error)
  } finally {
    loading.value = false
  }
}

const updateCharts = () => {
  // Détruire les anciens graphiques
  if (ventesChart) {
    ventesChart.destroy()
    ventesChart = null
  }
  if (paiementChart) {
    paiementChart.destroy()
    paiementChart = null
  }
  
  // Graphique des ventes (évolution)
  if (ventesChartCanvas.value && rapports.value.ventes_par_jour?.length > 0) {
    const labels = rapports.value.ventes_par_jour.map(v => {
      const date = new Date(v.date)
      return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })
    })
    const data = rapports.value.ventes_par_jour.map(v => v.ca)
    
    ventesChart = new Chart(ventesChartCanvas.value, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Chiffre d\'affaires (FCFA)',
          data: data,
          borderColor: '#22c55e',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.3,
          pointBackgroundColor: '#22c55e',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          tooltip: {
            callbacks: {
              label: (context) => `${formatPrice(context.raw)}`
            }
          },
          legend: { position: 'top' }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { callback: (val) => formatPrice(val) }
          }
        }
      }
    })
  }
  
  // Graphique des paiements (camembert)
  if (paiementChartCanvas.value && rapports.value.paiements?.length > 0) {
    const labels = rapports.value.paiements.map(p => getPaiementLabel(p.mode_paiement))
    const data = rapports.value.paiements.map(p => p.total)
    const colors = ['#22c55e', '#f97316', '#3b82f6', '#a855f7', '#ef4444', '#8b5cf6']
    
    paiementChart = new Chart(paiementChartCanvas.value, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: colors.slice(0, data.length),
          borderWidth: 0,
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          tooltip: {
            callbacks: {
              label: (context) => {
                const total = data.reduce((a, b) => a + b, 0)
                const pct = ((context.raw / total) * 100).toFixed(1)
                return `${context.label}: ${formatPrice(context.raw)} (${pct}%)`
              }
            }
          },
          legend: { position: 'bottom' }
        }
      }
    })
  }
}

const exportExcel = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch('http://127.0.0.1:8000/api/v1/rapports/excel', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    if (!response.ok) {
      const error = await response.json()
      throw new Error(error.message || 'Erreur lors de l\'export')
    }
    
    // Récupérer le blob et télécharger
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `rapports_${new Date().toISOString().split('T')[0]}.xlsx`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
    
    alert(' Export Excel réussi')
  } catch (error) {
    console.error('Erreur export:', error)
    alert(' ' + error.message)
  }
}
const exportPDF = () => {
  window.print()
}

// Recharger quand la période change
watch([periode, dateDebut, dateFin], () => {
  loadRapports()
})

onMounted(() => {
  loadRapports()
})
</script>

<style scoped>
@media print {
  .btn-primary, .btn-success, .btn-secondary, .flex.gap-4 {
    display: none;
  }
}
</style>