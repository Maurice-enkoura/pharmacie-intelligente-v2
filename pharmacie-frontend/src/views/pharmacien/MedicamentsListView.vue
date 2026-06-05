<template>
  <div class="medicaments-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Médicaments</h1>
      <router-link to="/medicaments/create" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouveau médicament
      </router-link>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" v-model="filters.search" @input="search" placeholder="Rechercher..." class="border rounded-lg px-3 py-2">
        <select v-model="filters.categorie_id" @change="search" class="border rounded-lg px-3 py-2">
          <option :value="null">Toutes les catégories</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nom }}</option>
        </select>
        <select v-model="filters.ordonnance_requise" @change="search" class="border rounded-lg px-3 py-2">
          <option :value="null">Tous</option>
          <option :value="true">Avec ordonnance</option>
          <option :value="false">Sans ordonnance</option>
        </select>
        <button @click="search" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filtrer</button>
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
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">DCI</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Forme</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dosage</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stock</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prix vente</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ordonnance</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="medicament in medicaments" :key="medicament.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 text-sm font-medium">{{ medicament.nom }}</td>
            <td class="px-4 py-3 text-sm">{{ medicament.dci }}</td>
            <td class="px-4 py-3 text-sm">{{ medicament.forme }}</td>
            <td class="px-4 py-3 text-sm">{{ medicament.dosage }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="getStockClass(medicament.stock_actuel, medicament.seuil_alerte)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ medicament.stock_actuel }}
              </span>
            </td>
            <td class="px-4 py-3 text-right font-medium">{{ formatPrice(medicament.prix_vente) }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="medicament.ordonnance_requise ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700'" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ medicament.ordonnance_requise ? 'Oui' : 'Non' }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center space-x-2">
                <!-- Bouton Modifier avec modal -->
                <button 
                  @click="openEditModal(medicament)" 
                  class="text-yellow-600 hover:text-yellow-800" 
                  title="Modifier"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <!-- Bouton Supprimer -->
                <button 
                  v-if="isAdmin" 
                  @click="deleteMedicament(medicament.id)" 
                  class="text-red-600 hover:text-red-800" 
                  title="Supprimer"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="medicaments.length === 0">
            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
              Aucun médicament trouvé
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        {{ (pagination.current_page - 1) * pagination.per_page + 1 }} - {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} sur {{ pagination.total }} médicaments
      </div>
      <div class="flex space-x-2">
        <button 
          @click="goToPage(pagination.current_page - 1)" 
          :disabled="pagination.current_page <= 1" 
          class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100"
        >
          Précédent
        </button>
        <button 
          @click="goToPage(pagination.current_page + 1)" 
          :disabled="pagination.current_page >= pagination.last_page" 
          class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100"
        >
          Suivant
        </button>
      </div>
    </div>

    <!-- Modal de modification -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">Modifier le médicament</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="updateMedicament">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">Nom *</label>
              <input v-model="editForm.nom" type="text" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">DCI *</label>
              <input v-model="editForm.dci" type="text" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Forme</label>
              <input v-model="editForm.forme" type="text" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Dosage</label>
              <input v-model="editForm.dosage" type="text" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Catégorie</label>
              <select v-model="editForm.categorie_id" class="w-full border rounded-lg px-3 py-2">
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nom }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Prix d'achat</label>
              <input v-model="editForm.prix_achat" type="number" step="0.01" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Prix de vente</label>
              <input v-model="editForm.prix_vente" type="number" step="0.01" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Seuil d'alerte</label>
              <input v-model="editForm.seuil_alerte" type="number" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="flex items-center">
                <input v-model="editForm.ordonnance_requise" type="checkbox" class="mr-2">
                <span class="text-sm">Ordonnance requise</span>
              </label>
            </div>
          </div>
          
          <div class="flex justify-end space-x-2 mt-6">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Annuler</button>
            <button type="submit" :disabled="saving" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
              {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
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
const { isAdmin } = storeToRefs(authStore)

const medicaments = ref([])
const categories = ref([])
const loading = ref(false)
const saving = ref(false)
const pagination = ref(null)
const showEditModal = ref(false)

const editForm = ref({
  id: null,
  nom: '',
  dci: '',
  forme: '',
  dosage: '',
  categorie_id: null,
  prix_achat: 0,
  prix_vente: 0,
  seuil_alerte: 10,
  ordonnance_requise: false
})

const filters = ref({
  search: '',
  categorie_id: null,
  ordonnance_requise: null,
  page: 1
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price || 0)
}

const getStockClass = (stock, seuil) => {
  if (stock <= 0) return 'bg-red-100 text-red-700'
  if (stock <= seuil) return 'bg-orange-100 text-orange-700'
  return 'bg-green-100 text-green-700'
}

const loadMedicaments = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.value.search) params.append('search', filters.value.search)
    if (filters.value.categorie_id) params.append('categorie_id', filters.value.categorie_id)
    if (filters.value.ordonnance_requise !== null) params.append('ordonnance_requise', filters.value.ordonnance_requise)
    params.append('page', filters.value.page)
    
    const response = await axios.get(`/api/v1/medicaments?${params.toString()}`)
    medicaments.value = response.data.data || []
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Erreur chargement médicaments:', error)
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await axios.get('/api/v1/categories')
    categories.value = response.data.data || []
  } catch (error) {
    console.error('Erreur chargement catégories:', error)
  }
}

const openEditModal = (medicament) => {
  editForm.value = {
    id: medicament.id,
    nom: medicament.nom,
    dci: medicament.dci,
    forme: medicament.forme,
    dosage: medicament.dosage,
    categorie_id: medicament.categorie_id,
    prix_achat: medicament.prix_achat,
    prix_vente: medicament.prix_vente,
    seuil_alerte: medicament.seuil_alerte,
    ordonnance_requise: medicament.ordonnance_requise
  }
  showEditModal.value = true
}

const updateMedicament = async () => {
  saving.value = true
  try {
    await axios.put(`/api/v1/medicaments/${editForm.value.id}`, {
      nom: editForm.value.nom,
      dci: editForm.value.dci,
      forme: editForm.value.forme,
      dosage: editForm.value.dosage,
      categorie_id: editForm.value.categorie_id,
      prix_achat: editForm.value.prix_achat,
      prix_vente: editForm.value.prix_vente,
      seuil_alerte: editForm.value.seuil_alerte,
      ordonnance_requise: editForm.value.ordonnance_requise
    })
    
    closeModal()
    loadMedicaments()
    alert(' Médicament modifié avec succès')
  } catch (error) {
    console.error('Erreur:', error)
    alert(' Erreur lors de la modification')
  } finally {
    saving.value = false
  }
}

const closeModal = () => {
  showEditModal.value = false
  editForm.value = {
    id: null,
    nom: '',
    dci: '',
    forme: '',
    dosage: '',
    categorie_id: null,
    prix_achat: 0,
    prix_vente: 0,
    seuil_alerte: 10,
    ordonnance_requise: false
  }
}

const search = () => {
  filters.value.page = 1
  loadMedicaments()
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    loadMedicaments()
  }
}

const deleteMedicament = async (id) => {
  if (confirm('Supprimer ce médicament ?')) {
    try {
      await axios.delete(`/api/v1/medicaments/${id}`)
      loadMedicaments()
      alert(' Médicament supprimé')
    } catch (error) {
      console.error('Erreur:', error)
      alert(' Erreur lors de la suppression')
    }
  }
}

onMounted(() => {
  loadMedicaments()
  loadCategories()
})
</script>