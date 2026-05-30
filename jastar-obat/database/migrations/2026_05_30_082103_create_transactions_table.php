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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // UBAH BAGIAN INI: Ganti 'set null' menjadi 'cascade'
            $table->foreignId('work_id')->nullable()->constrained('works')->onDelete('cascade');
            
            $table->string('deskripsi');
            $table->decimal('nominal', 12, 2);
            $table->enum('tipe', ['Uang Masuk', 'Uang Keluar'])->default('Uang Masuk');
            $table->string('metode_pembayaran')->default('Tunai / Cash');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
