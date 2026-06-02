<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageHistory extends Model
{
    use HasFactory;

    protected $fillable = ['package_id', 'status_pengiriman', 'keterangan'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}