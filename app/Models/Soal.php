<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soal';

    protected $primaryKey = 'id_soal';

    protected $fillable = [
        'id_test',
        'nomor_soal',
        'pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'kunci_jawaban',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class, 'id_test', 'id_test');
    }

    public function detailRiwayat()
    {
        return $this->hasMany(DetailRiwayat::class, 'id_soal', 'id_soal');
    }
}