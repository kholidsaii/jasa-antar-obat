<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('jarak_km', 8, 2)->nullable()->after('deskripsi_pesanan');
            $table->integer('total_harga')->nullable()->after('jarak_km');
        });
    }
    public function down(): void {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['jarak_km', 'total_harga']);
        });
    }
};