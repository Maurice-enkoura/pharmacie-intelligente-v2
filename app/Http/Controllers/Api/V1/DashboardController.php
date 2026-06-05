<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vente;
use App\Models\Medicament;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $pharmacyId = $user->pharmacy_id;
        
        $periode = $request->periode ?? 'mois';
        
        $dateDebut = match($periode) {
            'semaine' => now()->startOfWeek(),
            'mois' => now()->startOfMonth(),
            'annee' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
        
        // Chiffre d'affaires - filtrer par pharmacie
        $ca = Vente::whereDate('date_vente', '>=', $dateDebut);
        if (!$user->isSuperAdmin() && $pharmacyId) {
            $ca->where('pharmacy_id', $pharmacyId);
        }
        $ca = $ca->sum('montant_total');
        
        // Nombre de ventes - filtrer par pharmacie
        $nbVentes = Vente::whereDate('date_vente', '>=', $dateDebut);
        if (!$user->isSuperAdmin() && $pharmacyId) {
            $nbVentes->where('pharmacy_id', $pharmacyId);
        }
        $nbVentes = $nbVentes->count();
        
        // Top médicaments vendus - filtrer par pharmacie 🔥 CORRECTION ICI
        $topMedicamentsQuery = DB::table('ligne_ventes')
            ->join('medicaments', 'ligne_ventes.medicament_id', '=', 'medicaments.id')
            ->join('ventes', 'ligne_ventes.vente_id', '=', 'ventes.id')
            ->whereDate('ventes.date_vente', '>=', $dateDebut);
        
        // Ajouter le filtre pharmacie
        if (!$user->isSuperAdmin() && $pharmacyId) {
            $topMedicamentsQuery->where('ventes.pharmacy_id', $pharmacyId);
        }
        
        $topMedicaments = $topMedicamentsQuery
            ->select('medicaments.nom', DB::raw('SUM(ligne_ventes.quantite) as total_vendus'))
            ->groupBy('medicaments.id', 'medicaments.nom')
            ->orderBy('total_vendus', 'desc')
            ->limit(5)
            ->get();
        
        // Stock alerte - filtrer par pharmacie
        $stockAlerte = Medicament::whereColumn('stock_actuel', '<', 'seuil_alerte');
        if (!$user->isSuperAdmin() && $pharmacyId) {
            $stockAlerte->where('pharmacy_id', $pharmacyId);
        }
        $stockAlerte = $stockAlerte->count();
        
        // Nombre de clients - filtrer par pharmacie
        $nbClients = Client::query();
        if (!$user->isSuperAdmin() && $pharmacyId) {
            $nbClients->where('pharmacy_id', $pharmacyId);
        }
        $nbClients = $nbClients->count();
        
        // Ventes par mode de paiement - filtrer par pharmacie
        $paiements = Vente::whereDate('date_vente', '>=', $dateDebut);
        if (!$user->isSuperAdmin() && $pharmacyId) {
            $paiements->where('pharmacy_id', $pharmacyId);
        }
        $paiements = $paiements
            ->select('mode_paiement', DB::raw('SUM(montant_total) as total'))
            ->groupBy('mode_paiement')
            ->get();
        
        return response()->json([
            'chiffre_affaires' => $ca,
            'nombre_ventes' => $nbVentes,
            'top_medicaments' => $topMedicaments,
            'stock_alerte' => $stockAlerte,
            'nombre_clients' => $nbClients,
            'paiements' => $paiements,
            'periode' => $periode
        ]);
    }
}