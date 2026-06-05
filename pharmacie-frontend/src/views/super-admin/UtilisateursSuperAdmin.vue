<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6"> Utilisateurs par pharmacie</h1>
    
    <!-- Filtre par pharmacie -->
    <div class="mb-6 flex gap-4">
      <select v-model="selectedPharmacyId" @change="resetAndFetch" class="border rounded-lg px-4 py-2">
        <option :value="null"> Toutes les pharmacies</option>
        <option v-for="pharmacy in pharmacies" :key="pharmacy.id" :value="pharmacy.id">
           {{ pharmacy.name }}
        </option>
      </select>
      <button @click="fetchUsers" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
         Filtrer
      </button>
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
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Nom</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Rôle</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pharmacie</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"> Date création</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 font-medium text-gray-900">{{ user.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ user.email }}</td>
              <td class="px-6 py-4">
                <span :class="getRoleClass(user.role)" class="px-2 py-1 text-xs rounded-full">
                  {{ getRoleLabel(user.role) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ user.pharmacy?.name || ' Super Admin' }}</td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
            </tr>
            <tr v-if="paginatedUsers.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                Aucun utilisateur trouvé
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div v-if="totalUsers > 0" class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Affichage de {{ startIndex }} à {{ endIndex }} sur {{ totalUsers }} utilisateur(s)
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

const users = ref([])
const pharmacies = ref([])
const selectedPharmacyId = ref(null)
const loading = ref(false)

// Pagination
const currentPage = ref(1)
const itemsPerPage = 10

// Utilisateurs paginés
const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return users.value.slice(start, end)
})

// Total des utilisateurs
const totalUsers = computed(() => users.value.length)

// Nombre total de pages
const totalPages = computed(() => Math.ceil(totalUsers.value / itemsPerPage))

// Index de début et fin
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage + 1)
const endIndex = computed(() => Math.min(currentPage.value * itemsPerPage, totalUsers.value))

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

const resetAndFetch = () => {
  resetPagination()
  fetchUsers()
}

const fetchPharmacies = async () => {
  try {
    const response = await axios.get('/api/v1/admin/pharmacies')
    pharmacies.value = response.data.data || []
  } catch (error) {
    console.error('Erreur chargement pharmacies:', error)
  }
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const url = selectedPharmacyId.value 
      ? `/api/v1/super-admin/users?pharmacy_id=${selectedPharmacyId.value}`
      : '/api/v1/super-admin/users'
    const response = await axios.get(url)
    users.value = response.data
    resetPagination()
  } catch (error) {
    console.error('Erreur chargement utilisateurs:', error)
  } finally {
    loading.value = false
  }
}

const getRoleClass = (role) => {
  const classes = {
    super_admin: 'bg-purple-100 text-purple-800',
    admin: 'bg-green-100 text-green-800',
    pharmacien: 'bg-blue-100 text-blue-800',
    caissier: 'bg-yellow-100 text-yellow-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

const getRoleLabel = (role) => {
  const labels = {
    super_admin: 'Super Admin',
    admin: 'Administrateur',
    pharmacien: ' Pharmacien',
    caissier: ' Caissier'
  }
  return labels[role] || role
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

onMounted(() => {
  fetchPharmacies()
  fetchUsers()
})
</script>