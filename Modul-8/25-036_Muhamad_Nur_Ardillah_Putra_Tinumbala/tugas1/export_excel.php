<?php
require_once 'auth.php';
wajib_login(); // owner dan kasir

$ok = fn($d) => (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
$mulai   = $_GET['mulai']   ?? '';
$selesai = $_GET['selesai'] ?? '';
if (!$ok($mulai) || !$ok($selesai) || $mulai > $selesai) {
    set_flash('danger', 'Pilih rentang tanggal yang valid dulu, lalu klik Tampilkan sebelum export Excel.');
    header('Location: laporan.php');
    exit;
}

$st = $conn->prepare("SELECT DATE(waktu_transaksi) AS tgl, SUM(total) AS total_harian
                      FROM transaksi WHERE waktu_transaksi BETWEEN ? AND ?
                      GROUP BY DATE(waktu_transaksi) ORDER BY tgl ASC");
$st->bind_param('ss', $mulai, $selesai);
$st->execute();
$rekap = $st->get_result()->fetch_all(MYSQLI_ASSOC);

$st = $conn->prepare("SELECT COUNT(DISTINCT pelanggan_id) AS jml FROM transaksi WHERE waktu_transaksi BETWEEN ? AND ?");
$st->bind_param('ss', $mulai, $selesai);
$st->execute();
$jml_pelanggan = (int)$st->get_result()->fetch_assoc()['jml'];
$total = array_sum(array_column($rekap, 'total_harian'));

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="laporan_' . $mulai . '_sd_' . $selesai . '.xls"');
?>
<meta charset="UTF-8">
<table border="1">
    <tr><th colspan="3">Laporan Penjualan (<?= e($mulai) ?> s/d <?= e($selesai) ?>)</th></tr>
    <tr><th>No</th><th>Total</th><th>Tanggal</th></tr>
    <?php $no = 1; foreach ($rekap as $r): ?>
    <tr><td><?= $no++ ?></td><td><?= (int)$r["total_harian"] ?></td><td><?= e($r["tgl"]) ?></td></tr>
    <?php endforeach; ?>
    <tr><th colspan="2">Jumlah Pendapatan</th><th><?= (int)$total ?></th></tr>
    <tr><th colspan="2">Jumlah Pelanggan</th><th><?= $jml_pelanggan ?></th></tr>
</table>
