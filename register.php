<?php

session_start();

require_once __DIR__ . "/koneksi.php";


/* =========================================================
   JIKA SUDAH LOGIN
========================================================= */

if (
    isset($_SESSION["login"]) &&
    $_SESSION["login"] === true
) {
    header("Location: home.php");
    exit;
}


$error = "";


/* =========================================================
   PROSES REGISTRASI
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim(
        $_POST["username"] ?? ""
    );

    $password =
        $_POST["password"] ?? "";

    $konfirmasi_password =
        $_POST["konfirmasi_password"] ?? "";


    /* =====================================================
       VALIDASI INPUT
    ===================================================== */

    if (
        $username === "" ||
        $password === "" ||
        $konfirmasi_password === ""
    ) {

        $error = "Semua data wajib diisi.";

    } elseif (strlen($username) < 3) {

        $error =
            "Username minimal 3 karakter.";

    } elseif (strlen($username) > 100) {

        $error =
            "Username maksimal 100 karakter.";

    } elseif (strlen($password) < 6) {

        $error =
            "Password minimal 6 karakter.";

    } elseif ($password !== $konfirmasi_password) {

        $error =
            "Konfirmasi password tidak sama.";

    } else {


        /* =================================================
           PERIKSA USERNAME
        ================================================= */

        $stmt_cek = mysqli_prepare(
            $conn,
            "SELECT id_user
             FROM `user`
             WHERE username = ?
             AND deleted_at IS NULL
             LIMIT 1"
        );


        if (!$stmt_cek) {

            $error =
                "Terjadi kesalahan pada database.";

        } else {

            mysqli_stmt_bind_param(
                $stmt_cek,
                "s",
                $username
            );

            mysqli_stmt_execute($stmt_cek);

            $result_cek =
                mysqli_stmt_get_result($stmt_cek);

            $user_sudah_ada =
                mysqli_fetch_assoc($result_cek);

            mysqli_stmt_close($stmt_cek);


            if ($user_sudah_ada) {

                $error =
                    "Username sudah digunakan.";

            } else {


                /* =========================================
                   AMANKAN PASSWORD
                ========================================= */

                $password_hash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                /* =========================================
                   SIMPAN USER
                ========================================= */

                $stmt_insert = mysqli_prepare(
                    $conn,
                    "INSERT INTO `user`
                    (
                        username,
                        password
                    )
                    VALUES (?, ?)"
                );


                if (!$stmt_insert) {

                    $error =
                        "Akun gagal disimpan.";

                } else {

                    mysqli_stmt_bind_param(
                        $stmt_insert,
                        "ss",
                        $username,
                        $password_hash
                    );


                    if (
                        mysqli_stmt_execute(
                            $stmt_insert
                        )
                    ) {

                        mysqli_stmt_close(
                            $stmt_insert
                        );

                        header(
                            "Location: index.php?register=berhasil"
                        );

                        exit;

                    } else {

                        $error =
                            "Akun gagal disimpan.";

                        mysqli_stmt_close(
                            $stmt_insert
                        );
                    }
                }
            }
        }
    }
}

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
        Daftar Akun - FloTest
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body class="login-body">

    <div class="login-card">

        <div class="login-logo">
            <span>Flo</span>Test
        </div>

        <p class="login-subtitle">
            Buat akun baru
        </p>


        <?php if ($error !== ""): ?>

            <div class="error-message">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form
            action="register.php"
            method="POST"
        >

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Buat username"
                    minlength="3"
                    maxlength="100"
                    autocomplete="username"
                    required
                    value="<?= htmlspecialchars(
                        $_POST["username"] ?? ""
                    ) ?>"
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 6 karakter"
                    minlength="6"
                    autocomplete="new-password"
                    required
                >

            </div>


            <div class="form-group">

                <label for="konfirmasi-password">
                    Konfirmasi Password
                </label>

                <input
                    type="password"
                    id="konfirmasi-password"
                    name="konfirmasi_password"
                    placeholder="Ulangi password"
                    minlength="6"
                    autocomplete="new-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-primary"
            >
                Daftar
            </button>

        </form>


        <p class="register-link">

            Sudah punya akun?

            <a href="index.php">
                Login
            </a>

        </p>

    </div>

</body>

</html>