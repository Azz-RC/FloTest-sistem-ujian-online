<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - FloTest</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">
    <div class="login-card">
        <h2>FloTest</h2>
        <p>Sistem Belajar Online</p>
        
        <form action="home.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="user" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="******" required>
            </div>
            
            <button type="submit" class="btn-primary">Login</button>
        </form>

        <div class="login-hint">
            Username : user<br>
            Password : 161820
        </div>
    </div>
</body>
</html>