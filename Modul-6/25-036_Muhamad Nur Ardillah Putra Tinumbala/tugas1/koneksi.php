<?php
$koneksi = mysqli_connect("localhost", "root", "", "penjualan");

if (mysqli_connect_error()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Format angka menjadi rupiah, contoh: Rp 147.000
function rupiah($angka) {
    return "Rp " . number_format((int)$angka, 0, ',', '.');
}

// Format tanggal menjadi dd-mm-yyyy, jika kosong tampilkan tanda "-"
function tanggal($tgl) {
    if (empty($tgl) || $tgl == '0000-00-00') {
        return "-";
    }
    return date('d-m-Y', strtotime($tgl));
}
