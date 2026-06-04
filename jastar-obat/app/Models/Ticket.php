<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'category', 'resi', 'reporter', 'status'];

    // Relasi: Satu Tiket punya banyak Chat
    public function chats()
    {
        return $this->hasMany(TicketChat::class);
    }
}