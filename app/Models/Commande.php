<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\PharmacyScope;

class Commande extends Model
{
    protected $fillable = [
        'numero_commande', 'fournisseur_id', 'date_commande',
        'statut', 'montant_total', 'pharmacy_id'
    ];
    
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
    
    public function ligneCommandes()
    {
        return $this->hasMany(LigneCommande::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new PharmacyScope);
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}