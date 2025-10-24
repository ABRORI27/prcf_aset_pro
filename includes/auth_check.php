<?php
require_once __DIR__ . '/../config/init.php';

// Jalankan session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔐 Pastikan fungsi tidak dideklarasikan dua kali
if (!function_exists('require_login')) {
    function require_login() {
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'login.php');
            exit();
        }
    }
}

if (!function_exists('has_access')) {
    function has_access($allowed_roles = []) {
        if (!isset($_SESSION['user']['role'])) return false;
        $current_role = strtolower($_SESSION['user']['role']);
        $allowed_roles = array_map('strtolower', (array)$allowed_roles);
        return in_array($current_role, $allowed_roles);
    }
}
?>
