<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('password');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->enum('jenis_kepemilikan', ['Pribadi', 'Kantor'])->default('Kantor')->after('plat_nomor');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('jenis_kepemilikan');
        });
    }
};