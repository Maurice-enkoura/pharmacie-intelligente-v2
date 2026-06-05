<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneRetourFournisseur extends Model
{
    protected $table = 'ligne_retours_fournisseurs';  // ← Vérifier ce nom
    
    protected $fillable = [
        'retour_fournisseur_id', 'medicament_id', 'stock_lot_id',
        'quantite', 'prix_achat_unitaire', 'sous_total', 'motif'
    ];
    
    public function retour()
    {
        return $this->belongsTo(RetourFournisseur::class, 'retour_fournisseur_id');
    }
    
    public function medicament()
    {
        return $this->belongsTo(Medicament::class);
    }
    
    public function stockLot()
    {
        return $this->belongsTo(StockLot::class);
    }
}