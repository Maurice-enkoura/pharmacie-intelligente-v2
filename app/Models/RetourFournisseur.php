<?php

namespace App\Models;

use App\Scopes\PharmacyScope;
use Illuminate\Database\Eloquent\Model;

class RetourFournisseur extends Model
{
    protected $table = 'retours_fournisseurs';  // ← Vérifier ce nom
    
    protected $fillable = [
        'pharmacy_id', 'fournisseur_id', 'numero_retour', 'date_retour',
        'motif', 'motif_commentaire', 'statut', 'montant_total', 'notes'
    ];
    
    protected $casts = [
        'date_retour' => 'date'
    ];
    
    protected static function booted()
    {
        static::addGlobalScope(new PharmacyScope);
    }
    
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
    
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
    
    public function ligneRetours()
    {
        return $this->hasMany(LigneRetourFournisseur::class);
    }
}