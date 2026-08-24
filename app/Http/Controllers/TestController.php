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

        // Cari room yang masih draft
        $riwayat = RiwayatTes::where('id_user', $request->id_user)
            ->where('id_test', $test->id_test)
            ->where('status_pengerjaan', 'draft')
            ->first();

        // Kalau belum ada, buat room baru
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

            // Ambil room yang masih draft
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

            // Ambil test beserta soal
            $test = Test::with('soal')->find($request->id_test);

            if (!$test) {
                DB::rollBack();

                return response()->json([
                    'message' => 'Test tidak ditemukan'
                ], 404);
            }

            $benar = 0;
            $total = count($request->jawaban);

            // Simpan jawaban dan cek kunci
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

            // Update room dari draft menjadi selesai
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
}