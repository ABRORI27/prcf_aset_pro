<?php 
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/auth_check.php';

// Cegah halaman diakses tanpa login
require_login();
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= APP_NAME ?> - Internal</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= PATH_ASSETS ?>css/style.css">
  <link rel="stylesheet" href="<?= PATH_ASSETS ?>css/dashboard.css">

  <!-- JS -->
  <script src="<?= PATH_ASSETS ?>js/main.js" defer></script>
</head>

<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">
      <img src="<?= PATH_ASSETS ?>img/logo.png" alt="logo PRCF">
      <label class="theme-switch">
        <input type="checkbox" id="modeSwitch">
        <span class="slider"></span>
      </label>
    </div>

    <nav>
      <a href="<?= BASE_URL ?>index.php">Dashboard</a>

      <?php if (has_access([ROLE_ADMIN, ROLE_OPERATOR])): ?>
        <a href="<?= PATH_MODULES ?>aset_barang/read.php">Aset Barang</a>
        <a href="<?= PATH_MODULES ?>kendaraan/read.php">Kendaraan</a>
        <a href="<?= PATH_MODULES ?>kategori/read.php">Kategori</a>
      <?php endif; ?>

      <?php if (has_access([ROLE_ADMIN])): ?>
        <a href="<?= PATH_MODULES ?>program/read.php">Program Pendanaan</a>
        <a href="<?= PATH_MODULES ?>lokasi/read.php">Lokasi Barang</a>
        <a href="<?= PATH_MODULES ?>notifikasi/read.php">Notifikasi</a>
      <?php endif; ?>

      <?php if (has_access([ROLE_AUDITOR])): ?>
        <a href="<?= PATH_MODULES ?>laporan/view_laporan.php">Laporan</a>
      <?php endif; ?>

      <a href="<?= BASE_URL ?>logout.php">
        Logout 
        <span class="role-label">
          (<?= htmlspecialchars($_SESSION['user']['role'] ?? 'User') ?>)
        </span>
      </a>
    </nav>
  </aside>

  <main class="main">
