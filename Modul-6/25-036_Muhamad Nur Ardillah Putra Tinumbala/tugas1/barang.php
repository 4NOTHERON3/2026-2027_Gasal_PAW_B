<?php
include 'koneksi.php';

// Proses hapus dengan pengecekan data terkait
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_id'])) {
    $hapus_id = (int)$_POST['hapus_id'];
    $tipe  = "error";

    // Cek apakah barang ada
    $ada = mysqli_query($koneksi, "SELECT nama_barang FROM barang WHERE id = $hapus_id");
    $brg = mysqli_fetch_assoc($ada);

    // Cek apakah barang sudah dipakai di transaksi_detail
    $cek = mysqli_query($koneksi, "SELECT 1 FROM transaksi_detail WHERE barang_id = $hapus_id");

    if (!$brg) {
        $pesan = "Data barang tidak ditemukan.";
    } elseif (mysqli_num_rows($cek) > 0) {
        $pesan = "Barang \"" . $brg['nama_barang'] . "\" tidak dapat dihapus karena masih digunakan pada transaksi.";
    } else {
        mysqli_query($koneksi, "DELETE FROM barang WHERE id = $hapus_id");
        $tipe  = "sukses";
        $pesan = "Barang \"" . $brg['nama_barang'] . "\" berhasil dihapus.";
    }

    // Redirect agar refresh halaman tidak mengulang proses hapus
    header("Location: barang.php?tipe=$tipe&pesan=" . urlencode($pesan));
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
    <title>Data Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <a href="transaksi.php">Transaksi</a>
        <a href="barang.php" class="aktif">Data Barang</a>
    </div>

    <div class="container">
        <div class="card">
            <h1>Data Barang</h1>

            <?php if ($pesan != "") { ?>
                <div class="pesan <?php echo $tipe; ?>"><?php echo htmlspecialchars($pesan); ?></div>
            <?php } ?>

            <table>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th class="angka">Harga</th>
                    <th class="angka">Stok</th>
                    <th>Supplier</th>
                    <th>Aksi</th>
                </tr>
                <?php
                $q = mysqli_query($koneksi, "SELECT b.*, s.nama AS nama_supplier
                        FROM barang b
                        LEFT JOIN supplier s ON b.supplier_id = s.id
                        ORDER BY b.id");
                if (mysqli_num_rows($q) == 0) {
                    echo "<tr><td colspan='6' class='kosong'>Belum ada data barang.</td></tr>";
                }
                while ($row = mysqli_fetch_assoc($q)) {
                    echo "<tr>";
                    echo "<td>" . $row['kode_barang'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
                    echo "<td class='angka'>" . rupiah($row['harga']) . "</td>";
                    echo "<td class='angka'>" . $row['stok'] . "</td>";
                    echo "<td>" . htmlspecialchars((string)$row['nama_supplier']) . "</td>";
                    echo "<td>
                            <form method='post' class='inline' onsubmit=\"return confirm('Hapus barang ini?')\">
                                <input type='hidden' name='hapus_id' value='" . $row['id'] . "'>
                                <button type='submit' class='btn hapus kecil'>Hapus</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>
