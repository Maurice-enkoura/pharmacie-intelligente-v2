<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PharmacyController extends Controller
{
    // Supprime le constructeur avec middleware
    // Les middlewares sont maintenant dans les routes
    
    public function index(Request $request)
    {
        $query = Pharmacy::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        $pharmacies = $query->paginate(15);
        
        return response()->json($pharmacies);
    }

    public function show($id)
    {
        $pharmacy = Pharmacy::with(['users', 'medicaments', 'clients'])->findOrFail($id);
        return response()->json($pharmacy);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:pharmacies',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'siret' => 'nullable|string|unique:pharmacies',
            'is_active' => 'boolean'
        ]);

        $pharmacy = Pharmacy::create($request->all());

        return response()->json($pharmacy, 201);
    }

    public function update(Request $request, $id)
    {
        $pharmacy = Pharmacy::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|unique:pharmacies,email,' . $id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'siret' => 'nullable|string|unique:pharmacies,siret,' . $id,
            'is_active' => 'boolean',
            'subscription_ends_at' => 'nullable|date'
        ]);

        $pharmacy->update($request->all());

        return response()->json($pharmacy);
    }

    public function destroy($id)
    {
        $pharmacy = Pharmacy::findOrFail($id);
        
        // Empêcher la suppression si c'est la seule pharmacie
        if (Pharmacy::count() === 1) {
            return response()->json([
                'message' => 'Impossible de supprimer la dernière pharmacie'
            ], 400);
        }
        
        $pharmacy->delete();
        
        return response()->json(['message' => 'Pharmacie supprimée']);
    }

    public function stats($id)
    {
        $pharmacy = Pharmacy::findOrFail($id);
        
        return response()->json([
            'users_count' => $pharmacy->users()->count(),
            'medicaments_count' => $pharmacy->medicaments()->count(),
            'clients_count' => $pharmacy->clients()->count(),
            'ventes_count' => $pharmacy->ventes()->count(),
            'chiffre_affaires' => $pharmacy->ventes()->sum('montant_total'),
        ]);
    }
}