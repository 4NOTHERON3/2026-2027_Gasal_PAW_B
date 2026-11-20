<?php
session_start();
// unset session lalu hancurkan, kembali ke halaman login
session_unset();
session_destroy();
header('Location: login.php');
exit;
