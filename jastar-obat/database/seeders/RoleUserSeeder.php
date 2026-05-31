<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        // 1. Superadmin
        User::updateOrCreate(['email' => 'superadmin@jastar.com'], [
            'name' => 'Super Administrator',
            'password' => $defaultPassword,
            'role' => 'superadmin'
        ]);

        // 2. Admin
        User::updateOrCreate(['email' => 'admin@jastar.com'], [
            'name' => 'Admin Operasional',
            'password' => $defaultPassword,
            'role' => 'admin'
        ]);

        // 3. Farmasi
        User::updateOrCreate(['email' => 'farmasi@jastar.com'], [
            'name' => 'Apoteker Utama',
            'password' => $defaultPassword,
            'role' => 'farmasi'
        ]);

        // 4. Kurir
        $kurir = User::updateOrCreate(['email' => 'kurir.budi@jastar.com'], [
            'name' => 'Budi Kurir',
            'password' => $defaultPassword,
            'role' => 'kurir'
        ]);

        // Berikan kendaraan untuk kurir
        Vehicle::updateOrCreate(['user_id' => $kurir->id], [
            'nama_kendaraan' => 'Honda Vario 125',
            'plat_nomor' => 'B 1234 ABC',
            'status' => 'Tersedia'
        ]);
    }
}