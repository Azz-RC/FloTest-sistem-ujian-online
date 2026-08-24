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

    <title>Buat Soal - FloTest</title>

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

                <a href="buat-test.php" class="nav-link active">
                    Buat Test
                </a>

                <a href="jawab-test.php" class="nav-link">
                    Jawab Test
                </a>

                <a href="index.php" class="nav-link">
                    Logout
                </a>

            </nav>

        </header>


        <!-- =========================
             BUAT SOAL
        ========================== -->

        <main class="buat-soal-content">

            <div class="buat-soal-header">

                <h1>Buat Soal</h1>

                <span id="jumlah-langkah">
                    Langkah 1 dari 3
                </span>

            </div>


            <!-- Container Soal -->

            <div id="soal-container">

                <div class="soal-card">

                    <div class="soal-title">
                        Soal 1
                    </div>

                    <div class="soal-content">

                        <!-- Pertanyaan -->

                        <div class="pertanyaan-area">

                            <textarea
                                class="input-soal"
                                placeholder="Tulis soal di sini..."
                            ></textarea>

                        </div>


                        <!-- Pilihan -->

                        <div class="pilihan-area">

                            <label class="pilihan-item">

                                <input
                                    type="radio"
                                    name="soal-1"
                                    value="A"
                                >

                                <span>A.</span>

                                <input
                                    type="text"
                                    class="input-pilihan"
                                    placeholder="Pilihan A"
                                >

                            </label>


                            <label class="pilihan-item">

                                <input
                                    type="radio"
                                    name="soal-1"
                                    value="B"
                                >

                                <span>B.</span>

                                <input
                                    type="text"
                                    class="input-pilihan"
                                    placeholder="Pilihan B"
                                >

                            </label>


                            <label class="pilihan-item">

                                <input
                                    type="radio"
                                    name="soal-1"
                                    value="C"
                                >

                                <span>C.</span>

                                <input
                                    type="text"
                                    class="input-pilihan"
                                    placeholder="Pilihan C"
                                >

                            </label>


                            <label class="pilihan-item">

                                <input
                                    type="radio"
                                    name="soal-1"
                                    value="D"
                                >

                                <span>D.</span>

                                <input
                                    type="text"
                                    class="input-pilihan"
                                    placeholder="Pilihan D"
                                >

                            </label>

                        </div>


                        <!-- Kunci Jawaban -->

                        <div class="kunci-area">

                            <label for="kunci-1">
                                Kunci Jawaban
                            </label>

                            <select id="kunci-1">

                                <option value="">
                                    Pilih jawaban
                                </option>

                                <option value="A">
                                    A. Pilihan A
                                </option>

                                <option value="B">
                                    B. Pilihan B
                                </option>

                                <option value="C">
                                    C. Pilihan C
                                </option>

                                <option value="D">
                                    D. Pilihan D
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Tambah Soal -->

            <button
                type="button"
                class="btn-tambah-soal"
                onclick="tambahSoal()"
            >
                + Tambah Soal
            </button>


            <!-- Navigation -->

            <div class="soal-navigation">

                <button
                    type="button"
                    class="btn-secondary"
                    onclick="kembaliKeBuatTest()"
                >
                    Kembali
                </button>

                <button
                    type="button"
                    class="btn-next"
                    onclick="selesaiBuatSoal()"
                >
                    Selanjutnya
                </button>

            </div>

        </main>

    </div>


    <script src="assets/js/script.js"></script>

</body>

</html>