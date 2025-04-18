<?php
session_start();
include('db.php');

// Cek apakah user sudah login
if (!isset($_SESSION['session_token']) && !isset($_COOKIE['session_token'])) {
    header("Location: login.php");
    exit();
}

$session_token = $_SESSION['session_token'] ?? $_COOKIE['session_token'];
$sql = "SELECT * FROM sessions WHERE session_token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $session_data = $result->fetch_assoc();
    $_SESSION['user_id'] = $session_data['user_id'];
    $user_id = $session_data['user_id'];

    // Ambil data pengguna
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc(); // Menyimpan data pengguna dalam variabel $user
    } else {
        // Jika data pengguna tidak ditemukan
        echo "Pengguna tidak ditemukan!";
        exit();
    }

    // Proses pencarian tugas
    $search_query = "";
    $search_term = ''; // Variabel untuk menampung kata kunci pencarian
    if (isset($_POST['search_task'])) {
        $search_term = $_POST['search_term'];

        // Query untuk pencarian tugas berdasarkan nama tugas
        $search_query = "SELECT * FROM tasks WHERE user_id = ? AND task_name LIKE ?";
        $stmt = $conn->prepare($search_query);
        $search_term_wildcard = "%$search_term%"; // Menambahkan wildcard untuk pencarian
        $stmt->bind_param("is", $user_id, $search_term_wildcard);
        $stmt->execute();
        $tasks_result = $stmt->get_result();
    } else {
        // Ambil semua tugas jika tidak ada pencarian
        $tasks_result = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id");
    }
} else {
    // Jika session_token tidak valid
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Tugas</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="Extension/icon.png">
    <style>
        /* Your previous CSS */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
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
            width: calc(100% - 250px);
            height: 100%;
            overflow-y: auto;
        }

        .navbar {
            background: #444;
            padding: 15px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
        }

        .form-container {
            width: 50%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-container button:hover {
            background: #218838;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
        }

        th {
            background: #6b8e23;
            color: white;
        }

        tr:nth-child(even) {
            background: #f4f4f9;
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
            <span>Hai! <?php echo isset($user) ? $user['username'] : 'Pengguna'; ?></span>
            <div class="dropdown">
                <button class="dropdown-button">👤 Profile</button>
                <div class="dropdown-content">
                    <a href="profile.php">👤 Profil</a>
                    <a href="logout.php">🚪 Logout</a>
                </div>
            </div>
        </div>

        <!-- Form Pencarian -->
        <div class="form-container">
            <h2>Cari Tugas</h2>
            <form action="task_search.php" method="POST">
                <input type="text" name="search_term" placeholder="Cari nama tugas..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" name="search_task">Cari</button>
            </form>
        </div>

        <!-- Tabel Hasil Pencarian -->
        <table>
            <tr>
                <th>Nama Tugas</th>
                <th>Deskripsi</th>
                <th>Tanggal Deadline</th>
            </tr>
            <?php while ($task = $tasks_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                    <td><?php echo htmlspecialchars($task['description']); ?></td>
                    <td><?php echo htmlspecialchars($task['deadline']); ?></td>
                </tr>
            <?php } ?>
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