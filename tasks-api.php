<?php
// Simulasikan data tugas dari database atau data statis
$tasks = [
    [
        'id' => 1,
        'name' => 'Tugas 1',
        'status' => 'pending'
    ],
    [
        'id' => 2,
        'name' => 'Tugas 2',
        'status' => 'completed'
    ]
];

// Menampilkan data tugas dalam format JSON
header('Content-Type: application/json');
echo json_encode($tasks);
?>
