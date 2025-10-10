<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>PRCF - Internal</title>
<link rel="stylesheet" href="/prcf_aset_pro/assets/css/style.css">
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