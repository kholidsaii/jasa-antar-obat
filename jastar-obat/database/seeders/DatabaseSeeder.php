<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        
        // PERBAIKAN: Tambahkan $this-> sebelum call()
        $this->call([
            RoleUserSeeder::class,
            // Tambahkan class seeder lainnya di sini
        ]);
    }
}