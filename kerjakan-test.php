<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kerjakan Test - FloTest</title>

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

                <a href="home.php" class="nav-link">
                    Home
                </a>

                <a href="buat-test.php" class="nav-link">
                    Buat Test
                </a>

                <a href="jawab-test.php" class="nav-link active">
                    Jawab Test
                </a>

                <a href="index.php" class="nav-link">
                    Logout
                </a>

            </nav>

        </header>


        <!-- =========================
             KERJAKAN TEST
        ========================== -->

        <main class="kerjakan-content">

            <div class="kerjakan-header">

                <div>
                    <h1>Pengetahuan Umum</h1>

                    <p>
                        Kode Test:
                        <strong id="kode-test-display">335</strong>
                    </p>
                </div>

                <span class="nomor-soal" id="nomor-soal">
                     Soal 1 dari 5
                </span>

            </div>


            <!-- Soal -->

            <div class="pertanyaan-card">

                <div class="pertanyaan-card" id="pertanyaan-card">

                    <h2 id="pertanyaan">
                         1. Apa planet terbesar di tata surya?
                     </h2>

            <div class="jawaban-list">

                <label class="jawaban-option">
                    <input type="radio" name="jawaban" value="A">
                    <span id="jawaban-A">A. Bumi</span>
                </label>

                <label class="jawaban-option">
                    <input type="radio" name="jawaban" value="B">
                    <span id="jawaban-B">B. Mars</span>
                </label>

                <label class="jawaban-option">
                    <input type="radio" name="jawaban" value="C">
                    <span id="jawaban-C">C. Jupiter</span>
                </label>

                <label class="jawaban-option">
                    <input type="radio" name="jawaban" value="D">
                    <span id="jawaban-D">D. Venus</span>
                </label>

            </div>

            </div>


            <!-- Navigasi -->

            <div class="kerjakan-navigation">

                <button
                    type="button"
                    class="btn-secondary"
                    onclick="soalSebelumnya()"
                >
                    Sebelumnya
                </button>

                <button
                    type="button"
                    class="btn-next"
                    onclick="soalBerikutnya()"
                >
                    Selanjutnya
                </button>

            </div>

        </main>

    </div>


    <script src="assets/js/script.js"></script>

</body>

</html>