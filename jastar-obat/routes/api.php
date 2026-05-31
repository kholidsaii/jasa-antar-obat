<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\WorkController;
use App\Http\Controllers\Api\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Route Publik (Tanpa Token)
    Route::post('/login', [AuthController::class, 'login']);

    // Route Terproteksi (Wajib Bawa Token Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Akses Profil & Logout (Semua Role)
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/logout', [AuthController::class, 'logout']);

        // -------------------------------------------------------------
        // PEMBAGIAN HAK AKSES
        // -------------------------------------------------------------

        // 1. Khusus Kurir & Superadmin (Bisa melihat tugasnya sendiri)
        Route::middleware('role:superadmin,kurir')->group(function () {
            // Bisa diperluas nanti untuk fungsi khusus kurir update status paket
        });

        // 2. Modul Pengiriman & Pasien (Hanya Admin, Farmasi, Superadmin)
        Route::middleware('role:superadmin,admin,farmasi')->group(function () {
            Route::apiResource('customers', CustomerController::class);
            Route::apiResource('packages', PackageController::class);
        });

        // 3. Modul Teamwork / HR (Hanya Admin & Superadmin)
        Route::middleware('role:superadmin,admin')->group(function () {
            Route::apiResource('users', UserController::class);       
            Route::apiResource('vehicles', VehicleController::class); 
            Route::apiResource('works', WorkController::class);       
        });

        // 4. Modul Keuangan
        Route::middleware('role:superadmin,admin')->group(function () {
            Route::apiResource('transactions', TransactionController::class);
        });
    });
});