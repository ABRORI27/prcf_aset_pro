<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'auth_check.php'; // pastikan sudah ada auth_check
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>PRCF - Internal System</title>
<link rel="stylesheet" href="/prcf_aset_pro/assets/css/dashboard.css">
<script src="/prcf_aset_pro/assets/js/main.js" defer></script>
</head>
<body>
<div class="app">
<aside class="sidebar">
  <div class="brand">
    <img src="/prcf_aset_pro/assets/img/logo.png" alt="logo PRCF">
    <label class="theme-switch">
      <input type="checkbox" id="modeSwitch">
      <span class="slider"></span>
    </label>
  </div>
  <nav>
    <a href="/prcf_aset_pro/index.php">Dashboard</a>
    <a href="/prcf_aset_pro/modules/aset/output_aset.php">Aset</a>
    <a href="/prcf_aset_pro/modules/hr/output_employee.php">HR</a>
    <a href="/prcf_aset_pro/logout.php">Logout</a>
  </nav>
</aside>

<main class="main">
  <div class="header">
    <h2>Dashboard</h2>

    <div class="user-info">
      <?php if (isset($_SESSION['user'])): ?>
        <?php
          $role = $_SESSION['user']['role'];
          $roleColor = match ($role) {
            'Admin' => '#2b6b4f',
            'Operator' => '#006fbf',
            'Auditor' => '#a15d00',
            default => '#666'
          };
        ?>
        <span class="role-badge" style="background: <?= $roleColor ?>;">
          🧾 Anda login sebagai <strong><?= htmlspecialchars($role) ?></strong>
        </span>
        <a href="/prcf_aset_pro/logout.php" class="btn logout-btn">Logout</a>
      <?php else: ?>
        <a href="/prcf_aset_pro/login.php" class="btn">Login</a>
      <?php endif; ?>
    </div>
  </div>
