<?php
/**
 * ==============================================
 * INIT FILE (FINAL) - PRCF Indonesia Aset System
 * Author: Philo
 * ==============================================
 * Fungsi:
 *  - Inisialisasi konfigurasi global
 *  - Memuat koneksi database
 *  - Menetapkan timezone
 *  - Melindungi akses file langsung
 *  - Menyediakan helper functions global
 * ==============================================
 */



// 1️⃣ Jalankan session (jika belum aktif)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2️⃣ Set timezone default (WIB)
date_default_timezone_set('Asia/Jakarta');

// 3️⃣ Load constants
require_once __DIR__ . '/constants.php';

// 4️⃣ Load koneksi database
require_once __DIR__ . '/db.php';

// 5️⃣ Validasi koneksi database
if (!isset($conn) || !$conn instanceof mysqli || $conn->connect_error) {
    die("❌ Gagal koneksi ke database: " . ($conn->connect_error ?? 'Tidak terhubung.'));
}

// 6️⃣ Helper Functions
if (!function_exists('base_url')) {
    function base_url(string $path = ''): string {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void {
        header("Location: " . $url);
        exit;
    }
}

if (!function_exists('e')) {
    function e($string): string {
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }
}

// 🔐 TAMBAHKAN FUNGSI HAS_ACCESS DI SINI
if (!function_exists('has_access')) {
    function has_access($allowed_roles = []) {
        if (!isset($_SESSION['user']['role'])) {
            return false;
        }
        $current_role = $_SESSION['user']['role'];
        $allowed_roles = is_array($allowed_roles) ? $allowed_roles : [$allowed_roles];
        return in_array($current_role, $allowed_roles);
    }
}

// 7️⃣ Path constants (memudahkan include modular)
define('INCLUDES_PATH', realpath(__DIR__));
define('MODULES_PATH', realpath(__DIR__ . '/../modules'));
define('ASSETS_PATH', realpath(__DIR__ . '/../assets'));

// 8️⃣ Debug mode (nonaktif saat production)
define('DEBUG_MODE', true);
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// // 9️⃣ Tambahan keamanan dasar (anti hijack session)
// if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
//     session_unset();
//     session_destroy();
//     die('⚠️ Sesi tidak valid, silakan login ulang.');
// } else {
//     $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
// }

// 10️⃣ Log aktivitas opsional (bisa dikembangkan nanti)
if (!function_exists('log_activity')) {
    function log_activity($message) {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) mkdir($logDir, 0755, true);
        $file = $logDir . '/activity_' . date('Y-m-d') . '.log';
        $entry = "[" . date('H:i:s') . "] " . $message . PHP_EOL;
        file_put_contents($file, $entry, FILE_APPEND);
    }
}