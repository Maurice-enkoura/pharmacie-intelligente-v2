<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class PharmacyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        
        // Si pas d'utilisateur connecté, ne pas filtrer (ex: seeders, tinker)
        if (!$user) {
            return;
        }
        
        // Super Admin voit tout
        if ($user->isSuperAdmin()) {
            return;
        }
        
        // Utilisateur normal : filtre par sa pharmacie
        if ($user->pharmacy_id) {
            $builder->where($model->getTable() . '.pharmacy_id', $user->pharmacy_id);
        } else {
            // Si l'utilisateur n'a pas de pharmacie, ne rien retourner
            $builder->whereRaw('1 = 0');
        }
    }
}