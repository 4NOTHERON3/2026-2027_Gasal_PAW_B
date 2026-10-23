<?php
$errors = array();

// Mengambil isian lama XSS
function isian($nama)
{
    return isset($_POST[$nama]) ? htmlspecialchars($_POST[$nama]) : '';
}

$labelField = array(
    'surname' => 'surname', 'email' => 'email', 'passwd' => 'passwd',
    'address' => 'address', 'state' => 'state', 'sex' => 'sex'
);

if (isset($_POST['surname']))   // ada isian form?
{
    require 'validate.inc';
    validateName($errors, $_POST, 'surname');
    validateEmail($errors, $_POST, 'email');
    validatePassword($errors, $_POST, 'passwd');
    validateAddress($errors, $_POST, 'address');
    validateState($errors, $_POST, 'state');
    validateGender($errors, $_POST, 'sex');
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Register</title>
</head>
<body>
  <h1>Register</h1>
<?php
if (isset($_POST['surname']))
{
    if ($errors)
    {
        echo '<h1>Invalid, correct the following errors:</h1>';
        foreach ($errors as $field => $error)
            echo "$field $error</br>";

        // tampilkan kembali form (beserta isian sebelumnya)
        echo '<fieldset><legend>Person Details</legend>';
        include 'form.inc';
        echo '</fieldset>';
    }
    else
    {
        echo 'Form submitted successfully with no errors';
    }
}
else
{
    // tampilkan form kosong
    echo '<fieldset><legend>Person Details</legend>';
    include 'form.inc';
    echo '</fieldset>';
}
?>
</body>
</html>

<!-- 4.3.2

Soal 6
Uji form dengan surname benar dan salah. Jelaskan, dan apa bedanya dengan pengujian 4.3.1 Langkah #6?
Jawab:
- Form dan pemrosesan berada dalam satu alamat (`form.php`). Pertama dibuka, form kosong tampil. Setelah Submit, validasi berjalan: bila error, pesan error tampil dan form muncul lagi; bila benar, tampil pesan berhasil.
- Beda dengan 4.3.1: pengguna tidak berpindah ke halaman lain (`processData.php`) dan bisa langsung mencoba lagi pada form yang sama.

Soal 8
Uji kembali dengan surname benar dan salah. Jelaskan, dan apa bedanya dengan Langkah #6?
Jawab:
- Saat error, isian sebelumnya tetap terisi (diambil dari `$_POST` dan di-escape dengan `htmlspecialchars`), sehingga pengguna hanya memperbaiki kolom yang salah.
- Beda dengan Langkah 6: di Langkah 6 semua isian hilang dan harus diketik ulang.
 -->
