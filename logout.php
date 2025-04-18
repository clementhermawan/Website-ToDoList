<?php
session_start();
include('db.php');

// Cek jika session_token ada
if (isset($_SESSION['session_token'])) {
    $session_token = $_SESSION['session_token'];

    // Hapus sesi pengguna dari tabel sessions
    $stmt = $conn->prepare("DELETE FROM sessions WHERE session_token = ?");
    $stmt->bind_param("s", $session_token);
    $stmt->execute();
}

// Hapus session_token dari sesi PHP
session_unset();
session_destroy();

// Arahkan ke halaman login
header("Location: login.php");
exit();
?>
