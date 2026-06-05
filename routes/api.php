<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MedicamentController;
use App\Http\Controllers\Api\V1\VenteController;
use App\Http\Controllers\Api\V1\FournisseurController;
use App\Http\Controllers\Api\V1\CommandeController;
use App\Http\Controllers\Api\V1\StockController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\CategorieController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\RapportController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\PharmacyController;
use App\Http\Controllers\Api\V1\SuperAdminController;
use App\Http\Controllers\Api\V1\RegisterPharmacyController;
use App\Http\Controllers\Api\V1\RetourFournisseurController;
use App\Http\Controllers\Api\V1\BackupController;

Route::prefix('v1')->group(function () {

    // ================= AUTH =================
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register-pharmacy', [RegisterPharmacyController::class, 'register']);

    // ================= PROTECTED =================
    Route::middleware(['auth:sanctum', 'pharmacy'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // CATEGORIES
        Route::get('/categories', [CategorieController::class, 'index']);

        // VENTES PDF / EMAIL
        Route::get('/ventes/{id}/send-email', [VenteController::class, 'sendEmail']);
        Route::get('/ventes/{id}/pdf', [VenteController::class, 'generatePDF']);

        // MEDICAMENTS
        Route::get('/medicaments', [MedicamentController::class, 'index']);
        Route::get('/medicaments/{id}', [MedicamentController::class, 'show']);

        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::post('/medicaments', [MedicamentController::class, 'store']);
            Route::put('/medicaments/{id}', [MedicamentController::class, 'update']);
        });

        Route::middleware(['role:admin'])->group(function () {
            Route::delete('/medicaments/{id}', [MedicamentController::class, 'destroy']);
            Route::get('/medicaments/export', [MedicamentController::class, 'export']);
            Route::post('/medicaments/import', [MedicamentController::class, 'import']);
            Route::get('/medicaments/template', [MedicamentController::class, 'downloadTemplate']);
            Route::post('/medicaments/{id}/restore', [MedicamentController::class, 'restore']);
        });

        // VENTES
        Route::get('/ventes', [VenteController::class, 'index']);
        Route::get('/ventes/{id}', [VenteController::class, 'show']);
        Route::post('/ventes', [VenteController::class, 'store']);

        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::delete('/ventes/{id}/cancel', [VenteController::class, 'cancel']);
        });

        // CLIENTS
        Route::get('/clients', [ClientController::class, 'index']);
        Route::get('/clients/{id}', [ClientController::class, 'show']);
        Route::post('/clients', [ClientController::class, 'store']);

        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::put('/clients/{id}', [ClientController::class, 'update']);
            Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
        });

        // FOURNISSEURS (admin only)
        Route::middleware(['role:admin'])->group(function () {
            Route::apiResource('fournisseurs', FournisseurController::class);
        });

        // COMMANDES
        Route::middleware(['role:admin'])->group(function () {
            Route::apiResource('commandes', CommandeController::class);
            Route::put('/commandes/{id}/reception', [CommandeController::class, 'reception']);
        });

        // STOCK
        Route::get('/stock/alertes', [StockController::class, 'alertes']);

        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::post('/stock/entrees', [StockController::class, 'entrees']);
            Route::get('/stock/historique', [StockController::class, 'historique']);
            Route::get('/stock/lots/{medicament_id}', [StockController::class, 'getLotsByMedicament']);
        });

        // RETOURS FOURNISSEURS
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::apiResource('retours-fournisseurs', RetourFournisseurController::class);
            Route::put('/retours-fournisseurs/{id}/statut', [RetourFournisseurController::class, 'updateStatut']);
        });

        // DASHBOARD
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index']);
        });

        // RAPPORTS
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/rapports', [RapportController::class, 'index']);
            Route::get('/rapports/excel', [RapportController::class, 'exportExcel']);
        });

        // UTILISATEURS
        Route::middleware(['role:admin'])->group(function () {
            Route::apiResource('utilisateurs', UserController::class);
        });
    });

    // ================= SUPER ADMIN =================
    Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {

        Route::apiResource('admin/pharmacies', PharmacyController::class);
        Route::get('admin/pharmacies/{id}/stats', [PharmacyController::class, 'stats']);

        Route::get('/super-admin/stats', [SuperAdminController::class, 'stats']);
        Route::get('/super-admin/users', [SuperAdminController::class, 'users']);
        Route::post('/super-admin/pharmacies/{id}/renew', [SuperAdminController::class, 'renewSubscription']);

        // BACKUPS
        Route::get('/backups', [BackupController::class, 'index']);
        Route::post('/backups/create', [BackupController::class, 'create']);
        Route::get('/backups/download/{filename}', [BackupController::class, 'download']);
        Route::delete('/backups/{filename}', [BackupController::class, 'destroy']);
        Route::post('/backups/clean', [BackupController::class, 'clean']);
    });

});