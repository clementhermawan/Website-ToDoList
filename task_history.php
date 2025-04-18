<?php
session_start();
include('db.php');

// Cek apakah session_token ada di cookie dan apakah token valid di database
if (!isset($_SESSION['session_token']) && !isset($_COOKIE['session_token'])) {
    header("Location: login.php");
    exit();
}

$session_token = isset($_SESSION['session_token']) ? $_SESSION['session_token'] : $_COOKIE['session_token'];

// Cek validitas token
$sql = "SELECT * FROM sessions WHERE session_token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $session_data = $result->fetch_assoc();
    $_SESSION['user_id'] = $session_data['user_id'];
    $user_id = $session_data['user_id'];

    // Ambil data user
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
    } else {
        echo "Pengguna tidak ditemukan!";
        exit();
    }
} else {
    echo "Sesi tidak valid, silakan login kembali.";
    header("Location: login.php");
    exit();
}

// Ambil tugas yang sudah selesai
$task_sql = "SELECT * FROM tasks WHERE user_id = ? AND status = 'completed' ORDER BY deadline DESC";
$task_stmt = $conn->prepare($task_sql);
$task_stmt->bind_param("i", $user_id);
$task_stmt->execute();
$task_result = $task_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Tugas - <?php echo $user['username']; ?></title>
    <link rel="stylesheet" href="styles.css">
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
            height: 100%;
            overflow-y: auto;
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
        }

        th {
            background: #6b8e23;
            color: white;
        }

        tr:nth-child(even) {
            background: #f4f4f9;
        }

        tr:hover {
            background-color: #f1f1f1;
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
    <div class="sidebar">
        <h2>📌 Menu</h2>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="add_task.php">📝 Tambah Tugas</a>
        <a href="task_history.php">📅 Riwayat Tugas</a>
        <a href="task_search.php">📂 Kategori Tugas</a>
        <a href="manage_tasks.php">✏️ Edit & Hapus Tugas</a>
    </div>

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
        <h1>Riwayat Tugas</h1>
        <table>
            <tr>
                <th>Tugas</th>
                <th>Deskripsi</th>
                <th>Deadline</th>
                <th>Status</th>
            </tr>
            <?php if ($task_result->num_rows > 0) {
                while ($row = $task_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['task_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['deadline']) . "</td>";
                    echo "<td><span style='color: green; font-weight: bold;'>" . htmlspecialchars($row['status']) . "</span></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align: center;'>Belum ada tugas yang selesai.</td></tr>";
            } ?>
        </table>
    </div>
    <script>
        // Menambahkan event listener untuk klik pada tombol dropdown
        document.querySelector('.dropdown-button').addEventListener('click', function(event) {
            event.stopPropagation(); // Mencegah klik pada tombol membuat dropdown hilang
            const dropdown = this.parentNode;

            // Toggle class 'show' untuk menampilkan atau menyembunyikan dropdown
            dropdown.classList.toggle('show');
        });

        // Menutup dropdown jika pengguna mengklik di luar dropdown
        window.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.dropdown');

            // Jika klik berada di luar dropdown, sembunyikan dropdown
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
</body>

</html>