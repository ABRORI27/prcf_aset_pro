<?php 
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/auth_check.php';

// Cegah halaman diakses tanpa login
require_login();

// Ambil data kategori untuk dropdown
$kategori_result = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori_barang ORDER BY nama_kategori ASC");
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

  <style>
    /* Tambahan minimal agar dropdown sidebar rapi */
    .dropdown-container {
      display: none;
      background-color: var(--panel-light);
      padding-left: 20px;
      border-left: 3px solid var(--accent);
      transition: all 0.3s ease;
    }

    body:not(.light-mode) .dropdown-container {
      background-color: var(--panel-dark);
      color: var(--text-dark);
    }

    .dropdown-btn {
      cursor: pointer;
      display: block;
      padding: 10px 15px;
      color: var(--text-light);
      text-decoration: none;
      transition: background 0.3s ease;
      background-color: var(--accent);
    }

    .dropdown-btn:hover {
      background-color: #102a23;
      color: #fff;
    }

    .dropdown-container a {
      display: block;
      padding: 8px 15px;
      text-decoration: none;
      color: var(--text-light);
      transition: background 0.3s;
    }

    .dropdown-container a:hover {
      background-color: rgba(0, 0, 0, 0.1);
    }

    .arrow {
      float: right;
      transition: transform 0.3s;
    }

    .arrow.open {
      transform: rotate(90deg);
    }
  </style>
  <!-- FONT AWESOME -->
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

  <!-- ASET BARANG - Semua Role Bisa Akses -->
  <a href="<?= PATH_MODULES ?>aset_barang/read.php">Aset Barang</a>

  <!-- KENDARAAN - Semua Role Bisa Lihat -->
  <a href="<?= PATH_MODULES ?>kendaraan/read.php">Kendaraan</a>

  <?php if (has_access([ROLE_ADMIN, ROLE_OPERATOR])): ?>
    <!-- KATEGORI - Hanya Admin & Operator Bisa Kelola -->
    <button class="dropdown-btn" onclick="toggleDropdown(this)">
      Kategori <span class="arrow">▶</span>
    </button>
    <div class="dropdown-container">
      <a href="<?= PATH_MODULES ?>kategori/read.php">Semua Kategori</a>
      <?php while ($kat = mysqli_fetch_assoc($kategori_result)): ?>
        <a href="<?= PATH_MODULES ?>kategori/read.php?filter=<?= $kat['id'] ?>">
          <?= htmlspecialchars($kat['nama_kategori']) ?>
        </a>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>

  <?php if (has_access([ROLE_ADMIN])): ?>
    <!-- HANYA ADMIN -->
    <a href="<?= PATH_MODULES ?>program/read.php">Program Pendanaan</a>
    <a href="<?= PATH_MODULES ?>lokasi/read.php">Lokasi Barang</a>
    <a href="<?= PATH_MODULES ?>notifikasi/read.php">Notifikasi</a>
  <?php endif; ?>

  <!-- LAPORAN - Semua Role -->
  <a href="<?= PATH_MODULES ?>laporan/view_laporan.php">Laporan</a>

  <a href="<?= BASE_URL ?>logout.php">
    Logout
    <span class="role-label">
      (<?= htmlspecialchars($_SESSION['user']['username'] ?? 'User') ?> - <?= htmlspecialchars($_SESSION['user']['role'] ?? '-') ?>)
    </span>
  </a>
</nav>
  </aside>

  <main class="main">

<script>
  // Fungsi buka/tutup dropdown sidebar
  function toggleDropdown(btn) {
    const dropdown = btn.nextElementSibling;
    const arrow = btn.querySelector('.arrow');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    arrow.classList.toggle('open');
  }
</script>
