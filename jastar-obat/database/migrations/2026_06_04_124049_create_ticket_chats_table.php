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
        Schema::create('ticket_chats', function (Blueprint $table) {
            $table->id();
            // Menyambungkan chat ke tiket (hapus chat jika tiket dihapus)
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->enum('role', ['user', 'admin']); // Siapa yang mengirim pesan
            $table->string('sender_name');
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_chats');
    }
};
