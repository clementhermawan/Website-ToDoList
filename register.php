<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - To-Do List</title>
    <link rel="stylesheet" href="register.css">
    <link rel="icon" type="image/x-icon" href="Extension/icon.png">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <h1>Registrasi Akun</h1>
            <form method="POST" action="register_process.php" class="register-form">
                <input type="text" name="username" placeholder="Nama Pengguna" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Kata Sandi" required>
                <input type="password" name="confirm_password" placeholder="Konfirmasi Kata Sandi" required>
                <button type="submit">Daftar</button>
            </form>
            <div class="footer">
                <p>Sudah memiliki akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>
