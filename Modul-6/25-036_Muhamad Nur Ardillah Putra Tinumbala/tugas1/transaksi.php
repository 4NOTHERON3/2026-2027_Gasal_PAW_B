<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data master transaksi yang dipilih
$master = null;
if ($id > 0) {
    $qm = mysqli_query($koneksi, "SELECT t.*, p.nama AS nama_pelanggan
            FROM transaksi t
            LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
            WHERE t.id = $id");
    $master = mysqli_fetch_assoc($qm);
}

// Proses simpan detail transaksi + update total otomatis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $master) {
    $barang_id = isset($_POST['barang_id']) ? (int)$_POST['barang_id'] : 0;
    $qty       = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
    $tipe  = "error";
    $pesan = "";

    // Cek barang sudah ada di transaksi ini atau belum
    $cek = mysqli_query($koneksi, "SELECT 1 FROM transaksi_detail
            WHERE transaksi_id = $id AND barang_id = $barang_id");
    // Ambil harga dari tabel barang
    $rb = mysqli_query($koneksi, "SELECT harga FROM barang WHERE id = $barang_id");
    $b  = mysqli_fetch_assoc($rb);

    if ($barang_id < 1 || !$b) {
        $pesan = "Pilih barang terlebih dahulu.";
    } elseif ($qty < 1) {
        $pesan = "Qty minimal 1.";
    } elseif (mysqli_num_rows($cek) > 0) {
        $pesan = "Barang sudah ada di detail transaksi ini.";
    } else {
        $harga = (int)$b['harga'];

        // Simpan detail
        $ok = mysqli_query($koneksi, "INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty)
                VALUES ($id, $barang_id, $harga, $qty)");

        if ($ok) {
            // Hitung ulang total dari seluruh detail milik transaksi yang sama
            mysqli_query($koneksi, "UPDATE transaksi SET total =
                    (SELECT SUM(harga * qty) FROM transaksi_detail WHERE transaksi_id = $id)
                    WHERE id = $id");
            $tipe  = "sukses";
            $pesan = "Detail transaksi berhasil disimpan dan total diperbarui.";
        } else {
            $pesan = "Gagal menyimpan detail transaksi.";
        }
    }

    // Redirect agar refresh halaman tidak menyimpan ulang data
    header("Location: transaksi.php?id=$id&tipe=$tipe&pesan=" . urlencode($pesan) . "#detail");
    exit;
}

$pesan = isset($_GET['pesan']) ? $_GET['pesan'] : "";
$tipe  = (isset($_GET['tipe']) && $_GET['tipe'] == 'error') ? "error" : "sukses";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Master-Detail Transaksi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <a href="transaksi.php" class="aktif">Transaksi</a>
        <a href="barang.php">Data Barang</a>
    </div>

    <div class="container">

        <div class="card">
            <h1>Daftar Transaksi (Master)</h1>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Keterangan</th>
                    <th class="angka">Total</th>
                    <th>Aksi</th>
                </tr>
                <?php
                $q = mysqli_query($koneksi, "SELECT t.*, p.nama AS nama_pelanggan
                        FROM transaksi t
                        LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
                        ORDER BY t.id");
                if (mysqli_num_rows($q) == 0) {
                    echo "<tr><td colspan='6' class='kosong'>Belum ada data transaksi.</td></tr>";
                }
                while ($row = mysqli_fetch_assoc($q)) {
                    $kelas = ($row['id'] == $id) ? " class='aktif'" : "";
                    echo "<tr$kelas>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . tanggal($row['waktu_transaksi']) . "</td>";
                    echo "<td>" . htmlspecialchars((string)$row['nama_pelanggan']) . "</td>";
                    echo "<td>" . htmlspecialchars((string)$row['keterangan']) . "</td>";
                    echo "<td class='angka'>" . rupiah($row['total']) . "</td>";
                    echo "<td><a class='btn kecil' href='transaksi.php?id=" . $row['id'] . "#detail'>Load</a></td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>

        <?php if ($id > 0 && !$master) { ?>
            <div class="pesan error">Transaksi dengan ID <?php echo $id; ?> tidak ditemukan.</div>
        <?php } ?>

        <?php if ($master) { ?>
            <div class="card" id="detail">
                <h1>Detail Transaksi #<?php echo $master['id']; ?></h1>

                <?php if ($pesan != "") { ?>
                    <div class="pesan <?php echo $tipe; ?>"><?php echo htmlspecialchars($pesan); ?></div>
                <?php } ?>

                <div class="info" style="margin-bottom:20px;">
                    <div class="label">ID Transaksi</div>
                    <div><?php echo $master['id']; ?></div>
                    <div class="label">Waktu Transaksi</div>
                    <div><?php echo tanggal($master['waktu_transaksi']); ?></div>
                    <div class="label">Pelanggan</div>
                    <div><?php echo htmlspecialchars((string)$master['pelanggan_id'] . " - " . (string)$master['nama_pelanggan']); ?></div>
                    <div class="label">Keterangan</div>
                    <div><?php echo htmlspecialchars((string)$master['keterangan']); ?></div>
                    <div class="label">Total</div>
                    <div><b><?php echo rupiah($master['total']); ?></b></div>
                </div>

                <h2>Daftar Barang</h2>
                <table>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th class="angka">Harga</th>
                        <th class="angka">Qty</th>
                        <th class="angka">Subtotal</th>
                    </tr>
                    <?php
                    $qd = mysqli_query($koneksi, "SELECT d.*, b.kode_barang, b.nama_barang
                            FROM transaksi_detail d
                            JOIN barang b ON d.barang_id = b.id
                            WHERE d.transaksi_id = $id
                            ORDER BY b.kode_barang");
                    $no = 1;
                    if (mysqli_num_rows($qd) == 0) {
                        echo "<tr><td colspan='6' class='kosong'>Belum ada detail untuk transaksi ini.</td></tr>";
                    }
                    while ($d = mysqli_fetch_assoc($qd)) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $d['kode_barang'] . "</td>";
                        echo "<td>" . htmlspecialchars($d['nama_barang']) . "</td>";
                        echo "<td class='angka'>" . rupiah($d['harga']) . "</td>";
                        echo "<td class='angka'>" . $d['qty'] . "</td>";
                        echo "<td class='angka'>" . rupiah($d['harga'] * $d['qty']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="angka">Total</td>
                            <td class="angka"><?php echo rupiah($master['total']); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="card">
                <h2>Tambah Detail Transaksi</h2>
                <?php
                // Hanya barang yang belum ada di detail transaksi ini
                $qb = mysqli_query($koneksi, "SELECT id, kode_barang, nama_barang, harga
                        FROM barang
                        WHERE id NOT IN (SELECT barang_id FROM transaksi_detail
                                         WHERE transaksi_id = $id)
                        ORDER BY kode_barang");
                $sisa = mysqli_num_rows($qb);
                ?>
                <?php if ($sisa == 0) { ?>
                    <p class="kosong">Semua barang sudah dimasukkan ke transaksi ini.</p>
                <?php } else { ?>
                    <form method="post" action="transaksi.php?id=<?php echo $id; ?>">
                        <div class="form-baris">
                            <div>
                                <label>Barang</label>
                                <select name="barang_id" required>
                                    <option value="">-- Pilih Barang --</option>
                                    <?php
                                    while ($b = mysqli_fetch_assoc($qb)) {
                                        echo "<option value='" . $b['id'] . "'>" . $b['kode_barang'] . " - "
                                            . htmlspecialchars($b['nama_barang']) . " (" . rupiah($b['harga']) . ")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label>Qty</label>
                                <input type="number" name="qty" min="1" value="1" required>
                            </div>
                            <button type="submit" class="btn">Simpan</button>
                        </div>
                    </form>
                <?php } ?>
            </div>
        <?php } ?>

    </div>
</body>
</html>
