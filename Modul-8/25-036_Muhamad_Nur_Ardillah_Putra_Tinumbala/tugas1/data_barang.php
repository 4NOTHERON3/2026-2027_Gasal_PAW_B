<?php
require_once 'auth.php';
wajib_owner();

// PROSES (tambah / ubah / hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi        = $_POST['aksi'] ?? '';
    $id          = (int)($_POST['id'] ?? 0);
    $kode        = trim($_POST['kode_barang'] ?? '');
    $nama        = trim($_POST['nama_barang'] ?? '');
    $harga       = (int)($_POST['harga'] ?? 0);
    $stok        = (int)($_POST['stok'] ?? 0);
    $supplier_id = (int)($_POST['supplier_id'] ?? 0);

    try {
        if ($aksi === 'tambah' || $aksi === 'ubah') {
            if ($kode === '' || $nama === '' || $supplier_id <= 0) {
                set_flash('danger', 'Kode barang, nama barang, dan supplier wajib diisi.');
                header('Location: data_barang.php?aksi=' . $aksi . ($aksi === 'ubah' ? '&id=' . $id : ''));
                exit;
            }
            if ($aksi === 'tambah') {
                $st = $conn->prepare("INSERT INTO barang (kode_barang, nama_barang, harga, stok, supplier_id) VALUES (?, ?, ?, ?, ?)");
                $st->bind_param('ssiii', $kode, $nama, $harga, $stok, $supplier_id);
                $st->execute();
                set_flash('success', 'Data barang berhasil ditambahkan.');
            } else {
                $st = $conn->prepare("UPDATE barang SET kode_barang = ?, nama_barang = ?, harga = ?, stok = ?, supplier_id = ? WHERE id = ?");
                $st->bind_param('ssiiii', $kode, $nama, $harga, $stok, $supplier_id, $id);
                $st->execute();
                set_flash('success', 'Data barang berhasil diubah.');
            }
        } elseif ($aksi === 'hapus') {
            $st = $conn->prepare("DELETE FROM barang WHERE id = ?");
            $st->bind_param('i', $id);
            $st->execute();
            set_flash('success', 'Data barang berhasil dihapus.');
        }
    } catch (mysqli_sql_exception $ex) {
        if ($ex->getCode() === 1062) {
            set_flash('danger', 'Kode barang sudah digunakan.');
        } elseif ($ex->getCode() === 1451) {
            set_flash('danger', 'Barang tidak dapat dihapus karena sudah dipakai pada transaksi.');
        } else {
            set_flash('danger', 'Terjadi kesalahan: ' . $ex->getMessage());
        }
    }
    header('Location: data_barang.php');
    exit;
}

// ===== TAMPILAN =====
$aksi = $_GET['aksi'] ?? 'list';
$d = ['id' => '', 'kode_barang' => '', 'nama_barang' => '', 'harga' => '', 'stok' => '', 'supplier_id' => ''];

if ($aksi === 'ubah') {
    $st = $conn->prepare("SELECT * FROM barang WHERE id = ?");
    $idq = (int)($_GET['id'] ?? 0);
    $st->bind_param('i', $idq);
    $st->execute();
    $d = $st->get_result()->fetch_assoc();
    if (!$d) {
        set_flash('danger', 'Data barang tidak ditemukan.');
        header('Location: data_barang.php');
        exit;
    }
}

include 'header.php';

if ($aksi === 'tambah' || $aksi === 'ubah'):
    $sup = $conn->query("SELECT id, nama FROM supplier ORDER BY nama");
?>
<h3><?= $aksi === 'tambah' ? 'Tambah' : 'Ubah' ?> Barang</h3>
<form method="post" action="data_barang.php">
    <input type="hidden" name="aksi" value="<?= e($aksi) ?>">
    <input type="hidden" name="id" value="<?= e($d['id']) ?>">
    <div class="mb-3">
        <label class="form-label">Kode Barang</label>
        <input type="text" name="kode_barang" class="form-control" maxlength="10" value="<?= e($d['kode_barang']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="nama_barang" class="form-control" maxlength="100" value="<?= e($d['nama_barang']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Harga</label>
        <input type="number" name="harga" class="form-control" min="0" value="<?= e($d['harga']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Stok</label>
        <input type="number" name="stok" class="form-control" min="0" value="<?= e($d['stok']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Supplier</label>
        <select name="supplier_id" class="form-select" required>
            <option value="">-- Pilih Supplier --</option>
            <?php while ($s = $sup->fetch_assoc()): ?>
                <option value="<?= (int)$s['id'] ?>" <?= (int)$d['supplier_id'] === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['nama']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="data_barang.php" class="btn btn-secondary">Batal</a>
</form>
<?php else:
    $rs = $conn->query("SELECT b.*, s.nama AS nama_supplier FROM barang b LEFT JOIN supplier s ON s.id = b.supplier_id ORDER BY b.id");
?>
<div class="d-flex justify-content-between mb-3">
    <h3>Data Barang</h3>
    <div><a href="data_barang.php?aksi=tambah" class="btn btn-primary">Tambah Barang</a></div>
</div>
<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Harga</th><th>Stok</th><th>Supplier</th><th style="width:160px">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $no = 1; while ($r = $rs->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= e($r['kode_barang']) ?></td>
            <td><?= e($r['nama_barang']) ?></td>
            <td><?= rupiah($r['harga']) ?></td>
            <td><?= (int)$r['stok'] ?></td>
            <td><?= e($r['nama_supplier']) ?></td>
            <td>
                <a href="data_barang.php?aksi=ubah&id=<?= (int)$r['id'] ?>" class="btn btn-warning btn-sm">Ubah</a>
                <form method="post" action="data_barang.php" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                    <input type="hidden" name="aksi" value="hapus">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php endif;
include 'footer.php';
