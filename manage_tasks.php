<?php
session_start();
include('db.php');

// Cek apakah user sudah login
if (!isset($_SESSION['session_token']) && !isset($_COOKIE['session_token'])) {
    header("Location: login.php");
    exit();
}

// Ambil token session
$session_token = $_SESSION['session_token'] ?? $_COOKIE['session_token'];

// Cek apakah token valid
$sql = "SELECT * FROM sessions WHERE session_token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $session_data = $result->fetch_assoc();
    $_SESSION['user_id'] = $session_data['user_id'];
    $user_id = $session_data['user_id'];
} else {
    header("Location: login.php");
    exit();
}

// Ambil data tugas user
$sql = "SELECT * FROM tasks WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasks = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tugas</title>
    <link rel="icon" type="image/x-icon" href="Extension/icon.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e1e1e6;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100%;
            background: #4f4f4f;
            /* Dark gray with softer look */
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
        }

        .sidebar h2 {
            text-align: center;
            color: #fff;
            font-size: 24px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 18px;
            transition: background-color 0.3s ease, padding-left 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #6b8e23;
            /* Olive green for hover effect */
            padding-left: 30px;
            border-radius: 5px;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            background-color: #f9f9f9;
            width: calc(100% - 250px);
            height: 100vh;
            /* Pastikan full tinggi layar */
            overflow-y: auto;
            /* Scroll jika kontennya panjang */
        }

        /* Atur tabel agar bisa di-scroll jika banyak data */
        .table-container {
            max-height: 70vh;
            /* Batas tinggi tabel */
            overflow-y: auto;
            /* Scroll jika data terlalu banyak */
            border-radius: 10px;
            background: white;
            padding: 10px;
        }

        .navbar {
            background: linear-gradient(135deg, #5f6368, #75878a);
            padding: 15px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
        }

        /* Dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-button {
            background-color: #6b8e23;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dropdown-button:hover {
            background-color: #4b6b1f;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            margin-top: 10px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0s 0.3s;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
            color: #6b8e23;
            border-radius: 8px;
        }

        .dropdown.show .dropdown-content {
            display: block;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.3s ease, visibility 0s 0s;
        }

        /* Animasi smooth */
        .content,
        .sidebar,
        .navbar {
            transition: transform 0.3s ease-in-out;
        }

        .content,
        .sidebar {
            opacity: 0;
            animation: fadeIn 1s forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #6b8e23;
            color: white;
            position: sticky;
            top: 0;
            /* Sticky header agar tetap terlihat saat scroll */
        }

        tr:nth-child(even) {
            background: #f4f4f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Tombol Aksi */
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        /* Tombol Edit */
        .btn-edit {
            background-color: #ffa500;
            color: white;
        }

        .btn-edit:hover {
            background-color: #cc8400;
            transform: scale(1.1);
        }

        /* Tombol Hapus */
        .btn-delete {
            background-color: #ff4d4d;
            color: white;
        }

        .btn-delete:hover {
            background-color: #cc0000;
            transform: scale(1.1);
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>📌 Menu</h2>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="add_task.php">📝 Tambah Tugas</a>
        <a href="task_history.php">📅 Riwayat Tugas</a>
        <a href="task_search.php">📂 Kategori Tugas</a>
        <a href="manage_tasks.php">✏️ Edit & Hapus Tugas</a>
    </div>


    <!-- Navbar -->
    <div class="content">
        <div class="navbar">
            <h2>Kelola Tugas</h2>
            <div class="dropdown">
                <button class="dropdown-button">👤 Profile</button>
                <div class="dropdown-content">
                    <a href="profile.php">👤 Profil</a>
                    <a href="logout.php">🚪 Logout</a>
                </div>
            </div>
        </div>

        <!-- Konten -->
        <div class="table-container">
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Tugas</th>
                    <th>Deskripsi</th>
                    <th>Deadline</th>
                    <th>Aksi</th>
                </tr>
                <?php $no = 1;
                while ($task = $tasks->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($task['task_name']); ?></td>
                        <td><?= htmlspecialchars($task['description']); ?></td>
                        <td><?= $task['deadline']; ?></td>
                        <td class="action-buttons">
                            <a href="edit_task.php?id=<?= $task['id']; ?>" class="btn btn-edit">✏️ Edit</a>
                            <a href="delete_task.php?id=<?= $task['id']; ?>" class="btn btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">🗑️ Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>


        <script>
            document.querySelector('.dropdown-button').addEventListener('click', function() {
                document.querySelector('.dropdown').classList.toggle('show');
            });
        </script>

</body>

</html>