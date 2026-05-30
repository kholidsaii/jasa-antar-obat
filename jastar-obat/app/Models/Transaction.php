<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi secara otomatis
    protected $guarded = [];

    // Opsional: Relasi balik ke tabel Works jika dibutuhkan
    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}