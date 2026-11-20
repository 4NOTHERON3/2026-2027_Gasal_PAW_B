<?php
require_once 'auth.php';
wajib_login();
include 'header.php';
?>
<h3>Home</h3>
<p>Selamat datang, <strong><?= e($_SESSION['nama']) ?></strong>.</p>
<?php include 'footer.php'; ?>
