<!-- src/views/pharmacien/StockHistoriqueView.vue -->
<template>
  <div class="stock-historique p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800"> Historique des mouvements de stock</h1>
      <button 
        @click="refreshHistorique" 
        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center gap-2 text-sm"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Actualiser
      </button>
    </div>
    
    <!-- Barre de recherche et filtres -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Recherche globale -->
        <div class="relative md:col-span-2">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input 
            type="text" 
            v-model="searchQuery" 
            @input="filterMouvements"
            placeholder="Rechercher par médicament, lot, facture ou client..." 
            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"
          >
        </div>
        
        <!-- Filtre médicament -->
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <select v-model="filters.medicament_id" @change="loadHistorique" class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            <option :value="null"> Tous les médicaments</option>
            <option v-for="med in medicaments" :key="med.id" :value="med.id">
               {{ med.nom }} ({{ med.dosage }})
            </option>
          </select>
        </div>
        
        <!-- Type de mouvement -->
        <div class="relative">
          <select v-model="filters.type" @change="filterMouvements" class="w-full pl-3 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            <option :value="null"> Tous les mouvements</option>
            <option value="entree"> Entrées uniquement</option>
            <option value="sortie"> Sorties uniquement</option>
          </select>
        </div>
      </div>
      
      <!-- Deuxième ligne de filtres : Dates -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div class="flex gap-2">
          <input type="date" v-model="filters.date_debut" @change="filterMouvements" class="w-1/2 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500" placeholder="Date début">
          <input type="date" v-model="filters.date_fin" @change="filterMouvements" class="w-1/2 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500" placeholder="Date fin">
        </div>
        <div class="text-right text-sm text-gray-500 flex items-center justify-end gap-4">
          <span> Résultats: {{ filteredMouvements.length }} mouvement(s)</span>
          <button @click="resetFilters" class="text-blue-600 hover:text-blue-800"> Réinitialiser</button>
        </div>
      </div>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
    </div>
    
    <!-- Résumé -->
    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm"> Total entrées</p>
            <p class="text-2xl font-bold text-green-600">{{ totalEntrees }} unités</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl"></div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm"> Total sorties</p>
            <p class="text-2xl font-bold text-red-600">{{ totalSorties }} unités</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-2xl"></div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm"> Variation nette</p>
            <p class="text-2xl font-bold" :class="variationNette >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ variationNette >= 0 ? '+' : '' }}{{ variationNette }} unités
            </p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl"></div>
        </div>
      </div>
    </div>
    
    <!-- Tableau historique -->
    <div v-if="!loading" class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Date</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Médicament</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase"> Type</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase"> Quantité</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase"> Prix unitaire</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase"> Total</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Référence</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Fournisseur/Client</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="mouvement in filteredMouvements" :key="mouvement.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 text-sm text-gray-600">{{ mouvement.date }}</td>
            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ mouvement.medicament }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="mouvement.type === 'entree' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ mouvement.type === 'entree' ? ' Entrée' : ' Sortie' }}
              </span>
            </td>
            <td class="px-4 py-3 text-center" :class="mouvement.type === 'entree' ? 'text-green-600 font-medium' : 'text-red-600 font-medium'">
              {{ mouvement.type === 'entree' ? '+' : '-' }}{{ mouvement.quantite }}
            </td>
            <td class="px-4 py-3 text-right text-gray-600">{{ formatPrice(mouvement.prix_unitaire) }}</td>
            <td class="px-4 py-3 text-right text-gray-600">{{ formatPrice(mouvement.total) }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ mouvement.reference }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ mouvement.tiers }}</td>
          </tr>
          <tr v-if="filteredMouvements.length === 0">
            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
              🔍 Aucun mouvement de stock trouvé
            </td>
          </tr>
        </tbody>
        <tfoot v-if="filteredMouvements.length > 0" class="bg-gray-50">
          <tr>
            <td colspan="7" class="px-4 py-3 text-right font-bold"> Solde final :</td>
            <td class="px-4 py-3 text-left font-bold text-green-600">{{ soldeFinal }} unités</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const medicaments = ref([])
const entrees = ref([])
const sorties = ref([])
const loading = ref(false)
const searchQuery = ref('')

const filters = ref({
  medicament_id: null,
  type: null,
  date_debut: '',
  date_fin: '',
  page: 1
})

// Liste combinée des mouvements pour le tableau
const allMouvements = computed(() => {
  const mouvements = []
  
  // Ajouter les entrées
  entrees.value.forEach(entree => {
    mouvements.push({
      id: `entree-${entree.id}`,
      date: formatDateTime(entree.date_reception),
      medicament: entree.medicament?.nom || '',
      type: 'entree',
      quantite: entree.quantite_restante,
      prix_unitaire: entree.prix_achat_unitaire,
      total: entree.prix_achat_unitaire * entree.quantite_restante,
      reference: entree.lot_number,
      tiers: entree.fournisseur?.nom || '',
      rawDate: new Date(entree.date_reception)
    })
  })
  
  // Ajouter les sorties
  sorties.value.forEach(sortie => {
    mouvements.push({
      id: `sortie-${sortie.id}`,
      date: formatDateTime(sortie.vente?.date_vente),
      medicament: sortie.medicament?.nom || '',
      type: 'sortie',
      quantite: sortie.quantite,
      prix_unitaire: sortie.prix_unitaire,
      total: sortie.sous_total,
      reference: sortie.vente?.numero_facture || '',
      tiers: sortie.vente?.client ? `${sortie.vente.client.prenom} ${sortie.vente.client.nom}` : '',
      rawDate: new Date(sortie.vente?.date_vente)
    })
  })
  
  // Trier par date décroissante
  return mouvements.sort((a, b) => b.rawDate - a.rawDate)
})

// Filtrage des mouvements
const filteredMouvements = computed(() => {
  let result = [...allMouvements.value]
  
  // Filtre par type
  if (filters.value.type) {
    result = result.filter(m => m.type === filters.value.type)
  }
  
  // Filtre par recherche textuelle
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(m => 
      m.medicament.toLowerCase().includes(query) ||
      m.reference.toLowerCase().includes(query) ||
      m.tiers.toLowerCase().includes(query)
    )
  }
  
  // Filtre par dates
  if (filters.value.date_debut) {
    const debut = new Date(filters.value.date_debut)
    debut.setHours(0, 0, 0)
    result = result.filter(m => m.rawDate >= debut)
  }
  
  if (filters.value.date_fin) {
    const fin = new Date(filters.value.date_fin)
    fin.setHours(23, 59, 59)
    result = result.filter(m => m.rawDate <= fin)
  }
  
  return result
})

const totalEntrees = computed(() => {
  return filteredMouvements.value
    .filter(m => m.type === 'entree')
    .reduce((sum, m) => sum + m.quantite, 0)
})

const totalSorties = computed(() => {
  return filteredMouvements.value
    .filter(m => m.type === 'sortie')
    .reduce((sum, m) => sum + m.quantite, 0)
})

const variationNette = computed(() => {
  return totalEntrees.value - totalSorties.value
})

const soldeFinal = computed(() => {
  if (filters.value.medicament_id) {
    const medicament = medicaments.value.find(m => m.id === filters.value.medicament_id)
    return medicament?.stock_actuel || 0
  }
  return totalEntrees.value - totalSorties.value
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price || 0)
}

const formatDateTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('fr-FR')
}

const filterMouvements = () => {
  // Le computed fait le travail automatiquement
}

const resetFilters = () => {
  filters.value = {
    medicament_id: null,
    type: null,
    date_debut: '',
    date_fin: '',
    page: 1
  }
  searchQuery.value = ''
}

const refreshHistorique = () => {
  loadHistorique()
}

const loadMedicaments = async () => {
  try {
    const response = await axios.get('/api/v1/medicaments')
    medicaments.value = response.data.data || []
  } catch (error) {
    console.error('Erreur chargement médicaments:', error)
  }
}

const loadHistorique = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/stock/historique', {
      params: { medicament_id: filters.value.medicament_id }
    })
    entrees.value = response.data.entrees || []
    sorties.value = response.data.sorties || []
  } catch (error) {
    console.error('Erreur chargement historique:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadMedicaments()
  loadHistorique()
})
</script>