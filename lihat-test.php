<?php

session_start();

require_once __DIR__ . "/koneksi.php";

/** @var mysqli $conn */


/*
|--------------------------------------------------------------------------
| Cek login
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Ambil ID test
|--------------------------------------------------------------------------
*/

$id_test = isset($_GET["id_test"])
    ? (int) $_GET["id_test"]
    : 0;

if ($id_test <= 0) {
    die("ID test tidak valid.");
}


/*
|--------------------------------------------------------------------------
| Ambil test dan periksa pemilik
|--------------------------------------------------------------------------
*/

$stmt_test = mysqli_prepare(
    $conn,
    "SELECT
        id_test,
        judul_test,
        kode_test,
        tanggal_dibuat
     FROM test
     WHERE id_test = ?
        AND id_user = ?
        AND deleted_at IS NULL
     LIMIT 1"
);

if (!$stmt_test) {
    die("Gagal menyiapkan query test.");
}

mysqli_stmt_bind_param(
    $stmt_test,
    "ii",
    $id_test,
    $id_user
);

mysqli_stmt_execute($stmt_test);

$result_test = mysqli_stmt_get_result(
    $stmt_test
);

$test = mysqli_fetch_assoc($result_test);

mysqli_stmt_close($stmt_test);

if (!$test) {
    die(
        "Test tidak ditemukan atau bukan milik Anda."
    );
}


/*
|--------------------------------------------------------------------------
| Ambil seluruh soal
|--------------------------------------------------------------------------
*/

$daftar_soal = [];

$stmt_soal = mysqli_prepare(
    $conn,
    "SELECT
        id_soal,
        nomor_soal,
        pertanyaan,
        pilihan_a,
        pilihan_b,
        pilihan_c,
        pilihan_d,
        kunci_jawaban
     FROM soal
     WHERE id_test = ?
        AND deleted_at IS NULL
     ORDER BY nomor_soal ASC, id_soal ASC"
);

if (!$stmt_soal) {
    die("Gagal menyiapkan query soal.");
}

mysqli_stmt_bind_param(
    $stmt_soal,
    "i",
    $id_test
);

mysqli_stmt_execute($stmt_soal);

$result_soal = mysqli_stmt_get_result(
    $stmt_soal
);

while (
    $row = mysqli_fetch_assoc($result_soal)
) {
    $daftar_soal[] = $row;
}

mysqli_stmt_close($stmt_soal);

$active_page = "buat-test";

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
        Detail Test - FloTest
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="home-page">

    <?php
    include __DIR__ .
        "/components/navbar.php";
    ?>

    <main class="detail-test-content">

        <section class="detail-test-header">

            <div class="detail-test-info">

                <a
                    href="buat-test.php"
                    class="back-link"
                >
                    &larr; Kembali ke Test Saya
                </a>

                <h1>

                    <?= htmlspecialchars(
                        $test["judul_test"]
                    ) ?>

                </h1>

                <p>

                    Dibuat:

                    <?= htmlspecialchars(
                        $test["tanggal_dibuat"]
                    ) ?>

                    &middot;

                    <?= count($daftar_soal) ?>

                    soal

                </p>

            </div>


            <div class="detail-code">

                <span>
                    Kode Test
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $test["kode_test"]
                    ) ?>

                </strong>

            </div>

        </section>


        <?php if (count($daftar_soal) > 0): ?>

            <?php foreach (
                $daftar_soal as $soal
            ): ?>

                <article class="detail-soal-card">

                    <h2>

                        Soal

                        <?= (int)
                            $soal["nomor_soal"] ?>

                    </h2>


                    <p class="detail-pertanyaan">

                        <?= nl2br(
                            htmlspecialchars(
                                $soal["pertanyaan"]
                            )
                        ) ?>

                    </p>


                    <div class="detail-pilihan">

                        <?php foreach (
                            ["A", "B", "C", "D"]
                            as $huruf
                        ): ?>

                            <?php

                            $nama_kolom =
                                "pilihan_" .
                                strtolower($huruf);

                            $isi_pilihan =
                                $soal[$nama_kolom];

                            $jawaban_benar =
                                strtoupper(
                                    $soal[
                                        "kunci_jawaban"
                                    ]
                                ) === $huruf;

                            ?>


                            <div
                                class="
                                    detail-pilihan-item
                                    <?= $jawaban_benar
                                        ? "jawaban-benar"
                                        : "" ?>
                                "
                            >

                                <span>

                                    <?= $huruf ?>

                                </span>

                                <p>

                                    <?= htmlspecialchars(
                                        $isi_pilihan
                                    ) ?>

                                </p>


                                <?php if (
                                    $jawaban_benar
                                ): ?>

                                    <strong>
                                        Kunci Jawaban
                                    </strong>

                                <?php endif; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </article>

            <?php endforeach; ?>


        <?php else: ?>

            <div class="empty-test">

                Test ini belum memiliki soal.

            </div>

        <?php endif; ?>

    </main>

    <?php
    require_once __DIR__ .
        "/components/footer.php";
    ?>

</div>

<script src="assets/js/script.js"></script>

</body>

</html>