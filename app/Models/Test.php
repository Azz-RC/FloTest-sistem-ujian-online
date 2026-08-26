<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'test';

    protected $primaryKey = 'id_test';

    protected $fillable = [
        'id_user',
        'judul_test',
        'kode_test',
        'tanggal_dibuat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function soal()
    {
        return $this->hasMany(Soal::class, 'id_test', 'id_test');
    }

    public function riwayatTes()
    {
        return $this->hasMany(RiwayatTes::class, 'id_test', 'id_test');
    }
}