<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Client::query();
            
            // Filtrer par pharmacie (sauf super_admin)
            if (!$user->isSuperAdmin()) {
                $query->where('pharmacy_id', $user->pharmacy_id);
            }
            
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('nom', 'like', '%' . $request->search . '%')
                      ->orWhere('prenom', 'like', '%' . $request->search . '%')
                      ->orWhere('telephone', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }
            
            $clients = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return response()->json($clients);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des clients',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $user = Auth::user();
            $client = Client::with('ventes.ligneVentes.medicament')->findOrFail($id);
            
            // Vérifier que le client appartient à la pharmacie de l'utilisateur (sauf super_admin)
            if (!$user->isSuperAdmin() && $client->pharmacy_id != $user->pharmacy_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce client'
                ], 403);
            }
            
            return response()->json($client);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Client non trouvé'
            ], 404);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $pharmacyId = $user->pharmacy_id;
            
            // Vérifier que l'utilisateur a une pharmacie (sauf super_admin)
            if (!$user->isSuperAdmin() && !$pharmacyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune pharmacie associée à cet utilisateur'
                ], 403);
            }
            
            // Règles de validation
            $rules = [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'telephone' => 'required|string|unique:clients,telephone',
                'email' => 'nullable|email|unique:clients,email',
            ];
            
            // Champs supplémentaires pour admin et pharmacien (pas pour caissier)
            if ($user->role !== 'caissier') {
                $rules['adresse'] = 'nullable|string';
                $rules['medicaments_chroniques'] = 'nullable|string';
            }
            
            // Pour Super Admin, pharmacy_id est requis
            if ($user->isSuperAdmin()) {
                $rules['pharmacy_id'] = 'required|exists:pharmacies,id';
            }
            
            $request->validate($rules);
            
            // Déterminer le pharmacy_id
            if ($user->isSuperAdmin()) {
                $pharmacyId = $request->pharmacy_id;
            }
            
            $client = Client::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'telephone' => $request->telephone,
                'email' => $request->email ?? null,
                'adresse' => $request->adresse ?? null,
                'medicaments_chroniques' => $request->medicaments_chroniques ?? null,
                'pharmacy_id' => $pharmacyId
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Client créé avec succès',
                'data' => $client
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Vérifier les droits (seul admin et pharmacien peuvent modifier)
            if ($user->role === 'caissier') {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé - Seul l\'administrateur ou le pharmacien peut modifier un client'
                ], 403);
            }
            
            $client = Client::findOrFail($id);
            
            // Vérifier que le client appartient à la pharmacie de l'utilisateur (sauf super_admin)
            if (!$user->isSuperAdmin() && $client->pharmacy_id != $user->pharmacy_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce client'
                ], 403);
            }
            
            $rules = [
                'nom' => 'sometimes|string|max:255',
                'prenom' => 'sometimes|string|max:255',
                'telephone' => 'sometimes|string|unique:clients,telephone,' . $id,
                'email' => 'nullable|email|unique:clients,email,' . $id,
                'adresse' => 'nullable|string',
                'medicaments_chroniques' => 'nullable|string'
            ];
            
            // Pour Super Admin, possibilité de changer de pharmacie
            if ($user->isSuperAdmin()) {
                $rules['pharmacy_id'] = 'nullable|exists:pharmacies,id';
            }
            
            $request->validate($rules);
            
            $updateData = $request->all();
            
            // Super Admin peut changer pharmacy_id
            if ($user->isSuperAdmin() && $request->has('pharmacy_id')) {
                $updateData['pharmacy_id'] = $request->pharmacy_id;
            }
            
            $client->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Client modifié avec succès',
                'data' => $client
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            // Seul admin peut supprimer (et super_admin)
            if ($user->role !== 'admin' && !$user->isSuperAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé - Seul l\'administrateur peut supprimer un client'
                ], 403);
            }
            
            $client = Client::findOrFail($id);
            
            // Vérifier que le client appartient à la pharmacie de l'utilisateur (sauf super_admin)
            if (!$user->isSuperAdmin() && $client->pharmacy_id != $user->pharmacy_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce client'
                ], 403);
            }
            
            $client->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Client supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }
}