<?php
session_start();
include('db.php');

// Cek apakah session valid
if (!isset($_SESSION['session_token']) && !isset($_COOKIE['session_token'])) {
    header("Location: login.php");
    exit();
}

// Ambil token session
$session_token = isset($_SESSION['session_token']) ? $_SESSION['session_token'] : $_COOKIE['session_token'];

// Validasi token di database
$sql = "SELECT * FROM sessions WHERE session_token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $session_data = $result->fetch_assoc();
    $_SESSION['user_id'] = $session_data['user_id'];

    $user_id = $session_data['user_id'];
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();

        // Ambil daftar tugas
        $task_sql = "SELECT * FROM tasks 
        WHERE user_id = ? 
        AND (status = 'pending' OR DATE(deadline) = CURDATE()) 
        ORDER BY deadline ASC";

        $task_stmt = $conn->prepare($task_sql);
        $task_stmt->bind_param("i", $user_id);
        $task_stmt->execute();
        $task_result = $task_stmt->get_result();


        echo "<script>console.log('Memulai pengecekan tugas...');</script>";

        $has_pending_task = false;
        $now = new DateTime();

        $task_stmt->execute();
        $task_result = $task_stmt->get_result();

        while ($row = $task_result->fetch_assoc()) {
            $deadline = DateTime::createFromFormat('Y-m-d H:i:s', $row['deadline']) ?: new DateTime($row['deadline']);

            echo "<script>console.log('Cek tugas: " . $row['task_name'] . " | Status: " . $row['status'] . " | Deadline: " . $row['deadline'] . "');</script>";

            if (strtolower(trim($row['status'])) == 'pending') {
                $has_pending_task = true;
                echo "<script>console.log('Tugas pending ditemukan: " . $row['task_name'] . "');</script>";
                break;
            }
        }


        echo "<script>console.log('Hasil akhir: Ada tugas pending? " . ($has_pending_task ? "true" : "false") . "');</script>";

        $show_alert = $has_pending_task ? "display: block;" : "display: none;";




        // Jika tombol "Selesaikan" ditekan
        if (isset($_POST['complete_task_id'])) {
            $task_id = $_POST['complete_task_id'];
            $update_sql = "UPDATE tasks SET status = 'completed' WHERE id = ? AND user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $task_id, $user_id);
            $update_stmt->execute();

            // Refresh halaman untuk memperbarui status
            header("Location: dashboard.php");
            exit();
        }
    } else {
        echo "Pengguna tidak ditemukan!";
        exit();
    }
} else {
    echo "Sesi tidak valid, silakan login kembali.";
    header("Location: login.php");
    exit();
}

echo "<script>console.log('Ada tugas pending:', " . json_encode($has_pending_task) . ");</script>";

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hai! <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="icon" type="image/x-icon" href="Extension/icon.png">
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

        <h1>Daftar Tugas Anda</h1>

        <!-- Alert -->
        <div id="task-alert" class="alert" style="display: <?php echo $show_alert; ?>;">
            <span>Tugas Anda hampir melewati deadline! Segera selesaikan!</span>
        </div>

        <table>
            <tr>
                <th>Tugas</th>
                <th>Deskripsi</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php
            // Ambil ulang hasil query
            $task_stmt->execute();
            $task_result = $task_stmt->get_result();

            if ($task_result->num_rows > 0) {
                while ($row = $task_result->fetch_assoc()) {
                    $deadline = DateTime::createFromFormat('Y-m-d H:i:s', $row['deadline']);

                    if (!$deadline) {
                        $deadline = new DateTime($row['deadline']); // Fallback jika format berbeda
                    }

                    $now = new DateTime();
                    $row_class = "";

                    // Pemeriksaan untuk status tugas yang lewat deadline
                    if ($row['status'] == 'pending' && $deadline < $now) {
                        $row_class = "style='background-color: #ff4d4d; color: white;'"; // Merah untuk tugas yang lewat deadline
                    }
                    // Pemeriksaan untuk status tugas yang deadline-nya hari ini
                    elseif ($row['status'] == 'pending' && $deadline->format('Y-m-d') == $now->format('Y-m-d')) {
                        $row_class = "style='background-color: #ffd700;'"; // Kuning untuk tugas yang deadline hari ini
                    }

                    echo "<tr $row_class>";
                    echo "<td>" . htmlspecialchars($row['task_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['deadline']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";

                    if ($row['status'] == 'pending') {
                        echo "<td><form method='POST' action='dashboard.php'>
                                <button type='submit' name='complete_task_id' value='" . $row['id'] . "'>Selesaikan</button>
                              </form></td>";
                    } else {
                        echo "<td><span>Selesai</span></td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align: center;'>Tidak ada tugas untuk ditampilkan.</td></tr>";
            }
            ?>
        </table>
    </div>

    <script>
        document.querySelector('.dropdown-button').addEventListener('click', function() {
                document.querySelector('.dropdown').classList.toggle('show');
            });

    </script>

    <?php if ($has_pending_task): ?>
        <!-- JavaScript -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('task-alert').style.display = "block";
                console.log("Alert tugas muncul!");
            });


            
            window.addEventListener('click', function(event) {
                if (!document.querySelector('.dropdown').contains(event.target)) {
                    document.querySelector('.dropdown').classList.remove('show');
                }
            });
        </script>
    <?php endif; ?>

</body>

</html>