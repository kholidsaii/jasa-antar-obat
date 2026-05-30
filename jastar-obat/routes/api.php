<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\WorkController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Prefix v1 untuk standarisasi API
Route::prefix('v1')->group(function () {
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('packages', PackageController::class);
    Route::apiResource('users', UserController::class);       // API Karyawan/Kurir
    Route::apiResource('vehicles', VehicleController::class); // API Kendaraan
    Route::apiResource('works', WorkController::class);       // API Pekerjaan/Penugasan
    Route::apiResource('transactions', App\Http\Controllers\Api\TransactionController::class);
});