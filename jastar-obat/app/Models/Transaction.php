<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipe', // Uang Masuk, Uang Keluar
        'metode_pembayaran', 
        'nominal', 
        'deskripsi'
    ];
}