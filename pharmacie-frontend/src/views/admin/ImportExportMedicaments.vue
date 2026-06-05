<!-- src/views/admin/ImportExportMedicaments.vue -->
<template>
  <div class="import-export">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Import/Export Médicaments</h1>
    
    <!-- 🔥 AJOUT : Sélecteur de pharmacie pour Super Admin -->
    <div v-if="authStore.isSuperAdmin" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">
         Sélectionner une pharmacie
      </label>
      <select v-model="selectedPharmacyId" class="border rounded-lg px-3 py-2 w-64">
        <option :value="null">-- Choisir une pharmacie --</option>
        <option v-for="pharmacy in pharmacies" :key="pharmacy.id" :value="pharmacy.id">
          {{ pharmacy.name }}
        </option>
      </select>
      <p class="text-xs text-gray-500 mt-2">
        Les médicaments seront importés dans la pharmacie sélectionnée
      </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      <!-- Export -->
      <div class="card">
        <h2 class="text-lg font-semibold mb-4"> Exporter</h2>
        <p class="text-gray-600 mb-4">Exporter tous les médicaments au format Excel</p>
        <button @click="exportMedicaments" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
          Télécharger Excel
        </button>
      </div>
      
      <!-- Import -->
      <div class="card">
        <h2 class="text-lg font-semibold mb-4"> Importer</h2>
        <p class="text-gray-600 mb-4">Importer des médicaments depuis un fichier Excel</p>
        
        <div class="mb-4">
          <a href="#" @click.prevent="downloadTemplate" class="text-green-600 hover:underline text-sm">
             Télécharger le modèle Excel
          </a>
        </div>
        
        <input type="file" ref="fileInput" @change="handleFile" accept=".xlsx,.xls,.csv" class="hidden">
        <button @click="$refs.fileInput.click()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700" :disabled="uploading">
          Choisir un fichier
        </button>
        
        <!-- Barre de progression -->
        <div v-if="uploading" class="mt-4">
          <div class="flex items-center mb-2">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-green-600 mr-2"></div>
            <span>Importation en cours...</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-green-600 h-2 rounded-full transition-all duration-300" :style="{ width: progress + '%' }"></div>
          </div>
          <p class="text-sm text-gray-500 mt-1">{{ progress }}%</p>
        </div>
        
        <!-- Résultat de l'import -->
        <div v-if="importResult" class="mt-4">
          <!-- Succès -->
          <div v-if="importResult.imported > 0" class="p-3 bg-green-50 text-green-700 rounded-lg mb-2">
            <div class="font-semibold"> Import réussi !</div>
            <div>{{ importResult.imported }} médicament(s) importé(s) avec succès</div>
          </div>
          
          <!-- Doublons ignorés -->
          <div v-if="importResult.duplicates?.length" class="p-3 bg-yellow-50 text-yellow-700 rounded-lg mb-2">
            <div class="font-semibold"> Médicaments ignorés (déjà existants dans cette pharmacie)</div>
            <div>{{ importResult.duplicates.length }} médicament(s) déjà présents</div>
            <details class="mt-2 text-sm">
              <summary>Voir la liste</summary>
              <ul class="mt-2 list-disc pl-5">
                <li v-for="dup in importResult.duplicates" :key="dup">{{ dup }}</li>
              </ul>
            </details>
          </div>
          
          <!-- Erreurs -->
          <div v-if="importResult.errors?.length" class="p-3 bg-red-50 text-red-700 rounded-lg">
            <div class="font-semibold"> Erreurs rencontrées</div>
            <div>{{ importResult.errors.length }} erreur(s)</div>
            <details class="mt-2 text-sm">
              <summary>Voir les détails</summary>
              <ul class="mt-2 list-disc pl-5">
                <li v-for="err in importResult.errors" :key="err">{{ err }}</li>
              </ul>
            </details>
          </div>
          
          <!-- Résumé global -->
          <div class="mt-3 p-2 bg-gray-100 text-gray-600 rounded-lg text-sm">
             Résumé : {{ importResult.totalLignes || 0 }} ligne(s) traitées |
             {{ importResult.imported || 0 }} importé(s) |
             {{ importResult.duplicates?.length || 0 }} doublon(s) |
             {{ importResult.errors?.length || 0 }} erreur(s)
          </div>
        </div>
      </div>
    </div>
    
    <!-- Formulaire rapide d'ajout -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
      <h2 class="text-lg font-semibold mb-4">Ajout rapide</h2>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input v-model="quickForm.nom" placeholder="Nom" class="border rounded-lg px-3 py-2">
        <input v-model="quickForm.dci" placeholder="DCI" class="border rounded-lg px-3 py-2">
        <select v-model="quickForm.forme" class="border rounded-lg px-3 py-2">
          <option value="">Forme</option>
          <option v-for="forme in formes" :key="forme" :value="forme">{{ forme }}</option>
        </select>
        <input v-model="quickForm.dosage" placeholder="Dosage" class="border rounded-lg px-3 py-2">
        <select v-model="quickForm.categorie_id" class="border rounded-lg px-3 py-2">
          <option :value="null">Catégorie</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nom }}</option>
        </select>
        <input type="number" v-model="quickForm.prix_vente" placeholder="Prix vente" class="border rounded-lg px-3 py-2">
        <input type="number" v-model="quickForm.stock_actuel" placeholder="Stock" class="border rounded-lg px-3 py-2">
        <button @click="quickAdd" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Ajouter</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const fileInput = ref(null)
const uploading = ref(false)
const progress = ref(0)
const currentLine = ref(0)
const totalLines = ref(0)
const importResult = ref(null)
const categories = ref([])
const pharmacies = ref([])
const selectedPharmacyId = ref(null)

const formes = ref([
  'Comprimé', 'Comprimé effervescent', 'Comprimé à croquer',
  'Gélule', 'Capsule', 'Gélule à libération prolongée',
  'Sirop', 'Solution buvable', 'Suspension buvable', 'Gouttes buvables',
  'Injectable (ampoule)', 'Injectable (flacon)', 'Poudre pour injection',
  'Pommade', 'Crème', 'Gel', 'Onguent', 'Patch transdermique',
  'Suppositoire', 'Ovule', 'Collyre', 'Spray nasal', 'Gouttes nasales',
  'Inhalateur', 'Aérosol', 'Nébuliseur', 'Poudre', 'Granulé', 'Sachet'
])

const quickForm = ref({
  nom: '', dci: '', forme: '', dosage: '', categorie_id: null,
  prix_vente: 0, stock_actuel: 0
})

const loadCategories = async () => {
  try {
    const response = await axios.get('/api/v1/categories')
    categories.value = response.data.data || []
  } catch (error) {
    console.error('Erreur chargement catégories:', error)
  }
}

const loadPharmacies = async () => {
  if (authStore.isSuperAdmin) {
    try {
      const response = await axios.get('/api/v1/admin/pharmacies')
      pharmacies.value = response.data.data || []
    } catch (error) {
      console.error('Erreur chargement pharmacies:', error)
    }
  }
}

const exportMedicaments = async () => {
  window.open('http://127.0.0.1:8000/api/v1/medicaments/export', '_blank')
}

const downloadTemplate = async () => {
  window.open('http://127.0.0.1:8000/api/v1/medicaments/template', '_blank')
}

const handleFile = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  const formData = new FormData()
  formData.append('file', file)
  
  // 🔥 AJOUT : Pour Super Admin, envoyer pharmacy_id
  if (authStore.isSuperAdmin && selectedPharmacyId.value) {
    formData.append('pharmacy_id', selectedPharmacyId.value)
  }
  
  uploading.value = true
  importResult.value = null
  progress.value = 0
  currentLine.value = 0
  totalLines.value = 0
  
  try {
    const response = await axios.post('/api/v1/medicaments/import', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: (progressEvent) => {
        if (progressEvent.total) {
          const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          progress.value = percentCompleted
        }
      }
    })
    
    importResult.value = response.data
    
    if (response.data.imported > 0) {
      setTimeout(() => {
        window.location.reload()
      }, 3000)
    }
  } catch (error) {
    console.error('Erreur import:', error)
    if (error.response?.data?.message) {
      importResult.value = {
        imported: 0,
        duplicates: [],
        errors: [error.response.data.message]
      }
    } else {
      alert('Erreur lors de l\'import')
    }
  } finally {
    uploading.value = false
    fileInput.value.value = ''
  }
}

const quickAdd = async () => {
  if (!quickForm.value.nom || !quickForm.value.dci) {
    alert('Le nom et la DCI sont obligatoires')
    return
  }
  
  try {
    await axios.post('/api/v1/medicaments', quickForm.value)
    alert('Médicament ajouté avec succès')
    quickForm.value = { nom: '', dci: '', forme: '', dosage: '', categorie_id: null, prix_vente: 0, stock_actuel: 0 }
    setTimeout(() => {
      window.location.reload()
    }, 1000)
  } catch (error) {
    const message = error.response?.data?.message || 'Erreur lors de l\'ajout'
    alert(' ' + message)
  }
}

onMounted(() => {
  loadCategories()
  loadPharmacies()
})
</script>