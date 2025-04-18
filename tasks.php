<?php
// Menambahkan header CORS untuk mengizinkan permintaan dari domain lain (seperti Chrome extension)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Koneksi ke database
include('db.php');

session_start();
$user_id = $_SESSION['user_id'];  // Pastikan user_id sudah disimpan di sesi saat login

// Ambil data tugas dari database (hanya yang belum selesai atau deadline hari ini)
$sql = "SELECT * FROM tasks 
        WHERE user_id = ? 
        AND (status = 'pending' OR DATE(deadline) = CURDATE()) 
        ORDER BY deadline ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Mengambil tugas dalam format JSON
$tasks = array();
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

// Mengirimkan data tugas dalam format JSON
echo json_encode($tasks);
?>
