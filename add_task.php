<?php
session_start();
include('db.php');

// Cek apakah session_token ada di cookie dan apakah token valid di database
if (!isset($_SESSION['session_token']) && !isset($_COOKIE['session_token'])) {
    // Jika tidak ada session token, arahkan ke halaman login
    header("Location: login.php");
    exit();
}

// Ambil token session dari cookie atau session
$session_token = isset($_SESSION['session_token']) ? $_SESSION['session_token'] : $_COOKIE['session_token'];

// Cek apakah token session ini valid di database
$sql = "SELECT * FROM sessions WHERE session_token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_token);
$stmt->execute();
$result = $stmt->get_result();

// Jika token session valid ditemukan
if ($result->num_rows > 0) {
    $session_data = $result->fetch_assoc();
    $_SESSION['user_id'] = $session_data['user_id'];  // Set user_id ke session

    // Ambil data user berdasarkan user_id
    $user_id = $session_data['user_id'];
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
    } else {
        // Jika data user tidak ditemukan
        echo "Pengguna tidak ditemukan!";
        exit();
    }
} else {
    // Jika session_token tidak valid
    echo "Sesi tidak valid, silakan login kembali.";
    // Redirect ke halaman login jika token session tidak valid
    header("Location: login.php");
    exit();
}

// Cek apakah form ditampilkan dan valid
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_name = $_POST['task_name'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    // Validasi input
    if (!empty($task_name) && !empty($description) && !empty($deadline)) {
        // Query untuk menambahkan tugas baru ke dalam database
        $sql = "INSERT INTO tasks (user_id, task_name, description, deadline, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $task_name, $description, $deadline);

        if ($stmt->execute()) {
            // Jika berhasil, redirect ke halaman tugas saya
            header("Location: dashboard.php");
            exit();
        } else {
            // Jika gagal, tampilkan error
            echo "Gagal menambahkan tugas. Silakan coba lagi.";
        }
    } else {
        echo "Semua field harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas - <?php echo $user['username']; ?></title>
    <link rel="icon" type="image/x-icon" href="Extension/icon.png">
    <style>
        /* Desain yang digunakan sudah ada di halaman sebelumnya, tambahkan sesuai kebutuhan */
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
            padding-left: 30px;
            border-radius: 5px;
        }

        /* Konten utama */
        .content {
            margin-left: 250px;
            padding: 20px;
            background-color: #f9f9f9;
            width: calc(100% - 250px);
            height: 100%;
            overflow-y: auto;
            box-sizing: border-box;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #5f6368, #75878a);
            padding: 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
            font-size: 18px;
        }

        .navbar a:hover {
            color: #ffcc00;
            text-decoration: underline;
        }

        /* Form untuk tambah tugas */
        .add-task-form {
            margin: 20px auto;
            width: 80%;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .add-task-form input,
        .add-task-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .add-task-form button {
            background-color: #6b8e23;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }

        .add-task-form button:hover {
            background-color: #4b6b1f;
        }

        /* Dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Tombol dropdown */
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

        /* Dropdown content */
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
            /* Transisi untuk memudahkan efek */
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

        /* Dropdown aktif */
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


    <!-- Konten utama -->
    <div class="content">
        <!-- Navbar -->
        <div class="navbar">
            <span>Hai! <?php echo $user['username']; ?></span>
            <div class="dropdown">
                <button class="dropdown-button">👤 Profile</button>
                <div class="dropdown-content">
                    <a href="profile.php">👤 Profil</a>
                    <a href="logout.php">🚪 Logout</a>
                </div>
            </div>
        </div>

        <h1>Tambah Tugas Baru</h1>

        <div class="add-task-form">
            <form action="add_task.php" method="POST">
                <input type="text" name="task_name" placeholder="Nama Tugas" required>
                <textarea name="description" placeholder="Deskripsi Tugas" required></textarea>
                <input type="datetime-local" name="deadline" required>
                <button type="submit">Tambah Tugas</button>
            </form>
        </div>

    </div>

    <script>
        document.querySelector('.dropdown-button').addEventListener('click', function() {
            document.querySelector('.dropdown').classList.toggle('show');
        });
    </script>

</body>

</html>