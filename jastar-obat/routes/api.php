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
use App\Http\Controllers\Api\TicketController;

Route::prefix('v1')->group(function () {
    // Rute Publik (Tidak perlu login)
    Route::post('/login', [AuthController::class, 'login']);
    
    // Rute Publik untuk Halaman Tracking Pasien (Harus di luar auth:sanctum)
    Route::get('/packages/{id}', [PackageController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        
        Route::get('/user', function (Request $request) { return $request->user(); });
        Route::post('/logout', [AuthController::class, 'logout']);

        // 1. Khusus Kurir & Superadmin
        Route::middleware('role:superadmin,kurir')->group(function () {
            // Jika ada endpoint spesifik kurir, taruh di sini
        });

        // 2. Modul Pengiriman & Pasien (Tambahkan kurir di sini agar API bisa diakses)
        Route::middleware('role:superadmin,admin,farmasi,kurir')->group(function () {
            Route::apiResource('customers', CustomerController::class);
            // Pengecualian: Route index, store, update, destroy untuk packages (karena show sudah di public)
            Route::get('/packages', [PackageController::class, 'index']);
            Route::post('/packages', [PackageController::class, 'store']);
            Route::put('/packages/{id}', [PackageController::class, 'update']);
            Route::delete('/packages/{id}', [PackageController::class, 'destroy']);
        });

        // 3. Modul Teamwork / HR
        // Biarkan users (Buku Induk Karyawan) hanya bisa diakses admin
       Route::middleware('role:superadmin,admin')->group(function () {
            Route::apiResource('users', UserController::class); 
            
            // TAMBAHKAN INI:
            Route::get('/tickets', [TicketController::class, 'index']);
            Route::post('/tickets', [TicketController::class, 'store']);
            Route::post('/tickets/{id}/reply', [TicketController::class, 'reply']);
            Route::put('/tickets/{id}/status', [TicketController::class, 'updateStatus']);
        });

        // BUKA AKSES vehicles dan works untuk kurir agar fitur Auto-Assign berjalan lancar!
        Route::middleware('role:superadmin,admin,kurir')->group(function () {
            Route::apiResource('vehicles', VehicleController::class); 
            Route::apiResource('works', WorkController::class);       
        });

        // 4. Modul Keuangan
        Route::middleware('role:superadmin,admin')->group(function () {
            Route::apiResource('transactions', TransactionController::class);
        });
    });
});