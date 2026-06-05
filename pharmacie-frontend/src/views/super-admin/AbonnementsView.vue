<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6"> Gestion des abonnements</h1>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <select v-model="filtreStatut" @change="resetPagination" class="border rounded-lg px-3 py-2">
          <option value="all"> Tous les statuts</option>
          <option value="actif"> Actifs</option>
          <option value="expire_bientot"> Expire bientôt</option>
          <option value="expire"> Expirés</option>
          <option value="suspendu"> Suspendus</option>
        </select>
        <input type="text" v-model="searchQuery" @input="resetPagination" placeholder=" Rechercher une pharmacie..." class="border rounded-lg px-3 py-2">
        <button @click="fetchPharmacies" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Rafraîchir</button>
      </div>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-500">Chargement...</p>
    </div>
    
    <!-- Tableau -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Pharmacie</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Statut</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Date expiration</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="pharmacy in paginatedPharmacies" :key="pharmacy.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <div class="font-medium text-gray-900">{{ pharmacy.name }}</div>
                <div class="text-sm text-gray-500">{{ pharmacy.email }}</div>
              </td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(pharmacy)" class="px-2 py-1 text-xs rounded-full">
                  {{ getStatusText(pharmacy) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ formatDate(pharmacy.subscription_ends_at) || ' Illimité' }}
              </td>
              <td class="px-6 py-4 text-sm space-x-2">
                <button 
                  v-if="!pharmacy.is_active"
                  @click="activatePharmacy(pharmacy.id)"
                  class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition-colors"
                >
                  Activer
                </button>
                <button 
                  v-else
                  @click="suspendPharmacy(pharmacy.id)"
                  class="bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700 transition-colors"
                >
                   Suspendre
                </button>
                <button 
                  @click="renewPharmacy(pharmacy.id)"
                  class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors"
                >
                   Renouveler (+30j)
                </button>
              </td>
            </tr>
            <tr v-if="filteredPharmacies.length === 0">
              <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                 Aucune pharmacie trouvée
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div v-if="filteredPharmacies.length > 0" class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Affichage de {{ startIndex }} à {{ endIndex }} sur {{ filteredPharmacies.length }} pharmacie(s)
        </div>
        <div class="flex gap-2">
          <button 
            @click="currentPage--" 
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 transition-colors flex items-center gap-1"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Précédent
          </button>
          
          <div class="flex gap-1">
            <button 
              v-for="page in displayedPages"
              :key="page"
              @click="currentPage = page"
              :class="[
                'px-3 py-1 rounded-lg transition-colors',
                currentPage === page 
                  ? 'bg-blue-600 text-white' 
                  : 'border hover:bg-gray-100'
              ]"
            >
              {{ page }}
            </button>
          </div>
          
          <button 
            @click="currentPage++" 
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 transition-colors flex items-center gap-1"
          >
            Suivant
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const pharmacies = ref([])
const loading = ref(false)
const filtreStatut = ref('all')
const searchQuery = ref('')

// Pagination
const currentPage = ref(1)
const itemsPerPage = 10

// Pharmacies filtrées
const filteredPharmacies = computed(() => {
  let result = [...pharmacies.value]
  
  // Filtre par statut
  if (filtreStatut.value === 'actif') {
    result = result.filter(p => p.is_active && (!p.subscription_ends_at || new Date(p.subscription_ends_at) > new Date()))
  } else if (filtreStatut.value === 'expire_bientot') {
    result = result.filter(p => p.is_active && p.subscription_ends_at && new Date(p.subscription_ends_at) < new Date(Date.now() + 30 * 24 * 60 * 60 * 1000))
  } else if (filtreStatut.value === 'expire') {
    result = result.filter(p => p.is_active && p.subscription_ends_at && new Date(p.subscription_ends_at) < new Date())
  } else if (filtreStatut.value === 'suspendu') {
    result = result.filter(p => !p.is_active)
  }
  
  // Filtre par recherche
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(p => 
      p.name.toLowerCase().includes(query) || 
      p.email?.toLowerCase().includes(query)
    )
  }
  
  return result
})

// Pharmacies paginées
const paginatedPharmacies = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredPharmacies.value.slice(start, end)
})

// Nombre total de pages
const totalPages = computed(() => Math.ceil(filteredPharmacies.value.length / itemsPerPage))

// Index de début et fin
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage + 1)
const endIndex = computed(() => Math.min(currentPage.value * itemsPerPage, filteredPharmacies.value.length))

// Pages affichées dans la pagination
const displayedPages = computed(() => {
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }
  
  const pages = []
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

const resetPagination = () => {
  currentPage.value = 1
}

const fetchPharmacies = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/admin/pharmacies')
    pharmacies.value = response.data.data || []
    resetPagination()
  } catch (error) {
    console.error('Erreur:', error)
  } finally {
    loading.value = false
  }
}

const activatePharmacy = async (id) => {
  if (confirm(' Activer cette pharmacie ?')) {
    try {
      await axios.put(`/api/v1/admin/pharmacies/${id}`, { is_active: true })
      await fetchPharmacies()
      alert(' Pharmacie activée avec succès')
    } catch (error) {
      alert(' Erreur lors de l\'activation')
    }
  }
}

const suspendPharmacy = async (id) => {
  if (confirm(' Suspendre cette pharmacie ?')) {
    try {
      await axios.put(`/api/v1/admin/pharmacies/${id}`, { is_active: false })
      await fetchPharmacies()
      alert(' Pharmacie suspendue avec succès')
    } catch (error) {
      alert(' Erreur lors de la suspension')
    }
  }
}

const renewPharmacy = async (id) => {
  if (confirm('Renouveler l\'abonnement pour 30 jours ?')) {
    try {
      await axios.post(`/api/v1/super-admin/pharmacies/${id}/renew`)
      await fetchPharmacies()
      alert(' Abonnement renouvelé avec succès')
    } catch (error) {
      alert(' Erreur lors du renouvellement')
    }
  }
}

const getStatusClass = (pharmacy) => {
  if (!pharmacy.is_active) return 'bg-red-100 text-red-800'
  if (pharmacy.subscription_ends_at && new Date(pharmacy.subscription_ends_at) < new Date()) {
    return 'bg-red-100 text-red-800'
  }
  if (pharmacy.subscription_ends_at && new Date(pharmacy.subscription_ends_at) < new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)) {
    return 'bg-yellow-100 text-yellow-800'
  }
  return 'bg-green-100 text-green-800'
}

const getStatusText = (pharmacy) => {
  if (!pharmacy.is_active) return ' Suspendue'
  if (pharmacy.subscription_ends_at && new Date(pharmacy.subscription_ends_at) < new Date()) {
    return ' Expiré'
  }
  if (pharmacy.subscription_ends_at && new Date(pharmacy.subscription_ends_at) < new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)) {
    return ' Expire bientôt'
  }
  return ' Actif'
}

const formatDate = (date) => {
  if (!date) return null
  return new Date(date).toLocaleDateString('fr-FR')
}

onMounted(() => {
  fetchPharmacies()
})
</script>