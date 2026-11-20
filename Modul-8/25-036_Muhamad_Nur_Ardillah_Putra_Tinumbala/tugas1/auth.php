<?php
// File ini di-include di SETIAP halaman: memulai session + fungsi proteksi halaman
session_start();
require_once __DIR__ . '/koneksi.php';

function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function rupiah($n) {
    return 'Rp ' . number_format((int)$n, 0, ',', '.');
}

function set_flash($tipe, $pesan) {
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

// Belum login -> tidak boleh membuka halaman apa pun selain login
function wajib_login() {
    if (empty($_SESSION['id_user'])) {
        header('Location: login.php');
        exit;
    }
}

// Halaman Data Master hanya untuk level 1 (owner)
function wajib_owner() {
    wajib_login();
    if ((int)$_SESSION['level'] !== 1) {
        set_flash('danger', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        header('Location: index.php');
        exit;
    }
}
