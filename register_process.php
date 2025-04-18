<?php
// Koneksi ke database
include('db.php');

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi form
    if ($password != $confirm_password) {
        echo "Kata sandi tidak cocok!";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah username atau email sudah digunakan
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username atau email sudah digunakan!";
        exit();
    }

    // Masukkan data pengguna ke database
    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $email);

    if ($stmt->execute()) {
        echo "Akun berhasil dibuat! Silakan <a href='login.php'>login</a>";
        header("Location:Login.php");
    } else {
        echo "Terjadi kesalahan. Silakan coba lagi.";
    }
}
?>
