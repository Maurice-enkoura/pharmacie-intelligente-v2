<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-green-700 text-white flex flex-col">
      <!-- Logo -->
      <div class="p-4 border-b border-green-600">
        <h1 class="text-xl font-bold">Pharmacie</h1>
        <p class="text-xs text-green-200">Intelligente</p>
      </div>
      
      <!-- Navigation -->
      <nav class="flex-1 py-4 overflow-y-auto">
        <!-- SUPER ADMIN - 5 MENUS -->
        <template v-if="authStore.isSuperAdmin">
          <!-- 1. Dashboard -->
          <router-link to="/super-admin/dashboard" class="flex items-center px-4 py-3 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/super-admin/dashboard' }">
            <span class="mr-3 text-xl"></span>
            <span>Dashboard</span>
          </router-link>
          
          <!-- 2. Pharmacies -->
          <router-link to="/super-admin/pharmacies" class="flex items-center px-4 py-3 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/super-admin/pharmacies') }">
            <span class="mr-3 text-xl"></span>
            <span>Pharmacies</span>
          </router-link>
          
          <!-- 3. Utilisateurs -->
          <router-link to="/super-admin/utilisateurs" class="flex items-center px-4 py-3 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/super-admin/utilisateurs') }">
            <span class="mr-3 text-xl"></span>
            <span>Utilisateurs</span>
          </router-link>
          
          <!-- 4. Abonnements -->
          <router-link to="/super-admin/abonnements" class="flex items-center px-4 py-3 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/super-admin/abonnements') }">
            <span class="mr-3 text-xl"></span>
            <span>Abonnements</span>
          </router-link>
          
          <!-- 5. Sauvegardes (NOUVEAU) -->
          <router-link to="/super-admin/backups" class="flex items-center px-4 py-3 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/super-admin/backups') }">
            <span class="mr-3 text-xl"></span>
            <span>Sauvegardes</span>
          </router-link>
        </template>
        
        <!-- ADMIN -->
        <template v-else-if="authStore.isAdmin">
          <router-link to="/admin/dashboard" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/admin/dashboard' }">
            <span class="mr-3 text-xl"></span>
            <span>Dashboard</span>
          </router-link>
          
          <router-link to="/medicaments" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/medicaments') }">
            <span class="mr-3 text-xl"></span>
            <span>Médicaments</span>
          </router-link>
          
          <div class="px-4 py-1 text-xs text-green-300 uppercase mt-2">Stock</div>
          
          <router-link to="/stock/alertes" class="flex items-center px-4 py-2 pl-8 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/stock/alertes' }">
            <span class="mr-3"></span>
            <span>Alertes</span>
          </router-link>
          
          <router-link to="/stock/entrees" class="flex items-center px-4 py-2 pl-8 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/stock/entrees' }">
            <span class="mr-3"></span>
            <span>Entrées</span>
          </router-link>
          
          <router-link to="/stock/historique" class="flex items-center px-4 py-2 pl-8 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/stock/historique' }">
            <span class="mr-3"></span>
            <span>Historique</span>
          </router-link>
          
          <div class="border-t border-green-600 my-2"></div>
          
          <router-link to="/ventes" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/ventes') }">
            <span class="mr-3 text-xl"></span>
            <span>Ventes</span>
          </router-link>
          
          <router-link to="/clients" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/clients') }">
            <span class="mr-3 text-xl"></span>
            <span>Clients</span>
          </router-link>
          
          <div class="border-t border-green-600 my-2"></div>
          
          <router-link to="/fournisseurs" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/fournisseurs') }">
            <span class="mr-3 text-xl"></span>
            <span>Fournisseurs</span>
          </router-link>
          
          <router-link to="/commandes" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/commandes') }">
            <span class="mr-3 text-xl"></span>
            <span>Commandes</span>
          </router-link>
          
          <router-link to="/rapports" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/rapports' }">
            <span class="mr-3 text-xl"></span>
            <span>Rapports</span>
          </router-link>
          
          <router-link to="/import-export" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/import-export' }">
            <span class="mr-3 text-xl"></span>
            <span>Import/Export</span>
          </router-link>
          
          <div class="border-t border-green-600 my-2"></div>
          
          <router-link to="/utilisateurs" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/utilisateurs' }">
            <span class="mr-3 text-xl"></span>
            <span>Utilisateurs</span>
          </router-link>
        </template>
        
        <!-- PHARMACIEN -->
        <template v-else-if="authStore.isPharmacien">
          <router-link to="/medicaments" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/medicaments') }">
            <span class="mr-3 text-xl"></span>
            <span>Médicaments</span>
          </router-link>
          
          <div class="px-4 py-1 text-xs text-green-300 uppercase mt-2"> Stock</div>
          
          <router-link to="/stock/alertes" class="flex items-center px-4 py-2 pl-8 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/stock/alertes' }">
            <span class="mr-3"></span>
            <span>Alertes</span>
          </router-link>
          
          <router-link to="/stock/entrees" class="flex items-center px-4 py-2 pl-8 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/stock/entrees' }">
            <span class="mr-3"></span>
            <span>Entrées</span>
          </router-link>
          
          <router-link to="/stock/historique" class="flex items-center px-4 py-2 pl-8 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/stock/historique' }">
            <span class="mr-3"></span>
            <span>Historique</span>
          </router-link>
          
          <div class="border-t border-green-600 my-2"></div>
          
          <router-link to="/ventes" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/ventes') }">
            <span class="mr-3 text-xl"></span>
            <span>Ventes</span>
          </router-link>
          
          <router-link to="/clients" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/clients') }">
            <span class="mr-3 text-xl"></span>
            <span>Clients</span>
          </router-link>
        </template>
        
        <!-- CAISSIER -->
        <template v-else-if="authStore.isCaissier">
          <router-link to="/ventes" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/ventes' }">
            <span class="mr-3 text-xl"></span>
            <span>Mes ventes</span>
          </router-link>
          
          <router-link to="/ventes/create" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path === '/ventes/create' }">
            <span class="mr-3 text-xl">➕</span>
            <span>Nouvelle vente</span>
          </router-link>
          
          <div class="border-t border-green-600 my-2"></div>
          
          <router-link to="/clients" class="flex items-center px-4 py-2 hover:bg-green-600 transition-colors" :class="{ 'bg-green-600': $route.path.startsWith('/clients') }">
            <span class="mr-3 text-xl">👥</span>
            <span>Clients</span>
          </router-link>
        </template>
      </nav>
      
      <!-- Footer utilisateur -->
      <div class="p-4 border-t border-green-600 bg-green-800">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center font-bold">
            {{ authStore.userInitial }}
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium">{{ authStore.userName }}</p>
            <p class="text-xs text-green-200">{{ authStore.userRoleLabel }}</p>
          </div>
          <button @click="logout" class="text-red-300 hover:text-red-200" title="Déconnexion">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </div>
      </div>
    </aside>
    
    <!-- Main content -->
    <main class="flex-1 bg-gray-100">
      <div class="p-4">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>