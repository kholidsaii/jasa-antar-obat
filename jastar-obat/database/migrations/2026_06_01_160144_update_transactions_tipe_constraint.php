<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Dropping constraint lama bawaan PostgreSQL enum/check
        DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_tipe_check');
        
        // Menambahkan constraint baru yang mencakup tipe Mutasi Masuk & Keluar secara native
        DB::statement("ALTER TABLE transactions ADD CONSTRAINT transactions_tipe_check CHECK (tipe IN ('Uang Masuk', 'Uang Keluar', 'Mutasi', 'Mutasi Masuk', 'Mutasi Keluar'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_tipe_check');
        
        // Kembalikan ke aturan lama jika di-rollback
        DB::statement("ALTER TABLE transactions ADD CONSTRAINT transactions_tipe_check CHECK (tipe IN ('Uang Masuk', 'Uang Keluar', 'Mutasi'))");
    }
};