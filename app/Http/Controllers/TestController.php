<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Soal;
use App\Models\RiwayatTes;
use App\Models\DetailRiwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestController extends Controller
{
    // =========================
    // BUAT TEST
    // =========================

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:user,id_user',
            'judul_test' => 'required|string|max:255',
            'soal' => 'required|array|min:1',

            'soal.*.pertanyaan' => 'required|string',
            'soal.*.pilihan_a' => 'required|string|max:255',
            'soal.*.pilihan_b' => 'required|string|max:255',
            'soal.*.pilihan_c' => 'required|string|max:255',
            'soal.*.pilihan_d' => 'required|string|max:255',
            'soal.*.kunci_jawaban' => 'required|in:A,B,C,D',
        ]);

        DB::beginTransaction();

        try {
            do {
                $kodeTest = strtoupper(Str::random(6));
            } while (Test::where('kode_test', $kodeTest)->exists());

            $test = Test::create([
                'id_user' => $request->id_user,
                'judul_test' => $request->judul_test,
                'kode_test' => $kodeTest,
                'tanggal_dibuat' => now()->toDateString(),
            ]);

            foreach ($request->soal as $index => $dataSoal) {
                Soal::create([
                    'id_test' => $test->id_test,
                    'nomor_soal' => $index + 1,
                    'pertanyaan' => $dataSoal['pertanyaan'],
                    'pilihan_a' => $dataSoal['pilihan_a'],
                    'pilihan_b' => $dataSoal['pilihan_b'],
                    'pilihan_c' => $dataSoal['pilihan_c'],
                    'pilihan_d' => $dataSoal['pilihan_d'],
                    'kunci_jawaban' => $dataSoal['kunci_jawaban'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Test berhasil dibuat',
                'data' => [
                    'id_test' => $test->id_test,
                    'judul_test' => $test->judul_test,
                    'kode_test' => $test->kode_test,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal membuat test',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // =========================
    // AMBIL TEST BERDASARKAN ID
    // =========================

    public function show($id)
    {
        $test = Test::with('soal')->find($id);

        if (!$test) {
            return response()->json([
                'message' => 'Test tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Test ditemukan',
            'data' => $test
        ]);
    }


    // =========================
    // AMBIL TEST BERDASARKAN KODE
    // =========================

    public function showByKode($kode)
    {
        $test = Test::with('soal')
            ->where('kode_test', strtoupper($kode))
            ->first();

        if (!$test) {
            return response()->json([
                'message' => 'Kode test tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Test ditemukan',
            'data' => $test
        ]);
    }


    // =========================
    // MASUK ROOM
    // =========================

    public function masukRoom(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:user,id_user',
            'kode_test' => 'required|string|max:100',
        ]);

        $test = Test::with('soal')
            ->where('kode_test', strtoupper($request->kode_test))
            ->first();

        if (!$test) {
            return response()->json([
                'message' => 'Kode test tidak ditemukan'
            ], 404);
        }

        $riwayat = RiwayatTes::where('id_user', $request->id_user)
            ->where('id_test', $test->id_test)
            ->where('status_pengerjaan', 'draft')
            ->first();

        if (!$riwayat) {
            $riwayat = RiwayatTes::create([
                'id_user' => $request->id_user,
                'id_test' => $test->id_test,
                'tanggal_masuk' => now(),
                'skor_akhir' => null,
                'jumlah_benar' => 0,
                'jumlah_salah' => 0,
                'status_pengerjaan' => 'draft',
            ]);
        }

        return response()->json([
            'message' => 'Berhasil masuk room',
            'data' => [
                'id_riwayat_tes' => $riwayat->id_riwayat_tes,
                'id_user' => $riwayat->id_user,
                'id_test' => $test->id_test,
                'judul_test' => $test->judul_test,
                'kode_test' => $test->kode_test,
                'status_pengerjaan' => $riwayat->status_pengerjaan,
                'soal' => $test->soal,
            ]
        ]);
    }


    // =========================
    // KERJAKAN TEST / SUBMIT
    // =========================

    public function kerjakanTest(Request $request)
    {
        $request->validate([
            'id_riwayat_tes' => 'required|exists:riwayat_tes,id_riwayat_tes',
            'id_user' => 'required|exists:user,id_user',
            'id_test' => 'required|exists:test,id_test',
            'jawaban' => 'required|array|min:1',

            'jawaban.*.id_soal' => 'required|exists:soal,id_soal',
            'jawaban.*.jawaban' => 'required|in:A,B,C,D',
        ]);

        DB::beginTransaction();

        try {
            $riwayat = RiwayatTes::where(
                'id_riwayat_tes',
                $request->id_riwayat_tes
            )
                ->where('id_user', $request->id_user)
                ->where('id_test', $request->id_test)
                ->where('status_pengerjaan', 'draft')
                ->first();

            if (!$riwayat) {
                DB::rollBack();

                return response()->json([
                    'message' => 'Riwayat room tidak ditemukan atau sudah selesai'
                ], 404);
            }

            $test = Test::with('soal')->find($request->id_test);

            if (!$test) {
                DB::rollBack();

                return response()->json([
                    'message' => 'Test tidak ditemukan'
                ], 404);
            }

            $benar = 0;
            $total = count($request->jawaban);

            $benar = 0;
$total = count($request->jawaban);

// VALIDASI JUMLAH SOAL
$jumlahSoalTest = $test->soal->count();

if ($total !== $jumlahSoalTest) {
    DB::rollBack();

    return response()->json([
        'message' => 'Jumlah jawaban tidak sesuai dengan jumlah soal test',
        'jumlah_soal' => $jumlahSoalTest,
        'jumlah_jawaban' => $total,
    ], 422);
}

// VALIDASI DUPLIKAT SOAL
$idSoalJawaban = collect($request->jawaban)->pluck('id_soal');

if ($idSoalJawaban->count() !== $idSoalJawaban->unique()->count()) {
    DB::rollBack();

    return response()->json([
        'message' => 'Terdapat soal yang dijawab lebih dari satu kali'
    ], 422);
}

// VALIDASI SOAL HARUS MILIK TEST
$idSoalTest = $test->soal
    ->pluck('id_soal')
    ->sort()
    ->values()
    ->toArray();

$idSoalJawabanArray = $idSoalJawaban
    ->sort()
    ->values()
    ->toArray();

if ($idSoalTest !== $idSoalJawabanArray) {
    DB::rollBack();

    return response()->json([
        'message' => 'Jawaban tidak sesuai dengan soal pada test'
    ], 422);
}

            foreach ($request->jawaban as $jawaban) {
                $soal = $test->soal->firstWhere(
                    'id_soal',
                    $jawaban['id_soal']
                );

                if (!$soal) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Soal tidak termasuk dalam test ini',
                        'id_soal' => $jawaban['id_soal']
                    ], 422);
                }

                if ($soal->kunci_jawaban === $jawaban['jawaban']) {
                    $benar++;
                }

                DetailRiwayat::create([
                    'id_riwayat_tes' => $riwayat->id_riwayat_tes,
                    'id_soal' => $jawaban['id_soal'],
                    'jawaban_siswa' => $jawaban['jawaban'],
                ]);
            }

            $salah = $total - $benar;

            $nilai = $total > 0
                ? ($benar / $total) * 100
                : 0;

            $riwayat->update([
                'skor_akhir' => $nilai,
                'jumlah_benar' => $benar,
                'jumlah_salah' => $salah,
                'status_pengerjaan' => 'selesai',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Test berhasil dikerjakan',
                'data' => [
                    'id_riwayat_tes' => $riwayat->id_riwayat_tes,
                    'id_user' => $riwayat->id_user,
                    'id_test' => $riwayat->id_test,
                    'benar' => $benar,
                    'salah' => $salah,
                    'total_soal' => $total,
                    'nilai' => $nilai,
                    'status_pengerjaan' => $riwayat->status_pengerjaan,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan hasil test',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // =========================
    // AMBIL RIWAYAT / HASIL TEST
    // =========================

    public function riwayatTest($id)
    {
        $riwayat = RiwayatTes::with([
            'user',
            'test',
            'detailRiwayat.soal'
        ])->find($id);

        if (!$riwayat) {
            return response()->json([
                'message' => 'Riwayat test tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Riwayat test ditemukan',
            'data' => [
                'id_riwayat_tes' => $riwayat->id_riwayat_tes,
                'id_user' => $riwayat->id_user,
                'id_test' => $riwayat->id_test,
                'judul_test' => $riwayat->test->judul_test,
                'kode_test' => $riwayat->test->kode_test,
                'tanggal_masuk' => $riwayat->tanggal_masuk,
                'skor_akhir' => $riwayat->skor_akhir,
                'jumlah_benar' => $riwayat->jumlah_benar,
                'jumlah_salah' => $riwayat->jumlah_salah,
                'status_pengerjaan' => $riwayat->status_pengerjaan,
                'jawaban' => $riwayat->detailRiwayat,
            ]
        ]);
    }


    // =========================
    // DAFTAR SEMUA RIWAYAT TEST USER
    // =========================

    public function daftarRiwayat($id_user)
    {
        $riwayat = RiwayatTes::with('test')
            ->where('id_user', $id_user)
            ->orderBy('tanggal_masuk', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar riwayat test ditemukan',
            'data' => $riwayat->map(function ($item) {
                return [
                    'id_riwayat_tes' => $item->id_riwayat_tes,
                    'id_user' => $item->id_user,
                    'id_test' => $item->id_test,
                    'judul_test' => $item->test->judul_test,
                    'kode_test' => $item->test->kode_test,
                    'tanggal_masuk' => $item->tanggal_masuk,
                    'skor_akhir' => $item->skor_akhir,
                    'jumlah_benar' => $item->jumlah_benar,
                    'jumlah_salah' => $item->jumlah_salah,
                    'status_pengerjaan' => $item->status_pengerjaan,
                ];
            })
        ]);
    }
}