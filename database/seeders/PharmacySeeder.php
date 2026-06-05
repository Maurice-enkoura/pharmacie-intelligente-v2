<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Database\Seeder;

class PharmacySeeder extends Seeder
{
    public function run(): void
    {
        // Créer la pharmacie par défaut
        $defaultPharmacy = Pharmacy::create([
            'name' => 'Pharmacie Principale',
            'email' => 'contact@pharmacie-principale.com',
            'phone' => '+221 77 000 00 00',
            'address' => 'Dakar, Sénégal',
            'is_active' => true,
        ]);

        // Migrer les utilisateurs existants vers cette pharmacie
        User::whereNull('pharmacy_id')->update(['pharmacy_id' => $defaultPharmacy->id]);

        $this->command->info('Pharmacie par défaut créée avec ID: ' . $defaultPharmacy->id);
        $this->command->info('Tous les utilisateurs existants ont été migrés vers cette pharmacie.');
    }
}