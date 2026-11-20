<?php
require_once 'auth.php';
wajib_owner();

// PROSES (tambah / ubah / hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi   = $_POST['aksi'] ?? '';
    $id     = trim($_POST['id'] ?? '');
    $nama   = trim($_POST['nama'] ?? '');
    $jk     = $_POST['jenis_kelamin'] ?? '';
    $telp   = trim($_POST['telp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    try {
        if ($aksi === 'tambah' || $aksi === 'ubah') {
            if ($id === '' || $nama === '' || !in_array($jk, ['L', 'P'], true)) {
                set_flash('danger', 'ID, nama, dan jenis kelamin wajib diisi.');
                header('Location: data_pelanggan.php?aksi=' . $aksi . ($aksi === 'ubah' ? '&id=' . urlencode($id) : ''));
                exit;
            }
            if ($aksi === 'tambah') {
                $st = $conn->prepare("INSERT INTO pelanggan (id, nama, jenis_kelamin, telp, alamat) VALUES (?, ?, ?, ?, ?)");
                $st->bind_param('sssss', $id, $nama, $jk, $telp, $alamat);
                $st->execute();
                set_flash('success', 'Data pelanggan berhasil ditambahkan.');
            } else {
                $st = $conn->prepare("UPDATE pelanggan SET nama = ?, jenis_kelamin = ?, telp = ?, alamat = ? WHERE id = ?");
                $st->bind_param('sssss', $nama, $jk, $telp, $alamat, $id);
                $st->execute();
                set_flash('success', 'Data pelanggan berhasil diubah.');
            }
        } elseif ($aksi === 'hapus') {
            $st = $conn->prepare("DELETE FROM pelanggan WHERE id = ?");
            $st->bind_param('s', $id);
            $st->execute();
            set_flash('success', 'Data pelanggan berhasil dihapus.');
        }
    } catch (mysqli_sql_exception $ex) {
        if ($ex->getCode() === 1062) {
            set_flash('danger', 'ID pelanggan sudah digunakan.');
        } elseif ($ex->getCode() === 1451) {
            set_flash('danger', 'Pelanggan tidak dapat dihapus karena sudah memiliki transaksi.');
        } else {
            set_flash('danger', 'Terjadi kesalahan: ' . $ex->getMessage());
        }
    }
    header('Location: data_pelanggan.php');
    exit;
}

// ===== TAMPILAN =====
$aksi = $_GET['aksi'] ?? 'list';
$d = ['id' => '', 'nama' => '', 'jenis_kelamin' => '', 'telp' => '', 'alamat' => ''];

if ($aksi === 'ubah') {
    $st = $conn->prepare("SELECT * FROM pelanggan WHERE id = ?");
    $idq = $_GET['id'] ?? '';
    $st->bind_param('s', $idq);
    $st->execute();
    $d = $st->get_result()->fetch_assoc();
    if (!$d) {
        set_flash('danger', 'Data pelanggan tidak ditemukan.');
        header('Location: data_pelanggan.php');
        exit;
    }
}

include 'header.php';

if ($aksi === 'tambah' || $aksi === 'ubah'):
?>
<h3><?= $aksi === 'tambah' ? 'Tambah' : 'Ubah' ?> Pelanggan</h3>
<form method="post" action="data_pelanggan.php">
    <input type="hidden" name="aksi" value="<?= e($aksi) ?>">
    <div class="mb-3">
        <label class="form-label">ID Pelanggan</label>
        <input type="text" name="id" class="form-control" maxlength="20" value="<?= e($d['id']) ?>" <?= $aksi === 'ubah' ? 'readonly' : 'required' ?>>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control" maxlength="100" value="<?= e($d['nama']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="form-select" required>
            <option value="">-- Pilih --</option>
            <option value="L" <?= $d['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="P" <?= $d['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
        </select>
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
    <a href="data_pelanggan.php" class="btn btn-secondary">Batal</a>
</form>
<?php else:
    $rs = $conn->query("SELECT * FROM pelanggan ORDER BY id");
?>
<div class="d-flex justify-content-between mb-3">
    <h3>Data Pelanggan</h3>
    <div><a href="data_pelanggan.php?aksi=tambah" class="btn btn-primary">Tambah Pelanggan</a></div>
</div>
<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr><th>No</th><th>ID</th><th>Nama</th><th>JK</th><th>Telepon</th><th>Alamat</th><th style="width:160px">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $no = 1; while ($r = $rs->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= e($r['id']) ?></td>
            <td><?= e($r['nama']) ?></td>
            <td><?= e($r['jenis_kelamin']) ?></td>
            <td><?= e($r['telp']) ?></td>
            <td><?= e($r['alamat']) ?></td>
            <td>
                <a href="data_pelanggan.php?aksi=ubah&id=<?= urlencode($r['id']) ?>" class="btn btn-warning btn-sm">Ubah</a>
                <form method="post" action="data_pelanggan.php" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                    <input type="hidden" name="aksi" value="hapus">
                    <input type="hidden" name="id" value="<?= e($r['id']) ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php endif;
include 'footer.php';
