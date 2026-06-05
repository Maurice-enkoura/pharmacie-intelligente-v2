<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'address',
        'logo', 'siret', 'is_active', 'subscription_ends_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_ends_at' => 'datetime'
    ];

    // Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function medicaments()
    {
        return $this->hasMany(Medicament::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function fournisseurs()
    {
        return $this->hasMany(Fournisseur::class);
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    public function stockLots()
    {
        return $this->hasMany(StockLot::class);
    }

    // Vérifie si l'abonnement est actif
    public function hasActiveSubscription(): bool
    {
        return $this->is_active && (
            is_null($this->subscription_ends_at) || 
            $this->subscription_ends_at->isFuture()
        );
    }
}