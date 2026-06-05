import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Layouts
import MainLayout from '@/layouts/MainLayout.vue'

// Auth Views
import LoginView from '@/views/auth/LoginView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'

// Admin Views
import DashboardAdmin from '@/views/admin/DashboardAdmin.vue'
import PharmaciesView from '@/views/admin/PharmaciesView.vue'
import UtilisateursListView from '@/views/admin/UtilisateursListView.vue'
import ImportExportMedicaments from '@/views/admin/ImportExportMedicaments.vue'

// Super Admin Views
import DashboardSuperAdmin from '@/views/super-admin/DashboardSuperAdmin.vue'
import SuperAdminPharmacies from '@/views/admin/PharmaciesView.vue'
import SuperAdminUtilisateurs from '@/views/super-admin/UtilisateursSuperAdmin.vue'
import SuperAdminAbonnements from '@/views/super-admin/AbonnementsView.vue'
import BackupsView from '@/views/super-admin/BackupsView.vue'  // ← AJOUTER CETTE LIGNE

// Pharmacien Views
import DashboardView from '@/views/pharmacien/DashboardView.vue'
import MedicamentsListView from '@/views/pharmacien/MedicamentsListView.vue'
import MedicamentsFormView from '@/views/pharmacien/MedicamentsFormView.vue'
import StockAlertesView from '@/views/pharmacien/StockAlertesView.vue'
import StockEntreesView from '@/views/pharmacien/StockEntreesView.vue'
import StockHistoriqueView from '@/views/pharmacien/StockHistoriqueView.vue'
import FournisseursListView from '@/views/pharmacien/FournisseursListView.vue'
import FournisseursFormView from '@/views/pharmacien/FournisseursFormView.vue'
import CommandesListView from '@/views/pharmacien/CommandesListView.vue'
import CommandesFormView from '@/views/pharmacien/CommandesFormView.vue'
import CommandesReceptionView from '@/views/pharmacien/CommandesReceptionView.vue'
import RapportsView from '@/views/pharmacien/RapportsView.vue'

// Shared Views
import VentesListView from '@/views/shared/VentesListView.vue'
import VentesFormView from '@/views/shared/VentesFormView.vue'
import VentesDetailView from '@/views/shared/VentesDetailView.vue'
import ClientsListView from '@/views/shared/ClientsListView.vue'
import ClientsDetailView from '@/views/shared/ClientsDetailView.vue'
import ClientsFormView from '@/views/shared/ClientsFormView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: LoginView,
      meta: { guest: true }
    },
    {
      path: '/register',
      name: 'Register',
      component: RegisterView,
      meta: { guest: true }
    },
    {
      path: '/',
      component: MainLayout,
      meta: { requiresAuth: true },
      children: [
        // ========== SUPER ADMIN (5 MENUS) ==========
        // 1. Dashboard
        {
          path: 'super-admin/dashboard',
          name: 'SuperAdminDashboard',
          component: DashboardSuperAdmin,
          meta: { roles: ['super_admin'] }
        },
        // 2. Pharmacies
        {
          path: 'super-admin/pharmacies',
          name: 'SuperAdminPharmacies',
          component: SuperAdminPharmacies,
          meta: { roles: ['super_admin'] }
        },
        // 3. Utilisateurs
        {
          path: 'super-admin/utilisateurs',
          name: 'SuperAdminUtilisateurs',
          component: SuperAdminUtilisateurs,
          meta: { roles: ['super_admin'] }
        },
        // 4. Abonnements
        {
          path: 'super-admin/abonnements',
          name: 'SuperAdminAbonnements',
          component: SuperAdminAbonnements,
          meta: { roles: ['super_admin'] }
        },
        // 5. Sauvegardes (NOUVEAU)
        {
          path: 'super-admin/backups',
          name: 'SuperAdminBackups',
          component: BackupsView,
          meta: { roles: ['super_admin'] }
        },
        
        // ========== ADMIN (dashboard classique) ==========
        {
          path: 'admin/dashboard',
          name: 'AdminDashboard',
          component: DashboardAdmin,
          meta: { roles: ['admin'] }
        },
        {
          path: 'utilisateurs',
          name: 'UtilisateursList',
          component: UtilisateursListView,
          meta: { roles: ['admin'] }
        },
        {
          path: 'import-export',
          name: 'ImportExport',
          component: ImportExportMedicaments,
          meta: { roles: ['admin'] }
        },
        {
          path: 'fournisseurs',
          name: 'FournisseursList',
          component: FournisseursListView,
          meta: { roles: ['admin'] }
        },
        {
          path: 'fournisseurs/create',
          name: 'FournisseursCreate',
          component: FournisseursFormView,
          meta: { roles: ['admin'] }
        },
        {
          path: 'fournisseurs/:id/edit',
          name: 'FournisseursEdit',
          component: FournisseursFormView,
          meta: { roles: ['admin'] }
        },
        {
          path: 'commandes',
          name: 'CommandesList',
          component: CommandesListView,
          meta: { roles: ['admin'] }
        },
        {
          path: 'commandes/create',
          name: 'CommandesCreate',
          component: CommandesFormView,
          meta: { roles: ['admin'] }
        },
        {
          path: 'commandes/:id/reception',
          name: 'CommandesReception',
          component: CommandesReceptionView,
          meta: { roles: ['admin'] }
        },
        {
          path: 'rapports',
          name: 'Rapports',
          component: RapportsView,
          meta: { roles: ['admin'] }
        },
        
        // ========== ADMIN + PHARMACIEN ==========
        {
          path: '',
          name: 'Dashboard',
          component: DashboardView,
          meta: { roles: ['admin', 'pharmacien'] }
        },
        {
          path: 'medicaments',
          name: 'MedicamentsList',
          component: MedicamentsListView,
          meta: { roles: ['admin', 'pharmacien'] }
        },
        {
          path: 'medicaments/create',
          name: 'MedicamentsCreate',
          component: MedicamentsFormView,
          meta: { roles: ['admin', 'pharmacien'] }
        },
        {
          path: 'medicaments/:id/edit',
          name: 'MedicamentsEdit',
          component: MedicamentsFormView,
          meta: { roles: ['admin', 'pharmacien'] }
        },
        {
          path: 'stock/alertes',
          name: 'StockAlertes',
          component: StockAlertesView,
          meta: { roles: ['admin', 'pharmacien'] }
        },
        {
          path: 'stock/entrees',
          name: 'StockEntrees',
          component: StockEntreesView,
          meta: { roles: ['admin', 'pharmacien'] }
        },
        {
          path: 'stock/historique',
          name: 'StockHistorique',
          component: StockHistoriqueView,
          meta: { roles: ['admin', 'pharmacien'] }
        },
        
        // ========== TOUS RÔLES ==========
        {
          path: 'ventes',
          name: 'VentesList',
          component: VentesListView,
          meta: { roles: ['admin', 'pharmacien', 'caissier'] }
        },
        {
          path: 'ventes/create',
          name: 'VentesCreate',
          component: VentesFormView,
          meta: { roles: ['admin', 'pharmacien', 'caissier'] }
        },
        {
          path: 'ventes/:id',
          name: 'VentesDetail',
          component: VentesDetailView,
          meta: { roles: ['admin', 'pharmacien', 'caissier'] }
        },
        {
          path: 'clients',
          name: 'ClientsList',
          component: ClientsListView,
          meta: { roles: ['admin', 'pharmacien', 'caissier'] }
        },
        {
          path: 'clients/create',
          name: 'ClientsCreate',
          component: ClientsFormView,
          meta: { roles: ['admin', 'pharmacien'] }
        },
        {
          path: 'clients/:id',
          name: 'ClientsDetail',
          component: ClientsDetailView,
          meta: { roles: ['admin', 'pharmacien', 'caissier'] }
        },
        {
          path: 'clients/:id/edit',
          name: 'ClientsEdit',
          component: ClientsFormView,
          meta: { roles: ['admin', 'pharmacien'] }
        }
      ]
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@/views/NotFoundView.vue')
    }
  ]
})

// Navigation guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const token = localStorage.getItem('token')
  
  // Initialiser le store si nécessaire
  if (token && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (error) {
      console.error('Erreur récupération utilisateur:', error)
      authStore.logout()
      next('/login')
      return
    }
  }
  
  const isAuthenticated = !!token && !!authStore.user
  const userRole = authStore.user?.role
  
  console.log('Router guard:', { path: to.path, isAuthenticated, role: userRole })
  
  // Redirection vers login si non authentifié
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
    return
  }
  
  // Redirection depuis login/register si déjà connecté
  if ((to.meta.guest) && isAuthenticated) {
    if (userRole === 'super_admin') {
      next('/super-admin/dashboard')
    } else if (userRole === 'admin') {
      next('/admin/dashboard')
    } else if (userRole === 'caissier') {
      next('/ventes')
    } else if (userRole === 'pharmacien') {
      next('/medicaments')
    } else {
      next('/')
    }
    return
  }
  
  // Vérification des rôles
  if (to.meta.roles && !to.meta.roles.includes(userRole)) {
    console.log('Rôle non autorisé, redirection')
    if (userRole === 'super_admin') {
      next('/super-admin/dashboard')
    } else if (userRole === 'admin') {
      next('/admin/dashboard')
    } else if (userRole === 'caissier') {
      next('/ventes')
    } else if (userRole === 'pharmacien') {
      next('/medicaments')
    } else {
      next('/')
    }
    return
  }
  
  next()
})

export default router