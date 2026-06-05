<!-- src/views/pharmacien/StockEntreesView.vue -->
<template>
  <div class="stock-entrees p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800"> Entrées de stock</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez les entrées de stock de votre pharmacie</p>
      </div>
      <button @click="showModal = true" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvelle entrée
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
            @input="filterEntrees"
            placeholder=" Rechercher par médicament, lot ou fournisseur..." 
            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"
          >
        </div>
        
        <!-- Filtre médicament -->
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <select v-model="filters.medicament_id" @change="loadEntrees" class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            <option :value="null"> Tous les médicaments</option>
            <option v-for="med in medicaments" :key="med.id" :value="med.id">
               {{ med.nom }} ({{ med.dosage }})
            </option>
          </select>
        </div>
        
        <!-- Période -->
        <div class="flex gap-2">
          <input type="date" v-model="filters.date_debut" @change="loadEntrees" class="w-1/2 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500" placeholder="Début">
          <input type="date" v-model="filters.date_fin" @change="loadEntrees" class="w-1/2 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500" placeholder="Fin">
        </div>
      </div>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
    </div>
    
    <!-- Tableau des entrées -->
    <div v-else class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Date</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Médicament</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Lot</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase"> Quantité</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase"> Prix unitaire</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase"> Date péremption</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Fournisseur</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="entree in filteredEntreesList" :key="entree.id" class="border-t hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-sm text-gray-600">{{ formatDate(entree.date_reception) }}</td>
            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ entree.medicament?.nom }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ entree.lot_number }}</td>
            <td class="px-4 py-3 text-center text-sm font-medium text-green-600">{{ entree.quantite_restante }}</td>
            <td class="px-4 py-3 text-right text-sm">{{ formatPrice(entree.prix_achat_unitaire) }}</td>
            <td class="px-4 py-3 text-right text-sm font-medium">{{ formatPrice(entree.prix_achat_unitaire * entree.quantite_restante) }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="getPeremptionClass(entree.date_peremption)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ formatDate(entree.date_peremption) }}
              </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ entree.fournisseur?.nom }}</td>
          </tr>
          <tr v-if="filteredEntreesList.length === 0">
            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
               Aucune entrée de stock trouvée
            </td>
          </tr>
        </tbody>
        <tfoot v-if="filteredEntreesList.length > 0" class="bg-gray-50">
          <tr>
            <td colspan="5" class="px-4 py-3 text-right font-bold"> Total entrées :</td>
            <td class="px-4 py-3 text-right font-bold text-green-600">{{ formatPrice(totalEntrees) }}</td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
         {{ pagination.total }} entrée(s)
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
    
    <!-- Modal nouvelle entrée (conserver le même) -->
    <!-- ... (le modal reste identique) ... -->
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const entrees = ref([])
const medicaments = ref([])
const fournisseurs = ref([])
const loading = ref(false)
const submitting = ref(false)
const showModal = ref(false)
const pagination = ref(null)
const searchQuery = ref('')

const filters = ref({
  medicament_id: null,
  date_debut: '',
  date_fin: '',
  page: 1
})

// Nouvelle entrée
const newEntree = ref({
  medicament_id: null,
  fournisseur_id: null,
  lot_number: '',
  quantite: 1,
  prix_achat: 0,
  date_peremption: ''
})

// Recherche dans le modal
const medicamentSearch = ref('')
const fournisseurSearch = ref('')

// Filtrage local des entrées
const filteredEntreesList = computed(() => {
  let result = [...entrees.value]
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(entree => 
      entree.medicament?.nom?.toLowerCase().includes(query) ||
      entree.lot_number?.toLowerCase().includes(query) ||
      entree.fournisseur?.nom?.toLowerCase().includes(query)
    )
  }
  
  return result
})

const filteredMedicaments = computed(() => {
  if (!medicamentSearch.value) return medicaments.value
  const search = medicamentSearch.value.toLowerCase()
  return medicaments.value.filter(med => 
    med.nom.toLowerCase().includes(search) || 
    med.dci?.toLowerCase().includes(search)
  )
})

const filteredFournisseurs = computed(() => {
  if (!fournisseurSearch.value) return fournisseurs.value
  const search = fournisseurSearch.value.toLowerCase()
  return fournisseurs.value.filter(four => 
    four.nom.toLowerCase().includes(search)
  )
})

const minDate = computed(() => {
  const date = new Date()
  date.setDate(date.getDate() + 1)
  return date.toISOString().split('T')[0]
})

const totalEntrees = computed(() => {
  return filteredEntreesList.value.reduce((sum, e) => sum + (e.prix_achat_unitaire * e.quantite_restante), 0)
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price || 0)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const getPeremptionClass = (date) => {
  if (!date) return 'bg-gray-100 text-gray-700'
  const daysLeft = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24))
  if (daysLeft <= 30) return 'bg-red-100 text-red-700'
  if (daysLeft <= 90) return 'bg-orange-100 text-orange-700'
  return 'bg-green-100 text-green-700'
}

const loadEntrees = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/stock/historique', {
      params: { medicament_id: filters.value.medicament_id }
    })
    entrees.value = response.data.entrees || []
    pagination.value = {
      current_page: 1,
      last_page: 1,
      total: entrees.value.length
    }
  } catch (error) {
    console.error('Erreur chargement entrées:', error)
  } finally {
    loading.value = false
  }
}

const loadMedicaments = async () => {
  try {
    const response = await axios.get('/api/v1/medicaments')
    medicaments.value = response.data.data || []
  } catch (error) {
    console.error('Erreur chargement médicaments:', error)
  }
}

const loadFournisseurs = async () => {
  try {
    const response = await axios.get('/api/v1/fournisseurs')
    fournisseurs.value = response.data.data || []
  } catch (error) {
    console.error('Erreur chargement fournisseurs:', error)
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    loadEntrees()
  }
}

const submitEntree = async () => {
  if (!newEntree.value.medicament_id || !newEntree.value.fournisseur_id || !newEntree.value.lot_number || !newEntree.value.quantite || !newEntree.value.prix_achat || !newEntree.value.date_peremption) {
    alert(' Veuillez remplir tous les champs')
    return
  }
  
  submitting.value = true
  try {
    await axios.post('/api/v1/stock/entrees', {
      medicament_id: newEntree.value.medicament_id,
      fournisseur_id: newEntree.value.fournisseur_id,
      lot_number: newEntree.value.lot_number,
      quantite: newEntree.value.quantite,
      prix_achat: newEntree.value.prix_achat,
      date_peremption: newEntree.value.date_peremption
    })
    
    closeModal()
    loadEntrees()
    loadMedicaments()
    alert(' Entrée de stock enregistrée avec succès')
  } catch (error) {
    console.error('Erreur:', error)
    alert(' Erreur lors de l\'enregistrement')
  } finally {
    submitting.value = false
  }
}

const closeModal = () => {
  showModal.value = false
  medicamentSearch.value = ''
  fournisseurSearch.value = ''
  newEntree.value = {
    medicament_id: null,
    fournisseur_id: null,
    lot_number: '',
    quantite: 1,
    prix_achat: 0,
    date_peremption: ''
  }
}

onMounted(() => {
  loadEntrees()
  loadMedicaments()
  loadFournisseurs()
})
</script>