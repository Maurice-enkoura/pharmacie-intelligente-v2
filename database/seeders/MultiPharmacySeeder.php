<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Fournisseur;
use Illuminate\Support\Facades\Hash;

class MultiPharmacySeeder extends Seeder
{
    public function run()
    {
        // Liste des 10 pharmacies
        $pharmacies = [
            ['name' => 'Pharmacie Centrale', 'city' => 'Dakar', 'phone' => '771234560'],
            ['name' => 'Pharmacie Fass', 'city' => 'Dakar', 'phone' => '771234561'],
            ['name' => 'Pharmacie Liberté', 'city' => 'Dakar', 'phone' => '771234562'],
            ['name' => 'Pharmacie Mermoz', 'city' => 'Dakar', 'phone' => '771234563'],
            ['name' => 'Pharmacie Plateau', 'city' => 'Dakar', 'phone' => '771234564'],
            ['name' => 'Pharmacie Grand Yoff', 'city' => 'Dakar', 'phone' => '771234565'],
            ['name' => 'Pharmacie Ouakam', 'city' => 'Dakar', 'phone' => '771234566'],
            ['name' => 'Pharmacie Parcelles', 'city' => 'Dakar', 'phone' => '771234567'],
            ['name' => 'Pharmacie Almadies', 'city' => 'Dakar', 'phone' => '771234568'],
            ['name' => 'Pharmacie Thiaroye', 'city' => 'Dakar', 'phone' => '771234569'],
        ];

        // Liste des fournisseurs (pour varier)
        $fournisseursList = [
            ['nom' => 'LaboPharm Sénégal', 'contact' => 'M. Diop'],
            ['nom' => 'SantéPlus Distribution', 'contact' => 'Mme Sarr'],
            ['nom' => 'MediWest Afrique', 'contact' => 'M. Ndiaye'],
            ['nom' => 'PharmaLogistics', 'contact' => 'Mme Faye'],
            ['nom' => 'BioMédical Sénégal', 'contact' => 'M. Ba'],
            ['nom' => 'HealthCare Solutions', 'contact' => 'Mme Diallo'],
            ['nom' => 'Médicaments d\'Afrique', 'contact' => 'M. Sow'],
            ['nom' => 'PharmaExpress', 'contact' => 'Mme Mbaye'],
            ['nom' => 'Santé pour Tous', 'contact' => 'M. Thiam'],
            ['nom' => 'LaboMed International', 'contact' => 'Mme Fall'],
        ];

        $this->command->info('🚀 Création des 10 pharmacies...');
        $this->command->info('');

        foreach ($pharmacies as $index => $pharmacyData) {
            $pharmacyId = $index + 1;
            
            // Créer la pharmacie
            $pharmacy = Pharmacy::create([
                'name' => $pharmacyData['name'],
                'email' => strtolower(str_replace(' ', '', $pharmacyData['name'])) . '@pharmacie.sn',
                'phone' => $pharmacyData['phone'],
                'address' => $pharmacyData['city'] . ', Sénégal',
                'is_active' => true,
            ]);

            $this->command->info("📦 Pharmacie {$pharmacyId}: {$pharmacy->name}");

            // Créer l'administrateur (1)
            User::create([
                'name' => "Admin {$pharmacyData['name']}",
                'email' => "admin{$pharmacyId}@pharmacie.sn",
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'pharmacy_id' => $pharmacy->id,
            ]);

            // Créer le pharmacien (1)
            User::create([
                'name' => "Pharmacien {$pharmacyData['name']}",
                'email' => "pharmacien{$pharmacyId}@pharmacie.sn",
                'password' => Hash::make('password123'),
                'role' => 'pharmacien',
                'pharmacy_id' => $pharmacy->id,
            ]);

            // Créer les caissiers (2 ou 3 selon l'index)
            $caissiersCount = ($pharmacyId % 2 == 0) ? 3 : 2;
            
            for ($i = 1; $i <= $caissiersCount; $i++) {
                User::create([
                    'name' => "Caissier {$pharmacyData['name']}",
                    'email' => "caissier{$pharmacyId}_{$i}@pharmacie.sn",
                    'password' => Hash::make('password123'),
                    'role' => 'caissier',
                    'pharmacy_id' => $pharmacy->id,
                ]);
            }

            // 🔥 Créer 5 fournisseurs pour cette pharmacie
            for ($i = 1; $i <= 5; $i++) {
                $fournisseurIndex = (($pharmacyId - 1) * 5 + ($i - 1)) % count($fournisseursList);
                $fournisseur = $fournisseursList[$fournisseurIndex];
                
                Fournisseur::create([
                    'pharmacy_id' => $pharmacy->id,
                    'nom' => $fournisseur['nom'] . " " . $pharmacyId,
                    'contact' => $fournisseur['contact'],
                    'telephone' => "77" . str_pad($pharmacyId, 2, '0', STR_PAD_LEFT) . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'email' => "fournisseur{$pharmacyId}_{$i}@exemple.com",
                    'adresse' => "Zone industrielle, {$pharmacyData['city']}",
                ]);
            }

            $this->command->info("   👤 1 admin: admin{$pharmacyId}@pharmacie.sn");
            $this->command->info("   👤 1 pharmacien: pharmacien{$pharmacyId}@pharmacie.sn");
            $this->command->info("   👤 {$caissiersCount} caissier(s)");
            $this->command->info("   🏭 5 fournisseurs");
            $this->command->info("");
        }

        $this->command->info('========================================');
        $this->command->info('✅ 10 pharmacies créées avec succès !');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('🔑 Mot de passe par défaut: password123');
        $this->command->info('');
        
        for ($i = 1; $i <= 10; $i++) {
            $caissiersCount = ($i % 2 == 0) ? 3 : 2;
            $this->command->info("📦 Pharmacie {$i}:");
            $this->command->info("   👑 Admin: admin{$i}@pharmacie.sn");
            $this->command->info("   💊 Pharmacien: pharmacien{$i}@pharmacie.sn");
            for ($j = 1; $j <= $caissiersCount; $j++) {
                $this->command->info("   🏪 Caissier: caissier{$i}_{$j}@pharmacie.sn");
            }
            $this->command->info("   🏭 5 fournisseurs");
            $this->command->info('');
        }
    }
}