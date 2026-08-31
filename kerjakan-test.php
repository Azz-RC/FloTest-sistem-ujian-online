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
   CEK PARAMETER
========================================================= */

$id_test = isset($_GET["id_test"])
    ? (int) $_GET["id_test"]
    : 0;

$id_riwayat_tes = isset($_GET["id_riwayat_tes"])
    ? (int) $_GET["id_riwayat_tes"]
    : 0;


if ($id_test <= 0) {
    die("ID test tidak valid.");
}


if ($id_riwayat_tes <= 0) {
    die("ID room tidak valid.");
}


/* =========================================================
   CEK TEST
========================================================= */

$stmt_test = mysqli_prepare(
    $conn,
    "SELECT
        id_test,
        judul_test,
        kode_test
     FROM test
     WHERE id_test = ?
     AND deleted_at IS NULL
     LIMIT 1"
);


if (!$stmt_test) {
    die("Gagal menyiapkan query test.");
}


mysqli_stmt_bind_param(
    $stmt_test,
    "i",
    $id_test
);

mysqli_stmt_execute($stmt_test);

$result_test =
    mysqli_stmt_get_result($stmt_test);

$test =
    mysqli_fetch_assoc($result_test);

mysqli_stmt_close($stmt_test);


if (!$test) {
    die("Test tidak ditemukan.");
}


$judul_test = $test["judul_test"];
$kode_test = $test["kode_test"];


/* =========================================================
   CEK ROOM MILIK USER
========================================================= */

$stmt_room = mysqli_prepare(
    $conn,
    "SELECT
        id_riwayat_tes,
        id_user,
        id_test,
        status_pengerjaan,
        skor_akhir,
        jumlah_benar,
        jumlah_salah
     FROM riwayat_tes
     WHERE id_riwayat_tes = ?
     AND id_user = ?
     AND id_test = ?
     AND deleted_at IS NULL
     LIMIT 1"
);


if (!$stmt_room) {
    die("Gagal memeriksa room.");
}


mysqli_stmt_bind_param(
    $stmt_room,
    "iii",
    $id_riwayat_tes,
    $id_user,
    $id_test
);

mysqli_stmt_execute($stmt_room);

$result_room =
    mysqli_stmt_get_result($stmt_room);

$room =
    mysqli_fetch_assoc($result_room);

mysqli_stmt_close($stmt_room);


if (!$room) {
    die("Room tidak ditemukan atau bukan milik Anda.");
}


/* =========================================================
   JIKA SUDAH SELESAI
========================================================= */

if (
    $room["status_pengerjaan"] === "selesai"
) {

    header(
        "Location: test-selesai.php?id_test=" .
        $id_test .
        "&id_riwayat_tes=" .
        $id_riwayat_tes
    );

    exit;
}


/* =========================================================
   AMBIL SOAL
========================================================= */

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
     ORDER BY nomor_soal ASC,
              id_soal ASC"
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

$result_soal =
    mysqli_stmt_get_result($stmt_soal);

$soal = [];


while (
    $row =
    mysqli_fetch_assoc($result_soal)
) {

    $soal[] = $row;
}


mysqli_stmt_close($stmt_soal);


if (count($soal) === 0) {
    die("Test ini belum memiliki soal.");
}


/* =========================================================
   PROSES JAWABAN
========================================================= */

$nilai = null;
$jumlah_benar = 0;
$jumlah_salah = 0;
$sudah_selesai = false;


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $jawaban =
        $_POST["jawaban"] ?? [];


    if (!is_array($jawaban)) {
        $jawaban = [];
    }


    mysqli_begin_transaction($conn);


    try {

        /*
         * Kunci room dengan SELECT FOR UPDATE
         * agar submit tidak diproses dua kali
         * secara bersamaan.
         */

        $stmt_lock = mysqli_prepare(
            $conn,
            "SELECT
                status_pengerjaan
             FROM riwayat_tes
             WHERE id_riwayat_tes = ?
             AND id_user = ?
             AND id_test = ?
             AND deleted_at IS NULL
             LIMIT 1
             FOR UPDATE"
        );


        if (!$stmt_lock) {
            throw new Exception(
                "Gagal mengunci room."
            );
        }


        mysqli_stmt_bind_param(
            $stmt_lock,
            "iii",
            $id_riwayat_tes,
            $id_user,
            $id_test
        );


        mysqli_stmt_execute($stmt_lock);

        $result_lock =
            mysqli_stmt_get_result($stmt_lock);

        $locked_room =
            mysqli_fetch_assoc($result_lock);

        mysqli_stmt_close($stmt_lock);


        if (!$locked_room) {

            throw new Exception(
                "Room tidak ditemukan."
            );
        }


        if (
            $locked_room["status_pengerjaan"]
            === "selesai"
        ) {

            mysqli_rollback($conn);

            header(
                "Location: test-selesai.php?id_test=" .
                $id_test .
                "&id_riwayat_tes=" .
                $id_riwayat_tes
            );

            exit;
        }


        /*
         * Hapus detail lama jika ada.
         * Ini membuat submit tetap bersih
         * apabila ada data detail yang tersisa.
         */

        $stmt_delete = mysqli_prepare(
            $conn,
            "DELETE FROM detail_riwayat
             WHERE id_riwayat_tes = ?"
        );


        if (!$stmt_delete) {
            throw new Exception(
                "Gagal membersihkan jawaban sebelumnya."
            );
        }


        mysqli_stmt_bind_param(
            $stmt_delete,
            "i",
            $id_riwayat_tes
        );

        mysqli_stmt_execute($stmt_delete);

        mysqli_stmt_close($stmt_delete);


        /*
         * Prepare insert detail jawaban.
         */

        $stmt_detail = mysqli_prepare(
            $conn,
            "INSERT INTO detail_riwayat
            (
                id_riwayat_tes,
                id_soal,
                jawaban_siswa
            )
            VALUES (?, ?, ?)"
        );


        if (!$stmt_detail) {
            throw new Exception(
                "Gagal menyiapkan penyimpanan jawaban."
            );
        }


        $jumlah_soal =
            count($soal);


        foreach ($soal as $item) {

            $id_soal =
                (int) $item["id_soal"];


            $kunci =
                strtoupper(
                    trim(
                        $item["kunci_jawaban"]
                    )
                );


            $jawaban_user =
                strtoupper(
                    trim(
                        $jawaban[$id_soal] ?? ""
                    )
                );


            /*
             * Hanya izinkan A-D.
             */
            if (
                !in_array(
                    $jawaban_user,
                    ["A", "B", "C", "D"],
                    true
                )
            ) {
                $jawaban_user = "";
            }


            /*
             * Simpan jawaban.
             */
            mysqli_stmt_bind_param(
                $stmt_detail,
                "iis",
                $id_riwayat_tes,
                $id_soal,
                $jawaban_user
            );


            if (
                !mysqli_stmt_execute(
                    $stmt_detail
                )
            ) {

                throw new Exception(
                    "Gagal menyimpan jawaban."
                );
            }


            /*
             * Hitung nilai.
             */
            if (
                $jawaban_user !== "" &&
                $jawaban_user === $kunci
            ) {

                $jumlah_benar++;

            } else {

                $jumlah_salah++;
            }
        }


        mysqli_stmt_close($stmt_detail);


        $nilai = 0;

        if ($jumlah_soal > 0) {

            $nilai = round(
                (
                    $jumlah_benar /
                    $jumlah_soal
                ) * 100,
                2
            );
        }


        /*
         * Simpan hasil ke riwayat_tes.
         */
        $stmt_update = mysqli_prepare(
            $conn,
            "UPDATE riwayat_tes
             SET
                skor_akhir = ?,
                jumlah_benar = ?,
                jumlah_salah = ?,
                status_pengerjaan = 'selesai'
             WHERE id_riwayat_tes = ?
             AND id_user = ?
             AND id_test = ?
             AND deleted_at IS NULL"
        );


        if (!$stmt_update) {
            throw new Exception(
                "Gagal menyiapkan update hasil."
            );
        }


        mysqli_stmt_bind_param(
            $stmt_update,
            "diiiii",
            $nilai,
            $jumlah_benar,
            $jumlah_salah,
            $id_riwayat_tes,
            $id_user,
            $id_test
        );


        if (
            !mysqli_stmt_execute(
                $stmt_update
            )
        ) {

            throw new Exception(
                "Gagal menyimpan hasil test."
            );
        }


        mysqli_stmt_close($stmt_update);


        mysqli_commit($conn);


        $sudah_selesai = true;


    } catch (Throwable $e) {

        mysqli_rollback($conn);

        die(
            "Gagal menyimpan hasil test: " .
            htmlspecialchars(
                $e->getMessage()
            )
        );
    }
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
        Kerjakan Test - FloTest
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="home-page">


   <?php include __DIR__ . "/components/navbar.php"; ?>


    <?php if ($sudah_selesai): ?>


        <!-- =================================================
             HASIL TEST
        ================================================== -->

        <main class="kerjakan-test-content">

            <div class="kerjakan-test-card">

                <div class="test-header">

                    <div>

                        <h1>
                            <?= htmlspecialchars(
                                $judul_test
                            ) ?>
                        </h1>

                        <p>
                            Kode Test:
                            <strong>
                                <?= htmlspecialchars(
                                    $kode_test
                                ) ?>
                            </strong>
                        </p>

                    </div>

                </div>


                <div class="hasil-test">

                    <div class="success-icon">
                        ✓
                    </div>


                    <h2>
                        Test Selesai!
                    </h2>


                    <p>
                        Kamu sudah menyelesaikan test
                        <strong>
                            <?= htmlspecialchars(
                                $judul_test
                            ) ?>
                        </strong>.
                    </p>


                    <div class="nilai-box">

                        <span class="nilai-label">
                            Nilai Anda
                        </span>

                        <span class="nilai">
                            <?= htmlspecialchars(
                                number_format(
                                    (float) $nilai,
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
                                <?= count($soal) ?>
                            </strong>

                            <span>
                                Total Soal
                            </span>

                        </div>

                    </div>


                    <div class="hasil-buttons">

                        <a
                            href="jawab-test.php"
                            class="btn-secondary"
                        >
                            Kembali
                        </a>

                        <a
                            href="home.php"
                            class="btn-next"
                        >
                            Ke Home
                        </a>

                    </div>

                </div>

            </div>

        </main>


    <?php else: ?>


        <!-- =================================================
             HALAMAN PENGERJAAN
        ================================================== -->

        <main class="kerjakan-test-content">

            <div class="kerjakan-test-card">


                <div class="test-header">

                    <div>

                        <h1>
                            <?= htmlspecialchars(
                                $judul_test
                            ) ?>
                        </h1>

                        <p>
                            Kode Test:
                            <strong>
                                <?= htmlspecialchars(
                                    $kode_test
                                ) ?>
                            </strong>
                        </p>

                    </div>


                    <div
                        class="nomor-indikator"
                        id="nomor-indikator"
                    >
                        Soal 1
                    </div>

                </div>


                <form
                    method="POST"
                    action="kerjakan-test.php?id_test=<?= $id_test ?>&id_riwayat_tes=<?= $id_riwayat_tes ?>"
                    id="form-jawaban"
                >


                    <div id="soal-container">


                        <?php foreach (
                            $soal
                            as $index => $item
                        ): ?>

                            <?php

                            $nomor =
                                $index + 1;

                            $id_soal =
                                (int) $item["id_soal"];

                            ?>


                            <div
                                class="soal-jawab-card"
                                data-index="<?= $index ?>"
                            >

                                <div class="soal-jawab-title">

                                    Soal <?= $nomor ?>

                                </div>


                                <div class="soal-jawab-content">


                                    <div class="pertanyaan-test">

                                        <?= nl2br(
                                            htmlspecialchars(
                                                $item["pertanyaan"]
                                            )
                                        ) ?>

                                    </div>


                                    <label class="jawaban-item">

                                        <input
                                            type="radio"
                                            name="jawaban[<?= $id_soal ?>]"
                                            value="A"
                                        >

                                        <span class="huruf-jawaban">
                                            A
                                        </span>

                                        <span class="isi-jawaban">
                                            <?= htmlspecialchars(
                                                $item["pilihan_a"]
                                            ) ?>
                                        </span>

                                    </label>


                                    <label class="jawaban-item">

                                        <input
                                            type="radio"
                                            name="jawaban[<?= $id_soal ?>]"
                                            value="B"
                                        >

                                        <span class="huruf-jawaban">
                                            B
                                        </span>

                                        <span class="isi-jawaban">
                                            <?= htmlspecialchars(
                                                $item["pilihan_b"]
                                            ) ?>
                                        </span>

                                    </label>


                                    <label class="jawaban-item">

                                        <input
                                            type="radio"
                                            name="jawaban[<?= $id_soal ?>]"
                                            value="C"
                                        >

                                        <span class="huruf-jawaban">
                                            C
                                        </span>

                                        <span class="isi-jawaban">
                                            <?= htmlspecialchars(
                                                $item["pilihan_c"]
                                            ) ?>
                                        </span>

                                    </label>


                                    <label class="jawaban-item">

                                        <input
                                            type="radio"
                                            name="jawaban[<?= $id_soal ?>]"
                                            value="D"
                                        >

                                        <span class="huruf-jawaban">
                                            D
                                        </span>

                                        <span class="isi-jawaban">
                                            <?= htmlspecialchars(
                                                $item["pilihan_d"]
                                            ) ?>
                                        </span>

                                    </label>


                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>


                    <div class="soal-navigation">

                        <button
                            type="button"
                            class="btn-secondary"
                            id="btn-kembali"
                        >
                            Kembali
                        </button>


                        <button
                            type="button"
                            class="btn-next"
                            id="btn-selanjutnya"
                        >
                            Selanjutnya
                        </button>


                        <button
                            type="submit"
                            class="btn-next"
                            id="btn-selesai"
                            hidden
                        >
                            Selesai
                        </button>

                    </div>

                </form>

            </div>

        </main>


    <?php endif; ?>

    <?php require_once __DIR__ . "/components/footer.php"; ?>

</div>


<script src="assets/js/script.js"></script>

</body>

</html>