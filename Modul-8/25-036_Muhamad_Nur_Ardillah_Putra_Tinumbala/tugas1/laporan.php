<?php
require_once 'auth.php';
wajib_login(); // owner dan kasir

// Validasi format tanggal (YYYY-MM-DD)
$ok = fn($d) => (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
$mulai   = $_GET['mulai']   ?? '';
$selesai = $_GET['selesai'] ?? '';
if (!$ok($mulai))   $mulai = '';
if (!$ok($selesai)) $selesai = '';

$labels = []; $data_total = []; $rekap = [];
$total_pendapatan = 0; $total_pelanggan = 0;
$pesan = '';

if ($mulai !== '' && $selesai !== '') {
    if ($mulai > $selesai) {
        $pesan = 'Tanggal "Dari" tidak boleh lebih besar dari tanggal "Sampai".';
    } else {
        // Query agregasi total pendapatan harian
        $st = $conn->prepare("SELECT DATE(waktu_transaksi) AS tgl, SUM(total) AS total_harian
                              FROM transaksi WHERE waktu_transaksi BETWEEN ? AND ?
                              GROUP BY DATE(waktu_transaksi) ORDER BY tgl ASC");
        $st->bind_param('ss', $mulai, $selesai);
        $st->execute();
        $res = $st->get_result();
        while ($r = $res->fetch_assoc()) {
            $labels[]     = $r['tgl'];
            $data_total[] = $r['total_harian'];
            $rekap[]      = $r;
            $total_pendapatan += $r['total_harian'];
        }

        // Query jumlah pelanggan unik
        $st = $conn->prepare("SELECT COUNT(DISTINCT pelanggan_id) AS jml FROM transaksi WHERE waktu_transaksi BETWEEN ? AND ?");
        $st->bind_param('ss', $mulai, $selesai);
        $st->execute();
        $total_pelanggan = $st->get_result()->fetch_assoc()['jml'] ?? 0;
    }
}
include 'header.php';
?>
<style>
    table.lap { border-collapse: collapse; width: 100%; margin-top: 15px; }
    table.lap th, table.lap td { border: 1px solid #000; padding: 6px; text-align: left; }
    @media print { .no-print { display: none !important; } }
</style>

<div class="no-print">
    <h1>Filter Laporan Penjualan</h1>
    <form method="GET">
        Dari: <input type="date" name="mulai" value="<?= e($mulai) ?>" required>
        Sampai: <input type="date" name="selesai" value="<?= e($selesai) ?>" required>
        <button type="submit">Tampilkan</button>
    </form>
    <button onclick="window.print()">Cetak</button>
    <a href="export_excel.php?mulai=<?= urlencode($mulai) ?>&selesai=<?= urlencode($selesai) ?>">
        <button type="button">Excel</button>
    </a>
</div>

<?php if ($pesan !== ''): ?><p style="color:red"><?= e($pesan) ?></p><?php endif; ?>

<?php if (!empty($labels)): ?>
    <h2>Grafik Penjualan</h2>
    <div style="width: 600px; height: 300px;"><canvas id="myChart"></canvas></div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{ label: 'Total Penjualan', data: <?= json_encode($data_total) ?> }]
            }
        });
    </script>

    <h2>Rekapitulasi</h2>
    <table class="lap">
        <tr><th>No</th><th>Total</th><th>Tanggal</th></tr>
        <?php $no = 1; foreach ($rekap as $row): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>Rp. <?= number_format($row['total_harian'], 0, ',', '.') ?></td>
            <td><?= e($row['tgl']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <table class="lap" border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Jumlah Pelanggan</th>
            <th>Jumlah Pendapatan</th>
        </tr>
        <tr>
            <td><?= (int)$total_pelanggan ?> Orang</td>
            <td>Rp <?= number_format($total_pendapatan) ?></td>
        </tr>
    </table>
<?php elseif ($mulai !== '' && $selesai !== '' && $pesan === ''): ?>
    <p>Tidak ada transaksi pada rentang tanggal tersebut.</p>
<?php endif; ?>
<?php include 'footer.php'; ?>
