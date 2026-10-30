<?php

$servername = "localhost";
$username   = "root";      // default Laragon / XAMPP
$password   = "";          // default Laragon / XAMPP kosong
$dbname     = "db_supplier";

// 1) Koneksi ke server MySQL
$conn = mysqli_connect($servername, $username, $password);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2) Buat database jika belum ada
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!mysqli_query($conn, $sql)) {
    die("Gagal membuat database: " . mysqli_error($conn));
}

// 3) Pilih database
mysqli_select_db($conn, $dbname);

// 4) Buat tabel supplier jika belum ada
$sql = "CREATE TABLE IF NOT EXISTS supplier (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    telp VARCHAR(20) NOT NULL,
    alamat VARCHAR(150) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!mysqli_query($conn, $sql)) {
    die("Gagal membuat tabel: " . mysqli_error($conn));
}

// 5) Isi data awal (hanya jika tabel masih kosong)
$cek = mysqli_query($conn, "SELECT COUNT(*) AS total FROM supplier");
$row = mysqli_fetch_assoc($cek);

if ((int)$row['total'] === 0) {
    $sql  = "INSERT INTO supplier (nama, telp, alamat) VALUES ";
    $sql .= "('PT. Maju Bersama', '08123456', 'Surabaya'), ";
    $sql .= "('PT. Senang Sekali', '081515563', 'Bangkalan'), ";
    $sql .= "('PT. Segar Segar', '0845454663', 'Surabaya')";
    mysqli_query($conn, $sql);
}
?>
