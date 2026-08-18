<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Test - FloTest</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">FloTest</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="buat-test.php" class="active">Buat Test</a></li>
            <li><a href="jawab-test.php">Jawab Test</a></li>
            <li><a href="index.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Buat Test</h2>
        <p style="color: #666; margin-top: 5px; margin-bottom: 25px;">Atur judul test baru Anda.</p>
        
        <form action="#" method="POST">
            <div class="form-group">
                <label>Judul Test</label>
                <input type="text" name="judul_test" placeholder="Masukkan judul test" required>
            </div>
            
            <button type="submit" class="btn-primary" style="width: auto; padding: 10px 25px;">Selanjutnya</button>
        </form>
    </div>
</body>
</html>