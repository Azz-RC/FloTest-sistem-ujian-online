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

$error = "";


/* =========================================================
   PROSES MEMBUAT TEST
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $judul_test = trim(
        $_POST["judul_test"] ?? ""
    );


    /* -------------------------------------------------------
       VALIDASI JUDUL
    ------------------------------------------------------- */

    if ($judul_test === "") {

        $error = "Judul test wajib diisi.";

    } elseif (strlen($judul_test) > 255) {

        $error =
            "Judul test maksimal 255 karakter.";

    } else {


        /* ---------------------------------------------------
           MEMBUAT KODE TEST UNIK
        --------------------------------------------------- */

        do {

            $kode_test = (string) random_int(
                100,
                999
            );


            $stmt_cek = mysqli_prepare(
                $conn,
                "SELECT id_test
                 FROM test
                 WHERE kode_test = ?
                 LIMIT 1"
            );


            if (!$stmt_cek) {

                $error =
                    "Gagal memeriksa kode test.";

                break;
            }


            mysqli_stmt_bind_param(
                $stmt_cek,
                "s",
                $kode_test
            );


            mysqli_stmt_execute(
                $stmt_cek
            );


            $result_cek =
                mysqli_stmt_get_result(
                    $stmt_cek
                );


            $kode_sudah_ada =
                mysqli_fetch_assoc(
                    $result_cek
                );


            mysqli_stmt_close(
                $stmt_cek
            );

        } while ($kode_sudah_ada);


        /* ---------------------------------------------------
           SIMPAN TEST
        --------------------------------------------------- */

        if ($error === "") {

            $tanggal_dibuat =
                date("Y-m-d");


            $stmt_insert =
                mysqli_prepare(
                    $conn,
                    "INSERT INTO test
                    (
                        id_user,
                        judul_test,
                        kode_test,
                        tanggal_dibuat
                    )
                    VALUES (?, ?, ?, ?)"
                );


            if (!$stmt_insert) {

                $error =
                    "Gagal menyiapkan penyimpanan test.";

            } else {

                mysqli_stmt_bind_param(
                    $stmt_insert,
                    "isss",
                    $id_user,
                    $judul_test,
                    $kode_test,
                    $tanggal_dibuat
                );


                if (
                    mysqli_stmt_execute(
                        $stmt_insert
                    )
                ) {

                    $id_test_baru =
                        mysqli_insert_id($conn);


                    mysqli_stmt_close(
                        $stmt_insert
                    );


                    header(
                        "Location: buat-soal.php?id_test=" .
                        $id_test_baru
                    );

                    exit;

                } else {

                    $error =
                        "Gagal menyimpan test: " .
                        mysqli_stmt_error(
                            $stmt_insert
                        );


                    mysqli_stmt_close(
                        $stmt_insert
                    );
                }
            }
        }
    }
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

    <title>Buat Test - FloTest</title>

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


    <main class="test-content">


        <div class="test-card">

            <h1>Buat Test</h1>


            <?php if ($error !== ""): ?>

                <div class="error-message">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form
                action="test-create.php"
                method="POST"
            >

                <div class="form-group">

                    <label for="judul-test">
                        Judul Test
                    </label>


                    <input
                        type="text"
                        id="judul-test"
                        name="judul_test"
                        placeholder="Masukkan judul test"
                        maxlength="255"
                        required
                        value="<?= htmlspecialchars(
                            $_POST["judul_test"] ?? ""
                        ) ?>"
                    >

                </div>


               <div class="test-create-actions">

    <button
        type="submit"
        class="btn-next"
    >
        Selanjutnya
    </button>

    <a
        href="test.php"
        class="btn-back"
    >
        ← Kembali
    </a>

</div>

            </form>

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