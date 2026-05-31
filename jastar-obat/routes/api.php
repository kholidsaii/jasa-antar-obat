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

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        
        Route::get('/user', function (Request $request) { return $request->user(); });
        Route::post('/logout', [AuthController::class, 'logout']);

        // 1. Khusus Kurir & Superadmin
        Route::middleware('role:superadmin,kurir')->group(function () {
        });

        // 2. Modul Pengiriman & Pasien (Tambahkan kurir di sini agar API bisa diakses)
        Route::middleware('role:superadmin,admin,farmasi,kurir')->group(function () {
            Route::apiResource('customers', CustomerController::class);
            Route::apiResource('packages', PackageController::class);
        });

        // 3. Modul Teamwork / HR
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