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

$error = "";
$pesan = "";


/*
|--------------------------------------------------------------------------
| Buat token keamanan untuk fitur hapus
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(
        random_bytes(32)
    );
}


/*
|--------------------------------------------------------------------------
| Pesan setelah edit atau hapus
|--------------------------------------------------------------------------
*/

$status = $_GET["status"] ?? "";

if ($status === "hapus-berhasil") {
    $pesan = "Test berhasil dihapus.";
}

if ($status === "edit-berhasil") {
    $pesan = "Test berhasil diperbarui.";
}


/*
|--------------------------------------------------------------------------
| Proses membuat test
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $judul_test = trim(
        $_POST["judul_test"] ?? ""
    );

    if ($judul_test === "") {

        $error = "Judul test wajib diisi.";

    } elseif (strlen($judul_test) > 255) {

        $error = "Judul test maksimal 255 karakter.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Membuat kode test unik
        |--------------------------------------------------------------------------
        */

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
                $error = "Gagal memeriksa kode test.";
                break;
            }

            mysqli_stmt_bind_param(
                $stmt_cek,
                "s",
                $kode_test
            );

            mysqli_stmt_execute($stmt_cek);

            $result_cek = mysqli_stmt_get_result(
                $stmt_cek
            );

            $kode_sudah_ada = mysqli_fetch_assoc(
                $result_cek
            );

            mysqli_stmt_close($stmt_cek);

        } while ($kode_sudah_ada);


        /*
        |--------------------------------------------------------------------------
        | Simpan test
        |--------------------------------------------------------------------------
        */

        if ($error === "") {

            $tanggal_dibuat = date("Y-m-d");

            $stmt_insert = mysqli_prepare(
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

                if (mysqli_stmt_execute($stmt_insert)) {

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
                        mysqli_stmt_error($stmt_insert);

                    mysqli_stmt_close(
                        $stmt_insert
                    );
                }
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Ambil daftar test milik pengguna
|--------------------------------------------------------------------------
*/

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

    <?php
    include __DIR__ . "/components/navbar.php";
    ?>

    <main class="buat-test-content">

        <!-- FORM BUAT TEST -->

        <div class="buat-test-card">

            <h1>Buat Test</h1>

            <?php if ($error !== ""): ?>

                <div class="error-message">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <?php if ($pesan !== ""): ?>

                <div class="success-message">

                    <?= htmlspecialchars($pesan) ?>

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
                        maxlength="255"
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


        <!-- RIWAYAT TEST -->

        <div class="buat-test-card">

            <h1>Test Saya</h1>

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

                                <span>Kode Test</span>

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