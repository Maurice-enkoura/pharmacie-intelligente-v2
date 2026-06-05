<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vente;
use App\Models\Client;
use App\Models\Medicament;
use App\Models\LigneVente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\FactureMail;

class VenteController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Vente::with(['client', 'user', 'ligneVentes.medicament']);
            
            $user = auth()->user();
            
            // Si c'est un caissier, il ne voit que ses propres ventes
            if ($user->role === 'caissier') {
                $query->where('user_id', $user->id);
            }
            // Si ce n'est pas super_admin, filtrer par pharmacie
            else if (!$user->isSuperAdmin() && $user->pharmacy_id) {
                $query->where('pharmacy_id', $user->pharmacy_id);
            }
            
            if ($request->has('client_id') && $request->client_id) {
                $query->where('client_id', $request->client_id);
            }
            
            if ($request->has('date_debut') && $request->date_debut) {
                $query->whereDate('date_vente', '>=', $request->date_debut);
            }
            
            if ($request->has('date_fin') && $request->date_fin) {
                $query->whereDate('date_vente', '<=', $request->date_fin);
            }
            
            $ventes = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return response()->json($ventes);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des ventes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $vente = Vente::with(['client', 'user', 'ligneVentes.medicament'])->findOrFail($id);
            
            // Vérifier l'accès
            $user = auth()->user();
            if ($user->role === 'caissier' && $vente->user_id != $user->id) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
            if (!$user->isSuperAdmin() && $vente->pharmacy_id != $user->pharmacy_id) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
            
            return response()->json($vente);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Vente non trouvée',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'client_id' => 'required|exists:clients,id',
                'lignes' => 'required|array|min:1',
                'lignes.*.medicament_id' => 'required|exists:medicaments,id',
                'lignes.*.quantite' => 'required|integer|min:1',
                'mode_paiement' => 'required|in:especes,orange_money,wave,carte',
                'montant_paye' => 'required|numeric|min:0',
                'type_vente' => 'required|in:avec_ordonnance,sans_ordonnance',
                'ordonnance_ref' => 'required_if:type_vente,avec_ordonnance'
            ]);
            
            DB::beginTransaction();
            
            $user = auth()->user();
            $pharmacyId = $user->pharmacy_id;
            
            // Vérifier que l'utilisateur a une pharmacie (sauf super_admin)
            if (!$user->isSuperAdmin() && !$pharmacyId) {
                throw new \Exception('Aucune pharmacie associée à cet utilisateur');
            }
            
            // Calculer le montant total et vérifier les stocks
            $montantTotal = 0;
            foreach ($request->lignes as $ligne) {
                $medicament = Medicament::find($ligne['medicament_id']);
                
                if (!$medicament) {
                    throw new \Exception('Médicament non trouvé');
                }
                
                // Vérifier que le médicament appartient à la pharmacie
                if (!$user->isSuperAdmin() && $medicament->pharmacy_id != $pharmacyId) {
                    throw new \Exception('Médicament non trouvé dans votre pharmacie');
                }
                
                if ($medicament->stock_actuel < $ligne['quantite']) {
                    throw new \Exception("Stock insuffisant pour {$medicament->nom}");
                }
                
                if ($medicament->ordonnance_requise && $request->type_vente === 'sans_ordonnance') {
                    throw new \Exception("Ordonnance requise pour {$medicament->nom}");
                }
                
                $montantTotal += $medicament->prix_vente * $ligne['quantite'];
            }
            
            $monnaieRendue = $request->montant_paye - $montantTotal;
            
            if ($monnaieRendue < 0) {
                throw new \Exception('Montant payé insuffisant');
            }
            
            // Générer le numéro de facture unique PAR PHARMACIE
            $lastVente = Vente::where('pharmacy_id', $pharmacyId)
                ->orderBy('id', 'desc')
                ->first();
            
            $lastNumber = 0;
            if ($lastVente && $lastVente->numero_facture) {
                preg_match('/FACT-(\d+)/', $lastVente->numero_facture, $matches);
                if (isset($matches[1])) {
                    $lastNumber = intval($matches[1]);
                }
            }
            
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            $numeroFacture = 'FACT-' . $newNumber;
            
            // Sécurité : vérifier que le numéro n'existe pas déjà
            while (Vente::where('numero_facture', $numeroFacture)->exists()) {
                $lastNumber++;
                $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                $numeroFacture = 'FACT-' . $newNumber;
            }
            
            // Générer la référence ordonnance si nécessaire
            $ordonnanceRef = null;
            if ($request->type_vente === 'avec_ordonnance') {
                if ($request->filled('ordonnance_ref')) {
                    $ordonnanceRef = $request->ordonnance_ref;
                } else {
                    $lastOrdonnance = Vente::where('type_vente', 'avec_ordonnance')
                        ->where('pharmacy_id', $pharmacyId)
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    $lastOrdoNumber = 0;
                    if ($lastOrdonnance && $lastOrdonnance->ordonnance_ref) {
                        preg_match('/ORD-(\d+)/', $lastOrdonnance->ordonnance_ref, $matches);
                        if (isset($matches[1])) {
                            $lastOrdoNumber = intval($matches[1]);
                        }
                    }
                    $newOrdoNumber = str_pad($lastOrdoNumber + 1, 6, '0', STR_PAD_LEFT);
                    $ordonnanceRef = 'ORD-' . $newOrdoNumber;
                }
            }
            
            // Créer la vente
            $vente = Vente::create([
                'numero_facture' => $numeroFacture,
                'client_id' => $request->client_id,
                'user_id' => auth()->id(),
                'date_vente' => now(),
                'type_vente' => $request->type_vente,
                'ordonnance_ref' => $ordonnanceRef,
                'montant_total' => $montantTotal,
                'montant_paye' => $request->montant_paye,
                'monnaie_rendue' => $monnaieRendue,
                'mode_paiement' => $request->mode_paiement,
                'pharmacy_id' => $pharmacyId
            ]);
            
            // Créer les lignes de vente et mettre à jour les stocks
            foreach ($request->lignes as $ligne) {
                $medicament = Medicament::find($ligne['medicament_id']);
                
                LigneVente::create([
                    'vente_id' => $vente->id,
                    'medicament_id' => $ligne['medicament_id'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $medicament->prix_vente,
                    'sous_total' => $medicament->prix_vente * $ligne['quantite']
                ]);
                
                // Mettre à jour le stock
                $medicament->stock_actuel -= $ligne['quantite'];
                $medicament->save();
            }
            
            DB::commit();
            
            $vente->load(['client', 'user', 'ligneVentes.medicament']);
            
            return response()->json([
                'success' => true,
                'message' => 'Vente enregistrée avec succès',
                'data' => $vente
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
            ], 400);
        }
    }
    
    public function cancel($id)
    {
        try {
            $vente = Vente::findOrFail($id);
            $user = auth()->user();
            
            // Vérifier les droits
            if ($user->role !== 'admin' && $user->role !== 'pharmacien') {
                return response()->json(['message' => 'Non autorisé'], 403);
            }
            
            // Vérifier que la vente appartient à la pharmacie
            if (!$user->isSuperAdmin() && $vente->pharmacy_id != $user->pharmacy_id) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
            
            // Restaurer les stocks
            foreach ($vente->ligneVentes as $ligne) {
                $medicament = Medicament::find($ligne->medicament_id);
                if ($medicament) {
                    $medicament->stock_actuel += $ligne->quantite;
                    $medicament->save();
                }
            }
            
            $vente->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Vente annulée avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function generatePDF($id)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
            
            $vente = Vente::with(['client', 'user', 'ligneVentes.medicament'])->find($id);
            
            if (!$vente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vente non trouvée'
                ], 404);
            }
            
            $user = Auth::user();
            
            // Vérifier l'accès
            if ($user->role === 'caissier' && $vente->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas accès à cette facture'
                ], 403);
            }
            
            if (!$user->isSuperAdmin() && $vente->pharmacy_id != $user->pharmacy_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }
            
            $pdf = Pdf::loadView('pdf.facture', compact('vente'));
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download('facture_' . $vente->numero_facture . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Erreur PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function sendEmail($id)
    {
        try {
            $vente = Vente::with(['client'])->findOrFail($id);
            
            // Vérifier l'accès
            $user = Auth::user();
            if ($user->role === 'caissier' && $vente->user_id !== $user->id) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
            if (!$user->isSuperAdmin() && $vente->pharmacy_id != $user->pharmacy_id) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
            
            // Vérifier si le client a un email
            if (!$vente->client->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce client n\'a pas d\'adresse email'
                ], 400);
            }
            
            // Envoyer l'email
            Mail::to($vente->client->email)->send(new FactureMail($vente));
            
            return response()->json([
                'success' => true,
                'message' => 'Facture envoyée par email à ' . $vente->client->email
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()
            ], 500);
        }
    }


}