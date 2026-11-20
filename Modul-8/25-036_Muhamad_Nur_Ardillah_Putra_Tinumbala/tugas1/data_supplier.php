<?php
require_once 'auth.php';
wajib_owner();

// PROSES (tambah / ubah / hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi   = $_POST['aksi'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    $nama   = trim($_POST['nama'] ?? '');
    $telp   = trim($_POST['telp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    try {
        if ($aksi === 'tambah' || $aksi === 'ubah') {
            if ($nama === '') {
                set_flash('danger', 'Nama supplier wajib diisi.');
                header('Location: data_supplier.php?aksi=' . $aksi . ($aksi === 'ubah' ? '&id=' . $id : ''));
                exit;
            }
            if ($aksi === 'tambah') {
                $st = $conn->prepare("INSERT INTO supplier (nama, telp, alamat) VALUES (?, ?, ?)");
                $st->bind_param('sss', $nama, $telp, $alamat);
                $st->execute();
                set_flash('success', 'Data supplier berhasil ditambahkan.');
            } else {
                $st = $conn->prepare("UPDATE supplier SET nama = ?, telp = ?, alamat = ? WHERE id = ?");
                $st->bind_param('sssi', $nama, $telp, $alamat, $id);
                $st->execute();
                set_flash('success', 'Data supplier berhasil diubah.');
            }
        } elseif ($aksi === 'hapus') {
            $st = $conn->prepare("DELETE FROM supplier WHERE id = ?");
            $st->bind_param('i', $id);
            $st->execute();
            set_flash('success', 'Data supplier berhasil dihapus.');
        }
    } catch (mysqli_sql_exception $ex) {
        if ($ex->getCode() === 1451) {
            set_flash('danger', 'Supplier tidak dapat dihapus karena masih dipakai pada data barang.');
        } else {
            set_flash('danger', 'Terjadi kesalahan: ' . $ex->getMessage());
        }
    }
    header('Location: data_supplier.php');
    exit;
}

// ===== TAMPILAN =====
$aksi = $_GET['aksi'] ?? 'list';
$d = ['id' => '', 'nama' => '', 'telp' => '', 'alamat' => ''];

if ($aksi === 'ubah') {
    $st = $conn->prepare("SELECT * FROM supplier WHERE id = ?");
    $idq = (int)($_GET['id'] ?? 0);
    $st->bind_param('i', $idq);
    $st->execute();
    $d = $st->get_result()->fetch_assoc();
    if (!$d) {
        set_flash('danger', 'Data supplier tidak ditemukan.');
        header('Location: data_supplier.php');
        exit;
    }
}

include 'header.php';

if ($aksi === 'tambah' || $aksi === 'ubah'):
?>
<h3><?= $aksi === 'tambah' ? 'Tambah' : 'Ubah' ?> Supplier</h3>
<form method="post" action="data_supplier.php">
    <input type="hidden" name="aksi" value="<?= e($aksi) ?>">
    <input type="hidden" name="id" value="<?= e($d['id']) ?>">
    <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control" maxlength="100" value="<?= e($d['nama']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Telepon</label>
        <input type="text" name="telp" class="form-control" maxlength="12" value="<?= e($d['telp']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="3"><?= e($d['alamat']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="data_supplier.php" class="btn btn-secondary">Batal</a>
</form>
<?php else:
    $rs = $conn->query("SELECT * FROM supplier ORDER BY id");
?>
<div class="d-flex justify-content-between mb-3">
    <h3>Data Supplier</h3>
    <div><a href="data_supplier.php?aksi=tambah" class="btn btn-primary">Tambah Supplier</a></div>
</div>
<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr><th>No</th><th>Nama</th><th>Telepon</th><th>Alamat</th><th style="width:160px">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $no = 1; while ($r = $rs->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= e($r['nama']) ?></td>
            <td><?= e($r['telp']) ?></td>
            <td><?= e($r['alamat']) ?></td>
            <td>
                <a href="data_supplier.php?aksi=ubah&id=<?= (int)$r['id'] ?>" class="btn btn-warning btn-sm">Ubah</a>
                <form method="post" action="data_supplier.php" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
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
