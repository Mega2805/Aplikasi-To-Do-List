<?php
session_start();

// Inisialisasi data array jika belum ada
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [
        (object)[ 'id' => 1, 'task' => 'Merancang Desain', 'status' => 'belum' ],
        (object)[ 'id' => 2, 'task' => 'Membuat Flowchart', 'status' => 'belum' ],
        (object)[ 'id' => 3, 'task' => 'Melakukan Dokumentasi', 'status' => 'belum' ]
    ];
}

$tasks = $_SESSION['tasks'];

// Menangani perubahan status checkbox
if (isset($_GET['check'])) {
    $id = $_GET['check'];
    foreach ($tasks as $task) {
        if ($task->id == $id) {
            $task->status = $task->status === 'belum' ? 'selesai' : 'belum';
        }
    }
    $_SESSION['tasks'] = $tasks;
}

// Menangani penghapusan tugas
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $tasks = array_filter($tasks, fn($task) => $task->id != $id);
    $_SESSION['tasks'] = array_values($tasks); // reset indeks
}

// Menangani penambahan tugas baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    $newTask = (object)[
        'id' => count($tasks) + 1,
        'task' => htmlspecialchars($_POST['task']),
        'status' => 'belum'
    ];
    $tasks[] = $newTask;
    $_SESSION['tasks'] = $tasks;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>To-Do List (Bootstrap)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .body {
        background-color:rgb(247, 134, 173); 
    }

    .container {
        background-color:rgb(255, 210, 225); /* tetap putih untuk konten utama */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    .btn-tambah {
        background-color: #ff69b4;  /* Pink */
        color:rgb(255, 255, 255);
        font-weight: bold;
        border: none;
    }

    .btn-tambah:hover {
        background-color: #e758a8;
    }

    .btn-hapus {
        background-color: #ff69b4;  /* Pink */
        color: white;
        font-weight: bold;
        border: none;
    }

    .btn-hapus:hover {
        background-color: #e758a8;
    }
</style>
</head>
<body class="bg-light">

<div class="container my-5">
    <h2 class="mb-4 text-center">To-Do List</h2>

    <!-- Form tambah tugas -->
    <form method="POST" class="d-flex gap-2 mb-4">
        <input type="text" name="task" class="form-control" placeholder="Tambahkan Tugas Baru..." required>
       <button type="submit" class="btn btn-tambah">Tambah</button>
    </form>

    <h5>Daftar Tugas:</h5>
<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th style="width: 50px;"></th> <!-- Checkbox -->
            <th style="width: 50px;">No</th>
            <th>Daftar Tugas</th>
            <th style="width: 100px;">Status</th>
            <th style="width: 80px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tasks as $index => $task): ?>
            <tr>
                <!-- Checkbox -->
                <td>
                    <input
                        type="checkbox"
                        class="form-check-input"
                        onchange="window.location='?check=<?= $task->id ?>'"
                        <?= $task->status === 'selesai' ? 'checked' : '' ?>
                    >
                </td>

                <!-- Nomor -->
                <td><?= $index + 1 ?></td>

                <!-- Daftar tugas -->
                <td>
                    <span class="<?= $task->status === 'selesai' ? 'text-decoration-line-through text-muted' : 'fw-bold' ?>">
                        <?= $task->task ?>
                    </span>
                </td>

                <!-- Status -->
                <td>
                    <span class="fst-italic fw-semibold <?= $task->status === 'selesai' ? 'text-success' : 'text-danger' ?>">
                        <?= $task->status ?>
                    </span>
                </td>


                <!-- Tombol Hapus -->
                <td>
                    <a href="?hapus=<?= $task->id ?>" class="btn btn-sm btn-hapus" onclick="return confirm('Anda ingin menghapus tugas ini?')">
                        Hapus
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>