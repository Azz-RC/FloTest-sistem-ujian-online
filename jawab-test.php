<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jawab Test - FloTest</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">FloTest</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="buat-test.php">Buat Test</a></li>
            <li><a href="jawab-test.php" class="active">Jawab Test</a></li>
            <li><a href="index.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Jawab Test</h2>
        
        <form action="#" method="POST" style="display: flex; gap: 10px; align-items: flex-end; margin-top: 20px; margin-bottom: 30px;">
            <div class="form-group" style="flex: 1; margin-bottom: 0;">
                <label>Input Kode Test</label>
                <input type="text" name="kode_test" placeholder="Masukkan kode test" required>
            </div>
            <button type="submit" class="btn-primary" style="width: auto; margin-top: 0; padding: 10px 20px;">Masuk</button>
        </form>
        <p style="font-size: 12px; color: #888; margin-bottom: 30px;">* Masukkan kode test yang diberikan oleh pembuat test untuk masuk ke room.</p>

        <h3>Riwayat Room</h3>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin-top: 10px; color: #777;">
            Belum ada room yang diikuti.
        </div>
    </div>
</body>
</html>