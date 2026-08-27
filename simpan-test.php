<?php

session_start();

if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}

include "koneksi.php";


if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: buat-test.php");
    exit;
}


$judul_test = $_SESSION["judul_test"] ?? "";

$soal = $_POST["soal"] ?? [];


if ($judul_test == "" || empty($soal)) {
    die("Data test tidak lengkap.");
}


// =====================================
// AMBIL ID USER DARI SESSION
// =====================================

$id_user = $_SESSION["id_user"] ?? 0;

if ($id_user == 0) {
    die("ID user tidak ditemukan.");
}


// =====================================
// BUAT KODE TEST RANDOM
// =====================================

function buatKodeTest($panjang = 6)
{
    $karakter = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    $kode = "";

    for ($i = 0; $i < $panjang; $i++) {
        $kode .= $karakter[random_int(0, strlen($karakter) - 1)];
    }

    return $kode;
}


// =====================================
// PASTIKAN KODE TIDAK SAMA
// =====================================

do {

    $kode_test = buatKodeTest();

    $cekKode = mysqli_query(
        $conn,
        "SELECT id_test FROM test WHERE kode_test = '$kode_test' LIMIT 1"
    );

} while (mysqli_num_rows($cekKode) > 0);


// =====================================
// SIMPAN TEST
// =====================================

$tanggal = date("Y-m-d");


$queryTest = mysqli_query(
    $conn,
    "INSERT INTO test
    (id_user, judul_test, kode_test, tanggal_dibuat)
    VALUES
    ('$id_user', '$judul_test', '$kode_test', '$tanggal')"
);


if (!$queryTest) {
    die("Gagal menyimpan test: " . mysqli_error($conn));
}


$id_test = mysqli_insert_id($conn);


// =====================================
// SIMPAN SOAL
// =====================================

$nomor_soal = 1;


foreach ($soal as $data) {

    $pertanyaan = mysqli_real_escape_string(
        $conn,
        $data["pertanyaan"]
    );

    $pilihan_a = mysqli_real_escape_string(
        $conn,
        $data["pilihan_a"]
    );

    $pilihan_b = mysqli_real_escape_string(
        $conn,
        $data["pilihan_b"]
    );

    $pilihan_c = mysqli_real_escape_string(
        $conn,
        $data["pilihan_c"]
    );

    $pilihan_d = mysqli_real_escape_string(
        $conn,
        $data["pilihan_d"]
    );

    $kunci_jawaban = $data["kunci_jawaban"];


    $querySoal = mysqli_query(
        $conn,
        "INSERT INTO soal
        (
            id_test,
            nomor_soal,
            pertanyaan,
            pilihan_a,
            pilihan_b,
            pilihan_c,
            pilihan_d,
            kunci_jawaban
        )
        VALUES
        (
            '$id_test',
            '$nomor_soal',
            '$pertanyaan',
            '$pilihan_a',
            '$pilihan_b',
            '$pilihan_c',
            '$pilihan_d',
            '$kunci_jawaban'
        )"
    );


    if (!$querySoal) {
        die("Gagal menyimpan soal: " . mysqli_error($conn));
    }


    $nomor_soal++;
}


// =====================================
// SIMPAN KODE KE SESSION
// =====================================

$_SESSION["kode_test"] = $kode_test;
$_SESSION["id_test"] = $id_test;


// Hapus judul sementara

unset($_SESSION["judul_test"]);


// =====================================
// PINDAH KE HALAMAN BERHASIL
// =====================================

header("Location: test-selesai.php");
exit;

?>