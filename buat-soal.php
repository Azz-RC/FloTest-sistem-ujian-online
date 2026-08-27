<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["judul_test"])) {
        header("Location: buat-test.php");
        exit;
    }

    $_SESSION["judul_test"] = $_POST["judul_test"];
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

            <button
                type="button"
                class="menu-toggle"
                onclick="toggleMenu()"
                aria-label="Buka menu"
            >
                ☰
            </button>

        </header>


        <main class="buat-soal-content">

            <div class="buat-soal-header">

                <h1>Buat Soal</h1>

                <span id="jumlah-langkah">
                    Langkah 1
                </span>

            </div>


            <form action="simpan-test.php" method="POST">

                <div id="soal-container">

                    <!-- SOAL 1 -->

                    <div class="soal-card">

                        <div class="soal-title">
                            Soal 1
                        </div>

                        <div class="soal-content">

                            <div class="pertanyaan-area">

                                <textarea
                                    class="input-soal"
                                    name="soal[0][pertanyaan]"
                                    placeholder="Tulis soal di sini..."
                                    required
                                ></textarea>

                            </div>


                            <div class="pilihan-area">

                                <label class="pilihan-item">

                                    <span>A.</span>

                                    <input
                                        type="text"
                                        class="input-pilihan"
                                        name="soal[0][pilihan_a]"
                                        placeholder="Pilihan A"
                                        required
                                    >

                                </label>


                                <label class="pilihan-item">

                                    <span>B.</span>

                                    <input
                                        type="text"
                                        class="input-pilihan"
                                        name="soal[0][pilihan_b]"
                                        placeholder="Pilihan B"
                                        required
                                    >

                                </label>


                                <label class="pilihan-item">

                                    <span>C.</span>

                                    <input
                                        type="text"
                                        class="input-pilihan"
                                        name="soal[0][pilihan_c]"
                                        placeholder="Pilihan C"
                                        required
                                    >

                                </label>


                                <label class="pilihan-item">

                                    <span>D.</span>

                                    <input
                                        type="text"
                                        class="input-pilihan"
                                        name="soal[0][pilihan_d]"
                                        placeholder="Pilihan D"
                                        required
                                    >

                                </label>

                            </div>


                            <div class="kunci-area">

                                <label>
                                    Kunci Jawaban
                                </label>

                                <select
                                    name="soal[0][kunci_jawaban]"
                                    required
                                >

                                    <option value="">
                                        Pilih jawaban
                                    </option>

                                    <option value="A">
                                        A
                                    </option>

                                    <option value="B">
                                        B
                                    </option>

                                    <option value="C">
                                        C
                                    </option>

                                    <option value="D">
                                        D
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                </div>


                <button
                    type="button"
                    class="btn-tambah-soal"
                    onclick="tambahSoal()"
                >
                    + Tambah Soal
                </button>


                <div class="soal-navigation">

                    <a
                        href="buat-test.php"
                        class="btn-secondary"
                    >
                        Kembali
                    </a>

                    <button
                        type="submit"
                        class="btn-next"
                    >
                        Selesai
                    </button>

                </div>

            </form>

        </main>

    </div>


    <script src="assets/js/script.js"></script>

</body>

</html>