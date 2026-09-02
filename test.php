<?php

session_start();

require_once __DIR__ . "/koneksi.php";

/** @var mysqli $conn */

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

$pesan = "";


/* =========================================================
   BUAT TOKEN KEAMANAN UNTUK FITUR HAPUS
========================================================= */

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(
        random_bytes(32)
    );
}


/* =========================================================
   PESAN SETELAH EDIT ATAU HAPUS
========================================================= */

$status = $_GET["status"] ?? "";

if ($status === "hapus-berhasil") {
    $pesan = "Test berhasil dihapus.";
}

if ($status === "edit-berhasil") {
    $pesan = "Test berhasil diperbarui.";
}


/* =========================================================
   AMBIL DAFTAR TEST MILIK PENGGUNA
========================================================= */

$tests = [];

$stmt_tests = mysqli_prepare(
    $conn,
    "SELECT
        t.id_test,
        t.judul_test,
        t.kode_test,
        t.tanggal_dibuat,
        COUNT(s.id_soal) AS jumlah_soal
     FROM test t
     LEFT JOIN soal s
        ON s.id_test = t.id_test
        AND s.deleted_at IS NULL
     WHERE t.id_user = ?
        AND t.deleted_at IS NULL
     GROUP BY
        t.id_test,
        t.judul_test,
        t.kode_test,
        t.tanggal_dibuat
     ORDER BY t.id_test DESC"
);

if ($stmt_tests) {

    mysqli_stmt_bind_param(
        $stmt_tests,
        "i",
        $id_user
    );

    mysqli_stmt_execute($stmt_tests);

    $result_tests = mysqli_stmt_get_result(
        $stmt_tests
    );

    while (
        $row = mysqli_fetch_assoc($result_tests)
    ) {
        $tests[] = $row;
    }

    mysqli_stmt_close($stmt_tests);
}

$active_page = "test";

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Test - FloTest</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="home-page">

    <?php
    include __DIR__ . "/components/navbar.php";
    ?>


    <main class="test-content">


        <?php if ($pesan !== ""): ?>

            <div class="success-message">
                <?= htmlspecialchars($pesan) ?>
            </div>

        <?php endif; ?>


        <!-- =================================================
             TEST SAYA
        ================================================== -->

        <div class="test-card">

            <div class="test-section-header">

                <h1>Test Saya</h1>

                <a
                    href="test-create.php"
                    class="btn-crud btn-lihat btn-tambah-test"
                >
                    + Tambah Test
                </a>

            </div>


            <?php if (count($tests) > 0): ?>

                <div class="test-list">

                    <?php foreach ($tests as $test): ?>

                        <div class="test-item">


                            <div class="test-info">

                                <h4>
                                    <?= htmlspecialchars(
                                        $test["judul_test"]
                                    ) ?>
                                </h4>

                                <p>
                                    Dibuat:
                                    <?= htmlspecialchars(
                                        $test["tanggal_dibuat"]
                                    ) ?>
                                </p>

                                <p>
                                    Jumlah soal:
                                    <?= (int)
                                        $test["jumlah_soal"] ?>
                                </p>

                            </div>


                            <div class="test-code">

                                <span>
                                    Kode Test
                                </span>

                                <strong>
                                    <?= htmlspecialchars(
                                        $test["kode_test"]
                                    ) ?>
                                </strong>

                            </div>


                            <div class="test-actions">

                                <a
                                    href="lihat-test.php?id_test=<?= (int) $test["id_test"] ?>"
                                    class="btn-crud btn-lihat"
                                >
                                    Lihat
                                </a>


                                <a
                                    href="edit-test.php?id_test=<?= (int) $test["id_test"] ?>"
                                    class="btn-crud btn-edit"
                                >
                                    Edit
                                </a>


                                <form
                                    action="hapus-test.php"
                                    method="POST"
                                    class="form-hapus-test"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars(
                                            $_SESSION["csrf_token"]
                                        ) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id_test"
                                        value="<?= (int) $test["id_test"] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn-crud btn-hapus"
                                    >
                                        Hapus
                                    </button>

                                </form>

                            </div>


                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="empty-test">
                    Kamu belum membuat test.
                </div>

            <?php endif; ?>


        </div>


    </main>


    <?php
    require_once __DIR__ .
        "/components/footer.php";
    ?>

</div>


<script src="assets/js/script.js"></script>

</body>

</html>