<?php
require "koneksi.php";

// Hanya menerima permintaan POST dari tombol Hapus di index.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    $stmt = $conn->prepare("DELETE FROM supplier WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    mysqli_close($conn);
    header("Location: index.php?status=hapus");
    exit;
}

mysqli_close($conn);
header("Location: index.php");
exit;
