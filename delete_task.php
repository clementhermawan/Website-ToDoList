<?php
session_start();
include('db.php');

// Cek apakah user login
if (!isset($_SESSION['session_token']) && !isset($_COOKIE['session_token'])) {
    header("Location: login.php");
    exit();
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'] ?? null;

// Cek apakah ID tugas tersedia di URL
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Hapus tugas hanya jika milik user
    $sql = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        header("Location: manage_tasks.php");
        exit();
    } else {
        echo "Gagal menghapus tugas.";
    }
} else {
    echo "ID tugas tidak valid!";
}
?>
