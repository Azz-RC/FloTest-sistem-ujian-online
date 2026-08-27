<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}

include "koneksi.php";


// =====================================
// AMBIL KODE TEST
// =====================================

$kode_test = $_POST["kode_test"] ?? "";

if ($kode_test == "") {
    header("Location: jawab-test.php");
    exit;
}


// =====================================
// CARI TEST BERDASARKAN KODE
// =====================================

$kode_test = mysqli_real_escape_string($conn, $kode_test);

$queryTest = mysqli_query(
    $conn,
    "SELECT id_test, judul_test, kode_test
     FROM test
     WHERE kode_test = '$kode_test'
     LIMIT 1"
);

$dataTest = mysqli_fetch_assoc($queryTest);


if (!$dataTest) {
    die("Kode test tidak ditemukan.");
}


$id_test = $dataTest["id_test"];
$judul_test = $dataTest["judul_test"];


// =====================================
// AMBIL SOAL BERDASARKAN ID TEST
// =====================================

$querySoal = mysqli_query(
    $conn,
    "SELECT
        id_soal,
        nomor_soal,
        pertanyaan,
        pilihan_a,
        pilihan_b,
        pilihan_c,
        pilihan_d
     FROM soal
     WHERE id_test = '$id_test'
     ORDER BY nomor_soal ASC"
);


$soal = [];

while ($data = mysqli_fetch_assoc($querySoal)) {
    $soal[] = $data;
}


if (empty($soal)) {
    die("Soal untuk test ini belum tersedia.");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kerjakan Test - FloTest</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <div class="home-page">

        <!-- =========================
             NAVBAR
        ========================== -->

        <header class="home-navbar">

            <div class="logo">
                <span>Flo</span>Test
            </div>

            <nav class="navbar-menu">

                <a href="home.php" class="nav-link">
                    Home
                </a>

                <a href="buat-test.php" class="nav-link">
                    Buat Test
                </a>

                <a href="jawab-test.php" class="nav-link active">
                    Jawab Test
                </a>

                <a href="index.php" class="nav-link">
                    Logout
                </a>

            </nav>

            <button
                type="button"
                class="menu-toggle"
                onclick="toggleMenu()"
                aria-label="Buka menu"
            >
                ☰
            </button>

        </header>


        <!-- =========================
             KERJAKAN TEST
        ========================== -->

        <main class="kerjakan-content">

            <!-- FORM UNTUK MENGIRIM JAWABAN KE HASIL-TEST.PHP -->
            <form action="hasil-test.php" method="POST">

                <input type="hidden" name="id_test" value="<?php echo $id_test; ?>">

                <div class="kerjakan-header">

                    <div>

                        <h1>
                            <?php echo htmlspecialchars($judul_test); ?>
                        </h1>

                        <p>
                            Kode Test:
                            <strong>
                                <?php echo htmlspecialchars($dataTest["kode_test"]); ?>
                            </strong>
                        </p>

                    </div>

                    <span class="nomor-soal">
                        <?php echo count($soal); ?> Soal
                    </span>

                </div>


                <!-- =========================
                     DAFTAR SOAL
                ========================== -->

                <?php foreach ($soal as $data) : ?>

                    <div class="pertanyaan-card">

                        <h2>
                            <?php
                            echo $data["nomor_soal"] . ". " .
                                 htmlspecialchars($data["pertanyaan"]);
                            ?>
                        </h2>


                        <div class="jawaban-list">

                    <label class="jawaban-option">

                        <input
                            type="radio"
                            name="jawaban[<?php echo $data['id_soal']; ?>]"
                            value="A"
                            required
                        >

                        <span>
                            A.
                            <?php echo htmlspecialchars($data["pilihan_a"]); ?>
                        </span>

                    </label>


                    <label class="jawaban-option">

                        <input
                            type="radio"
                            name="jawaban[<?php echo $data['id_soal']; ?>]"
                            value="B"
                        >

                        <span>
                            B.
                            <?php echo htmlspecialchars($data["pilihan_b"]); ?>
                        </span>

                    </label>


                    <label class="jawaban-option">

                        <input
                            type="radio"
                            name="jawaban[<?php echo $data['id_soal']; ?>]"
                            value="C"
                        >

                        <span>
                            C.
                            <?php echo htmlspecialchars($data["pilihan_c"]); ?>
                        </span>

                    </label>


                    <label class="jawaban-option">

                        <input
                            type="radio"
                            name="jawaban[<?php echo $data['id_soal']; ?>]"
                            value="D"
                        >

                        <span>
                            D.
                            <?php echo htmlspecialchars($data["pilihan_d"]); ?>
                        </span>

                    </label>

                </div>
                
                <?php endforeach; ?>


                <!-- =========================
                     NAVIGASI
                ========================== -->

                <div class="kerjakan-navigation">

                    <a
                        href="jawab-test.php"
                        class="btn-secondary"
                    >
                        Kembali
                    </a>

                    <button
                        type="submit"
                        class="btn-next"
                    >
                        Selesai
                    </button>

                </div>

            </form>

        </main>

    </div>


    <script src="assets/js/script.js"></script>

</body>

</html>