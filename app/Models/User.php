<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function test()
    {
        return $this->hasMany(Test::class, 'id_user', 'id_user');
    }

    public function riwayatTes()
    {
        return $this->hasMany(RiwayatTes::class, 'id_user', 'id_user');
    }
}