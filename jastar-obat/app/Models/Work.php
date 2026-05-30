<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id', 
        'user_id',     // ID Kurir
        'vehicle_id', 
        'harga_ongkos'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}