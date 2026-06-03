<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            // Menambahkan kolom waktu_pengantaran
            if (!Schema::hasColumn('packages', 'waktu_pengantaran')) {
                $table->string('waktu_pengantaran')->default('Segera')->after('metode_pembayaran');
            }
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('waktu_pengantaran');
        });
    }
};