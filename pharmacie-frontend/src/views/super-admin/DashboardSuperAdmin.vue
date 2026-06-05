<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6"> Dashboard Super Admin</h1>
    
    <!-- NOTIFICATION NOUVELLES INSCRIPTIONS -->
    <div v-if="newPharmacies.length > 0" class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
      <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
            <span class="text-xl"></span>
          </div>
          <div>
            <p class="font-semibold text-blue-800">Nouvelles pharmacies en attente de validation</p>
            <p class="text-sm text-blue-600">{{ newPharmacies.length }} pharmacie(s) en attente d'activation</p>
          </div>
        </div>
        <button @click="goToPharmacies" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
          Voir →
        </button>
      </div>
      <div class="mt-3">
        <div v-for="pharmacy in newPharmacies.slice(0, 3)" :key="pharmacy.id" class="flex justify-between items-center py-1 text-sm">
          <span>{{ pharmacy.name }} ({{ pharmacy.email }})</span>
          <button @click="activatePharmacy(pharmacy.id)" class="bg-green-600 text-white px-2 py-1 rounded text-xs">
            Activer
          </button>
        </div>
        <div v-if="newPharmacies.length > 3" class="text-xs text-blue-600 mt-1">
          + {{ newPharmacies.length - 3 }} autre(s) pharmacie(s)
        </div>
      </div>
    </div>
    
    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total pharmacies</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.total_pharmacies || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
            <span class="text-xl"></span>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Pharmacies actives</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.active_pharmacies || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <span class="text-xl"></span>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Pharmacies inactives</p>
            <p class="text-2xl font-bold text-orange-600">{{ stats.inactive_pharmacies || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
            <span class="text-xl"></span>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total utilisateurs</p>
            <p class="text-2xl font-bold">{{ stats.total_users || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
            <span class="text-xl"></span>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">CA total</p>
            <p class="text-2xl font-bold text-green-600">{{ formatMoney(stats.total_revenue) }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
            <span class="text-xl"></span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="font-semibold"> Top 5 pharmacies (CA)</h2>
        </div>
        <div class="p-6">
          <div v-for="(pharmacy, index) in stats.top_pharmacies" :key="pharmacy.id" class="flex justify-between items-center py-2 border-b last:border-0">
            <div class="flex items-center gap-3">
              <span class="w-6 text-gray-500">{{ index + 1 }}</span>
              <span class="font-medium">{{ pharmacy.name }}</span>
              <span v-if="!pharmacy.is_active" class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Inactive</span>
            </div>
            <span class="text-green-600 font-semibold">{{ formatMoney(pharmacy.revenue) }}</span>
          </div>
          <div v-if="!stats.top_pharmacies?.length" class="text-center text-gray-500 py-4">
            Aucune donnée disponible
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="font-semibold"> Utilisateurs par rôle</h2>
        </div>
        <div class="p-6">
          <div class="flex justify-between items-center py-2 border-b">
            <span> Super Admin</span>
            <span class="font-semibold">{{ stats.users_by_role?.super_admin || 0 }}</span>
          </div>
          <div class="flex justify-between items-center py-2 border-b">
            <span> Administrateurs</span>
            <span class="font-semibold">{{ stats.users_by_role?.admin || 0 }}</span>
          </div>
          <div class="flex justify-between items-center py-2 border-b">
            <span> Pharmaciens</span>
            <span class="font-semibold">{{ stats.users_by_role?.pharmacien || 0 }}</span>
          </div>
          <div class="flex justify-between items-center py-2 border-b">
            <span> Caissiers</span>
            <span class="font-semibold">{{ stats.users_by_role?.caissier || 0 }}</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Liste des pharmacies avec pagination -->
    <div class="bg-white rounded-lg shadow mb-8">
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="font-semibold"> Liste des pharmacies</h2>
        <div class="flex gap-4">
          <select v-model="pharmacyFilter" @change="resetPagination" class="border rounded-lg px-3 py-1 text-sm">
            <option value="all">Toutes les pharmacies</option>
            <option value="active">Actives uniquement</option>
            <option value="inactive">Inactives uniquement</option>
          </select>
          <button @click="refreshData" class="text-blue-600 hover:text-blue-800 text-sm"> Rafraîchir</button>
        </div>
      </div>
      <div class="p-6">
        <div v-for="pharmacy in paginatedPharmacies" :key="pharmacy.id" class="flex justify-between items-center py-3 border-b last:border-0">
          <div>
            <div class="flex items-center gap-2">
              <span class="font-medium">{{ pharmacy.name }}</span>
              <span v-if="!pharmacy.is_active" class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">En attente</span>
              <span v-else class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Actif</span>
            </div>
            <div class="text-sm text-gray-500">{{ pharmacy.email }} | {{ pharmacy.phone || 'Pas de téléphone' }}</div>
          </div>
          <div class="flex gap-2">
            <button v-if="!pharmacy.is_active" @click="activatePharmacy(pharmacy.id)" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
               Activer
            </button>
            <button v-else @click="deactivatePharmacy(pharmacy.id)" class="bg-orange-600 text-white px-3 py-1 rounded text-sm hover:bg-orange-700">
               Suspendre
            </button>
            <button @click="openStatsModal(pharmacy)" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
               Stats
            </button>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="filteredPharmacies.length > 0" class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
          <div class="text-sm text-gray-500">
            Affichage de {{ (currentPage - 1) * itemsPerPage + 1 }} à {{ Math.min(currentPage * itemsPerPage, filteredPharmacies.length) }} sur {{ filteredPharmacies.length }} pharmacies
          </div>
          <div class="flex gap-2">
            <button 
              @click="currentPage--" 
              :disabled="currentPage === 1"
              class="px-3 py-1 border rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 transition-colors"
            >
              ◀ Précédent
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
              class="px-3 py-1 border rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 transition-colors"
            >
              Suivant ▶
            </button>
          </div>
        </div>
        <div v-if="filteredPharmacies.length === 0" class="text-center text-gray-500 py-4">
          Aucune pharmacie trouvée
        </div>
      </div>
    </div>
    
    <!-- Abonnements expirant -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
        <h2 class="font-semibold text-yellow-800"> Abonnements expirant dans 30 jours</h2>
      </div>
      <div class="p-6">
        <div v-for="sub in stats.expiring_subscriptions" :key="sub.id" class="flex justify-between items-center py-2 border-b">
          <div>
            <span class="font-medium">{{ sub.pharmacy_name }}</span>
            <span class="text-sm text-gray-500 ml-2">Expire le {{ formatDate(sub.subscription_ends_at) }}</span>
          </div>
          <button @click="renewSubscription(sub.id)" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
             Renouveler
          </button>
        </div>
        <div v-if="!stats.expiring_subscriptions?.length" class="text-center text-gray-500 py-4">
          ✅ Aucun abonnement expirant bientôt
        </div>
      </div>
    </div>

    <!-- ==================== MODAL STATISTIQUES ==================== -->
    <Teleport to="body">
      <Transition name="modal-slide-down" @after-leave="cleanupModal">
        <div v-if="showStatsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-end justify-center z-50" @click.self="closeStatsModal">
          <div class="bg-white rounded-t-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] overflow-y-auto">
            
            <!-- En-tête avec dégradé -->
            <div class="relative bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5 rounded-t-2xl sticky top-0 z-10">
              <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mt-16 -mr-16"></div>
              <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -mb-12 -ml-12"></div>
              
              <div class="relative z-10 flex justify-between items-start">
                <div class="flex items-center gap-4">
                  <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <span class="text-3xl"></span>
                  </div>
                  <div>
                    <h2 class="text-2xl font-bold text-white">{{ selectedPharmacy?.name }}</h2>
                    <p class="text-indigo-100 text-sm mt-1">Statistiques détaillées de la pharmacie</p>
                  </div>
                </div>
                <button @click="closeStatsModal" class="text-white/80 hover:text-white transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>
            
            <!-- Corps -->
            <div class="p-6">
              <!-- Cartes KPI modernes -->
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 text-center">
                  <div class="w-12 h-12 bg-blue-200 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                  </div>
                  <p class="text-2xl font-bold text-blue-700">{{ statsData.users_count || 0 }}</p>
                  <p class="text-xs text-blue-600 font-medium">Utilisateurs</p>
                </div>
                
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 text-center">
                  <div class="w-12 h-12 bg-green-200 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                  </div>
                  <p class="text-2xl font-bold text-green-700">{{ statsData.medicaments_count || 0 }}</p>
                  <p class="text-xs text-green-600 font-medium">Médicaments</p>
                </div>
                
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 text-center">
                  <div class="w-12 h-12 bg-purple-200 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a4 4 0 11-6 0 4 4 0 016 0z"/>
                    </svg>
                  </div>
                  <p class="text-2xl font-bold text-purple-700">{{ statsData.clients_count || 0 }}</p>
                  <p class="text-xs text-purple-600 font-medium">Clients</p>
                </div>
                
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 text-center">
                  <div class="w-12 h-12 bg-orange-200 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                  </div>
                  <p class="text-2xl font-bold text-orange-700">{{ statsData.ventes_count || 0 }}</p>
                  <p class="text-xs text-orange-600 font-medium">Ventes</p>
                </div>
              </div>
              
              <!-- Carte Chiffre d'affaires -->
              <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl p-6 mb-8 text-white">
                <div class="flex justify-between items-center">
                  <div>
                    <p class="text-emerald-100 text-sm font-medium">Chiffre d'affaires total</p>
                    <p class="text-4xl font-bold mt-1">{{ formatMoney(statsData.chiffre_affaires) }}</p>
                    <div class="flex items-center gap-2 mt-2">
                      <span class="text-emerald-200 text-xs"> Toutes ventes confondues</span>
                    </div>
                  </div>
                  <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
              </div>
              
              <!-- Informations complémentaires -->
              <div class="bg-gray-50 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Informations générales
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-500">Email</span>
                    <span class="font-medium text-gray-800">{{ selectedPharmacy?.email || '-' }}</span>
                  </div>
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-500"> Téléphone</span>
                    <span class="font-medium text-gray-800">{{ selectedPharmacy?.phone || '-' }}</span>
                  </div>
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-500"> Date de création</span>
                    <span class="font-medium text-gray-800">{{ formatDate(selectedPharmacy?.created_at) }}</span>
                  </div>
                  <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-500"> Statut</span>
                    <span :class="selectedPharmacy?.is_active ? 'text-green-600 bg-green-100' : 'text-orange-600 bg-orange-100'" class="font-medium px-2 py-0.5 rounded-full text-xs">
                      {{ selectedPharmacy?.is_active ? 'Actif' : 'Inactif' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end border-t border-gray-200">
              <button @click="closeStatsModal" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors text-gray-700">
                Fermer
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const stats = ref({
  total_pharmacies: 0,
  total_users: 0,
  active_pharmacies: 0,
  inactive_pharmacies: 0,
  total_revenue: 0,
  users_by_role: {},
  top_pharmacies: [],
  expiring_subscriptions: []
})

const pharmacies = ref([])
const usersByPharmacy = ref([])
const selectedPharmacyId = ref(null)
const pharmacyFilter = ref('all')

// Pagination
const currentPage = ref(1)
const itemsPerPage = 5

// Modal stats
const showStatsModal = ref(false)
const selectedPharmacy = ref(null)
const statsData = ref({
  users_count: 0,
  medicaments_count: 0,
  clients_count: 0,
  ventes_count: 0,
  chiffre_affaires: 0
})

// Pharmacies filtrées
const filteredPharmacies = computed(() => {
  let result = [...pharmacies.value]
  if (pharmacyFilter.value === 'active') {
    result = result.filter(p => p.is_active)
  } else if (pharmacyFilter.value === 'inactive') {
    result = result.filter(p => !p.is_active)
  }
  return result
})

// Pagination des pharmacies
const paginatedPharmacies = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredPharmacies.value.slice(start, end)
})

// Nombre total de pages
const totalPages = computed(() => {
  return Math.ceil(filteredPharmacies.value.length / itemsPerPage)
})

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

// Pharmacies en attente
const newPharmacies = computed(() => {
  return pharmacies.value.filter(p => !p.is_active)
})

const resetPagination = () => {
  currentPage.value = 1
}

const refreshData = () => {
  fetchPharmacies()
  fetchStats()
}

const formatMoney = (value) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(value || 0)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/v1/super-admin/stats')
    stats.value = response.data
  } catch (error) {
    console.error('Erreur chargement stats:', error)
  }
}

const fetchPharmacies = async () => {
  try {
    const response = await axios.get('/api/v1/admin/pharmacies')
    pharmacies.value = response.data.data || []
    const inactiveCount = pharmacies.value.filter(p => !p.is_active).length
    stats.value.inactive_pharmacies = inactiveCount
    resetPagination()
  } catch (error) {
    console.error('Erreur chargement pharmacies:', error)
  }
}

const fetchUsersByPharmacy = async () => {
  try {
    const url = selectedPharmacyId.value 
      ? `/api/v1/super-admin/users?pharmacy_id=${selectedPharmacyId.value}`
      : '/api/v1/super-admin/users'
    const response = await axios.get(url)
    usersByPharmacy.value = response.data
  } catch (error) {
    console.error('Erreur chargement utilisateurs:', error)
  }
}

const activatePharmacy = async (id) => {
  if (confirm(' Activer cette pharmacie ?')) {
    try {
      await axios.put(`/api/v1/admin/pharmacies/${id}`, { is_active: true })
      alert(' Pharmacie activée avec succès')
      fetchPharmacies()
      fetchStats()
    } catch (error) {
      alert(' Erreur lors de l\'activation')
    }
  }
}

const deactivatePharmacy = async (id) => {
  if (confirm(' Suspendre cette pharmacie ?')) {
    try {
      await axios.put(`/api/v1/admin/pharmacies/${id}`, { is_active: false })
      alert(' Pharmacie suspendue avec succès')
      fetchPharmacies()
      fetchStats()
    } catch (error) {
      alert(' Erreur lors de la suspension')
    }
  }
}

const openStatsModal = async (pharmacy) => {
  selectedPharmacy.value = pharmacy
  showStatsModal.value = true
  try {
    const response = await axios.get(`/api/v1/admin/pharmacies/${pharmacy.id}/stats`)
    statsData.value = response.data
  } catch (error) {
    console.error('Erreur chargement stats:', error)
    statsData.value = {
      users_count: 0,
      medicaments_count: 0,
      clients_count: 0,
      ventes_count: 0,
      chiffre_affaires: 0
    }
  }
}

const closeStatsModal = () => {
  showStatsModal.value = false
  setTimeout(() => {
    selectedPharmacy.value = null
    statsData.value = {
      users_count: 0,
      medicaments_count: 0,
      clients_count: 0,
      ventes_count: 0,
      chiffre_affaires: 0
    }
  }, 300)
}

const cleanupModal = () => {
  selectedPharmacy.value = null
  statsData.value = {
    users_count: 0,
    medicaments_count: 0,
    clients_count: 0,
    ventes_count: 0,
    chiffre_affaires: 0
  }
}

const goToPharmacies = () => {
  router.push('/super-admin/pharmacies')
}

const renewSubscription = async (pharmacyId) => {
  if (confirm(' Renouveler l\'abonnement pour 30 jours ?')) {
    try {
      await axios.post(`/api/v1/super-admin/pharmacies/${pharmacyId}/renew`)
      alert(' Abonnement renouvelé avec succès')
      fetchStats()
    } catch (error) {
      alert(' Erreur lors du renouvellement')
    }
  }
}

onMounted(() => {
  fetchStats()
  fetchPharmacies()
  fetchUsersByPharmacy()
})
</script>

<style scoped>
.modal-slide-down-enter-active,
.modal-slide-down-leave-active {
  transition: all 0.3s ease-out;
}

.modal-slide-down-enter-from,
.modal-slide-down-leave-to {
  opacity: 0;
  transform: translateY(-100%);
}

.modal-slide-down-enter-to,
.modal-slide-down-leave-from {
  opacity: 1;
  transform: translateY(0);
}
</style>