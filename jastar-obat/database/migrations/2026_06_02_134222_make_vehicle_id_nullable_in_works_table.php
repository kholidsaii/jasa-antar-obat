<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menghapus aturan NOT NULL dari kolom vehicle_id di PostgreSQL
        DB::statement('ALTER TABLE works ALTER COLUMN vehicle_id DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan aturan NOT NULL jika di-rollback
        DB::statement('ALTER TABLE works ALTER COLUMN vehicle_id SET NOT NULL');
    }
};