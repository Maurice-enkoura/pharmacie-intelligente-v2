sur la page client il manque Clients
Fiche client : coordonnées, historique achats
Médicaments chroniques (pour rappel automatique), ici faut afficher tout les client d'une phramacie sa pharmacie uniquement <!-- src/views/admin/UtilisateursListView.vue -->
<template>
  <div class="utilisateurs-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Utilisateurs</h1>
      <button @click="openCreateModal" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
        + Nouvel utilisateur
      </button>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
      <p class="mt-2 text-gray-500">Chargement...</p>
    </div>
    
    <div v-else class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Rôle</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date création</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in filteredUtilisateurs" :key="user.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 font-medium">{{ user.name }}</td>
            <td class="px-4 py-3">{{ user.email }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="getRoleClass(user.role)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ getRoleLabel(user.role) }}
              </span>
            </td>
            <td class="px-4 py-3 text-center text-sm">{{ formatDate(user.created_at) }}</td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center space-x-2">
                <button 
                  @click="editUser(user)" 
                  class="text-yellow-600 hover:text-yellow-800" 
                  title="Modifier"
                >
                  
                </button>
                <button 
                  v-if="canDeleteUser(user)"
                  @click="confirmDelete(user)" 
                  class="text-red-600 hover:text-red-800" 
                  title="Supprimer"
                >
                  
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="filteredUtilisateurs.length === 0 && !loading" class="text-center py-8 text-gray-500">
        Aucun utilisateur trouvé
      </div>
    </div>
    
    <!-- Modal création/modification -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">
          {{ isEditing ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}
        </h3>
        
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
           {{ errorMessage }}
        </div>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Nom *</label>
            <input 
              type="text" 
              v-model="userForm.name" 
              class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
              required
            >
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Email *</label>
            <input 
              type="email" 
              v-model="userForm.email" 
              class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
              required
            >
          </div>
          <div v-if="!isEditing">
            <label class="block text-sm text-gray-600 mb-1">Mot de passe *</label>
            <input 
              type="password" 
              v-model="userForm.password" 
              class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
              required
            >
          </div>
          <div v-if="isEditing && userForm.password">
            <label class="block text-sm text-gray-600 mb-1">Nouveau mot de passe (optionnel)</label>
            <input 
              type="password" 
              v-model="userForm.password" 
              class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
              placeholder="Laisser vide pour ne pas changer"
            >
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Rôle *</label>
            <select v-model="userForm.role" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
              <option value="caissier"> Caissier</option>
              <option value="pharmacien"> Pharmacien</option>
              <option value="admin"> Administrateur</option>
            </select>
          </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Annuler</button>
          <button @click="saveUser" :disabled="saving" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
            {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>
      </div>
    </div>
    
    <!-- Modal confirmation suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Confirmer la suppression</h3>
        <p>Voulez-vous vraiment supprimer l'utilisateur <strong>{{ userToDelete?.name }}</strong> ?</p>
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="showDeleteModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Annuler</button>
          <button @click="deleteUser" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const authStore = useAuthStore()
const currentUserId = computed(() => authStore.user?.id)
const currentUserRole = computed(() => authStore.user?.role)

const utilisateurs = ref([])
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const userToDelete = ref(null)
const userToEdit = ref(null)
const errorMessage = ref('')

const userForm = ref({
  name: '',
  email: '',
  password: '',
  role: 'caissier'
})

// Filtrer les utilisateurs (cacher super_admin pour les non-super_admin)
const filteredUtilisateurs = computed(() => {
  if (currentUserRole.value === 'super_admin') {
    return utilisateurs.value
  }
  // Cacher le super_admin pour les autres rôles
  return utilisateurs.value.filter(user => user.role !== 'super_admin')
})

const getRoleLabel = (role) => {
  const labels = {
    super_admin: ' Super Admin',
    admin: ' Administrateur',
    pharmacien: ' Pharmacien',
    caissier: ' Caissier'
  }
  return labels[role] || role
}

const getRoleClass = (role) => {
  const classes = {
    super_admin: 'bg-purple-100 text-purple-700',
    admin: 'bg-green-100 text-green-700',
    pharmacien: 'bg-blue-100 text-blue-700',
    caissier: 'bg-gray-100 text-gray-700'
  }
  return classes[role] || 'bg-gray-100'
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const canDeleteUser = (user) => {
  // Ne pas permettre la suppression de son propre compte
  if (user.id === currentUserId.value) return false
  // Ne pas permettre la suppression du super_admin (sauf si super_admin lui-même)
  if (user.role === 'super_admin' && currentUserRole.value !== 'super_admin') return false
  return true
}

const loadUtilisateurs = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/utilisateurs')
    utilisateurs.value = response.data
  } catch (error) {
    console.error('Erreur chargement utilisateurs:', error)
    if (error.response?.status === 403) {
      errorMessage.value = 'Vous n\'avez pas les droits pour voir les utilisateurs'
      setTimeout(() => { errorMessage.value = '' }, 3000)
    }
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  isEditing.value = false
  userForm.value = {
    name: '',
    email: '',
    password: '',
    role: 'caissier'
  }
  errorMessage.value = ''
  showModal.value = true
}

const editUser = (user) => {
  userToEdit.value = user
  isEditing.value = true
  userForm.value = {
    name: user.name,
    email: user.email,
    password: '',
    role: user.role
  }
  errorMessage.value = ''
  showModal.value = true
}

const saveUser = async () => {
  saving.value = true
  errorMessage.value = ''
  
  try {
    const pharmacyId = authStore.user?.pharmacy_id
    const currentUserRole = authStore.user?.role
    
    // Vérifier que l'admin a bien une pharmacie
    if (currentUserRole === 'admin' && !pharmacyId) {
      errorMessage.value = 'Vous n\'êtes pas associé à une pharmacie'
      saving.value = false
      return
    }
    
    if (isEditing.value) {
      // Mise à jour d'un utilisateur
      const updateData = {
        name: userForm.value.name,
        email: userForm.value.email,
        role: userForm.value.role,
        pharmacy_id: pharmacyId
      }
      
      if (userForm.value.password) {
        updateData.password = userForm.value.password
      }
      
      const response = await axios.put(`/api/v1/utilisateurs/${userToEdit.value.id}`, updateData)
      
      if (response.data.success === false) {
        throw new Error(response.data.message)
      }
      
      alert('✅ Utilisateur modifié avec succès')
      
    } else {
      // Création d'un nouvel utilisateur
      if (!userForm.value.password) {
        errorMessage.value = 'Le mot de passe est obligatoire'
        saving.value = false
        return
      }
      
      // 🔥 IMPORTANT : Un admin ne peut pas créer de super_admin
      if (currentUserRole === 'admin' && userForm.value.role === 'super_admin') {
        errorMessage.value = 'Vous ne pouvez pas créer un compte Super Admin'
        saving.value = false
        return
      }
      
      const response = await axios.post('/api/v1/utilisateurs', {
        name: userForm.value.name,
        email: userForm.value.email,
        password: userForm.value.password,
        role: userForm.value.role,
        pharmacy_id: pharmacyId
      })
      
      if (response.data.success === false) {
        throw new Error(response.data.message)
      }
      
      alert('✅ Utilisateur créé avec succès')
    }
    
    closeModal()
    loadUtilisateurs()
    
  } catch (error) {
    console.error('Erreur:', error)
    
    // Afficher les erreurs de validation détaillées
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat().join(', ')
      errorMessage.value = errorMessages
    } else {
      const message = error.response?.data?.message || error.message || 'Erreur lors de l\'enregistrement'
      errorMessage.value = message
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = (user) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const deleteUser = async () => {
  if (!userToDelete.value) return
  
  try {
    const response = await axios.delete(`/api/v1/utilisateurs/${userToDelete.value.id}`)
    
    if (response.data.success === false) {
      throw new Error(response.data.message)
    }
    
    alert(' Utilisateur supprimé avec succès')
    showDeleteModal.value = false
    userToDelete.value = null
    loadUtilisateurs()
    
  } catch (error) {
    console.error('Erreur:', error)
    const message = error.response?.data?.message || 'Erreur lors de la suppression'
    alert('❌ ' + message)
  }
}

const closeModal = () => {
  showModal.value = false
  isEditing.value = false
  userForm.value = { name: '', email: '', password: '', role: 'caissier' }
  userToEdit.value = null
  errorMessage.value = ''
}

onMounted(() => {
  loadUtilisateurs()
})
</script>

<style scoped>
.utilisateurs-list {
  padding: 1.5rem;
}
</style>