<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Vente;
use App\Models\Medicament;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    /**
     * Statistiques générales pour le Super Admin
     */
    public function stats()
{
    try {
        // Statistiques pharmacies
        $totalPharmacies = Pharmacy::count();
        $activePharmacies = Pharmacy::where('is_active', true)->count();
        $inactivePharmacies = Pharmacy::where('is_active', false)->count();
        
        // Statistiques utilisateurs
        $totalUsers = User::count();
        $usersByRole = [
            'super_admin' => User::where('role', 'super_admin')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'pharmacien' => User::where('role', 'pharmacien')->count(),
            'caissier' => User::where('role', 'caissier')->count(),
        ];
        
        // Chiffre d'affaires total
        $totalRevenue = Vente::sum('montant_total');
        
        // Top 5 pharmacies par CA avec leur statut
        $topPharmacies = Pharmacy::select(
                'pharmacies.id',
                'pharmacies.name',
                'pharmacies.is_active',
                DB::raw('COALESCE(SUM(ventes.montant_total), 0) as revenue')
            )
            ->leftJoin('ventes', 'pharmacies.id', '=', 'ventes.pharmacy_id')
            ->groupBy('pharmacies.id', 'pharmacies.name', 'pharmacies.is_active')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get()
            ->map(function($pharmacy) {
                return [
                    'id' => $pharmacy->id,
                    'name' => $pharmacy->name,
                    'is_active' => (bool) $pharmacy->is_active,  // ← Important
                    'revenue' => (float) $pharmacy->revenue
                ];
            });
        
        // Abonnements expirant
        $expiringSubscriptions = Pharmacy::where('is_active', true)
            ->whereNotNull('subscription_ends_at')
            ->whereDate('subscription_ends_at', '<=', now()->addDays(30))
            ->whereDate('subscription_ends_at', '>=', now())
            ->get()
            ->map(function($pharmacy) {
                return [
                    'id' => $pharmacy->id,
                    'pharmacy_name' => $pharmacy->name,
                    'subscription_ends_at' => $pharmacy->subscription_ends_at
                ];
            });
        
        return response()->json([
            'total_pharmacies' => $totalPharmacies,
            'total_users' => $totalUsers,
            'active_pharmacies' => $activePharmacies,
            'inactive_pharmacies' => $inactivePharmacies,
            'total_revenue' => (float) $totalRevenue,
            'users_by_role' => $usersByRole,
            'top_pharmacies' => $topPharmacies,
            'expiring_subscriptions' => $expiringSubscriptions
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du chargement des statistiques',
            'error' => $e->getMessage()
        ], 500);
    }
}
    
    /**
     * Liste des utilisateurs avec filtre par pharmacie
     */
    public function users(Request $request)
    {
        try {
            $query = User::with('pharmacy');
            
            // Filtrer par pharmacie si demandé
            if ($request->has('pharmacy_id') && $request->pharmacy_id) {
                $query->where('pharmacy_id', $request->pharmacy_id);
            }
            
            $users = $query->orderBy('created_at', 'desc')->get();
            
            return response()->json($users);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des utilisateurs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Renouveler l'abonnement d'une pharmacie
     */
    public function renewSubscription(Request $request, $id)
    {
        try {
            $pharmacy = Pharmacy::findOrFail($id);
            
            // Ajouter 30 jours à l'abonnement
            $newEndDate = now()->addDays(30);
            $pharmacy->subscription_ends_at = $newEndDate;
            $pharmacy->is_active = true;
            $pharmacy->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Abonnement renouvelé jusqu\'au ' . $newEndDate->format('d/m/Y'),
                'data' => $pharmacy
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du renouvellement: ' . $e->getMessage()
            ], 500);
        }
    }
}