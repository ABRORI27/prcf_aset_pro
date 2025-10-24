<?php
/**
 * File: constants.php
 * Deskripsi: Menyimpan semua konstanta global untuk sistem PRCF Aset PRO
 * Lokasi: /config/constants.php
 */

// ==========================
// 🌍 General Configuration
// ==========================
define('APP_NAME', 'PRCF Aset PRO');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'philo');
define('APP_COPYRIGHT', '© ' . date('Y') . ' PRCF Indonesia');

// Base URL (sesuaikan dengan lokasi di server/localhost)
define('BASE_URL', 'http://localhost/prcf_aset_pro/');

// ==========================
// 🕒 Timezone & Locale
// ==========================
date_default_timezone_set('Asia/Jakarta');

// ==========================
// 🔐 User Roles
// ==========================
define('ROLE_ADMIN', 'admin');
define('ROLE_OPERATOR', 'operator');
define('ROLE_AUDITOR', 'auditor');

// ==========================
// 📦 Aset Status
// ==========================
define('KONDISI_BAIK', 'Baik');
define('KONDISI_RUSAK', 'Rusak');
define('KONDISI_HILANG', 'Hilang');

// ==========================
// ⚙️ Status Penggunaan
// ==========================
define('STATUS_AKTIF', 'Aktif');
define('STATUS_NONAKTIF', 'Tidak Aktif');

// ==========================
// 🔔 Notifikasi
// ==========================
define('NOTIF_PAJAK', 'Pajak Kendaraan');
define('NOTIF_PERAWATAN', 'Perawatan');
define('NOTIF_AUDIT', 'Audit');

// ==========================
// 🗃️ Folder Paths (relative)
// ==========================
define('PATH_ASSETS', BASE_URL . 'assets/');
define('PATH_MODULES', BASE_URL . 'modules/');
define('PATH_INCLUDES', BASE_URL . 'includes/');
define('PATH_CONFIG', BASE_URL . 'config/');

// ==========================
// 🧾 Default Pagination
// ==========================
define('ITEMS_PER_PAGE', 10);
?>
