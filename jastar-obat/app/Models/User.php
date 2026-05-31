<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Pastikan trait ini ada

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Tambahkan HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'foto',
        'no_telepon',
        'is_online',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Helper untuk mengecek role di middleware
    public function hasRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    public function works()
    {
        return $this->hasMany(Work::class, 'user_id');
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }
}