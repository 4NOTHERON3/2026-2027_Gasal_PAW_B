<?php
require "koneksi.php";

$error = "";

//  Proses update (saat tombol Update ditekan) 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $nama   = trim($_POST['nama'] ?? '');
    $telp   = trim($_POST['telp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if ($nama === '' || $telp === '' || $alamat === '') {
        $error = "Nama, telp, dan alamat wajib diisi.";
    } else {
        $stmt = $conn->prepare("UPDATE supplier SET nama = ?, telp = ?, alamat = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nama, $telp, $alamat, $id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php?status=edit");
            exit;
        }
        $error = "Gagal memperbarui data: " . $stmt->error;
        $stmt->close();
    }
} else {
    //  Ambil data lama untuk ditampilkan di form 
    $id = (int)($_GET['id'] ?? 0);

    $stmt = $conn->prepare("SELECT nama, telp, alamat FROM supplier WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $hasil = $stmt->get_result();
    $data  = $hasil->fetch_assoc();
    $stmt->close();

    if (!$data) {
        header("Location: index.php");
        exit;
    }
    $nama   = $data['nama'];
    $telp   = $data['telp'];
    $alamat = $data['alamat'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Data Master Supplier</title>
    <style>
* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background: #ffffff;
    color: #1f2937;
}

.wrapper {
    max-width: 900px;
    margin: 32px auto;
    padding: 0 16px;
}

.card {
    background: transparent;
}

.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 14px;
    border-bottom: 1px solid #e5e9f0;
    margin-bottom: 20px;
}

h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
    color: #337ab7;
}

/*  Tombol  */
.btn {
    display: inline-block;
    padding: 7px 14px;
    border: 1px solid transparent;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
    color: #ffffff;
    text-decoration: none;
    cursor: pointer;
    line-height: 1.3;
}
.btn:focus-visible { outline: 3px solid #93c5fd; outline-offset: 2px; }

/* Simpan / Update / Tambah / BIRU */
.btn-simpan { background: #1d6fd8; border-color: #1559b0; }
.btn-simpan:hover { background: #1559b0; }

/* Edit / ORANGE */
.btn-edit { background: #f28c1b; border-color: #d4750c; }
.btn-edit:hover { background: #d4750c; }

/* Hapus / MERAH */
.btn-hapus { background: #d62d2d; border-color: #b01e1e; }
.btn-hapus:hover { background: #b01e1e; }

/* Batal = abu-abu netral */
.btn-batal { background: #6b7280; border-color: #4b5563; }
.btn-batal:hover { background: #4b5563; }

.btn-sm { padding: 5px 11px; font-size: 13px; }

/*  Tabel  */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
th, td {
    padding: 11px 12px;
    text-align: left;
    border: 1px solid #e1e5ec;
}
th {
    background: #d9edf7;
    color: #31708f;
    font-weight: 600;
}
tbody tr:nth-child(even) { background: #fafbfd; }
td.no, th.no { width: 50px; text-align: center; }
td.aksi { white-space: nowrap; width: 150px; }
td.kosong { text-align: center; color: #6b7280; padding: 24px; }
.inline { display: inline; margin: 0; }

/*  Form  */
.form-row {
    display: flex;
    align-items: center;
    margin-bottom: 16px;
}
.form-row label {
    width: 120px;
    text-align: right;
    padding-right: 18px;
    font-weight: 600;
    font-size: 14px;
}
.form-row input {
    width: 100%;
    max-width: 380px;
    padding: 9px 11px;
    border: 1px solid #c3cad6;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
}
.form-row input:focus {
    outline: none;
    border-color: #1d6fd8;
    box-shadow: 0 0 0 3px rgba(29, 111, 216, 0.2);
}
.form-aksi { margin-left: 120px; padding-left: 18px; }

.pesan {
    padding: 10px 14px;
    border-radius: 4px;
    margin-bottom: 16px;
    font-size: 14px;
}
.pesan-sukses { background: #e3f4e8; border: 1px solid #a8d8b5; color: #1d5c31; }
.pesan-error  { background: #fde8e8; border: 1px solid #f2b5b5; color: #8a1f1f; }

@media (max-width: 600px) {
    .form-row { flex-direction: column; align-items: flex-start; }
    .form-row label { width: auto; text-align: left; padding: 0 0 6px; }
    .form-aksi { margin-left: 0; padding-left: 0; }
    td.aksi { width: auto; }
}
</style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1>Edit Data Master Supplier</h1>
        </div>

        <?php if ($error !== ""): ?>
            <div class="pesan pesan-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="edit.php" method="post">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-row">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
            </div>
            <div class="form-row">
                <label for="telp">Telp</label>
                <input type="text" id="telp" name="telp" value="<?= htmlspecialchars($telp) ?>" required>
            </div>
            <div class="form-row">
                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($alamat) ?>" required>
            </div>
            <div class="form-aksi">
                <button type="submit" class="btn btn-simpan">Update</button>
                <a href="index.php" class="btn btn-batal">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>
