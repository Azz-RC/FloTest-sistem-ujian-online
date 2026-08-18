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

    <title>Test Berhasil - FloTest</title>

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
             TEST SELESAI
        ========================== -->

        <main class="test-selesai-content">

            <div class="test-selesai-card">

                <div class="success-icon">
                    ✓
                </div>

                <h1>Test berhasil dibuat!</h1>

                <p class="kode-label">
                    Kode Test Anda
                </p>

                <div class="kode-test">
                    335
                </div>

                <p class="kode-info">
                    Bagikan kode ini kepada teman-teman
                    <br>
                    untuk mengakses test Anda.
                </p>


                <div class="test-selesai-buttons">

                    <a href="home.php" class="btn-secondary">
                        Kembali ke Home
                    </a>

                    <a href="home.php" class="btn-next">
                        Selesai
                    </a>

                </div>

            </div>

        </main>

    </div>

</body>

</html>