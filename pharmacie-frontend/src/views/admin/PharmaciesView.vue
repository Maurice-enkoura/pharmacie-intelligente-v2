<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold"> Gestion des pharmacies</h1>
      <button @click="openCreateModal" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
        + Nouvelle pharmacie
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
      <p class="mt-2 text-gray-600">Chargement...</p>
    </div>
    
    <!-- Message d'erreur -->
    <div v-else-if="error" class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
       {{ error }}
    </div>
    
    <!-- Liste des pharmacies -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Abonnement</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="pharmacy in pharmacies" :key="pharmacy.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-900">{{ pharmacy.id }}</td>
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ pharmacy.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ pharmacy.email || '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ pharmacy.phone || '-' }}</td>
            <td class="px-6 py-4">
              <span 
                :class="pharmacy.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                class="px-2 py-1 text-xs rounded-full"
              >
                {{ pharmacy.is_active ? 'Actif' : 'Inactif' }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm">
              <div v-if="pharmacy.subscription_ends_at">
                <span :class="getSubscriptionClass(pharmacy.subscription_ends_at)" class="px-2 py-1 text-xs rounded-full">
                  {{ formatDate(pharmacy.subscription_ends_at) }}
                </span>
              </div>
              <span v-else class="text-gray-400 text-xs">Illimité</span>
            </td>
            <td class="px-6 py-4 text-sm space-x-3">
              <!-- Activer/Désactiver -->
              <button 
                v-if="!pharmacy.is_active"
                @click="activatePharmacy(pharmacy.id)" 
                class="text-green-600 hover:text-green-800" 
                title="Activer"
              >
                 Activer
              </button>
              <button 
                v-else
                @click="deactivatePharmacy(pharmacy.id)" 
                class="text-yellow-600 hover:text-yellow-800" 
                title="Désactiver"
              >
                 Désactiver
              </button>
              
              <!-- Statistiques -->
              <button 
                @click="viewStats(pharmacy.id)" 
                class="text-blue-600 hover:text-blue-800" 
                title="Statistiques"
              >
                 Stats
              </button>
              
              <!-- Modifier -->
              <button 
                @click="editPharmacy(pharmacy)" 
                class="text-yellow-600 hover:text-yellow-800" 
                title="Modifier"
              >
                
              </button>
              
              <!-- Supprimer -->
              <button 
                @click="deletePharmacy(pharmacy.id)" 
                class="text-red-600 hover:text-red-800" 
                title="Supprimer"
              >
                
              </button>
              
              <!-- Renouveler abonnement -->
              <button 
                @click="renewSubscription(pharmacy.id)" 
                class="text-purple-600 hover:text-purple-800" 
                title="Renouveler abonnement (+30 jours)"
              >
                 Renouveler
              </button>
            </td>
          </tr>
        </tbody>
       </table>
      
      <!-- Pagination -->
      <div v-if="pagination" class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Page {{ pagination.current_page }} sur {{ pagination.last_page }}
        </div>
        <div class="flex space-x-2">
          <button 
            @click="fetchPharmacies(pagination.current_page - 1)" 
            :disabled="!pagination.prev_page_url"
            class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-50"
          >
            Précédent
          </button>
          <button 
            @click="fetchPharmacies(pagination.current_page + 1)" 
            :disabled="!pagination.next_page_url"
            class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-50"
          >
            Suivant
          </button>
        </div>
      </div>
      
      <div v-if="pharmacies.length === 0" class="text-center py-8 text-gray-500">
        Aucune pharmacie trouvée
      </div>
    </div>

    <!-- Modal Création/Modification -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-96 max-w-md">
        <h2 class="text-xl font-bold mb-4">{{ isEditing ? 'Modifier' : 'Nouvelle' }} pharmacie</h2>
        <form @submit.prevent="savePharmacy">
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nom *</label>
            <input 
              v-model="form.name" 
              type="text" 
              required 
              class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
            >
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Email</label>
            <input 
              v-model="form.email" 
              type="email" 
              class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
            >
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Téléphone</label>
            <input 
              v-model="form.phone" 
              type="text" 
              class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
            >
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Adresse</label>
            <textarea 
              v-model="form.address" 
              rows="2"
              class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
            ></textarea>
          </div>
          <div class="mb-4">
            <label class="flex items-center cursor-pointer">
              <input v-model="form.is_active" type="checkbox" class="mr-2">
              <span class="text-sm">Pharmacie active</span>
            </label>
          </div>
          <div class="flex justify-end space-x-2">
            <button 
              type="button" 
              @click="closeModal" 
              class="px-4 py-2 border rounded-lg hover:bg-gray-50"
            >
              Annuler
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
            >
              Enregistrer
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Statistiques -->
    <div v-if="showStatsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-96 max-w-md">
        <h2 class="text-xl font-bold mb-4"> Statistiques</h2>
        <p class="text-lg font-semibold text-green-700 mb-4">{{ selectedPharmacy?.name }}</p>
        <div class="space-y-3">
          <div class="flex justify-between py-2 border-b">
            <span class="text-gray-600"> Utilisateurs :</span>
            <span class="font-semibold">{{ stats.users_count || 0 }}</span>
          </div>
          <div class="flex justify-between py-2 border-b">
            <span class="text-gray-600"> Médicaments :</span>
            <span class="font-semibold">{{ stats.medicaments_count || 0 }}</span>
          </div>
          <div class="flex justify-between py-2 border-b">
            <span class="text-gray-600"> Clients :</span>
            <span class="font-semibold">{{ stats.clients_count || 0 }}</span>
          </div>
          <div class="flex justify-between py-2 border-b">
            <span class="text-gray-600"> Ventes :</span>
            <span class="font-semibold">{{ stats.ventes_count || 0 }}</span>
          </div>
          <div class="flex justify-between py-2 border-b">
            <span class="text-gray-600"> Chiffre d'affaires :</span>
            <span class="font-semibold text-green-600">{{ formatMoney(stats.chiffre_affaires) }}</span>
          </div>
        </div>
        <div class="mt-6 flex justify-end">
          <button 
            @click="showStatsModal = false" 
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const pharmacies = ref([])
const loading = ref(false)
const error = ref(null)
const showModal = ref(false)
const showStatsModal = ref(false)
const isEditing = ref(false)
const selectedPharmacy = ref(null)
const stats = ref({})
const pagination = ref(null)

const form = ref({
  id: null,
  name: '',
  email: '',
  phone: '',
  address: '',
  is_active: true
})

// Récupérer les pharmacies
const fetchPharmacies = async (page = 1) => {
  loading.value = true
  error.value = null
  
  try {
    const response = await axios.get(`/api/v1/admin/pharmacies?page=${page}`)
    pharmacies.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      prev_page_url: response.data.prev_page_url,
      next_page_url: response.data.next_page_url
    }
  } catch (err) {
    console.error('Erreur:', err)
    error.value = err.response?.data?.message || 'Erreur lors du chargement des pharmacies'
    if (err.response?.status === 403) {
      error.value = 'Accès non autorisé. Vous devez être Super Admin.'
    }
  } finally {
    loading.value = false
  }
}

// Activer une pharmacie
const activatePharmacy = async (id) => {
  if (confirm(' Activer cette pharmacie ?')) {
    try {
      await axios.put(`/api/v1/admin/pharmacies/${id}`, { is_active: true })
      await fetchPharmacies()
      alert('Pharmacie activée avec succès')
    } catch (error) {
      alert('Erreur lors de l\'activation')
    }
  }
}

// Désactiver une pharmacie
const deactivatePharmacy = async (id) => {
  if (confirm(' Désactiver cette pharmacie ? Les utilisateurs ne pourront plus se connecter.')) {
    try {
      await axios.put(`/api/v1/admin/pharmacies/${id}`, { is_active: false })
      await fetchPharmacies()
      alert('Pharmacie désactivée avec succès')
    } catch (error) {
      alert('Erreur lors de la désactivation')
    }
  }
}

// Renouveler l'abonnement
const renewSubscription = async (id) => {
  if (confirm('Renouveler l\'abonnement pour 30 jours ?')) {
    try {
      await axios.post(`/api/v1/super-admin/pharmacies/${id}/renew`)
      await fetchPharmacies()
      alert('Abonnement renouvelé avec succès (+30 jours)')
    } catch (error) {
      alert('Erreur lors du renouvellement')
    }
  }
}

// Sauvegarder une pharmacie
const savePharmacy = async () => {
  try {
    if (isEditing.value) {
      await axios.put(`/api/v1/admin/pharmacies/${form.value.id}`, {
        name: form.value.name,
        email: form.value.email,
        phone: form.value.phone,
        address: form.value.address,
        is_active: form.value.is_active
      })
      alert('Pharmacie modifiée avec succès')
    } else {
      await axios.post('/api/v1/admin/pharmacies', {
        name: form.value.name,
        email: form.value.email,
        phone: form.value.phone,
        address: form.value.address,
        is_active: form.value.is_active
      })
      alert('Pharmacie créée avec succès')
    }
    await fetchPharmacies()
    closeModal()
  } catch (err) {
    console.error('Erreur:', err)
    alert(err.response?.data?.message || 'Erreur lors de l\'enregistrement')
  }
}

// Modifier une pharmacie
const editPharmacy = (pharmacy) => {
  isEditing.value = true
  form.value = {
    id: pharmacy.id,
    name: pharmacy.name,
    email: pharmacy.email,
    phone: pharmacy.phone,
    address: pharmacy.address,
    is_active: pharmacy.is_active
  }
  showModal.value = true
}

// Supprimer une pharmacie
const deletePharmacy = async (id) => {
  if (confirm(' Supprimer cette pharmacie ? TOUTES les données associées seront supprimées !')) {
    try {
      await axios.delete(`/api/v1/admin/pharmacies/${id}`)
      await fetchPharmacies()
      alert('Pharmacie supprimée avec succès')
    } catch (err) {
      alert(err.response?.data?.message || 'Erreur lors de la suppression')
    }
  }
}

// Voir les statistiques d'une pharmacie
const viewStats = async (id) => {
  try {
    const response = await axios.get(`/api/v1/admin/pharmacies/${id}/stats`)
    stats.value = response.data
    selectedPharmacy.value = pharmacies.value.find(p => p.id === id)
    showStatsModal.value = true
  } catch (err) {
    console.error('Erreur:', err)
    alert('Erreur lors du chargement des statistiques')
  }
}

// Ouvrir le modal de création
const openCreateModal = () => {
  isEditing.value = false
  form.value = {
    id: null,
    name: '',
    email: '',
    phone: '',
    address: '',
    is_active: true
  }
  showModal.value = true
}

// Fermer le modal
const closeModal = () => {
  showModal.value = false
  isEditing.value = false
  form.value = {
    id: null,
    name: '',
    email: '',
    phone: '',
    address: '',
    is_active: true
  }
}

// Formater la date
const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

// Formater l'argent
const formatMoney = (value) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(value || 0)
}

// Classe pour l'abonnement
const getSubscriptionClass = (date) => {
  if (!date) return 'bg-gray-100 text-gray-800'
  const endDate = new Date(date)
  const now = new Date()
  const daysLeft = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24))
  
  if (daysLeft < 0) return 'bg-red-100 text-red-800'
  if (daysLeft < 30) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

onMounted(() => {
  fetchPharmacies()
})
</script>