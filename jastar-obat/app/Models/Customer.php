<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama', 
        'no_telp', 
        'jenis_kelamin', 
        'umur', 
        'alamat'
    ];

    // Relasi: Satu customer bisa punya banyak paket
    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}