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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            // Foreign key relasi ke tabel customers
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            
            $table->text('deskripsi_pesanan');
            $table->enum('status_pengiriman', [
                'Pesanan diverifikasi', 
                'Pengemasan', 
                'Menunggu Driver', 
                'Diperjalanan', 
                'Terkirim'
            ])->default('Pesanan diverifikasi');
            $table->enum('status_pembayaran', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->string('metode_pembayaran')->nullable()->default('Tunai / Cash');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
