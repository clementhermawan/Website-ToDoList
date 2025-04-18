<?php
session_start();
include('db.php');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data pengguna dari database
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Proses update profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // Update username dan email
    $update_sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $username, $email, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = "Profil berhasil diperbarui!";
        header("Location: profile.php");
        exit();
    } else {
        $error_message = "Gagal memperbarui profil.";
    }
}

// Proses perubahan password
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Ambil password lama dari database
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    // Verifikasi password lama
    if (password_verify($current_password, $user_data['password'])) {
        if ($new_password === $confirm_password) {
            // Hash password baru
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password di database
            $update_password_sql = "UPDATE users SET password = ? WHERE id = ?";
            $update_password_stmt = $conn->prepare($update_password_sql);
            $update_password_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_password_stmt->execute()) {
                $_SESSION['success_message'] = "Password berhasil diubah!";
                header("Location: profile.php");
                exit();
            } else {
                $error_message = "Gagal mengubah password.";
            }
        } else {
            $error_message = "Konfirmasi password tidak cocok.";
        }
    } else {
        $error_message = "Password lama salah.";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="icon" type="image/x-icon" href="Extension/icon.png">
</head>
<body>

<div class="container">
    <h2>Edit Profil</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <button type="submit">Simpan Perubahan</button>
    </form>

    <h3>Ubah Password</h3>
    <form method="POST">
        <label>Password Lama:</label>
        <input type="password" name="current_password" required>

        <label>Password Baru:</label>
        <input type="password" name="new_password" required>

        <label>Konfirmasi Password Baru:</label>
        <input type="password" name="confirm_password" required>

        <button type="submit" name="change_password">Ubah Password</button>
    </form>

    <a href="dashboard.php">Kembali ke Dashboard</a>
</div>

</body>
</html>
