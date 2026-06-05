<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // D'abord créer la table principale
        Schema::create('retours_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained()->onDelete('cascade');
            $table->foreignId('fournisseur_id')->constrained()->onDelete('cascade');
            $table->string('numero_retour')->unique();
            $table->date('date_retour');
            $table->enum('motif', ['perime', 'defectueux', 'erreur_commande', 'autre'])->default('autre');
            $table->text('motif_commentaire')->nullable();
            $table->enum('statut', ['en_attente', 'valide', 'refuse', 'traite'])->default('en_attente');
            $table->decimal('montant_total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Ensuite créer la table des lignes
        Schema::create('ligne_retours_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retour_fournisseur_id')->constrained('retours_fournisseurs')->onDelete('cascade');
            $table->foreignId('medicament_id')->constrained();
            $table->foreignId('stock_lot_id')->nullable()->constrained();
            $table->integer('quantite');
            $table->decimal('prix_achat_unitaire', 10, 2);
            $table->decimal('sous_total', 10, 2);
            $table->text('motif')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_retours_fournisseurs');
        Schema::dropIfExists('retours_fournisseurs');
    }
};