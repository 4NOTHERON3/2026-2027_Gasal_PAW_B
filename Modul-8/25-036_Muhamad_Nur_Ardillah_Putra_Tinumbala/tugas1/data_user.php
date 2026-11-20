<?php
require_once 'auth.php';
wajib_owner();

// PROSES (tambah / ubah / hapus) 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi     = $_POST['aksi'] ?? '';
    $id       = (int)($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $nama     = trim($_POST['nama'] ?? '');
    $alamat   = trim($_POST['alamat'] ?? '');
    $hp       = trim($_POST['hp'] ?? '');
    $level    = (int)($_POST['level'] ?? 0);

    try {
        if ($aksi === 'tambah' || $aksi === 'ubah') {
            $salah = ($username === '' || $nama === '' || !in_array($level, [1, 2], true));
            if ($aksi === 'tambah' && $password === '') {
                $salah = true;
            }
            if ($salah) {
                set_flash('danger', 'Username, password, nama, dan level wajib diisi.');
                header('Location: data_user.php?aksi=' . $aksi . ($aksi === 'ubah' ? '&id=' . $id : ''));
                exit;
            }
            if ($aksi === 'tambah') {
                $pw = md5($password);
                $st = $conn->prepare("INSERT INTO user (username, password, nama, alamat, hp, level) VALUES (?, ?, ?, ?, ?, ?)");
                $st->bind_param('sssssi', $username, $pw, $nama, $alamat, $hp, $level);
                $st->execute();
                set_flash('success', 'Data user berhasil ditambahkan.');
            } else {
                if ($password !== '') { // password diisi -> ganti, kosong -> tetap
                    $pw = md5($password);
                    $st = $conn->prepare("UPDATE user SET username = ?, password = ?, nama = ?, alamat = ?, hp = ?, level = ? WHERE id_user = ?");
                    $st->bind_param('sssssii', $username, $pw, $nama, $alamat, $hp, $level, $id);
                } else {
                    $st = $conn->prepare("UPDATE user SET username = ?, nama = ?, alamat = ?, hp = ?, level = ? WHERE id_user = ?");
                    $st->bind_param('ssssii', $username, $nama, $alamat, $hp, $level, $id);
                }
                $st->execute();
                if ($id === (int)$_SESSION['id_user']) { // user yang sedang login mengubah dirinya sendiri
                    $_SESSION['nama']     = $nama;
                    $_SESSION['username'] = $username;
                    $_SESSION['level']    = $level;
                }
                set_flash('success', 'Data user berhasil diubah.');
            }
        } elseif ($aksi === 'hapus') {
            if ($id === (int)$_SESSION['id_user']) {
                set_flash('danger', 'Akun yang sedang digunakan tidak dapat dihapus.');
            } else {
                $st = $conn->prepare("DELETE FROM user WHERE id_user = ?");
                $st->bind_param('i', $id);
                $st->execute();
                set_flash('success', 'Data user berhasil dihapus.');
            }
        }
    } catch (mysqli_sql_exception $ex) {
        if ($ex->getCode() === 1062) {
            set_flash('danger', 'Username sudah digunakan.');
        } else {
            set_flash('danger', 'Terjadi kesalahan: ' . $ex->getMessage());
        }
    }
    header('Location: data_user.php');
    exit;
}

//  TAMPILAN 
$aksi = $_GET['aksi'] ?? 'list';
$d = ['id_user' => '', 'username' => '', 'nama' => '', 'alamat' => '', 'hp' => '', 'level' => ''];

if ($aksi === 'ubah') {
    $st = $conn->prepare("SELECT id_user, username, nama, alamat, hp, level FROM user WHERE id_user = ?");
    $idq = (int)($_GET['id'] ?? 0);
    $st->bind_param('i', $idq);
    $st->execute();
    $d = $st->get_result()->fetch_assoc();
    if (!$d) {
        set_flash('danger', 'Data user tidak ditemukan.');
        header('Location: data_user.php');
        exit;
    }
}

include 'header.php';

if ($aksi === 'tambah' || $aksi === 'ubah'):
?>
<h3><?= $aksi === 'tambah' ? 'Tambah' : 'Ubah' ?> User</h3>
<form method="post" action="data_user.php">
    <input type="hidden" name="aksi" value="<?= e($aksi) ?>">
    <input type="hidden" name="id" value="<?= e($d['id_user']) ?>">
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" maxlength="30" value="<?= e($d['username']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password<?= $aksi === 'ubah' ? ' (kosongkan jika tidak diganti)' : '' ?></label>
        <input type="password" name="password" class="form-control" <?= $aksi === 'tambah' ? 'required' : '' ?>>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control" maxlength="50" value="<?= e($d['nama']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Alamat</label>
        <input type="text" name="alamat" class="form-control" maxlength="150" value="<?= e($d['alamat']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">HP</label>
        <input type="text" name="hp" class="form-control" maxlength="20" value="<?= e($d['hp']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Level</label>
        <select name="level" class="form-select" required>
            <option value="">-- Pilih Level --</option>
            <option value="1" <?= (int)$d['level'] === 1 ? 'selected' : '' ?>>1 - Owner</option>
            <option value="2" <?= (int)$d['level'] === 2 ? 'selected' : '' ?>>2 - Kasir</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="data_user.php" class="btn btn-secondary">Batal</a>
</form>
<?php else:
    $rs = $conn->query("SELECT id_user, username, nama, alamat, hp, level FROM user ORDER BY id_user");
?>
<div class="d-flex justify-content-between mb-3">
    <h3>Data User</h3>
    <div><a href="data_user.php?aksi=tambah" class="btn btn-primary">Tambah User</a></div>
</div>
<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr><th>No</th><th>Username</th><th>Nama</th><th>Alamat</th><th>HP</th><th>Level</th><th style="width:160px">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $no = 1; while ($r = $rs->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= e($r['username']) ?></td>
            <td><?= e($r['nama']) ?></td>
            <td><?= e($r['alamat']) ?></td>
            <td><?= e($r['hp']) ?></td>
            <td><?= (int)$r['level'] === 1 ? 'Owner' : 'Kasir' ?></td>
            <td>
                <a href="data_user.php?aksi=ubah&id=<?= (int)$r['id_user'] ?>" class="btn btn-warning btn-sm">Ubah</a>
                <form method="post" action="data_user.php" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                    <input type="hidden" name="aksi" value="hapus">
                    <input type="hidden" name="id" value="<?= (int)$r['id_user'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php endif;
include 'footer.php';
