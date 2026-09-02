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


/*
|--------------------------------------------------------------------------
| Hanya menerima metode POST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: test.php");
    exit;
}

$id_user = (int) $_SESSION["id_user"];

$id_test = isset($_POST["id_test"])
    ? (int) $_POST["id_test"]
    : 0;

$csrf_token = $_POST["csrf_token"] ?? "";


/*
|--------------------------------------------------------------------------
| Validasi ID test
|--------------------------------------------------------------------------
*/

if ($id_test <= 0) {
    die("ID test tidak valid.");
}


/*
|--------------------------------------------------------------------------
| Validasi token keamanan
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals(
        $_SESSION["csrf_token"],
        $csrf_token
    )
) {
    http_response_code(403);

    die(
        "Permintaan tidak valid. " .
        "Silakan kembali dan muat ulang halaman."
    );
}


/*
|--------------------------------------------------------------------------
| Proses soft delete
|--------------------------------------------------------------------------
*/

mysqli_begin_transaction($conn);

try {

    /*
    |--------------------------------------------------------------------------
    | Periksa test dan pemilik
    |--------------------------------------------------------------------------
    */

    $stmt_test = mysqli_prepare(
        $conn,
        "SELECT id_test
         FROM test
         WHERE id_test = ?
            AND id_user = ?
            AND deleted_at IS NULL
         FOR UPDATE"
    );

    if (!$stmt_test) {
        throw new Exception(
            "Gagal memeriksa test."
        );
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
        throw new Exception(
            "Test tidak ditemukan atau bukan milik Anda."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Soft delete detail jawaban peserta
    |--------------------------------------------------------------------------
    */

    $stmt_detail = mysqli_prepare(
        $conn,
        "UPDATE detail_riwayat AS detail
         INNER JOIN riwayat_tes AS riwayat
            ON detail.id_riwayat_tes =
               riwayat.id_riwayat_tes
         SET detail.deleted_at = NOW()
         WHERE riwayat.id_test = ?
            AND detail.deleted_at IS NULL"
    );

    if (!$stmt_detail) {
        throw new Exception(
            "Gagal menyiapkan penghapusan detail riwayat."
        );
    }

    mysqli_stmt_bind_param(
        $stmt_detail,
        "i",
        $id_test
    );

    if (!mysqli_stmt_execute($stmt_detail)) {
        throw new Exception(
            "Gagal menghapus detail riwayat."
        );
    }

    mysqli_stmt_close($stmt_detail);


    /*
    |--------------------------------------------------------------------------
    | Soft delete riwayat pengerjaan
    |--------------------------------------------------------------------------
    */

    $stmt_riwayat = mysqli_prepare(
        $conn,
        "UPDATE riwayat_tes
         SET deleted_at = NOW()
         WHERE id_test = ?
            AND deleted_at IS NULL"
    );

    if (!$stmt_riwayat) {
        throw new Exception(
            "Gagal menyiapkan penghapusan riwayat."
        );
    }

    mysqli_stmt_bind_param(
        $stmt_riwayat,
        "i",
        $id_test
    );

    if (!mysqli_stmt_execute($stmt_riwayat)) {
        throw new Exception(
            "Gagal menghapus riwayat test."
        );
    }

    mysqli_stmt_close($stmt_riwayat);


    /*
    |--------------------------------------------------------------------------
    | Soft delete seluruh soal
    |--------------------------------------------------------------------------
    */

    $stmt_soal = mysqli_prepare(
        $conn,
        "UPDATE soal
         SET deleted_at = NOW()
         WHERE id_test = ?
            AND deleted_at IS NULL"
    );

    if (!$stmt_soal) {
        throw new Exception(
            "Gagal menyiapkan penghapusan soal."
        );
    }

    mysqli_stmt_bind_param(
        $stmt_soal,
        "i",
        $id_test
    );

    if (!mysqli_stmt_execute($stmt_soal)) {
        throw new Exception(
            "Gagal menghapus soal."
        );
    }

    mysqli_stmt_close($stmt_soal);


    /*
    |--------------------------------------------------------------------------
    | Soft delete test
    |--------------------------------------------------------------------------
    */

    $stmt_hapus_test = mysqli_prepare(
        $conn,
        "UPDATE test
         SET deleted_at = NOW()
         WHERE id_test = ?
            AND id_user = ?
            AND deleted_at IS NULL"
    );

    if (!$stmt_hapus_test) {
        throw new Exception(
            "Gagal menyiapkan penghapusan test."
        );
    }

    mysqli_stmt_bind_param(
        $stmt_hapus_test,
        "ii",
        $id_test,
        $id_user
    );

    if (!mysqli_stmt_execute(
        $stmt_hapus_test
    )) {
        throw new Exception(
            "Gagal menghapus test."
        );
    }

    mysqli_stmt_close($stmt_hapus_test);


    /*
    |--------------------------------------------------------------------------
    | Simpan seluruh perubahan
    |--------------------------------------------------------------------------
    */

    mysqli_commit($conn);

    header(
        "Location: test.php?" .
        "status=hapus-berhasil"
    );

    exit;

} catch (Throwable $e) {

    mysqli_rollback($conn);

    die(
        "Gagal menghapus test: " .
        htmlspecialchars(
            $e->getMessage()
        )
    );
}