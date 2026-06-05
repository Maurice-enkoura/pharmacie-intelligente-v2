<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Non authentifié'
            ], 401);
        }
        
        $userRole = Auth::user()->role;
        
        // Super Admin a tous les accès
        if ($userRole === 'super_admin') {
            return $next($request);
        }
        
        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Accès non autorisé. Rôle requis : ' . implode(', ', $roles)
            ], 403);
        }
        
        return $next($request);
    }
}