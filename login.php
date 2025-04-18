<?php
session_start();
include('db.php');

// Cek apakah sudah ada session token yang tersimpan di cookie
if (isset($_COOKIE['session_token'])) {
    // Ambil token session dari cookie
    $session_token = $_COOKIE['session_token'];

    // Cek apakah token session ini valid di database
    $sql = "SELECT * FROM sessions WHERE session_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_token);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika token session valid ditemukan
    if ($result->num_rows > 0) {
        $session_data = $result->fetch_assoc();
        $_SESSION['user_id'] = $session_data['user_id'];
        $_SESSION['session_token'] = $session_data['session_token'];

        // Ambil data user berdasarkan user_id
        $user_id = $session_data['user_id'];
        $user_sql = "SELECT * FROM users WHERE id = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            // Redirect langsung ke dashboard jika session token valid
            header("Location: dashboard.php");
            exit();
        } else {
            // Jika data user tidak ditemukan
            echo "Pengguna tidak ditemukan!";
        }
    } else {
        // Jika token session tidak valid atau tidak ditemukan
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mendapatkan data pengguna berdasarkan username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);  // Menggunakan prepared statement untuk mencegah SQL injection
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah pengguna ditemukan
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Hapus sesi lama yang ada jika ada sesi aktif untuk user ini
            $delete_sql = "DELETE FROM sessions WHERE user_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $user['id']);
            $delete_stmt->execute();

            // Buat token sesi yang unik
            $session_token = bin2hex(random_bytes(32)); // Membuat token acak 64 karakter

            // Simpan session_token ke dalam tabel sessions
            $insert_stmt = $conn->prepare("INSERT INTO sessions (user_id, session_token) VALUES (?, ?)");
            $insert_stmt->bind_param("is", $user['id'], $session_token);
            $insert_stmt->execute();

            // Simpan session token ke dalam sesi PHP (untuk referensi di halaman berikutnya)
            $_SESSION['session_token'] = $session_token;
            $_SESSION['user_id'] = $user['id'];  // Simpan user_id ke dalam session untuk validasi

            // Simpan token session dalam cookie
            setcookie('session_token', $session_token, time() + (86400 * 30), "/");  // Cookie bertahan selama 30 hari

            // Redirect ke halaman dashboard setelah login sukses
            header("Location: dashboard.php");
            exit();
        } else {
            // Jika password salah
            $error_message = "Username atau password salah!";
        }
    } else {
        // Jika username tidak ditemukan
        $error_message = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - To-Do List</title>
    <link rel="stylesheet" href="login.css">
    <link rel="icon" type="image/x-icon" href="Extension/icon.png">
    <style>
        /* Pastikan body tidak lebih besar dari viewport */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Pastikan loading screen tidak menyebabkan halaman lebih besar */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: linear-gradient(135deg, #00C8FF, #0072ff);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-in-out;
        }

        /* Logo Loading */
        .loading-logo {
            width: 120px;
            animation: pulseLogo 1.5s infinite ease-in-out;
        }

        /* Animasi Pulse untuk logo */
        @keyframes pulseLogo {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.1);
                opacity: 1;
            }

            100% {
                transform: scale(1);
                opacity: 0.7;
            }
        }

        /* Saat loading selesai */
        .loading-screen.fade-out {
            opacity: 0;
            pointer-events: none;
        }

        /* Form login tersembunyi saat loading */
        .login-container {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 1s ease-in-out, transform 0.8s ease-in-out;
            position: absolute;
            width: 100%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Munculkan form setelah loading selesai */
        .login-container.show {
            opacity: 1;
            transform: translate(-50%, -50%);
        }

        /* Kembalikan scroll setelah loading selesai */
        body.loaded {
            overflow: auto;
        }
    </style>
</head>

<body class="laoding">
    <!-- Loading Screen -->
    <div class="loading-screen">
        <img src="Extension/icon.png" alt="Logo" class="loading-logo">
    </div>


    <!-- Login Container -->
    <div class="login-container">
        <div class="login-box">
            <h1>Login ke Akun Anda</h1>
            <form method="POST" class="login-form" autocomplete="on">
                <input type="text" name="username" placeholder="Nama Pengguna" required autocomplete="username">
                <input type="password" name="password" placeholder="Kata Sandi" required autocomplete="current-password">
                <button type="submit">Masuk</button>
            </form>

            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <div class="footer">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                document.querySelector(".loading-screen").classList.add("fade-out");
            }, 2000); // 2 detik loading screen

            setTimeout(() => {
                document.querySelector(".loading-screen").remove(); // Hapus loading screen
                document.body.classList.remove("loading"); // Hapus class loading
                document.body.classList.add("loaded"); // Tambah class loaded
                document.querySelector(".login-container").classList.add("show");
            }, 2500); // Hapus loading screen setelah animasi selesai
        };
    </script>





</body>

</html>