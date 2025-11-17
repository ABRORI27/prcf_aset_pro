<?php
require_once __DIR__ . '/../config/init.php';

// 🔐 Fungsi: pastikan user sudah login
if (!function_exists('require_login')) {
    function require_login() {
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'login.php');
            exit();
        }
    }
}

// ❗ Jangan deklarasikan has_access() di sini
// karena sudah ada di init.php
?>
