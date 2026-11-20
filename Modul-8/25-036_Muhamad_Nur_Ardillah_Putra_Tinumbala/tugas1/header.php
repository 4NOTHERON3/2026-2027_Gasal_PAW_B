<?php /* Navigasi: menu menyesuaikan level user */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary d-print-none">
    <div class="container">
        <a class="navbar-brand" href="index.php">Sistem Penjualan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <?php if ((int)$_SESSION['level'] === 1): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Data Master</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="data_barang.php">Data Barang</a></li>
                        <li><a class="dropdown-item" href="data_supplier.php">Data Supplier</a></li>
                        <li><a class="dropdown-item" href="data_pelanggan.php">Data Pelanggan</a></li>
                        <li><a class="dropdown-item" href="data_user.php">Data User</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="transaksi.php">Transaksi</a></li>
                <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><?= e($_SESSION['nama']) ?></a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
<?php
if (!empty($_SESSION['flash'])) {
    echo '<div class="alert alert-' . e($_SESSION['flash']['tipe']) . '">' . e($_SESSION['flash']['pesan']) . '</div>';
    unset($_SESSION['flash']);
}
