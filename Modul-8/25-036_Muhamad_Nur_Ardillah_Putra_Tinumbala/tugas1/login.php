<?php
require_once 'auth.php';

// Sudah login -> tidak boleh membuka halaman login
if (!empty($_SESSION['id_user'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $pw = md5($password); // password di tabel user disimpan dengan MD5
        $st = $conn->prepare("SELECT id_user, username, nama, level FROM user WHERE username = ? AND password = ?");
        $st->bind_param('ss', $username, $pw);
        $st->execute();
        $u = $st->get_result()->fetch_assoc();

        if ($u && in_array((int)$u['level'], [1, 2], true)) {
            session_regenerate_id(true);
            // set session
            $_SESSION['id_user']  = $u['id_user'];
            $_SESSION['username'] = $u['username'];
            $_SESSION['nama']     = $u['nama'];
            $_SESSION['level']    = (int)$u['level'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistem Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width:400px; margin-top:100px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="text-center mb-4">Login Sistem Penjualan</h4>
            <?php if ($error !== ''): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= e($_POST['username'] ?? '') ?>" autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
