<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}

include "koneksi.php";

// Pastikan request menggunakan POST dan ada id_test
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["id_test"])) {
    header("Location: jawab-test.php");
    exit;
}

$id_test = intval($_POST["id_test"]);
$jawaban_user = $_POST["jawaban"] ?? [];

// Ambil data test untuk judul/kode jika diperlukan
$queryTest = mysqli_query($conn, "SELECT judul_test FROM test WHERE id_test = '$id_test' LIMIT 1");
$dataTest = mysqli_fetch_assoc($queryTest);

// Ambil semua soal dan kunci jawaban berdasarkan id_test
$querySoal = mysqli_query(
    $conn,
    "SELECT id_soal, kunci_jawaban FROM soal WHERE id_test = '$id_test'"
);

$total_soal = mysqli_num_rows($querySoal);

$jawaban_benar = 0;
$jawaban_salah = 0;

while ($row = mysqli_fetch_assoc($querySoal)) {
    $id_soal = $row["id_soal"];
    $kunci = trim(strtoupper($row["kunci_jawaban"]));

    if (isset($jawaban_user[$id_soal])) {
        $pilihan = trim(strtoupper($jawaban_user[$id_soal]));
        if ($pilihan === $kunci) {
            $jawaban_benar++;
        } else {
            $jawaban_salah++;
        }
    } else {
        $jawaban_salah++; // Dianggap salah jika tidak dijawab
    }
}

// Hitung nilai akhir (skor skala 0 - 100)
$nilai = ($total_soal > 0) ? ($jawaban_benar / $total_soal) * 100 : 0;
$nilai = round($nilai);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Test - FloTest</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <div class="home-page">

        <!-- =========================
             NAVBAR
        ========================== -->
        <header class="home-navbar">
            <div class="logo">
                <span>Flo</span>Test
            </div>

            <nav class="navbar-menu">
                <a href="home.php" class="nav-link">Home</a>
                <a href="buat-test.php" class="nav-link">Buat Test</a>
                <a href="jawab-test.php" class="nav-link active">Jawab Test</a>
                <a href="index.php" class="nav-link">Logout</a>
            </nav>
        </header>

        <!-- =========================
             HASIL TEST CONTENT
        ========================== -->
        <main class="kerjakan-content" style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">

            <div style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 450px; text-align: center;">
                
                <!-- Ikon Centang Hijau -->
                <div style="font-size: 40px; margin-bottom: 10px;">✅</div>
                
                <h2 style="margin-bottom: 5px; font-size: 22px; color: #111827;">Test Selesai!</h2>
                <p style="color: #6b7280; font-size: 14px; margin-bottom: 25px;">Berikut adalah hasil test kamu.</p>

                <!-- Kotak Nilai -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                    <span style="font-size: 13px; color: #64748b; display: block; margin-bottom: 5px;">Nilai Kamu</span>
                    <strong style="font-size: 48px; color: #4f46e5; line-height: 1;"><?php echo $nilai; ?></strong>
                    <span style="font-size: 12px; color: #94a3b8; display: block; margin-top: 5px;">dari 100</span>
                </div>

                <!-- Statistik Ringkas -->
                <div style="display: flex; justify-content: space-between; gap: 10px; margin-bottom: 25px;">
                    <div style="flex: 1; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <span style="display: block; font-size: 11px; color: #64748b;">Jumlah Soal</span>
                        <strong style="font-size: 16px; color: #1e293b;"><?php echo $total_soal; ?></strong>
                    </div>
                    <div style="flex: 1; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <span style="display: block; font-size: 11px; color: #64748b;">Jawaban Benar</span>
                        <strong style="font-size: 16px; color: #16a34a;"><?php echo $jawaban_benar; ?></strong>
                    </div>
                    <div style="flex: 1; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <span style="display: block; font-size: 11px; color: #64748b;">Jawaban Salah</span>
                        <strong style="font-size: 16px; color: #dc2626;"><?php echo $jawaban_salah; ?></strong>
                    </div>
                </div>

                <!-- Tombol Navigasi -->
                <div style="display: flex; gap: 10px;">
                    <a href="home.php" style="flex: 1; background: #4f46e5; color: #fff; padding: 10px 0; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; text-align: center;">
                        Kembali ke Home
                    </a>
                    <a href="jawab-test.php" style="flex: 1; background: #fff; color: #4f46e5; border: 1px solid #4f46e5; padding: 10px 0; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; text-align: center;">
                        Kerjakan Test Lain
                    </a>
                </div>

            </div>

        </main>

    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>