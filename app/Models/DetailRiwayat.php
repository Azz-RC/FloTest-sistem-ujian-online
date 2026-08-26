<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailRiwayat extends Model
{
    protected $table = 'detail_riwayat';

    protected $primaryKey = 'id_detail_riwayat';

    protected $fillable = [
        'id_riwayat_tes',
        'id_soal',
        'jawaban_siswa',
    ];

    public function riwayatTes()
    {
        return $this->belongsTo(
            RiwayatTes::class,
            'id_riwayat_tes',
            'id_riwayat_tes'
        );
    }

    public function soal()
    {
        return $this->belongsTo(
            Soal::class,
            'id_soal',
            'id_soal'
        );
    }
}