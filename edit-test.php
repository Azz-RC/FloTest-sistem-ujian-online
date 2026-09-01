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

$error = "";


/*
|--------------------------------------------------------------------------
| Ambil ID test
|--------------------------------------------------------------------------
*/

$id_test = isset($_GET["id_test"])
    ? (int) $_GET["id_test"]
    : (int) ($_POST["id_test"] ?? 0);

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
        kode_test
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
| Proses edit test
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $judul_test = trim(
        $_POST["judul_test"] ?? ""
    );

    $soal_json = $_POST["soal_data"] ?? "";

    $soal_data = json_decode(
        $soal_json,
        true
    );


    /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

    if ($judul_test === "") {

        $error = "Judul test wajib diisi.";

    } elseif (strlen($judul_test) > 255) {

        $error =
            "Judul test maksimal 255 karakter.";

    } elseif (
        !is_array($soal_data) ||
        count($soal_data) < 1
    ) {

        $error =
            "Minimal harus ada 1 soal.";

    } elseif (count($soal_data) > 100) {

        $error =
            "Maksimal 100 soal dalam satu test.";

    } else {

        mysqli_begin_transaction($conn);

        try {

            /*
            |--------------------------------------------------------------------------
            | Kunci dan periksa test
            |--------------------------------------------------------------------------
            */

            $stmt_pemilik = mysqli_prepare(
                $conn,
                "SELECT id_test
                 FROM test
                 WHERE id_test = ?
                    AND id_user = ?
                    AND deleted_at IS NULL
                 FOR UPDATE"
            );

            if (!$stmt_pemilik) {
                throw new Exception(
                    "Gagal memeriksa pemilik test."
                );
            }

            mysqli_stmt_bind_param(
                $stmt_pemilik,
                "ii",
                $id_test,
                $id_user
            );

            mysqli_stmt_execute($stmt_pemilik);

            $result_pemilik =
                mysqli_stmt_get_result(
                    $stmt_pemilik
                );

            $pemilik = mysqli_fetch_assoc(
                $result_pemilik
            );

            mysqli_stmt_close($stmt_pemilik);

            if (!$pemilik) {
                throw new Exception(
                    "Test tidak ditemukan atau bukan milik Anda."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Ambil ID soal lama
            |--------------------------------------------------------------------------
            */

            $id_soal_valid = [];

            $stmt_soal_lama = mysqli_prepare(
                $conn,
                "SELECT id_soal
                 FROM soal
                 WHERE id_test = ?
                    AND deleted_at IS NULL"
            );

            if (!$stmt_soal_lama) {
                throw new Exception(
                    "Gagal mengambil data soal lama."
                );
            }

            mysqli_stmt_bind_param(
                $stmt_soal_lama,
                "i",
                $id_test
            );

            mysqli_stmt_execute(
                $stmt_soal_lama
            );

            $result_soal_lama =
                mysqli_stmt_get_result(
                    $stmt_soal_lama
                );

            while (
                $row = mysqli_fetch_assoc(
                    $result_soal_lama
                )
            ) {
                $id_soal_valid[
                    (int) $row["id_soal"]
                ] = true;
            }

            mysqli_stmt_close(
                $stmt_soal_lama
            );


            /*
            |--------------------------------------------------------------------------
            | Update judul test
            |--------------------------------------------------------------------------
            */

            $stmt_update_test = mysqli_prepare(
                $conn,
                "UPDATE test
                 SET judul_test = ?
                 WHERE id_test = ?
                    AND id_user = ?
                    AND deleted_at IS NULL"
            );

            if (!$stmt_update_test) {
                throw new Exception(
                    "Gagal menyiapkan perubahan test."
                );
            }

            mysqli_stmt_bind_param(
                $stmt_update_test,
                "sii",
                $judul_test,
                $id_test,
                $id_user
            );

            if (!mysqli_stmt_execute(
                $stmt_update_test
            )) {
                throw new Exception(
                    "Gagal mengubah judul test."
                );
            }

            mysqli_stmt_close(
                $stmt_update_test
            );


            /*
            |--------------------------------------------------------------------------
            | Update atau tambah soal
            |--------------------------------------------------------------------------
            */

            $id_dipertahankan = [];

            foreach (
                $soal_data as $index => $data
            ) {

                $nomor_soal = $index + 1;

                $id_soal = (int) (
                    $data["id_soal"] ?? 0
                );

                $pertanyaan = trim(
                    $data["pertanyaan"] ?? ""
                );

                $pilihan_a = trim(
                    $data["pilihan_a"] ?? ""
                );

                $pilihan_b = trim(
                    $data["pilihan_b"] ?? ""
                );

                $pilihan_c = trim(
                    $data["pilihan_c"] ?? ""
                );

                $pilihan_d = trim(
                    $data["pilihan_d"] ?? ""
                );

                $kunci_jawaban = strtoupper(
                    trim(
                        $data[
                            "kunci_jawaban"
                        ] ?? ""
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | Validasi setiap soal
                |--------------------------------------------------------------------------
                */

                if ($pertanyaan === "") {
                    throw new Exception(
                        "Pertanyaan soal nomor " .
                        $nomor_soal .
                        " wajib diisi."
                    );
                }

                if (
                    $pilihan_a === "" ||
                    $pilihan_b === "" ||
                    $pilihan_c === "" ||
                    $pilihan_d === ""
                ) {
                    throw new Exception(
                        "Semua pilihan soal nomor " .
                        $nomor_soal .
                        " wajib diisi."
                    );
                }

                if (
                    !in_array(
                        $kunci_jawaban,
                        ["A", "B", "C", "D"],
                        true
                    )
                ) {
                    throw new Exception(
                        "Kunci jawaban soal nomor " .
                        $nomor_soal .
                        " belum dipilih."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Update soal lama
                |--------------------------------------------------------------------------
                */

                if ($id_soal > 0) {

                    if (
                        !isset(
                            $id_soal_valid[$id_soal]
                        )
                    ) {
                        throw new Exception(
                            "Data soal tidak valid."
                        );
                    }

                    $stmt_update_soal =
                        mysqli_prepare(
                            $conn,
                            "UPDATE soal
                             SET
                                nomor_soal = ?,
                                pertanyaan = ?,
                                pilihan_a = ?,
                                pilihan_b = ?,
                                pilihan_c = ?,
                                pilihan_d = ?,
                                kunci_jawaban = ?
                             WHERE id_soal = ?
                                AND id_test = ?
                                AND deleted_at IS NULL"
                        );

                    if (!$stmt_update_soal) {
                        throw new Exception(
                            "Gagal menyiapkan perubahan soal."
                        );
                    }

                    mysqli_stmt_bind_param(
                        $stmt_update_soal,
                        "issssssii",
                        $nomor_soal,
                        $pertanyaan,
                        $pilihan_a,
                        $pilihan_b,
                        $pilihan_c,
                        $pilihan_d,
                        $kunci_jawaban,
                        $id_soal,
                        $id_test
                    );

                    if (!mysqli_stmt_execute(
                        $stmt_update_soal
                    )) {
                        throw new Exception(
                            "Gagal mengubah soal nomor " .
                            $nomor_soal .
                            "."
                        );
                    }

                    mysqli_stmt_close(
                        $stmt_update_soal
                    );

                    $id_dipertahankan[
                        $id_soal
                    ] = true;


                /*
                |--------------------------------------------------------------------------
                | Tambah soal baru
                |--------------------------------------------------------------------------
                */

                } else {

                    $stmt_insert_soal =
                        mysqli_prepare(
                            $conn,
                            "INSERT INTO soal
                            (
                                id_test,
                                nomor_soal,
                                pertanyaan,
                                pilihan_a,
                                pilihan_b,
                                pilihan_c,
                                pilihan_d,
                                kunci_jawaban
                            )
                            VALUES
                            (?, ?, ?, ?, ?, ?, ?, ?)"
                        );

                    if (!$stmt_insert_soal) {
                        throw new Exception(
                            "Gagal menyiapkan soal baru."
                        );
                    }

                    mysqli_stmt_bind_param(
                        $stmt_insert_soal,
                        "iissssss",
                        $id_test,
                        $nomor_soal,
                        $pertanyaan,
                        $pilihan_a,
                        $pilihan_b,
                        $pilihan_c,
                        $pilihan_d,
                        $kunci_jawaban
                    );

                    if (!mysqli_stmt_execute(
                        $stmt_insert_soal
                    )) {
                        throw new Exception(
                            "Gagal menambahkan soal nomor " .
                            $nomor_soal .
                            "."
                        );
                    }

                    mysqli_stmt_close(
                        $stmt_insert_soal
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Soft delete soal yang dihapus dari form
            |--------------------------------------------------------------------------
            */

            foreach (
                array_keys($id_soal_valid)
                as $id_soal_lama
            ) {

                if (
                    !isset(
                        $id_dipertahankan[
                            $id_soal_lama
                        ]
                    )
                ) {

                    $stmt_hapus_soal =
                        mysqli_prepare(
                            $conn,
                            "UPDATE soal
                             SET deleted_at = NOW()
                             WHERE id_soal = ?
                                AND id_test = ?
                                AND deleted_at IS NULL"
                        );

                    if (!$stmt_hapus_soal) {
                        throw new Exception(
                            "Gagal menghapus soal."
                        );
                    }

                    mysqli_stmt_bind_param(
                        $stmt_hapus_soal,
                        "ii",
                        $id_soal_lama,
                        $id_test
                    );

                    mysqli_stmt_execute(
                        $stmt_hapus_soal
                    );

                    mysqli_stmt_close(
                        $stmt_hapus_soal
                    );
                }
            }


            mysqli_commit($conn);

            header(
                "Location: lihat-test.php?id_test=" .
                $id_test .
                "&status=edit-berhasil"
            );

            exit;

        } catch (Throwable $e) {

            mysqli_rollback($conn);

            $error = $e->getMessage();
        }
    }
}


/*
|--------------------------------------------------------------------------
| Ambil soal untuk ditampilkan dalam form
|--------------------------------------------------------------------------
*/

$daftar_soal = [];

$stmt_daftar_soal = mysqli_prepare(
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

if (!$stmt_daftar_soal) {
    die("Gagal mengambil daftar soal.");
}

mysqli_stmt_bind_param(
    $stmt_daftar_soal,
    "i",
    $id_test
);

mysqli_stmt_execute(
    $stmt_daftar_soal
);

$result_daftar_soal =
    mysqli_stmt_get_result(
        $stmt_daftar_soal
    );

while (
    $row = mysqli_fetch_assoc(
        $result_daftar_soal
    )
) {
    $daftar_soal[] = $row;
}

mysqli_stmt_close($stmt_daftar_soal);

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

    <title>Edit Test - FloTest</title>

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

    <main class="edit-test-content">

        <a
            href="buat-test.php"
            class="back-link"
        >
            &larr; Kembali ke Test Saya
        </a>


        <div class="edit-test-heading">

            <h1>Edit Test</h1>

            <p>

                Kode test:

                <strong>

                    <?= htmlspecialchars(
                        $test["kode_test"]
                    ) ?>

                </strong>

            </p>

        </div>


        <?php if ($error !== ""): ?>

            <div class="error-message">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form
            action="edit-test.php"
            method="POST"
            id="form-edit-test"
        >

            <input
                type="hidden"
                name="id_test"
                value="<?= $id_test ?>"
            >

            <input
                type="hidden"
                name="soal_data"
                id="edit-soal-data"
            >


            <!-- JUDUL TEST -->

            <div class="buat-test-card">

                <div class="form-group">

                    <label for="judul-test">
                        Judul Test
                    </label>

                    <input
                        type="text"
                        id="judul-test"
                        name="judul_test"
                        maxlength="255"
                        required
                        value="<?= htmlspecialchars(
                            $_POST["judul_test"] ??
                            $test["judul_test"]
                        ) ?>"
                    >

                </div>

            </div>


            <!-- DAFTAR SOAL -->

            <div id="edit-soal-container">

                <?php foreach (
                    $daftar_soal
                    as $index => $soal
                ): ?>

                    <div
                        class="soal-card edit-soal-card"
                        data-id-soal="<?= (int)
                            $soal["id_soal"] ?>"
                    >

                        <div class="soal-title">

                            <span>

                                Soal <?= $index + 1 ?>

                            </span>

                            <button
                                type="button"
                                class="btn-remove-soal"
                            >
                                Hapus Soal
                            </button>

                        </div>


                        <div class="soal-content">

                            <div class="pertanyaan-area">

                                <textarea
                                    class="input-soal"
                                    maxlength="65535"
                                    required
                                ><?= htmlspecialchars(
                                    $soal["pertanyaan"]
                                ) ?></textarea>

                            </div>


                            <div class="pilihan-area">

                                <?php foreach (
                                    ["a", "b", "c", "d"]
                                    as $huruf
                                ): ?>

                                    <label
                                        class="pilihan-item"
                                    >

                                        <span>

                                            <?= strtoupper(
                                                $huruf
                                            ) ?>

                                        </span>

                                        <input
                                            type="text"
                                            class="input-pilihan pilihan-<?= $huruf ?>"
                                            maxlength="255"
                                            required
                                            value="<?= htmlspecialchars(
                                                $soal[
                                                    "pilihan_" .
                                                    $huruf
                                                ]
                                            ) ?>"
                                        >

                                    </label>

                                <?php endforeach; ?>

                            </div>


                            <div class="kunci-area">

                                <label>
                                    Kunci Jawaban
                                </label>

                                <select
                                    class="kunci-jawaban"
                                    required
                                >

                                    <option value="">
                                        Pilih jawaban
                                    </option>

                                    <?php foreach (
                                        ["A", "B", "C", "D"]
                                        as $pilihan
                                    ): ?>

                                        <option
                                            value="<?= $pilihan ?>"
                                            <?= strtoupper(
                                                $soal[
                                                    "kunci_jawaban"
                                                ]
                                            ) === $pilihan
                                                ? "selected"
                                                : "" ?>
                                        >
                                            <?= $pilihan ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


            <div class="edit-actions">

                <button
                    type="button"
                    id="btn-tambah-edit"
                    class="btn-tambah-soal"
                >
                    + Tambah Soal
                </button>

                <button
                    type="submit"
                    class="btn-next"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </main>

    <?php
    require_once __DIR__ .
        "/components/footer.php";
    ?>

</div>

<script src="assets/js/script.js"></script>

</body>

</html>