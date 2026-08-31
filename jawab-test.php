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
   PROSES MASUK TEST
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $kode_test =
        trim($_POST["kode_test"] ?? "");


    if ($kode_test === "") {

        $error =
            "Kode test harus diisi.";

    } else {

        $stmt_test = mysqli_prepare(
            $conn,
            "SELECT
                id_test,
                judul_test,
                kode_test
             FROM test
             WHERE kode_test = ?
             AND deleted_at IS NULL
             LIMIT 1"
        );


        if (!$stmt_test) {

            $error =
                "Gagal menyiapkan query test.";

        } else {

            mysqli_stmt_bind_param(
                $stmt_test,
                "s",
                $kode_test
            );

            mysqli_stmt_execute($stmt_test);

            $result_test =
                mysqli_stmt_get_result($stmt_test);

            $test =
                mysqli_fetch_assoc($result_test);

            mysqli_stmt_close($stmt_test);


            if (!$test) {

                $error =
                    "Kode test tidak ditemukan. Silakan periksa kembali kode yang diberikan.";

            } else {

                $id_test =
                    (int) $test["id_test"];


                /*
                 * Cari room terakhir milik user.
                 */
                $stmt_room = mysqli_prepare(
                    $conn,
                    "SELECT
                        id_riwayat_tes,
                        status_pengerjaan
                     FROM riwayat_tes
                     WHERE id_user = ?
                     AND id_test = ?
                     AND deleted_at IS NULL
                     ORDER BY id_riwayat_tes DESC
                     LIMIT 1"
                );


                if (!$stmt_room) {

                    $error =
                        "Gagal memeriksa riwayat room.";

                } else {

                    mysqli_stmt_bind_param(
                        $stmt_room,
                        "ii",
                        $id_user,
                        $id_test
                    );

                    mysqli_stmt_execute($stmt_room);

                    $result_room =
                        mysqli_stmt_get_result($stmt_room);

                    $room =
                        mysqli_fetch_assoc($result_room);

                    mysqli_stmt_close($stmt_room);


                    if ($room) {

                        $id_riwayat_tes =
                            (int) $room["id_riwayat_tes"];

                        $status =
                            $room["status_pengerjaan"];


                        if ($status === "draft") {

                            header(
                                "Location: kerjakan-test.php?id_test=" .
                                $id_test .
                                "&id_riwayat_tes=" .
                                $id_riwayat_tes
                            );

                            exit;
                        }


                        if ($status === "selesai") {

                            $error =
                                "Anda sudah menyelesaikan test ini. Silakan lihat hasilnya pada Riwayat Room.";
                        }

                    } else {

                        /*
                         * Buat room baru.
                         */
                        $stmt_insert = mysqli_prepare(
                            $conn,
                            "INSERT INTO riwayat_tes
                            (
                                id_user,
                                id_test,
                                tanggal_masuk,
                                skor_akhir,
                                jumlah_benar,
                                jumlah_salah,
                                status_pengerjaan
                            )
                            VALUES
                            (
                                ?,
                                ?,
                                NOW(),
                                NULL,
                                0,
                                0,
                                'draft'
                            )"
                        );


                        if (!$stmt_insert) {

                            $error =
                                "Gagal menyiapkan pembuatan room.";

                        } else {

                            mysqli_stmt_bind_param(
                                $stmt_insert,
                                "ii",
                                $id_user,
                                $id_test
                            );


                            if (
                                mysqli_stmt_execute(
                                    $stmt_insert
                                )
                            ) {

                                $id_riwayat_tes =
                                    mysqli_insert_id($conn);

                                mysqli_stmt_close(
                                    $stmt_insert
                                );


                                header(
                                    "Location: kerjakan-test.php?id_test=" .
                                    $id_test .
                                    "&id_riwayat_tes=" .
                                    $id_riwayat_tes
                                );

                                exit;

                            } else {

                                $error =
                                    "Gagal membuat room test.";

                                mysqli_stmt_close(
                                    $stmt_insert
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}


/* =========================================================
   AMBIL RIWAYAT ROOM
========================================================= */

$riwayat = [];


$stmt_history = mysqli_prepare(
    $conn,
    "SELECT
        r.id_riwayat_tes,
        r.id_test,
        r.tanggal_masuk,
        r.skor_akhir,
        r.jumlah_benar,
        r.jumlah_salah,
        r.status_pengerjaan,
        t.judul_test,
        t.kode_test
     FROM riwayat_tes r
     INNER JOIN test t
        ON r.id_test = t.id_test
     WHERE r.id_user = ?
     AND r.deleted_at IS NULL
     AND t.deleted_at IS NULL
     ORDER BY r.updated_at DESC,
              r.id_riwayat_tes DESC"
);


if ($stmt_history) {

    mysqli_stmt_bind_param(
        $stmt_history,
        "i",
        $id_user
    );

    mysqli_stmt_execute($stmt_history);

    $result_history =
        mysqli_stmt_get_result($stmt_history);


    while (
        $row =
        mysqli_fetch_assoc($result_history)
    ) {

        $riwayat[] = $row;
    }


    mysqli_stmt_close($stmt_history);
}

$active_page = "jawab-test";

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
        Jawab Test - FloTest
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="home-page">


    <?php include __DIR__ . "/components/navbar.php"; ?>


    <main class="jawab-test-content">


        <div class="jawab-test-card">

            <h1>
                Jawab Test
            </h1>

            <p class="page-description">
                Masukkan kode test yang diberikan oleh
                pembuat test.
            </p>


            <?php if ($error !== ""): ?>

                <div class="alert-error">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form
                action="jawab-test.php"
                method="POST"
                class="kode-test-form"
            >

                <div class="form-group">

                    <label for="kode-test">
                        Input Kode Test
                    </label>

                    <input
                        type="text"
                        id="kode-test"
                        name="kode_test"
                        placeholder="Contoh: 840"
                        maxlength="100"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn-primary btn-masuk"
                >
                    Masuk
                </button>

            </form>


            <p class="form-hint">
                * Masukkan kode test yang diberikan oleh
                pembuat test untuk masuk ke room.
            </p>

        </div>


        <div class="jawab-test-card">

            <h2>
                Riwayat Room
            </h2>


            <div class="riwayat-room">


                <?php if (count($riwayat) === 0): ?>

                    <div class="empty-room">

                        Belum ada room yang diikuti.

                    </div>

                <?php else: ?>


                    <?php foreach ($riwayat as $room): ?>

                        <div class="room-item">


                            <div class="room-info">

                                <h4>
                                    <?= htmlspecialchars(
                                        $room["judul_test"]
                                    ) ?>
                                </h4>


                                <p>

                                    Kode Test:

                                    <strong>
                                        <?= htmlspecialchars(
                                            $room["kode_test"]
                                        ) ?>
                                    </strong>

                                </p>


                                <p>

                                    Masuk:

                                    <?= htmlspecialchars(
                                        $room["tanggal_masuk"]
                                    ) ?>

                                </p>


                                <?php if (
                                    $room["status_pengerjaan"]
                                    === "draft"
                                ): ?>

                                    <span class="room-status status-draft">
                                        Draft
                                    </span>

                                <?php else: ?>

                                    <span class="room-status status-selesai">
                                        Selesai
                                    </span>

                                <?php endif; ?>

                            </div>


                            <div class="room-action">

                                <?php if (
                                    $room["status_pengerjaan"]
                                    === "draft"
                                ): ?>

                                    <a
                                        href="kerjakan-test.php?id_test=<?= (int) $room["id_test"] ?>&id_riwayat_tes=<?= (int) $room["id_riwayat_tes"] ?>"
                                        class="btn-room"
                                    >
                                        Lanjutkan
                                    </a>

                                <?php else: ?>

                                    <a
                                        href="test-selesai.php?id_test=<?= (int) $room["id_test"] ?>&id_riwayat_tes=<?= (int) $room["id_riwayat_tes"] ?>"
                                        class="btn-hasil"
                                    >
                                        Lihat Hasil
                                    </a>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>

</div>


<script src="assets/js/script.js"></script>

</body>

</html>