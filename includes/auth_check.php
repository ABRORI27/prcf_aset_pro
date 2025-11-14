<?php
require_once __DIR__ . '/../config/init.php';

// 🔐 Fungsi: pastikan user sudah login
function require_login() {
    if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}

// 🔑 Fungsi: cek apakah user punya akses berdasarkan role
// FUNGSI has_access() SUDAH ADA DI init.php, JANGAN DEKLARASI LAGI DI SINI
?>
