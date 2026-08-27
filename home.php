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

    <title>Home - FloTest</title>

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

    <div class="navbar-wrapper">

        <nav class="navbar-menu">

            <a href="home.php" class="nav-link active">
                Home
            </a>

            <a href="buat-test.php" class="nav-link">
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

    </div>

</header>


        <!-- =========================
             MAIN CONTENT
        ========================== -->

        <main class="home-content">

            <!-- Welcome Section -->

            <section class="welcome-section">

                <div class="welcome-illustration">

                    <div class="illustration-board">

                        <div class="board-line"></div>
                        <div class="board-line"></div>
                        <div class="board-line"></div>

                        <div class="board-check">✓</div>
                        <div class="board-check">✓</div>
                        <div class="board-check">✓</div>

                    </div>

                    <div class="illustration-plant">
                        🌿
                    </div>

                </div>


                <div class="welcome-text">

                    <h1>Selamat datang di FloTest!</h1>

                    <p>
                        Belajar dan uji kemampuanmu
                        <br>
                        dengan test online.
                    </p>

                </div>

            </section>


            <!-- About FloTest -->

            <section class="about-card">

                <h2>Apa itu FloTest?</h2>

                <p>
                    FloTest adalah platform belajar online yang memungkinkan
                    kamu membuat test, mengerjakan test, dan melihat hasilnya
                    dengan mudah.
                </p>

            </section>

        </main>

    </div>

    <script src="assets/js/script.js"></script>

</body>

</html>