<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Scopes\PharmacyScope;

#[ObservedBy(\App\Observers\StockLotObserver::class)]
class StockLot extends Model
{
    protected $fillable = [
        'medicament_id', 'fournisseur_id', 'lot_number',
        'quantite_initiale', 'quantite_restante', 'date_peremption',
        'prix_achat_unitaire', 'date_reception', 'pharmacy_id'
    ];
    
    protected $casts = [
        'date_peremption' => 'date',
        'date_reception' => 'date'
    ];
    
    // Alerte péremption < 30 jours
    public function isExpiringSoon(): bool
    {
        return $this->date_peremption->diffInDays(now()) <= 30;
    }
    
    public function medicament()
    {
        return $this->belongsTo(Medicament::class);
    }
    
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
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