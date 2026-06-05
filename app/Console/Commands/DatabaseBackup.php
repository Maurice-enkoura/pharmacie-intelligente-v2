<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Sauvegarde la base de données';

    public function handle()
    {
        $this->info('🔄 Sauvegarde en cours...');
        
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backupDir = storage_path('app/backups');
        
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $backupPath = $backupDir . '/' . $filename;
        
        // Chemin mysqldump
        $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        
        $command = "\"$mysqldump\" --host=$host --user=$username --password=$password $database > \"$backupPath\"";
        
        exec($command, $output, $result);
        
        if ($result === 0 && file_exists($backupPath) && filesize($backupPath) > 0) {
            $this->info('✅ Sauvegarde réussie !');
            $this->info('📁 Fichier : ' . $filename);
            $this->info('📁 Emplacement : ' . $backupPath);
        } else {
            $this->error('❌ Échec de la sauvegarde');
            
            // Essayer sans le chemin absolu
            $command2 = "mysqldump --host=$host --user=$username --password=$password $database > \"$backupPath\"";
            exec($command2, $output2, $result2);
            
            if ($result2 === 0 && file_exists($backupPath) && filesize($backupPath) > 0) {
                $this->info('✅ Sauvegarde réussie (méthode alternative) !');
            } else {
                $this->error('Erreur: ' . implode("\n", $output));
            }
        }
    }
}