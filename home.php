<?php

session_start();

if (
    !isset($_SESSION["login"]) ||
    $_SESSION["login"] !== true
) {
    header("Location: index.php");
    exit;
}

$active_page = "home";

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Home - FloTest
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="home-page">


   <?php include __DIR__ . "/components/navbar.php"; ?>

    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="home-content">


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

                <h1>
                    Selamat datang di FloTest!
                </h1>

                <p>
                    Belajar dan uji kemampuanmu
                    <br>
                    dengan test online.
                </p>

            </div>

        </section>


        <section class="about-card">

            <h2>
                Apa itu FloTest?
            </h2>

            <p>
                FloTest adalah platform belajar online yang memungkinkan
                kamu membuat test, mengerjakan test, dan melihat hasilnya
                dengan mudah.
            </p>

        </section>

    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>

</div>


<script src="assets/js/script.js"></script>

</body>

</html>