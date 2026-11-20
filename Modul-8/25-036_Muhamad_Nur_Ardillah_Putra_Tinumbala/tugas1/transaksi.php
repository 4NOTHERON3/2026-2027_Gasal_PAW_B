<?php
require_once 'auth.php';
wajib_login(); // owner dan kasir boleh

// ===== PROSES SIMPAN TRANSAKSI =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pel = $_POST['pelanggan_id'] ?? '';
    $tgl = $_POST['waktu_transaksi'] ?? '';
    $ket = trim($_POST['keterangan'] ?? '');

    // kumpulkan item (barang yang sama digabung qty-nya)
    $items = [];
    foreach (($_POST['barang_id'] ?? []) as $i => $bid) {
        $bid = (int)$bid;
        $qty = (int)($_POST['qty'][$i] ?? 0);
        if ($bid > 0 && $qty > 0) {
            $items[$bid] = ($items[$bid] ?? 0) + $qty;
        }
    }

    if ($pel === '' || $tgl === '' || count($items) === 0) {
        set_flash('danger', 'Pelanggan, tanggal, dan minimal satu barang wajib diisi.');
        header('Location: transaksi.php?aksi=tambah');
        exit;
    }

    $conn->begin_transaction();
    try {
        $st = $conn->prepare("INSERT INTO transaksi (waktu_transaksi, keterangan, total, pelanggan_id) VALUES (?, ?, 0, ?)");
        $st->bind_param('sss', $tgl, $ket, $pel);
        $st->execute();
        $tid = $conn->insert_id;

        $get = $conn->prepare("SELECT harga FROM barang WHERE id = ?");
        $ins = $conn->prepare("INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty) VALUES (?, ?, ?, ?)");
        $total = 0;
        foreach ($items as $bid => $qty) {
            $get->bind_param('i', $bid);
            $get->execute();
            $b = $get->get_result()->fetch_assoc();
            if (!$b) {
                throw new Exception('Barang tidak ditemukan.');
            }
            $harga = (int)$b['harga'];
            $ins->bind_param('iiii', $tid, $bid, $harga, $qty);
            $ins->execute();
            $total += $harga * $qty;
        }

        $up = $conn->prepare("UPDATE transaksi SET total = ? WHERE id = ?");
        $up->bind_param('ii', $total, $tid);
        $up->execute();

        $conn->commit();
        set_flash('success', 'Transaksi berhasil disimpan.');
    } catch (Throwable $ex) {
        $conn->rollback();
        set_flash('danger', 'Transaksi gagal disimpan: ' . $ex->getMessage());
    }
    header('Location: transaksi.php');
    exit;
}

// ===== TAMPILAN =====
$aksi = $_GET['aksi'] ?? 'list';
$t = null;
if ($aksi === 'detail') {
    $st = $conn->prepare("SELECT t.*, p.nama AS pelanggan FROM transaksi t LEFT JOIN pelanggan p ON p.id = t.pelanggan_id WHERE t.id = ?");
    $idq = (int)($_GET['id'] ?? 0);
    $st->bind_param('i', $idq);
    $st->execute();
    $t = $st->get_result()->fetch_assoc();
    if (!$t) {
        set_flash('danger', 'Transaksi tidak ditemukan.');
        header('Location: transaksi.php');
        exit;
    }
}

include 'header.php';

if ($aksi === 'tambah'):
    $pelanggan = $conn->query("SELECT id, nama FROM pelanggan ORDER BY nama");
    $barang = $conn->query("SELECT id, kode_barang, nama_barang, harga FROM barang ORDER BY nama_barang");
    $opsiBarang = '<option value="0">-- Pilih Barang --</option>';
    while ($b = $barang->fetch_assoc()) {
        $opsiBarang .= '<option value="' . (int)$b['id'] . '">' . e($b['kode_barang'] . ' - ' . $b['nama_barang'] . ' (' . rupiah($b['harga']) . ')') . '</option>';
    }
?>
<h3>Tambah Transaksi</h3>
<form method="post" action="transaksi.php">
    <div class="mb-3">
        <label class="form-label">Tanggal</label>
        <input type="date" name="waktu_transaksi" class="form-control" value="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Pelanggan</label>
        <select name="pelanggan_id" class="form-select" required>
            <option value="">-- Pilih Pelanggan --</option>
            <?php while ($p = $pelanggan->fetch_assoc()): ?>
                <option value="<?= e($p['id']) ?>"><?= e($p['id'] . ' - ' . $p['nama']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="2"></textarea>
    </div>

    <table class="table table-bordered" id="tabelItem">
        <thead class="table-light">
            <tr><th>Barang</th><th style="width:150px">Qty</th><th style="width:90px">Aksi</th></tr>
        </thead>
        <tbody>
            <tr class="baris">
                <td><select name="barang_id[]" class="form-select"><?= $opsiBarang ?></select></td>
                <td><input type="number" name="qty[]" class="form-control" min="1" value="1"></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)">Hapus</button></td>
            </tr>
        </tbody>
    </table>
    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="tambahBaris()">+ Tambah Barang</button>
    <br>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="transaksi.php" class="btn btn-secondary">Batal</a>
</form>
<script>
function tambahBaris() {
    var tb = document.querySelector('#tabelItem tbody');
    var b = tb.querySelector('.baris').cloneNode(true);
    b.querySelector('select').selectedIndex = 0;
    b.querySelector('input').value = 1;
    tb.appendChild(b);
}
function hapusBaris(btn) {
    var tb = document.querySelector('#tabelItem tbody');
    if (tb.querySelectorAll('.baris').length > 1) {
        btn.closest('tr').remove();
    }
}
</script>

<?php elseif ($aksi === 'detail'):
    $st = $conn->prepare("SELECT b.kode_barang, b.nama_barang, d.harga, d.qty FROM transaksi_detail d JOIN barang b ON b.id = d.barang_id WHERE d.transaksi_id = ?");
    $st->bind_param('i', $t['id']);
    $st->execute();
    $rs = $st->get_result();
?>
<h3>Detail Transaksi #<?= (int)$t['id'] ?></h3>
<table class="table table-borderless w-auto">
    <tr><td>Tanggal</td><td>: <?= e($t['waktu_transaksi']) ?></td></tr>
    <tr><td>Pelanggan</td><td>: <?= e($t['pelanggan']) ?></td></tr>
    <tr><td>Keterangan</td><td>: <?= e($t['keterangan']) ?></td></tr>
</table>
<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
    <?php $no = 1; while ($r = $rs->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= e($r['kode_barang']) ?></td>
            <td><?= e($r['nama_barang']) ?></td>
            <td><?= rupiah($r['harga']) ?></td>
            <td><?= (int)$r['qty'] ?></td>
            <td><?= rupiah($r['harga'] * $r['qty']) ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="5" class="text-end">Total</th><th><?= rupiah($t['total']) ?></th></tr>
    </tfoot>
</table>
<a href="transaksi.php" class="btn btn-secondary">Kembali</a>

<?php else:
    $rs = $conn->query("SELECT t.*, p.nama AS pelanggan FROM transaksi t LEFT JOIN pelanggan p ON p.id = t.pelanggan_id ORDER BY t.waktu_transaksi DESC, t.id DESC");
?>
<div class="d-flex justify-content-between mb-3">
    <h3>Transaksi</h3>
    <div><a href="transaksi.php?aksi=tambah" class="btn btn-primary">Tambah Transaksi</a></div>
</div>
<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr><th>No</th><th>Tanggal</th><th>Pelanggan</th><th>Keterangan</th><th>Total</th><th style="width:90px">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $no = 1; while ($r = $rs->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= e($r['waktu_transaksi']) ?></td>
            <td><?= e($r['pelanggan']) ?></td>
            <td><?= e($r['keterangan']) ?></td>
            <td><?= rupiah($r['total']) ?></td>
            <td><a href="transaksi.php?aksi=detail&id=<?= (int)$r['id'] ?>" class="btn btn-info btn-sm">Detail</a></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php endif;
include 'footer.php';
