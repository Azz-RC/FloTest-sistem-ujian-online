<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatTes extends Model
{
    protected $table = 'riwayat_tes';

    protected $primaryKey = 'id_riwayat_tes';

    protected $fillable = [
        'id_user',
        'id_test',
        'tanggal_masuk',
        'skor_akhir',
        'jumlah_benar',
        'jumlah_salah',
        'status_pengerjaan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'id_test', 'id_test');
    }

    public function detailRiwayat()
    {
        return $this->hasMany(
            DetailRiwayat::class,
            'id_riwayat_tes',
            'id_riwayat_tes'
        );
    }
}