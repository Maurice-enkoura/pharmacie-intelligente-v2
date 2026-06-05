<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RetourFournisseur;
use App\Models\LigneRetourFournisseur;
use App\Models\Medicament;
use App\Models\StockLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RetourFournisseurController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = RetourFournisseur::with(['fournisseur', 'ligneRetours.medicament']);
            
            // Filtrer par pharmacie
            $user = Auth::user();
            if (!$user->isSuperAdmin() && $user->pharmacy_id) {
                $query->where('pharmacy_id', $user->pharmacy_id);
            }
            
            if ($request->has('fournisseur_id') && $request->fournisseur_id) {
                $query->where('fournisseur_id', $request->fournisseur_id);
            }
            
            if ($request->has('statut') && $request->statut) {
                $query->where('statut', $request->statut);
            }
            
            if ($request->has('date_debut') && $request->date_debut) {
                $query->whereDate('date_retour', '>=', $request->date_debut);
            }
            
            if ($request->has('date_fin') && $request->date_fin) {
                $query->whereDate('date_retour', '<=', $request->date_fin);
            }
            
            $retours = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return response()->json($retours);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des retours',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $retour = RetourFournisseur::with(['fournisseur', 'ligneRetours.medicament', 'ligneRetours.stockLot'])
                ->findOrFail($id);
            
            return response()->json($retour);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Retour non trouvé'], 404);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'fournisseur_id' => 'required|exists:fournisseurs,id',
                'date_retour' => 'required|date',
                'motif' => 'required|in:perime,defectueux,erreur_commande,autre',
                'lignes' => 'required|array|min:1',
                'lignes.*.medicament_id' => 'required|exists:medicaments,id',
                'lignes.*.stock_lot_id' => 'required|exists:stock_lots,id',
                'lignes.*.quantite' => 'required|integer|min:1'
            ]);
            
            DB::beginTransaction();
            
            $user = Auth::user();
            $pharmacyId = $user->pharmacy_id;
            
            // Générer le numéro de retour
            $lastRetour = RetourFournisseur::where('pharmacy_id', $pharmacyId)
                ->orderBy('id', 'desc')
                ->first();
            
            $lastNumber = 0;
            if ($lastRetour && $lastRetour->numero_retour) {
                preg_match('/RET-(\d+)/', $lastRetour->numero_retour, $matches);
                if (isset($matches[1])) {
                    $lastNumber = intval($matches[1]);
                }
            }
            
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            $numeroRetour = 'RET-' . $newNumber;
            
            $montantTotal = 0;
            foreach ($request->lignes as $ligne) {
                $stockLot = StockLot::find($ligne['stock_lot_id']);
                $medicament = Medicament::find($ligne['medicament_id']);
                
                if (!$stockLot || $stockLot->medicament_id != $ligne['medicament_id']) {
                    throw new \Exception('Lot non trouvé pour ce médicament');
                }
                
                if ($stockLot->quantite_restante < $ligne['quantite']) {
                    throw new \Exception("Quantité insuffisante dans le lot {$stockLot->lot_number}");
                }
                
                $sousTotal = $stockLot->prix_achat_unitaire * $ligne['quantite'];
                $montantTotal += $sousTotal;
            }
            
            // Créer le retour
            $retour = RetourFournisseur::create([
                'pharmacy_id' => $pharmacyId,
                'fournisseur_id' => $request->fournisseur_id,
                'numero_retour' => $numeroRetour,
                'date_retour' => $request->date_retour,
                'motif' => $request->motif,
                'motif_commentaire' => $request->motif_commentaire,
                'statut' => 'en_attente',
                'montant_total' => $montantTotal,
                'notes' => $request->notes
            ]);
            
            // Créer les lignes de retour et mettre à jour les stocks
            foreach ($request->lignes as $ligne) {
                $stockLot = StockLot::find($ligne['stock_lot_id']);
                $sousTotal = $stockLot->prix_achat_unitaire * $ligne['quantite'];
                
                LigneRetourFournisseur::create([
                    'retour_fournisseur_id' => $retour->id,
                    'medicament_id' => $ligne['medicament_id'],
                    'stock_lot_id' => $ligne['stock_lot_id'],
                    'quantite' => $ligne['quantite'],
                    'prix_achat_unitaire' => $stockLot->prix_achat_unitaire,
                    'sous_total' => $sousTotal,
                    'motif' => $ligne['motif'] ?? $request->motif
                ]);
                
                // Diminuer la quantité dans le lot
                $stockLot->quantite_restante -= $ligne['quantite'];
                $stockLot->save();
                
                // Diminuer le stock du médicament
                $medicament = Medicament::find($ligne['medicament_id']);
                $medicament->stock_actuel -= $ligne['quantite'];
                $medicament->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Retour fournisseur créé avec succès',
                'data' => $retour->load(['fournisseur', 'ligneRetours.medicament'])
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateStatut(Request $request, $id)
    {
        try {
            $retour = RetourFournisseur::findOrFail($id);
            
            $request->validate([
                'statut' => 'required|in:en_attente,valide,refuse,traite'
            ]);
            
            $retour->update(['statut' => $request->statut]);
            
            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour',
                'data' => $retour
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}