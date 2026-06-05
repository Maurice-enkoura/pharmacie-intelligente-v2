<template>
  <div class="retours-fournisseurs p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800"> Retours fournisseurs</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez les retours de médicaments (périmés, défectueux, etc.)</p>
      </div>
      <button @click="showModal = true" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouveau retour
      </button>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <select v-model="filters.statut" @change="loadRetours" class="border rounded-lg px-3 py-2">
          <option :value="null"> Tous les statuts</option>
          <option value="en_attente"> En attente</option>
          <option value="valide"> Validé</option>
          <option value="refuse"> Refusé</option>
          <option value="traite"> Traité</option>
        </select>
        <input type="date" v-model="filters.date_debut" @change="loadRetours" class="border rounded-lg px-3 py-2">
        <input type="date" v-model="filters.date_fin" @change="loadRetours" class="border rounded-lg px-3 py-2">
        <button @click="loadRetours" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filtrer</button>
      </div>
    </div>
    
    <!-- Tableau -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left"> N° Retour</th>
            <th class="px-4 py-3 text-left"> Fournisseur</th>
            <th class="px-4 py-3 text-center"> Date</th>
            <th class="px-4 py-3 text-center"> Montant</th>
            <th class="px-4 py-3 text-center"> Statut</th>
            <th class="px-4 py-3 text-center"> Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="retour in retours" :key="retour.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 font-medium">{{ retour.numero_retour }}</td>
            <td class="px-4 py-3">{{ retour.fournisseur?.nom }}</td>
            <td class="px-4 py-3 text-center">{{ formatDate(retour.date_retour) }}</td>
            <td class="px-4 py-3 text-center font-medium">{{ formatPrice(retour.montant_total) }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="getStatutClass(retour.statut)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ getStatutLabel(retour.statut) }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <button @click="voirDetails(retour)" class="text-blue-600 hover:text-blue-800"> Détails</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Modal création -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Nouveau retour fournisseur</h2>
        
        <form @submit.prevent="submitRetour">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium mb-1">Fournisseur *</label>
              <select v-model="form.fournisseur_id" required class="w-full border rounded-lg px-3 py-2">
                <option :value="null">-- Sélectionner --</option>
                <option v-for="four in fournisseurs" :key="four.id" :value="four.id">{{ four.nom }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Date retour *</label>
              <input type="date" v-model="form.date_retour" required class="w-full border rounded-lg px-3 py-2">
            </div>
          </div>
          
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Motif *</label>
            <select v-model="form.motif" required class="w-full border rounded-lg px-3 py-2">
              <option value="perime"> Périmé</option>
              <option value="defectueux"> Défectueux</option>
              <option value="erreur_commande"> Erreur de commande</option>
              <option value="autre"> Autre</option>
            </select>
          </div>
          
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Commentaire (optionnel)</label>
            <textarea v-model="form.motif_commentaire" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
          </div>
          
          <!-- Lignes de retour -->
          <div class="border-t pt-4 mt-4">
            <div class="flex justify-between items-center mb-3">
              <label class="font-medium"> Produits à retourner</label>
              <button type="button" @click="ajouterLigne" class="text-green-600 text-sm">+ Ajouter un produit</button>
            </div>
            
            <div v-for="(ligne, index) in form.lignes" :key="index" class="border rounded-lg p-3 mb-3 bg-gray-50">
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="text-xs font-medium">Médicament</label>
                  <select v-model="ligne.medicament_id" @change="loadLots(ligne)" class="w-full border rounded-lg px-2 py-1 text-sm">
                    <option :value="null">-- Choisir --</option>
                    <option v-for="med in medicaments" :key="med.id" :value="med.id">{{ med.nom }}</option>
                  </select>
                </div>
                <div>
                  <label class="text-xs font-medium">Lot</label>
                  <select v-model="ligne.stock_lot_id" class="w-full border rounded-lg px-2 py-1 text-sm">
                    <option :value="null">-- Choisir --</option>
                    <option v-for="lot in lots[ligne.medicament_id]" :key="lot.id" :value="lot.id">
                      {{ lot.lot_number }} (Stock: {{ lot.quantite_restante }})
                    </option>
                  </select>
                </div>
                <div>
                  <label class="text-xs font-medium">Quantité</label>
                  <input type="number" v-model="ligne.quantite" min="1" class="w-full border rounded-lg px-2 py-1 text-sm">
                </div>
                <div class="flex items-end">
                  <button type="button" @click="supprimerLigne(index)" class="text-red-600 text-sm">🗑️ Supprimer</button>
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex justify-end space-x-2 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg">Annuler</button>
            <button type="submit" :disabled="submitting" class="px-4 py-2 bg-green-600 text-white rounded-lg">
              {{ submitting ? 'Enregistrement...' : 'Créer le retour' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const retours = ref([])
const fournisseurs = ref([])
const medicaments = ref([])
const lots = ref({})
const loading = ref(false)
const submitting = ref(false)
const showModal = ref(false)

const filters = ref({
  statut: null,
  date_debut: '',
  date_fin: ''
})

const form = ref({
  fournisseur_id: null,
  date_retour: new Date().toISOString().split('T')[0],
  motif: 'perime',
  motif_commentaire: '',
  lignes: []
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price || 0)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const getStatutClass = (statut) => {
  const classes = {
    en_attente: 'bg-yellow-100 text-yellow-700',
    valide: 'bg-green-100 text-green-700',
    refuse: 'bg-red-100 text-red-700',
    traite: 'bg-blue-100 text-blue-700'
  }
  return classes[statut] || 'bg-gray-100'
}

const getStatutLabel = (statut) => {
  const labels = {
    en_attente: ' En attente',
    valide: ' Validé',
    refuse: ' Refusé',
    traite: ' Traité'
  }
  return labels[statut] || statut
}

const loadRetours = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/retours-fournisseurs', { params: filters.value })
    retours.value = response.data.data || []
  } catch (error) {
    console.error('Erreur:', error)
  } finally {
    loading.value = false
  }
}

const loadFournisseurs = async () => {
  try {
    const response = await axios.get('/api/v1/fournisseurs')
    fournisseurs.value = response.data.data || []
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const loadMedicaments = async () => {
  try {
    const response = await axios.get('/api/v1/medicaments')
    medicaments.value = response.data.data || []
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const loadLots = async (ligne) => {
  if (!ligne.medicament_id) return
  try {
    const response = await axios.get(`/api/v1/stock/lots/${ligne.medicament_id}`)
    lots.value[ligne.medicament_id] = response.data || []
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const ajouterLigne = () => {
  form.value.lignes.push({
    medicament_id: null,
    stock_lot_id: null,
    quantite: 1,
    motif: ''
  })
}

const supprimerLigne = (index) => {
  form.value.lignes.splice(index, 1)
}

const submitRetour = async () => {
  if (form.value.lignes.length === 0) {
    alert('Ajoutez au moins un produit à retourner')
    return
  }
  
  submitting.value = true
  try {
    await axios.post('/api/v1/retours-fournisseurs', form.value)
    alert(' Retour créé avec succès')
    closeModal()
    loadRetours()
  } catch (error) {
    console.error('Erreur:', error)
    alert(error.response?.data?.message || ' Erreur lors de la création')
  } finally {
    submitting.value = false
  }
}

const voirDetails = (retour) => {
  // TODO: Implémenter la vue détail
  console.log('Détails du retour:', retour)
}

const closeModal = () => {
  showModal.value = false
  form.value = {
    fournisseur_id: null,
    date_retour: new Date().toISOString().split('T')[0],
    motif: 'perime',
    motif_commentaire: '',
    lignes: []
  }
}

onMounted(() => {
  loadRetours()
  loadFournisseurs()
  loadMedicaments()
})
</script>