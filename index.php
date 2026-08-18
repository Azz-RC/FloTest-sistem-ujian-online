<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username == "user" && $password == "161820") {

        $_SESSION["login"] = true;
        $_SESSION["username"] = $username;

        header("Location: home.php");
        exit;

    } else {

        $error = "Username atau password salah.";

    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FloTest</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <?php if (isset($error)) : ?>
        <div class="toast-error" id="toastError">
            <span>⚠</span>
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <div class="login-page">

        <div class="login-card">

            <div class="logo">
                <span>Flo</span>Test
            </div>

            <p class="login-subtitle">Sistem Belajar Online</p>

            <form action="" method="POST">

                <div class="form-group">
                    <label for="username">Username</label>

                    <div class="input-wrapper">
                        <span class="input-icon">♙</span>
                        <input 
                            type="text" 
                            id="username" 
                            name="username"
                            placeholder="Masukkan username"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            placeholder="Masukkan password"
                        >
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Login
                </button>

            </form>

            <div class="login-info">
                <p><strong>Username :</strong> user</p>
                <p><strong>Password :</strong> 161820</p>
            </div>

        </div>

    </div>

</body>
</html>