<?php
// Koneksi database (sesuaikan jika user/password MySQL berbeda)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli('localhost', 'root', '', 'penjualan');
$conn->set_charset('utf8mb4');
