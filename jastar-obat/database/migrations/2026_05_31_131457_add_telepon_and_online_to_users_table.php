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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan setelah kolom 'email' agar rapi di database
            $table->string('no_telepon', 20)->nullable()->after('email');
            
            // Menambahkan setelah kolom 'password'
            $table->boolean('is_online')->default(false)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn(['no_telepon', 'is_online']);
        });
    }
};