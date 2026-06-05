<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\PharmacyScope;

class Client extends Model
{
    protected $fillable = [
        'nom', 'prenom', 'telephone', 'email', 
        'adresse', 'medicaments_chroniques','pharmacy_id'
    ];
    
    protected $casts = [
        'medicaments_chroniques' => 'array'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new PharmacyScope);
    }

    
    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

   
}