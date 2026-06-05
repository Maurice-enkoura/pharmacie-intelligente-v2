<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterPharmacyController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'pharmacy.name' => 'required|string|max:255',
                'pharmacy.email' => 'required|email|unique:pharmacies,email',
                'pharmacy.phone' => 'nullable|string',
                'pharmacy.address' => 'nullable|string',
                'admin.name' => 'required|string|max:255',
                'admin.email' => 'required|email|unique:users,email',
                'admin.password' => 'required|string|min:6'
            ]);

            DB::beginTransaction();

            // 1. Créer la pharmacie (désactivée par défaut, en attente de validation)
            $pharmacy = Pharmacy::create([
                'name' => $request->pharmacy['name'],
                'email' => $request->pharmacy['email'],
                'phone' => $request->pharmacy['phone'] ?? null,
                'address' => $request->pharmacy['address'] ?? null,
                'is_active' => false  // En attente de validation par Super Admin
            ]);

            // 2. Créer l'administrateur de la pharmacie
            $admin = User::create([
                'name' => $request->admin['name'],
                'email' => $request->admin['email'],
                'password' => Hash::make($request->admin['password']),
                'role' => 'admin',
                'pharmacy_id' => $pharmacy->id
            ]);

            DB::commit();

            // Envoyer un email au Super Admin pour l'informer (optionnel)
            // Mail::to('super@admin.com')->send(new NewPharmacyNotification($pharmacy, $admin));

            return response()->json([
                'success' => true,
                'message' => 'Votre pharmacie a été créée. En attente de validation par l\'administrateur.',
                'data' => [
                    'pharmacy' => $pharmacy,
                    'admin' => $admin
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()
            ], 500);
        }
    }
}