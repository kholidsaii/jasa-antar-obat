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
        // 1. Hapus constraint bawaan lama
        DB::statement('ALTER TABLE packages DROP CONSTRAINT IF EXISTS packages_status_pengiriman_check');
        
        // 2. Tambahkan constraint baru dengan 9 status pengiriman yang sesuai catatan buku Anda
        DB::statement("ALTER TABLE packages ADD CONSTRAINT packages_status_pengiriman_check CHECK (status_pengiriman IN (
            '1. Verifikasi Jastar',
            '2. Stor Struk ke farmasi',
            '3. Ambil paket obat farmasi',
            '4. Diserah paket obat jastar',
            '5. Sedang menunggu kurir',
            '6. Diserahkan ke kurir',
            '7. Dalam perjalanan',
            '8. Sampai (Selesai)',
            '9. Cancel / Pending'
        ))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus constraint yang baru jika di-rollback
        DB::statement('ALTER TABLE packages DROP CONSTRAINT IF EXISTS packages_status_pengiriman_check');
        
        // Kembalikan ke constraint lama bawaan sebelumnya
        DB::statement("ALTER TABLE packages ADD CONSTRAINT packages_status_pengiriman_check CHECK (status_pengiriman IN (
            'Pesanan diverifikasi', 
            'Pengemasan', 
            'Menunggu Driver', 
            'Diperjalanan', 
            'Terkirim', 
            'Dibatalkan'
        ))");
    }
};