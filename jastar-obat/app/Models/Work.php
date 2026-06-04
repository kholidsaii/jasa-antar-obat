<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pekerjaan', 
        'deskripsi', 
        'package_id', 
        'user_id', 
        'vehicle_id',
        'harga_ongkos'
    ];

    // Relasi ke Paket
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // Relasi ke Kurir (Digunakan oleh Teamwork.vue)
    public function courier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // TAMBAHKAN INI: Relasi ke Kurir (Digunakan oleh Tracking.vue & PackageController)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Kendaraan
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}