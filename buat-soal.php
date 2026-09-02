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
   AMBIL ID TEST
========================================================= */

$id_test = isset($_GET["id_test"])
    ? (int) $_GET["id_test"]
    : 0;


if ($id_test <= 0) {
    die("ID test tidak valid.");
}


/* =========================================================
   CEK TEST DAN PEMILIK
========================================================= */

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
    die(
        "Gagal menyiapkan query test."
    );
}


mysqli_stmt_bind_param(
    $stmt_test,
    "ii",
    $id_test,
    $id_user
);


mysqli_stmt_execute($stmt_test);

$result_test =
    mysqli_stmt_get_result($stmt_test);

$test =
    mysqli_fetch_assoc($result_test);

mysqli_stmt_close($stmt_test);


if (!$test) {
    die("Test tidak ditemukan atau bukan milik Anda.");
}


$judul_test = $test["judul_test"];
$kode_test = $test["kode_test"];


/* =========================================================
   PROSES SIMPAN SOAL
========================================================= */

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $soal_json =
        $_POST["soal_data"] ?? "";


    if ($soal_json === "") {

        $error =
            "Data soal tidak ditemukan.";

    } else {

        $soal_data =
            json_decode(
                $soal_json,
                true
            );


        if (
            !is_array($soal_data) ||
            count($soal_data) === 0
        ) {

            $error =
                "Minimal harus ada 1 soal.";

        } elseif (count($soal_data) > 100) {

            $error =
                "Maksimal 100 soal dalam satu test.";

        } else {

            mysqli_begin_transaction($conn);


            $stmt_soal = null;


            try {

                $stmt_soal = mysqli_prepare(
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
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );


                if (!$stmt_soal) {

                    throw new Exception(
                        "Gagal menyiapkan query soal."
                    );
                }


                $nomor_soal = 1;


                foreach ($soal_data as $data) {

                    if (!is_array($data)) {

                        throw new Exception(
                            "Format data soal tidak valid."
                        );
                    }


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

                    $kunci = strtoupper(
                        trim(
                            $data["kunci_jawaban"] ?? ""
                        )
                    );


                    if ($pertanyaan === "") {

                        throw new Exception(
                            "Pertanyaan soal nomor " .
                            $nomor_soal .
                            " belum diisi."
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
                            " harus diisi."
                        );
                    }


                    if (
                        strlen($pertanyaan) > 65535
                    ) {

                        throw new Exception(
                            "Pertanyaan soal nomor " .
                            $nomor_soal .
                            " terlalu panjang."
                        );
                    }


                    if (
                        strlen($pilihan_a) > 255 ||
                        strlen($pilihan_b) > 255 ||
                        strlen($pilihan_c) > 255 ||
                        strlen($pilihan_d) > 255
                    ) {

                        throw new Exception(
                            "Pilihan soal nomor " .
                            $nomor_soal .
                            " terlalu panjang."
                        );
                    }


                    if (
                        !in_array(
                            $kunci,
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


                    mysqli_stmt_bind_param(
                        $stmt_soal,
                        "iissssss",
                        $id_test,
                        $nomor_soal,
                        $pertanyaan,
                        $pilihan_a,
                        $pilihan_b,
                        $pilihan_c,
                        $pilihan_d,
                        $kunci
                    );


                    if (
                        !mysqli_stmt_execute(
                            $stmt_soal
                        )
                    ) {

                        throw new Exception(
                            "Gagal menyimpan soal nomor " .
                            $nomor_soal .
                            "."
                        );
                    }


                    $nomor_soal++;
                }


                mysqli_stmt_close($stmt_soal);
                $stmt_soal = null;


                mysqli_commit($conn);


                header(
                    "Location: test-selesai.php?id_test=" .
                    $id_test
                );

                exit;


            } catch (Throwable $e) {

                mysqli_rollback($conn);


                if ($stmt_soal) {
                    mysqli_stmt_close($stmt_soal);
                }


                $error =
                    $e->getMessage();
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

    <title>
        Buat Soal - FloTest
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="home-page">


    <?php include __DIR__ . "/components/navbar.php"; ?>


    <main class="buat-soal-content">


        <div class="buat-soal-header">

            <div>

                <h1>
                    Buat Soal
                </h1>

                <p class="buat-soal-info">

                    <?= htmlspecialchars($judul_test) ?>

                    —

                    Kode:

                    <strong>
                        <?= htmlspecialchars($kode_test) ?>
                    </strong>

                </p>

            </div>


            <span id="jumlah-langkah">
                1 soal
            </span>

        </div>


        <?php if ($error !== ""): ?>

            <div class="error-message">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="buat-soal.php?id_test=<?= $id_test ?>"
            id="form-soal"
        >

            <input
                type="hidden"
                name="soal_data"
                id="soal_data"
            >


            <div id="soal-container">


                <div
                    class="soal-card"
                    data-nomor="1"
                >

                    <div class="soal-title">
                        Soal 1
                    </div>


                    <div class="soal-content">

                        <div class="pertanyaan-area">

                            <textarea
                                class="input-soal"
                                placeholder="Tulis soal di sini..."
                                maxlength="65535"
                            ></textarea>

                        </div>


                        <div class="pilihan-area">

                            <label class="pilihan-item">

                                <span>A</span>

                                <input
                                    type="text"
                                    class="input-pilihan pilihan-a"
                                    placeholder="Pilihan A"
                                    maxlength="255"
                                >

                            </label>


                            <label class="pilihan-item">

                                <span>B</span>

                                <input
                                    type="text"
                                    class="input-pilihan pilihan-b"
                                    placeholder="Pilihan B"
                                    maxlength="255"
                                >

                            </label>


                            <label class="pilihan-item">

                                <span>C</span>

                                <input
                                    type="text"
                                    class="input-pilihan pilihan-c"
                                    placeholder="Pilihan C"
                                    maxlength="255"
                                >

                            </label>


                            <label class="pilihan-item">

                                <span>D</span>

                                <input
                                    type="text"
                                    class="input-pilihan pilihan-d"
                                    placeholder="Pilihan D"
                                    maxlength="255"
                                >

                            </label>

                        </div>


                        <div class="kunci-area">

                            <label for="kunci-1">
                                Kunci Jawaban
                            </label>

                            <select
                                id="kunci-1"
                                class="kunci-jawaban"
                            >

                                <option value="">
                                    Pilih jawaban
                                </option>

                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>

                            </select>

                        </div>

                    </div>

                </div>

            </div>


            <button
                type="button"
                class="btn-tambah-soal"
                onclick="tambahSoal()"
            >
                + Tambah Soal
            </button>


            <div class="soal-navigation">

                <button
                    type="button"
                    class="btn-secondary"
                    onclick="kembaliKeBuatTest()"
                >
                    Kembali
                </button>


                <button
                    type="submit"
                    class="btn-next"
                >
                    Selesai
                </button>

            </div>

        </form>

    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>

</div>


<script src="assets/js/script.js"></script>

</body>

</html>