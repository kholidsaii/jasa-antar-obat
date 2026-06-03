<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('works', function (Blueprint $table) {
            if (!Schema::hasColumn('works', 'nama_pekerjaan')) {
                $table->string('nama_pekerjaan')->nullable()->after('id');
            }
            if (!Schema::hasColumn('works', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('nama_pekerjaan');
            }
        });
    }

    public function down()
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn(['nama_pekerjaan', 'deskripsi']);
        });
    }
};