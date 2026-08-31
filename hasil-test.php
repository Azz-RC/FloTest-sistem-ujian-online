<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}

$active_page = "jawab-test";

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

        <?php include __DIR__ . "/components/navbar.php"; ?>

        <!-- =========================
             HASIL TEST
        ========================== -->

        <main class="hasil-test-content">

            <div class="hasil-test-card">

                <div class="hasil-icon">
                    ✓
                </div>

                <h1>Test Selesai!</h1>

                <p class="hasil-subtitle">
                    Berikut adalah hasil test kamu.
                </p>


                <!-- Nilai -->

                <div class="nilai-section">

                    <span class="nilai-label">
                        Nilai Kamu
                    </span>

                    <div class="nilai">
                        80
                    </div>

                    <span class="nilai-maksimal">
                        dari 100
                    </span>

                </div>


                <!-- Ringkasan -->

                <div class="hasil-summary">

                    <div class="summary-item">

                        <span>
                            Jumlah Soal
                        </span>

                        <strong>
                            5
                        </strong>

                    </div>


                    <div class="summary-item">

                        <span>
                            Jawaban Benar
                        </span>

                        <strong>
                            4
                        </strong>

                    </div>


                    <div class="summary-item">

                        <span>
                            Jawaban Salah
                        </span>

                        <strong>
                            1
                        </strong>

                    </div>

                </div>


                <!-- Tombol -->

                <div class="hasil-buttons">

                    <a
                        href="home.php"
                        class="btn-next"
                    >
                        Kembali ke Home
                    </a>

                    <a
                        href="jawab-test.php"
                        class="btn-secondary"
                    >
                        Kerjakan Test Lain
                    </a>

                </div>

            </div>

        </main>

        <?php require_once __DIR__ . "/components/footer.php"; ?>

    </div>

</body>

</html>