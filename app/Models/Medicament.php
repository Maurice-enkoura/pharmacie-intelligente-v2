<?php

namespace App\Models;

use App\Scopes\PharmacyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Categorie;
use App\Models\StockLot;
use App\Models\LigneVente;
use App\Models\LigneCommande;

class Medicament extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom', 'dci', 'forme', 'dosage', 'categorie_id',
        'stock_actuel', 'seuil_alerte', 'prix_achat',
        'prix_vente', 'ordonnance_requise', 'pharmacy_id',
    ];

    // 🔥 AJOUTER CETTE MÉTHODE - TRÈS IMPORTANT !
    protected static function booted()
    {
        static::addGlobalScope(new PharmacyScope);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function stockLots()
    {
        return $this->hasMany(StockLot::class);
    }

    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class);
    }

    public function ligneCommandes()
    {
        return $this->hasMany(LigneCommande::class);
    }
    
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}