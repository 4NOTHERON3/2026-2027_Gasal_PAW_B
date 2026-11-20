<?php
require_once 'auth.php';
wajib_login(); // owner dan kasir boleh
include 'header.php';

$rs = $conn->query("
    SELECT t.id, t.waktu_transaksi, t.keterangan, t.total, p.nama AS pelanggan,
           GROUP_CONCAT(CONCAT(b.nama_barang, ' x', d.qty) SEPARATOR ', ') AS item
    FROM transaksi t
    LEFT JOIN pelanggan p ON p.id = t.pelanggan_id
    LEFT JOIN transaksi_detail d ON d.transaksi_id = t.id
    LEFT JOIN barang b ON b.id = d.barang_id
    GROUP BY t.id, t.waktu_transaksi, t.keterangan, t.total, p.nama
    ORDER BY t.waktu_transaksi DESC, t.id DESC
");
$grand = 0;
?>
<h3>Laporan Transaksi</h3>
<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr><th>No</th><th>Tanggal</th><th>Pelanggan</th><th>Barang</th><th>Keterangan</th><th>Total</th></tr>
    </thead>
    <tbody>
    <?php $no = 1; while ($r = $rs->fetch_assoc()): $grand += $r['total']; ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= e($r['waktu_transaksi']) ?></td>
            <td><?= e($r['pelanggan']) ?></td>
            <td><?= e($r['item']) ?></td>
            <td><?= e($r['keterangan']) ?></td>
            <td><?= rupiah($r['total']) ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="5" class="text-end">Total Keseluruhan</th><th><?= rupiah($grand) ?></th></tr>
    </tfoot>
</table>
<?php include 'footer.php'; ?>
