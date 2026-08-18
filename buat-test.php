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

    <title>Buat Test - FloTest</title>

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
             BUAT TEST
        ========================== -->

        <main class="buat-test-content">

            <div class="buat-test-card">

                <h1>Buat Test</h1>

                <div class="form-group">

                    <label for="judul-test">
                        Judul Test
                    </label>

                    <input
                        type="text"
                        id="judul-test"
                        name="judul_test"
                        placeholder="Masukkan judul test"
                    >

                </div>


                <button
                    type="button"
                    class="btn-next"
                    onclick="lanjutBuatSoal()"
                >
                    Selanjutnya
                </button>

            </div>

        </main>

    </div>


    <script src="assets/js/script.js"></script>

</body>

</html>