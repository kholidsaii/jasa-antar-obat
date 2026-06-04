<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketChat extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'role', 'sender_name', 'message'];

    // Relasi: Chat ini milik sebuah Tiket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}