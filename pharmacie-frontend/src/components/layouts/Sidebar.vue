<template>
  <aside class="w-64 bg-green-700 text-white flex flex-col">
    <!-- Logo -->
    <div class="p-6 border-b border-green-600">
      <h1 class="text-2xl font-bold text-white">Pharmacie</h1>
      <p class="text-sm text-green-200">Intelligente</p>
      <!-- Affichage nom pharmacie pour non super_admin -->
      <div v-if="!isSuperAdmin && pharmacyName" class="mt-2 text-xs text-green-200 border-t border-green-600 pt-2">
         {{ pharmacyName }}
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-4">
      
      <!-- ========== SUPER ADMIN ========== -->
      <template v-if="isSuperAdmin">
        <router-link to="/admin/pharmacies" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/admin/pharmacies') }">
          <span class="mr-3 text-xl"></span>
          <span>Pharmacies</span>
        </router-link>
        
        <router-link to="/admin/utilisateurs" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/admin/utilisateurs') }">
          <span class="mr-3 text-xl">👥</span>
          <span>Tous les utilisateurs</span>
        </router-link>
        
        <div class="my-3 border-t border-green-600"></div>
      </template>
      
      <!-- ========== ADMIN SEULEMENT ========== -->
      <template v-else-if="isAdmin">
        <router-link to="/" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/' }">
          <span class="mr-3 text-xl"></span>
          <span>Dashboard</span>
        </router-link>
        
        <router-link to="/medicaments" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/medicaments') }">
          <span class="mr-3 text-xl"></span>
          <span>Médicaments</span>
        </router-link>
        
        <div class="px-6 py-2 text-xs text-green-200 uppercase tracking-wider mt-2"> Gestion du stock</div>
        
        <router-link to="/stock/alertes" class="flex items-center px-6 py-2 bg-green-700 hover:bg-green-600 transition-colors pl-12" :class="{ 'bg-green-600': $route.path === '/stock/alertes' }">
          <span class="mr-3"></span>
          <span>Alertes stock</span>
        </router-link>
        
        <router-link to="/stock/entrees" class="flex items-center px-6 py-2 bg-green-700 hover:bg-green-600 transition-colors pl-12" :class="{ 'bg-green-600': $route.path === '/stock/entrees' }">
          <span class="mr-3"></span>
          <span>Entrées de stock</span>
        </router-link>
        
        <router-link to="/stock/historique" class="flex items-center px-6 py-2 bg-green-700 hover:bg-green-600 transition-colors pl-12" :class="{ 'bg-green-600': $route.path === '/stock/historique' }">
          <span class="mr-3"></span>
          <span>Historique stock</span>
        </router-link>
        
        <div class="my-3 border-t border-green-600"></div>
        
        <router-link to="/ventes" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/ventes' }">
          <span class="mr-3 text-xl"></span>
          <span>Ventes</span>
        </router-link>
        
        <router-link to="/ventes/create" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/ventes/create' }">
          <span class="mr-3 text-xl"></span>
          <span>Nouvelle vente</span>
        </router-link>
        
        <div class="my-3 border-t border-green-600"></div>
        
        <router-link to="/clients" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/clients') }">
          <span class="mr-3 text-xl"></span>
          <span>Clients</span>
        </router-link>
        
        <div class="my-3 border-t border-green-600"></div>
        
        <router-link to="/fournisseurs" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/fournisseurs') }">
          <span class="mr-3 text-xl"></span>
          <span>Fournisseurs</span>
        </router-link>
        
        <router-link to="/commandes" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/commandes') }">
          <span class="mr-3 text-xl"></span>
          <span>Commandes</span>
        </router-link>
        
        <router-link to="/rapports" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/rapports' }">
          <span class="mr-3 text-xl"> </span>
          <span>Rapports</span>
        </router-link>
        
        <router-link to="/import-export" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/import-export' }">
          <span class="mr-3 text-xl"></span>
          <span>Import / Export</span>
        </router-link>
        
        <div class="my-3 border-t border-green-600"></div>
        
        <router-link to="/utilisateurs" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/utilisateurs' }">
          <span class="mr-3 text-xl">👤</span>
          <span>Utilisateurs</span>
        </router-link>
      </template>
      
      <!-- ========== PHARMACIEN SEULEMENT ========== -->
      <template v-else-if="isPharmacien">
        <!-- Même contenu qu'avant -->
        <router-link to="/medicaments" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/medicaments') }">
          <span class="mr-3 text-xl"></span>
          <span>Médicaments</span>
        </router-link>
        
        <div class="px-6 py-2 text-xs text-green-200 uppercase tracking-wider mt-2"> Gestion du stock</div>
        
        <router-link to="/stock/alertes" class="flex items-center px-6 py-2 bg-green-700 hover:bg-green-600 transition-colors pl-12" :class="{ 'bg-green-600': $route.path === '/stock/alertes' }">
          <span class="mr-3"></span>
          <span>Alertes stock</span>
        </router-link>
        
        <router-link to="/stock/entrees" class="flex items-center px-6 py-2 bg-green-700 hover:bg-green-600 transition-colors pl-12" :class="{ 'bg-green-600': $route.path === '/stock/entrees' }">
          <span class="mr-3"></span>
          <span>Entrées de stock</span>
        </router-link>
        
        <router-link to="/stock/historique" class="flex items-center px-6 py-2 bg-green-700 hover:bg-green-600 transition-colors pl-12" :class="{ 'bg-green-600': $route.path === '/stock/historique' }">
          <span class="mr-3"></span>
          <span>Historique stock</span>
        </router-link>
        
        <div class="my-3 border-t border-green-600"></div>
        
        <router-link to="/ventes" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/ventes' }">
          <span class="mr-3 text-xl"></span>
          <span>Ventes</span>
        </router-link>
        
        <router-link to="/ventes/create" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/ventes/create' }">
          <span class="mr-3 text-xl"></span>
          <span>Nouvelle vente</span>
        </router-link>
        
        <div class="my-3 border-t border-green-600"></div>
        
        <router-link to="/clients" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/clients') }">
          <span class="mr-3 text-xl"></span>
          <span>Clients</span>
        </router-link>
      </template>
      
      <!-- ========== CAISSIER SEULEMENT ========== -->
      <template v-else-if="isCaissier">
        <router-link to="/ventes" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/ventes' }">
          <span class="mr-3 text-xl"></span>
          <span>Mes ventes</span>
        </router-link>
        
        <router-link to="/ventes/create" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/ventes/create' }">
          <span class="mr-3 text-xl"></span>
          <span>Nouvelle vente</span>
        </router-link>
        
        <div class="my-3 border-t border-green-600"></div>
        
        <router-link to="/clients" class="flex items-center px-6 py-3 bg-green-700 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/clients') }">
          <span class="mr-3 text-xl"></span>
          <span>Clients</span>
        </router-link>
      </template>
      
    </nav>
    
    <!-- Footer avec informations utilisateur -->
    <div class="p-4 border-t border-green-600 bg-green-800">
      <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
          {{ userInitial }}
        </div>
        <div class="flex-1">
          <p class="text-sm font-medium text-white">{{ userName }}</p>
          <p class="text-xs text-green-200">{{ userRoleLabel }}</p>
          <p v-if="!isSuperAdmin && pharmacyName" class="text-xs text-green-300 mt-1"> {{ pharmacyName }}</p>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

const authStore = useAuthStore()
const { isAdmin, isPharmacien, isCaissier, isSuperAdmin, userRoleLabel, userName, userInitial, pharmacyName } = storeToRefs(authStore)
</script>