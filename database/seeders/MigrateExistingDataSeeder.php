<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Fournisseur;
use App\Models\Medicament;
use App\Models\Pharmacy;
use App\Models\StockLot;
use App\Models\Vente;
use Illuminate\Database\Seeder;

class MigrateExistingDataSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPharmacy = Pharmacy::first();
        
        if (!$defaultPharmacy) {
            $this->command->error('Aucune pharmacie trouvée ! Lance d\'abord PharmacySeeder.');
            return;
        }

        $pharmacyId = $defaultPharmacy->id;

        // Migrer les médicaments
        $medicamentsCount = Medicament::whereNull('pharmacy_id')->update(['pharmacy_id' => $pharmacyId]);
        $this->command->info("$medicamentsCount médicaments migrés.");

        // Migrer les clients
        $clientsCount = Client::whereNull('pharmacy_id')->update(['pharmacy_id' => $pharmacyId]);
        $this->command->info("$clientsCount clients migrés.");

        // Migrer les fournisseurs
        $fournisseursCount = Fournisseur::whereNull('pharmacy_id')->update(['pharmacy_id' => $pharmacyId]);
        $this->command->info("$fournisseursCount fournisseurs migrés.");

        // Migrer les ventes
        $ventesCount = Vente::whereNull('pharmacy_id')->update(['pharmacy_id' => $pharmacyId]);
        $this->command->info("$ventesCount ventes migrées.");

        // Migrer les commandes
        $commandesCount = Commande::whereNull('pharmacy_id')->update(['pharmacy_id' => $pharmacyId]);
        $this->command->info("$commandesCount commandes migrées.");

        // Migrer les stock_lots
        $stockLotsCount = StockLot::whereNull('pharmacy_id')->update(['pharmacy_id' => $pharmacyId]);
        $this->command->info("$stockLotsCount lots de stock migrés.");

        $this->command->info('✅ Migration des données existantes terminée !');
    }
}