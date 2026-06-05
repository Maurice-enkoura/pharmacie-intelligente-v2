<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\PharmacyScope;

class Fournisseur extends Model
{
    protected $fillable = [
        'nom', 'contact', 'telephone', 'email', 'adresse','pharmacy_id'
    ];
    
    public function stockLots()
    {
        return $this->hasMany(StockLot::class);
    }
    
    public function commandes()
    {
        return $this->hasMany(Commande::class);
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