<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 
        'deskripsi_pesanan', 
        'status_pengiriman', 
        'status_pembayaran',
        'metode_pembayaran'
    ];

    // Relasi: Paket ini milik siapa?
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi: Paket ini dikerjakan di penugasan mana?
    public function work()
    {
        return $this->hasOne(Work::class);
    }
}