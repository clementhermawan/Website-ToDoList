<?php
include('db.php');
session_start();

if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $sql = "SELECT task_name, deadline FROM tasks WHERE user_id = '$user_id' ORDER BY deadline ASC";
    $result = $conn->query($sql);

    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    echo json_encode($tasks);
}
?>
