<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:super_admin']);
    }
    
    public function index()
    {
        $backupDir = storage_path('app/backups');
        $files = [];
        
        if (is_dir($backupDir)) {
            foreach (glob($backupDir . '/backup_*.sql') as $file) {
                $files[] = [
                    'name' => basename($file),
                    'size' => $this->formatSize(filesize($file)),
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
        }
        
        // Trier par date décroissante
        usort($files, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
        
        return response()->json([
            'success' => true,
            'data' => $files
        ]);
    }
    
    public function create()
    {
        try {
            Artisan::call('db:backup');
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => '✅ Sauvegarde créée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function download($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'Fichier non trouvé'], 404);
        }
        
        return response()->download($filePath, $filename);
    }
    
    public function destroy($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (file_exists($filePath)) {
            unlink($filePath);
            return response()->json(['success' => true, 'message' => 'Sauvegarde supprimée']);
        }
        
        return response()->json(['success' => false, 'message' => 'Fichier non trouvé'], 404);
    }
    
    public function clean()
    {
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . '/backup_*.sql');
        $now = time();
        $deleted = 0;
        
        foreach ($files as $file) {
            if (filemtime($file) < $now - (7 * 24 * 60 * 60)) {
                unlink($file);
                $deleted++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "🧹 $deleted sauvegarde(s) ancienne(s) supprimée(s)"
        ]);
    }
    
    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}