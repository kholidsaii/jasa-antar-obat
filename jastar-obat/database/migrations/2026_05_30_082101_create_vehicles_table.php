<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            // Tambahkan relasi ke tabel kurir (Cascade Delete)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->nullable();
            
            $table->string('nama_kendaraan');
            $table->string('plat_nomor', 50)->unique();
            $table->enum('status', ['Tersedia', 'Sedang Digunakan', 'Perbaikan'])->default('Tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
