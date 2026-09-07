<?php

session_start();

require_once __DIR__ . "/koneksi.php";

$error = "";
$success = "";


/* =========================================================
   PESAN SETELAH REGISTRASI
========================================================= */

if (($_GET["register"] ?? "") === "berhasil") {
    $success = "Akun berhasil dibuat. Silakan login.";
}


/* =========================================================
   LOGOUT
========================================================= */

if (isset($_GET["logout"])) {

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    header("Location: index.php");
    exit;
}


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


/* =========================================================
   PROSES LOGIN
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim(
        $_POST["username"] ?? ""
    );

    $password =
        $_POST["password"] ?? "";


    if ($username === "" || $password === "") {

        $error =
            "Username dan password wajib diisi.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT
                id_user,
                username,
                password
             FROM `user`
             WHERE username = ?
             AND deleted_at IS NULL
             LIMIT 1"
        );


        if (!$stmt) {

            $error =
                "Terjadi kesalahan pada database.";

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "s",
                $username
            );

            mysqli_stmt_execute($stmt);

            $result =
                mysqli_stmt_get_result($stmt);

            $user =
                mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);


            $password_valid = false;
            $password_is_plaintext = false;


            if ($user) {

                /*
                 * Periksa password yang sudah menggunakan hash.
                 */
                if (
                    password_verify(
                        $password,
                        $user["password"]
                    )
                ) {

                    $password_valid = true;

                } elseif (
                    /*
                     * Kompatibilitas untuk password lama
                     * yang masih berupa teks biasa.
                     */
                    hash_equals(
                        (string) $user["password"],
                        (string) $password
                    )
                ) {

                    $password_valid = true;
                    $password_is_plaintext = true;
                }
            }


            if ($password_valid) {

                /*
                 * Ubah password lama menjadi password hash.
                 */
                if ($password_is_plaintext) {

                    $password_hash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $stmt_update = mysqli_prepare(
                        $conn,
                        "UPDATE `user`
                         SET password = ?
                         WHERE id_user = ?"
                    );


                    if ($stmt_update) {

                        $user_id =
                            (int) $user["id_user"];

                        mysqli_stmt_bind_param(
                            $stmt_update,
                            "si",
                            $password_hash,
                            $user_id
                        );

                        mysqli_stmt_execute(
                            $stmt_update
                        );

                        mysqli_stmt_close(
                            $stmt_update
                        );
                    }
                }


                session_regenerate_id(true);

                $_SESSION["login"] = true;

                $_SESSION["id_user"] =
                    (int) $user["id_user"];

                $_SESSION["username"] =
                    $user["username"];


                header("Location: home.php");
                exit;

            } else {

                $error =
                    "Username atau password salah.";
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

    <title>Login - FloTest</title>

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
            Sistem Belajar Online
        </p>


        <!-- PESAN REGISTRASI BERHASIL -->

        <?php if ($success !== ""): ?>

            <div class="success-message">

                <?= htmlspecialchars($success) ?>

            </div>

        <?php endif; ?>


        <!-- PESAN LOGIN GAGAL -->

        <?php if ($error !== ""): ?>

            <div class="error-message">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- FORM LOGIN -->

        <form
            action="index.php"
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
                    placeholder="Masukkan username"
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
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-primary"
            >
                Login
            </button>

        </form>


        <!-- LINK REGISTRASI -->

        <p class="register-link">

            Belum punya akun?

            <a href="register.php">
                Daftar
            </a>

        </p>

    </div>

</body>

</html>