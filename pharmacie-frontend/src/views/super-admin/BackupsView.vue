<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800"> Sauvegardes</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez les sauvegardes automatiques de la plateforme</p>
      </div>
      <div class="flex gap-3">
        <button @click="createBackup" :disabled="creating" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
          <svg v-if="creating" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Sauvegarde manuelle
        </button>
        <button @click="cleanBackups" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center gap-2">
           Nettoyer
        </button>
      </div>
    </div>
    
    <!-- Informations -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-blue-700">
            Sauvegarde automatique quotidienne à <strong>02h00</strong>.
            Conservation : <strong>7 jours</strong>.
          </p>
        </div>
      </div>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
    </div>
    
    <!-- Tableau des sauvegardes -->
    <div v-else class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom du fichier</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Taille</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="backup in backups" :key="backup.name" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ backup.name }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ backup.date }}</td>
            <td class="px-4 py-3 text-right text-sm text-gray-600">{{ backup.size }}</td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center space-x-3">
                <a :href="`/api/v1/backups/download/${backup.name}`" class="text-blue-600 hover:text-blue-800" title="Télécharger">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                  </svg>
                </a>
                <button @click="deleteBackup(backup.name)" class="text-red-600 hover:text-red-800" title="Supprimer">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="backups.length === 0 && !loading">
            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
              Aucune sauvegarde trouvée
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const backups = ref([])
const loading = ref(false)
const creating = ref(false)

const loadBackups = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/backups')
    backups.value = response.data.data || []
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors du chargement des sauvegardes')
  } finally {
    loading.value = false
  }
}

const createBackup = async () => {
  creating.value = true
  try {
    const response = await axios.post('/api/v1/backups/create')
    alert(response.data.message)
    loadBackups()
  } catch (error) {
    alert('Erreur lors de la création de la sauvegarde')
  } finally {
    creating.value = false
  }
}

const cleanBackups = async () => {
  if (confirm('Nettoyer les anciennes sauvegardes ?')) {
    try {
      const response = await axios.post('/api/v1/backups/clean')
      alert(response.data.message)
      loadBackups()
    } catch (error) {
      alert('Erreur lors du nettoyage')
    }
  }
}

const deleteBackup = async (filename) => {
  if (confirm(`Supprimer la sauvegarde ${filename} ?`)) {
    try {
      await axios.delete(`/api/v1/backups/${filename}`)
      alert('Sauvegarde supprimée')
      loadBackups()
    } catch (error) {
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(() => {
  loadBackups()
})
</script>