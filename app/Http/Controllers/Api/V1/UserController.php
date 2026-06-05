<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Super Admin voit tous les utilisateurs
        if ($user->isSuperAdmin()) {
            $users = User::with('pharmacy')->get();
        } 
        // Admin voit uniquement les utilisateurs de SA pharmacie (sauf super_admin)
        else {
            $users = User::where('pharmacy_id', $user->pharmacy_id)
                ->where('role', '!=', 'super_admin')
                ->with('pharmacy')
                ->get();
        }
        
        return response()->json($users);
    }
    
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $pharmacyId = null;
            
            // Si l'utilisateur connecté est admin, il ne peut créer que des utilisateurs pour sa pharmacie
            if ($user->isAdmin()) {
                $pharmacyId = $user->pharmacy_id;
                $allowedRoles = ['admin', 'pharmacien', 'caissier'];
            } 
            // Super Admin peut créer n'importe quel rôle avec n'importe quelle pharmacie
            else if ($user->isSuperAdmin()) {
                $allowedRoles = ['admin', 'pharmacien', 'caissier', 'super_admin'];
                $pharmacyId = $request->pharmacy_id;
            } 
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à créer des utilisateurs'
                ], 403);
            }
            
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'role' => ['required', Rule::in($allowedRoles)],
            ];
            
            // Super Admin doit fournir pharmacy_id pour les non super_admin
            if ($user->isSuperAdmin() && $request->role !== 'super_admin') {
                $rules['pharmacy_id'] = 'required|exists:pharmacies,id';
            }
            
            $validated = $request->validate($rules);
            
            $newUser = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'pharmacy_id' => $pharmacyId ?? $validated['pharmacy_id'] ?? null
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'data' => $newUser->load('pharmacy')
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
            $userToUpdate = User::findOrFail($id);
            $currentUser = auth()->user();
            
            // Vérifier les permissions
            if ($currentUser->isAdmin()) {
                // Admin ne peut modifier que les utilisateurs de sa pharmacie (sauf super_admin)
                if ($userToUpdate->pharmacy_id != $currentUser->pharmacy_id || $userToUpdate->isSuperAdmin()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous n\'êtes pas autorisé à modifier cet utilisateur'
                    ], 403);
                }
                $allowedRoles = ['admin', 'pharmacien', 'caissier'];
            } 
            else if (!$currentUser->isSuperAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à modifier des utilisateurs'
                ], 403);
            }
            
            $rules = [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'role' => ['sometimes', Rule::in($allowedRoles ?? ['admin', 'pharmacien', 'caissier', 'super_admin'])],
                'password' => 'sometimes|string|min:6',
            ];
            
            if ($currentUser->isSuperAdmin()) {
                $rules['pharmacy_id'] = 'nullable|exists:pharmacies,id';
            }
            
            $validated = $request->validate($rules);
            
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }
            
            $userToUpdate->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur modifié avec succès',
                'data' => $userToUpdate->load('pharmacy')
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
            $userToDelete = User::findOrFail($id);
            $currentUser = auth()->user();
            
            // Vérifier les permissions
            if ($currentUser->isAdmin()) {
                // Admin ne peut supprimer que les utilisateurs de sa pharmacie (sauf super_admin et lui-même)
                if ($userToDelete->pharmacy_id != $currentUser->pharmacy_id || 
                    $userToDelete->isSuperAdmin() || 
                    $userToDelete->id === $currentUser->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous n\'êtes pas autorisé à supprimer cet utilisateur'
                    ], 403);
                }
            } 
            else if (!$currentUser->isSuperAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à supprimer des utilisateurs'
                ], 403);
            }
            
            // Super Admin ne peut pas se supprimer lui-même
            if ($currentUser->isSuperAdmin() && $userToDelete->id === $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas supprimer votre propre compte'
                ], 400);
            }
            
            $userToDelete->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }
}