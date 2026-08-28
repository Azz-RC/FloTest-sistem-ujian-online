<?php

session_start();

require_once __DIR__ . "/koneksi.php";

/** @var mysqli $conn */


/* =========================================================
   CEK LOGIN
========================================================= */

if (
    !isset($_SESSION["login"]) ||
    $_SESSION["login"] !== true
) {
    header("Location: index.php");
    exit;
}


if (!isset($_SESSION["id_user"])) {
    die("ERROR: session id_user belum tersedia.");
}


$id_user = (int) $_SESSION["id_user"];


/* =========================================================
   PARAMETER
========================================================= */

$id_test = isset($_GET["id_test"])
    ? (int) $_GET["id_test"]
    : 0;

$id_riwayat_tes = isset(
    $_GET["id_riwayat_tes"]
)
    ? (int) $_GET["id_riwayat_tes"]
    : 0;


if ($id_test <= 0) {
    die("ID test tidak valid.");
}


/* =========================================================
   MODE PESERTA
========================================================= */

if ($id_riwayat_tes > 0) {


    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            r.id_riwayat_tes,
            r.id_test,
            r.skor_akhir,
            r.jumlah_benar,
            r.jumlah_salah,
            r.status_pengerjaan,
            t.judul_test,
            t.kode_test
         FROM riwayat_tes r
         INNER JOIN test t
            ON r.id_test = t.id_test
         WHERE r.id_riwayat_tes = ?
         AND r.id_user = ?
         AND r.id_test = ?
         AND r.deleted_at IS NULL
         AND t.deleted_at IS NULL
         LIMIT 1"
    );


    if (!$stmt) {
        die("Gagal menyiapkan query hasil.");
    }


    mysqli_stmt_bind_param(
        $stmt,
        "iii",
        $id_riwayat_tes,
        $id_user,
        $id_test
    );


    mysqli_stmt_execute($stmt);

    $result =
        mysqli_stmt_get_result($stmt);

    $data =
        mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);


    if (!$data) {
        die("Riwayat test tidak ditemukan.");
    }


    if (
        $data["status_pengerjaan"]
        !== "selesai"
    ) {

        header(
            "Location: kerjakan-test.php?id_test=" .
            $id_test .
            "&id_riwayat_tes=" .
            $id_riwayat_tes
        );

        exit;
    }


    $mode_peserta = true;

    $judul_test =
        $data["judul_test"];

    $kode_test =
        $data["kode_test"];

    $nilai =
        (float) $data["skor_akhir"];

    $jumlah_benar =
        (int) $data["jumlah_benar"];

    $jumlah_salah =
        (int) $data["jumlah_salah"];


    /*
     * Ambil total soal.
     */
    $stmt_total = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS total
         FROM soal
         WHERE id_test = ?
         AND deleted_at IS NULL"
    );


    $total_soal = 0;


    if ($stmt_total) {

        mysqli_stmt_bind_param(
            $stmt_total,
            "i",
            $id_test
        );

        mysqli_stmt_execute($stmt_total);

        $result_total =
            mysqli_stmt_get_result($stmt_total);

        $row_total =
            mysqli_fetch_assoc($result_total);

        $total_soal =
            (int) $row_total["total"];

        mysqli_stmt_close($stmt_total);
    }


/* =========================================================
   MODE PEMBUAT TEST
========================================================= */

} else {


    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            id_test,
            judul_test,
            kode_test
         FROM test
         WHERE id_test = ?
         AND id_user = ?
         AND deleted_at IS NULL
         LIMIT 1"
    );


    if (!$stmt) {
        die("Gagal menyiapkan query test.");
    }


    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $id_test,
        $id_user
    );


    mysqli_stmt_execute($stmt);

    $result =
        mysqli_stmt_get_result($stmt);

    $data =
        mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);


    if (!$data) {
        die("Test tidak ditemukan atau bukan milik Anda.");
    }


    $mode_peserta = false;

    $judul_test =
        $data["judul_test"];

    $kode_test =
        $data["kode_test"];

}

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
        <?= $mode_peserta
            ? "Hasil Test - FloTest"
            : "Test Berhasil - FloTest" ?>
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="home-page">


    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <header class="home-navbar">

        <div class="logo">
            <span>Flo</span>Test
        </div>


        <div class="navbar-wrapper">

            <nav class="navbar-menu">

                <a
                    href="home.php"
                    class="nav-link"
                >
                    Home
                </a>

                <a
                    href="buat-test.php"
                    class="nav-link <?= !$mode_peserta ? "active" : "" ?>"
                >
                    Buat Test
                </a>

                <a
                    href="jawab-test.php"
                    class="nav-link <?= $mode_peserta ? "active" : "" ?>"
                >
                    Jawab Test
                </a>

                <a
                    href="index.php?logout=1"
                    class="nav-link"
                >
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


    <main class="test-selesai-content">

        <div class="test-selesai-card">


            <?php if (!$mode_peserta): ?>


                <!-- =================================================
                     PEMBUAT TEST
                ================================================== -->

                <div class="success-icon">
                    ✓
                </div>


                <h1>
                    Test berhasil dibuat!
                </h1>


                <p class="kode-label">
                    Kode Test Anda
                </p>


                <div class="kode-test">
                    <?= htmlspecialchars(
                        $kode_test
                    ) ?>
                </div>


                <p class="kode-info">

                    Bagikan kode ini kepada teman-teman
                    <br>
                    untuk mengakses test Anda.

                </p>


                <div class="test-selesai-buttons">

                    <a
                        href="buat-test.php"
                        class="btn-secondary"
                    >
                        Kembali ke Buat Test
                    </a>


                    <a
                        href="home.php"
                        class="btn-next"
                    >
                        Ke Home
                    </a>

                </div>


            <?php else: ?>


                <!-- =================================================
                     HASIL PESERTA
                ================================================== -->

                <div class="success-icon">
                    ✓
                </div>


                <h1>
                    Test Selesai!
                </h1>


                <p class="kode-info">

                    Kamu sudah menyelesaikan:

                    <strong>
                        <?= htmlspecialchars(
                            $judul_test
                        ) ?>
                    </strong>

                </p>


                <div class="nilai-box">

                    <span class="nilai-label">
                        Nilai Anda
                    </span>

                    <span class="nilai">
                        <?= htmlspecialchars(
                            number_format(
                                $nilai,
                                2
                            )
                        ) ?>
                    </span>

                </div>


                <div class="detail-nilai">

                    <div>

                        <strong>
                            <?= $jumlah_benar ?>
                        </strong>

                        <span>
                            Jawaban Benar
                        </span>

                    </div>


                    <div>

                        <strong>
                            <?= $jumlah_salah ?>
                        </strong>

                        <span>
                            Jawaban Salah
                        </span>

                    </div>


                    <div>

                        <strong>
                            <?= $total_soal ?>
                        </strong>

                        <span>
                            Total Soal
                        </span>

                    </div>

                </div>


                <div class="test-selesai-buttons">

                    <a
                        href="jawab-test.php"
                        class="btn-secondary"
                    >
                        Kembali ke Riwayat
                    </a>


                    <a
                        href="home.php"
                        class="btn-next"
                    >
                        Ke Home
                    </a>

                </div>


            <?php endif; ?>


        </div>

    </main>

</div>


<script src="assets/js/script.js"></script>

</body>

</html>