<?php

session_start();

require_once __DIR__ . "/koneksi.php";

/** @var mysqli $conn */

$error = "";


/* =========================================================
   CEK LOGIN
========================================================= */

if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}


/* =========================================================
   CEK ID USER
========================================================= */

if (!isset($_SESSION["id_user"])) {
    die("ERROR: session id_user belum tersedia.");
}

$id_user = (int) $_SESSION["id_user"];


/* =========================================================
   PROSES BUAT TEST
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $judul_test = trim($_POST["judul_test"] ?? "");


    /* -----------------------------------------------------
       VALIDASI JUDUL
    ----------------------------------------------------- */

    if ($judul_test === "") {

        $error = "Judul test wajib diisi.";

    } else {

        /* -------------------------------------------------
           BUAT KODE TEST UNIK
        ------------------------------------------------- */

        do {

            $kode_test = (string) random_int(100, 999);

            $stmt_cek_kode = mysqli_prepare(
                $conn,
                "SELECT id_test
                 FROM test
                 WHERE kode_test = ?
                 LIMIT 1"
            );

            if (!$stmt_cek_kode) {
                $error = "Gagal memeriksa kode test.";
                break;
            }

            mysqli_stmt_bind_param(
                $stmt_cek_kode,
                "s",
                $kode_test
            );

            mysqli_stmt_execute($stmt_cek_kode);

            $result_kode = mysqli_stmt_get_result(
                $stmt_cek_kode
            );

            $kode_sudah_ada = mysqli_fetch_assoc(
                $result_kode
            );

            mysqli_stmt_close($stmt_cek_kode);

        } while ($kode_sudah_ada);


        /* -------------------------------------------------
           SIMPAN TEST
        ------------------------------------------------- */

        if ($error === "") {

            $tanggal_dibuat = date("Y-m-d");

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO test
                    (
                        id_user,
                        judul_test,
                        kode_test,
                        tanggal_dibuat
                    )
                 VALUES
                    (?, ?, ?, ?)"
            );


            if ($stmt) {

                mysqli_stmt_bind_param(
                    $stmt,
                    "isss",
                    $id_user,
                    $judul_test,
                    $kode_test,
                    $tanggal_dibuat
                );


                if (mysqli_stmt_execute($stmt)) {

                    /* -------------------------------------
                       AMBIL ID TEST BARU
                    ------------------------------------- */

                    $id_test = mysqli_insert_id($conn);

                    mysqli_stmt_close($stmt);


                    /* -------------------------------------
                       LANJUT KE BUAT SOAL
                    ------------------------------------- */

                    header(
                        "Location: buat-soal.php?id_test=" .
                        $id_test
                    );

                    exit;

                } else {

                    $error =
                        "Gagal menyimpan test: " .
                        mysqli_stmt_error($stmt);

                    mysqli_stmt_close($stmt);
                }

            } else {

                $error =
                    "Gagal menyiapkan query: " .
                    mysqli_error($conn);
            }
        }
    }
}


/* =========================================================
   AMBIL RIWAYAT TEST MILIK USER
========================================================= */

$tests = [];

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id_test,
        judul_test,
        kode_test,
        tanggal_dibuat
     FROM test
     WHERE id_user = ?
     AND deleted_at IS NULL
     ORDER BY id_test DESC"
);


if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id_user
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);


    while ($row = mysqli_fetch_assoc($result)) {

        $tests[] = $row;
    }


    mysqli_stmt_close($stmt);
}

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

    <title>Buat Test - FloTest</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>

<div class="home-page">


   <?php include __DIR__ . "/components/navbar.php"; ?>


    <!-- =====================================================
         KONTEN
    ====================================================== -->

    <main class="buat-test-content">


        <!-- =================================================
             CARD BUAT TEST
        ================================================== -->

        <div class="buat-test-card">

            <h1>
                Buat Test
            </h1>


            <?php if ($error !== ""): ?>

                <div class="error-message">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form
                action="buat-test.php"
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
                        required
                        value="<?= htmlspecialchars(
                            $_POST["judul_test"] ?? ""
                        ) ?>"
                    >

                </div>


                <button
                    type="submit"
                    class="btn-next"
                >
                    Selanjutnya
                </button>

            </form>

        </div>



        <!-- =================================================
             CARD TEST SAYA
        ================================================== -->

        <div class="buat-test-card">

            <h1>
                Test Saya
            </h1>


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

    <?php require_once __DIR__ . "/components/footer.php"; ?>

</div>


<script src="assets/js/script.js"></script>

</body>

</html>