<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\PharmacyScope;

class Vente extends Model
{
    protected $fillable = [
        'numero_facture', 'client_id', 'user_id', 'date_vente',
        'type_vente', 'ordonnance_ref', 'montant_total', 
        'montant_paye', 'monnaie_rendue', 'mode_paiement','pharmacy_id'
    ];
    
    // Relations
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class);
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
