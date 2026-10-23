<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Posted data</title>
</head>
<body>
  <h3>Posted data: </h3>
  <?php
  foreach ($_POST as $key => $value)
      echo "($key) => (" . htmlspecialchars($value) . ")<br/>";

  //$surname = $_POST['surname'];
  ?>
</body>
</html>

<!-- 4.3

Soal 3
Lakukan pengujian form: isi semua field, klik Submit, amati data yang dikirim (Developer Tools F12 → Network), coba ganti method menjadi "GET", dan amati perbedaan GET dan POST.
Jawab:
- POST: data dikirim di body request. URL tetap `processData.php`; data terlihat di F12 → Network → Payload (Form Data).
- GET: data ditempel di URL, misalnya `processData.php?surname=Budi&email=...&passwd=123456`. Kata sandi ikut terlihat di URL dan riwayat browser, sehingga GET tidak cocok untuk data sensitif. Panjang data juga terbatas dan halaman bisa di-bookmark.
- Checkbox Vegetarian? hanya muncul di hasil bila dicentang, karena browser tidak mengirim checkbox yang tidak dicentang. -->