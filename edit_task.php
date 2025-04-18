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

// Ambil ID tugas dari parameter URL
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Ambil data tugas berdasarkan ID
    $sql = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    if (!$task) {
        echo "Tugas tidak ditemukan atau bukan milik Anda!";
        exit();
    }
}

// Proses update tugas
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = $_POST['task_name'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    $sql = "UPDATE tasks SET task_name = ?, description = ?, deadline = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $task_name, $description, $deadline, $task_id, $user_id);

    if ($stmt->execute()) {
        header("Location: manage_tasks.php");
        exit();
    } else {
        echo "Gagal memperbarui tugas.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas</title>
    <link rel="icon" type="image/x-icon" href="Extension/icon.png">
    <style>
        /* Reset dasar */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e1e1e6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            /* Pastikan halaman full tinggi */
        }

        /* Container form */
        .edit-container {
            background: white;
            padding: 25px;
            width: 400px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            text-align: center;
        }

        /* Judul */
        .edit-container h2 {
            margin-bottom: 20px;
            font-size: 22px;
            color: #333;
        }

        /* Form input */
        .edit-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            text-align: left;
            display: block;
            color: #555;
        }

        /* Input dan textarea */
        input[type="text"],
        textarea,
        input[type="datetime-local"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        input[type="text"]:focus,
        textarea:focus,
        input[type="datetime-local"]:focus {
            border-color: #6b8e23;
            box-shadow: 0px 0px 5px rgba(107, 142, 35, 0.5);
        }

        /* Tombol */
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        /* Tombol simpan */
        .btn-save {
            background-color: #6b8e23;
            color: white;
        }

        .btn-save:hover {
            background-color: #4b6b1f;
        }

        /* Tombol batal */
        .btn-cancel {
            background-color: #ccc;
            color: black;
            text-decoration: none;
            display: inline-block;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn-cancel:hover {
            background-color: #999;
        }
    </style>
</head>

<body>

    <div class="edit-container">
        <h2>Edit Tugas</h2>
        <form action="edit_task.php?id=<?= $task['id']; ?>" method="POST">
            <input type="hidden" name="id" value="<?= $task['id']; ?>">

            <label for="task_name">Nama Tugas</label>
            <input type="text" id="task_name" name="task_name" value="<?= htmlspecialchars($task['task_name']); ?>" required>

            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($task['description']); ?></textarea>

            <label for="deadline">Deadline</label>
            <input type="datetime-local" id="deadline" name="deadline" value="<?= date('Y-m-d\TH:i', strtotime($task['deadline'])); ?>" required>

            <button type="submit" class="btn btn-save">Simpan Perubahan</button>
            <a href="manage_tasks.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>

</body>

</html>