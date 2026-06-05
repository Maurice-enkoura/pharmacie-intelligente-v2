<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Super Admin n'a pas besoin de pharmacie
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }
        
        // Vérifier que l'utilisateur a une pharmacie
        if (!$user || !$user->pharmacy_id) {
            return response()->json([
                'message' => 'Aucune pharmacie associée à cet utilisateur'
            ], 403);
        }
        
        // Vérifier que la pharmacie est active
        $pharmacy = $user->pharmacy;
        if (!$pharmacy || !$pharmacy->is_active) {
            return response()->json([
                'message' => 'Pharmacie inactive ou abonnement expiré'
            ], 403);
        }
        
        return $next($request);
    }
}