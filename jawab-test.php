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

    <title>Jawab Test - FloTest</title>

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
             JAWAB TEST
        ========================== -->

        <main class="jawab-test-content">

            <div class="jawab-test-card">

                <h1>Jawab Test</h1>

                <p class="jawab-description">
                    Masukkan kode test yang diberikan oleh pembuat test.
                </p>


                <div class="form-group">

                    <label for="kode-test">
                        Kode Test
                    </label>

                    <input
                        type="text"
                        id="kode-test"
                        name="kode_test"
                        placeholder="Masukkan kode test"
                        maxlength="6"
                    >

                </div>


                <button
                    type="button"
                    class="btn-next"
                    onclick="masukTest()"
                >
                    Mulai Test
                </button>

            </div>

        </main>

    </div>


    <script src="assets/js/script.js"></script>

</body>

</html>