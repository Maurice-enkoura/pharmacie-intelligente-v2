<template>
  <div class="commandes-list p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Commandes fournisseurs</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez les commandes et les retours aux fournisseurs</p>
      </div>
      <div class="flex gap-3">
        <router-link to="/commandes/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Nouvelle commande
        </router-link>
        <router-link to="/retours-fournisseurs" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
          Retours fournisseur
        </router-link>
      </div>
    </div>
    
    <!-- Onglets -->
    <div class="border-b border-gray-200 mb-6">
      <nav class="flex space-x-8">
        <button 
          @click="activeTab = 'commandes'"
          :class="['pb-4 px-1 text-sm font-medium', activeTab === 'commandes' ? 'border-b-2 border-green-500 text-green-600' : 'text-gray-500 hover:text-gray-700']"
        >
           Commandes
        </button>
        <button 
          @click="activeTab = 'retours'"
          :class="['pb-4 px-1 text-sm font-medium', activeTab === 'retours' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-gray-500 hover:text-gray-700']"
        >
           Retours fournisseur
        </button>
      </nav>
    </div>
    
    <!-- ==================== SECTION COMMANDES ==================== -->
    <div v-if="activeTab === 'commandes'">
      <!-- Filtres Commandes -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <select v-model="filtersCommandes.statut" @change="searchCommandes" class="border rounded-lg px-3 py-2">
            <option :value="null">Tous les statuts</option>
            <option value="en_attente"> En attente</option>
            <option value="recue_partielle"> Réception partielle</option>
            <option value="recue_complete"> Réception complète</option>
          </select>
          <input type="date" v-model="filtersCommandes.date_debut" @change="searchCommandes" class="border rounded-lg px-3 py-2" placeholder="Date début">
          <input type="date" v-model="filtersCommandes.date_fin" @change="searchCommandes" class="border rounded-lg px-3 py-2" placeholder="Date fin">
          <button @click="searchCommandes" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700"> Filtrer</button>
        </div>
      </div>
      
      <!-- Tableau Commandes -->
      <div v-if="loadingCommandes" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
        <p class="mt-2 text-gray-500">Chargement des commandes...</p>
      </div>
      
      <div v-else class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Commande</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="commande in commandes" :key="commande.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ commande.numero_commande }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ commande.fournisseur?.nom || 'N/A' }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ formatDate(commande.date_commande) }}</td>
              <td class="px-4 py-3 text-right font-medium text-green-600">{{ formatPrice(commande.montant_total) }}</td>
              <td class="px-4 py-3 text-center">
                <span :class="getStatutClass(commande.statut)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getStatutLabel(commande.statut) }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <router-link 
                  v-if="commande.statut !== 'recue_complete'"
                  :to="`/commandes/${commande.id}/reception`" 
                  class="text-green-600 hover:text-green-800 text-sm flex items-center justify-center gap-1"
                >
                   Réception
                </router-link>
                <span v-else class="text-gray-400 text-sm"> Reçue</span>
              </td>
            </tr>
            <tr v-if="commandes.length === 0 && !loadingCommandes">
              <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                Aucune commande enregistrée
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination Commandes -->
      <div v-if="paginationCommandes && paginationCommandes.total > 0" class="mt-6 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Page {{ paginationCommandes.current_page }} sur {{ paginationCommandes.last_page }} ({{ paginationCommandes.total }} commandes)
        </div>
        <div class="flex space-x-2">
          <button @click="goToPageCommandes(paginationCommandes.current_page - 1)" :disabled="paginationCommandes.current_page <= 1" class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100">Précédent</button>
          <button @click="goToPageCommandes(paginationCommandes.current_page + 1)" :disabled="paginationCommandes.current_page >= paginationCommandes.last_page" class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100">Suivant</button>
        </div>
      </div>
    </div>
    
    <!-- ==================== SECTION RETOURS ==================== -->
    <div v-if="activeTab === 'retours'">
      <!-- Filtres Retours -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <select v-model="filtersRetours.statut" @change="searchRetours" class="border rounded-lg px-3 py-2">
            <option :value="null"> Tous les statuts</option>
            <option value="en_attente">En attente</option>
            <option value="valide"> Validé</option>
            <option value="refuse"> Refusé</option>
            <option value="traite"> Traité</option>
          </select>
          <input type="date" v-model="filtersRetours.date_debut" @change="searchRetours" class="border rounded-lg px-3 py-2">
          <input type="date" v-model="filtersRetours.date_fin" @change="searchRetours" class="border rounded-lg px-3 py-2">
          <button @click="searchRetours" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700"> Filtrer</button>
        </div>
      </div>
      
      <!-- Bouton Nouveau Retour -->
      <div class="flex justify-end mb-4">
        <button @click="showModal = true" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Nouveau retour
        </button>
      </div>
      
      <!-- Tableau Retours -->
      <div v-if="loadingRetours" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600"></div>
        <p class="mt-2 text-gray-500">Chargement des retours...</p>
      </div>
      
      <div v-else class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Retour</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="retour in retours" :key="retour.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ retour.numero_retour }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ retour.fournisseur?.nom }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ formatDate(retour.date_retour) }}</td>
              <td class="px-4 py-3 text-right font-medium text-orange-600">{{ formatPrice(retour.montant_total) }}</td>
              <td class="px-4 py-3 text-center">
                <span :class="getStatutRetourClass(retour.statut)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getStatutRetourLabel(retour.statut) }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <button @click="voirDetails(retour)" class="text-blue-600 hover:text-blue-800 text-sm">📄 Détails</button>
              </td>
            </tr>
            <tr v-if="retours.length === 0 && !loadingRetours">
              <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                Aucun retour enregistré
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- ==================== MODAL RETOUR ==================== -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold"> Nouveau retour fournisseur</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="submitRetour">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium mb-1"> Fournisseur *</label>
              <select v-model="form.fournisseur_id" required class="w-full border rounded-lg px-3 py-2">
                <option :value="null">-- Sélectionner --</option>
                <option v-for="four in fournisseurs" :key="four.id" :value="four.id">{{ four.nom }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1"> Date retour *</label>
              <input type="date" v-model="form.date_retour" required class="w-full border rounded-lg px-3 py-2">
            </div>
          </div>
          
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1"> Motif *</label>
            <select v-model="form.motif" required class="w-full border rounded-lg px-3 py-2">
              <option value="perime"> Périmé</option>
              <option value="defectueux"> Défectueux</option>
              <option value="erreur_commande"> Erreur de commande</option>
              <option value="autre"> Autre</option>
            </select>
          </div>
          
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1"> Commentaire (optionnel)</label>
            <textarea v-model="form.motif_commentaire" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
          </div>
          
          <div class="border-t pt-4 mt-4">
            <div class="flex justify-between items-center mb-3">
              <label class="font-medium"> Produits à retourner</label>
              <button type="button" @click="ajouterLigne" class="text-green-600 text-sm">+ Ajouter un produit</button>
            </div>
            
            <div v-for="(ligne, index) in form.lignes" :key="index" class="border rounded-lg p-3 mb-3 bg-gray-50">
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="text-xs font-medium"> Médicament</label>
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
                  <label class="text-xs font-medium"> Quantité</label>
                  <input type="number" v-model="ligne.quantite" min="1" class="w-full border rounded-lg px-2 py-1 text-sm">
                </div>
                <div class="flex items-end">
                  <button type="button" @click="supprimerLigne(index)" class="text-red-600 text-sm"> Supprimer</button>
                </div>
              </div>
            </div>
            
            <div v-if="form.lignes.length === 0" class="text-center text-gray-400 text-sm py-4">
              Cliquez sur "Ajouter un produit" pour commencer
            </div>
          </div>
          
          <div class="flex justify-end space-x-2 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Annuler</button>
            <button type="submit" :disabled="submitting" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50">
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

// ==================== ÉTAT ====================
const activeTab = ref('commandes')

// Commandes
const commandes = ref([])
const loadingCommandes = ref(false)
const paginationCommandes = ref(null)
const filtersCommandes = ref({
  statut: null,
  date_debut: '',
  date_fin: '',
  page: 1
})

// Retours
const retours = ref([])
const loadingRetours = ref(false)
const filtersRetours = ref({
  statut: null,
  date_debut: '',
  date_fin: ''
})

// Modal Retour
const showModal = ref(false)
const submitting = ref(false)
const fournisseurs = ref([])
const medicaments = ref([])
const lots = ref({})

const form = ref({
  fournisseur_id: null,
  date_retour: new Date().toISOString().split('T')[0],
  motif: 'perime',
  motif_commentaire: '',
  lignes: []
})

// ==================== FONCTIONS COMMUNES ====================
const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price || 0)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

// ==================== COMMANDES ====================
const getStatutLabel = (statut) => {
  const labels = {
    en_attente: ' En attente',
    recue_partielle: ' Réception partielle',
    recue_complete: ' Réception complète'
  }
  return labels[statut] || statut
}

const getStatutClass = (statut) => {
  const classes = {
    en_attente: 'bg-yellow-100 text-yellow-700',
    recue_partielle: 'bg-orange-100 text-orange-700',
    recue_complete: 'bg-green-100 text-green-700'
  }
  return classes[statut] || 'bg-gray-100'
}

const loadCommandes = async () => {
  loadingCommandes.value = true
  try {
    const params = new URLSearchParams()
    if (filtersCommandes.value.statut) params.append('statut', filtersCommandes.value.statut)
    if (filtersCommandes.value.date_debut) params.append('date_debut', filtersCommandes.value.date_debut)
    if (filtersCommandes.value.date_fin) params.append('date_fin', filtersCommandes.value.date_fin)
    params.append('page', filtersCommandes.value.page)
    
    const response = await axios.get(`/api/v1/commandes?${params.toString()}`)
    commandes.value = response.data.data || []
    paginationCommandes.value = {
      current_page: response.data.current_page || 1,
      last_page: response.data.last_page || 1,
      total: response.data.total || 0
    }
  } catch (error) {
    console.error('Erreur chargement commandes:', error)
  } finally {
    loadingCommandes.value = false
  }
}

const searchCommandes = () => {
  filtersCommandes.value.page = 1
  loadCommandes()
}

const goToPageCommandes = (page) => {
  if (page >= 1 && page <= paginationCommandes.value.last_page) {
    filtersCommandes.value.page = page
    loadCommandes()
  }
}

// ==================== RETOURS ====================
const getStatutRetourLabel = (statut) => {
  const labels = {
    en_attente: ' En attente',
    valide: ' Validé',
    refuse: ' Refusé',
    traite: ' Traité'
  }
  return labels[statut] || statut
}

const getStatutRetourClass = (statut) => {
  const classes = {
    en_attente: 'bg-yellow-100 text-yellow-700',
    valide: 'bg-green-100 text-green-700',
    refuse: 'bg-red-100 text-red-700',
    traite: 'bg-blue-100 text-blue-700'
  }
  return classes[statut] || 'bg-gray-100'
}

const loadRetours = async () => {
  loadingRetours.value = true
  try {
    const response = await axios.get('/api/v1/retours-fournisseurs', { params: filtersRetours.value })
    retours.value = response.data.data || []
  } catch (error) {
    console.error('Erreur chargement retours:', error)
  } finally {
    loadingRetours.value = false
  }
}

const searchRetours = () => {
  loadRetours()
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
    alert(' Ajoutez au moins un produit à retourner')
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
  console.log('Détails du retour:', retour)
  alert(` Détails du retour ${retour.numero_retour}\nMotif: ${retour.motif}\nStatut: ${getStatutRetourLabel(retour.statut)}\nMontant: ${formatPrice(retour.montant_total)}`)
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

// ==================== INIT ====================
onMounted(() => {
  loadCommandes()
  loadRetours()
  loadFournisseurs()
  loadMedicaments()
})
</script>