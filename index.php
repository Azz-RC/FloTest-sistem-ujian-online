<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (!empty($username) && !empty($password)) {
        $_SESSION["login"] = true;
        $_SESSION["id_user"] = 1;
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
    <title>Login - FloTest</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">

    <?php if ($error): ?>
        <div class="toast-error">
            <span>⚠️</span>
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <div class="login-card">
        <div class="logo">Flo<span>Test</span></div>
        <div class="login-subtitle">Sistem Belajar Online</div>

        <form action="" method="POST">
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="text" name="username" placeholder="Masukkan username" required autocomplete="off">
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        
    </div>

</body>
</html>