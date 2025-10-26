<?php
include '../../includes/auth_check.php';
include '../../includes/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = trim($_POST['nama']);
  $nik = trim($_POST['nik']);
  $jabatan = trim($_POST['jabatan']);
  $unit = trim($_POST['unit']);
  $kontak = trim($_POST['kontak']);
  $tanggal_masuk = $_POST['tanggal_masuk'];

  // Cek apakah NIK sudah ada
  $chk = $conn->prepare("SELECT id FROM employees WHERE nik = ?");
  $chk->bind_param("s", $nik);
  $chk->execute();
  $chk->store_result();

  if ($chk->num_rows > 0) {
    $error = "⚠️ NIK sudah terdaftar di sistem!";
  } else {
    $stmt = $conn->prepare("INSERT INTO employees (nama, nik, jabatan, unit, kontak, tanggal_masuk) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $nik, $jabatan, $unit, $kontak, $tanggal_masuk);

    if ($stmt->execute()) {
      echo "<script>alert('✅ Data pegawai berhasil ditambahkan!');window.location='output_employee.php';</script>";
      exit;
    } else {
      $error = "Gagal menyimpan data: " . $stmt->error;
    }
  }
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Tambah Pegawai - PRCF Indonesia</title>
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
<div class="page">
  <div class="header">
    <h2>Tambah Pegawai</h2>
    <a href="output_employee.php" class="btn">← Kembali</a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert red"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" class="form-section">
    <label>Nama Lengkap</label>
    <input type="text" name="nama" placeholder="Masukkan nama lengkap pegawai" required>

    <label>NIK</label>
    <input type="text" name="nik" placeholder="Nomor Induk Karyawan" required>

    <label>Jabatan</label>
    <input type="text" name="jabatan" placeholder="Misal: Operator, Admin, Auditor" required>

    <label>Unit Kerja</label>
    <input type="text" name="unit" placeholder="Kantor Pusat / Cabang Sintang / Kapuas Hulu" required>

    <label>Kontak</label>
    <input type="text" name="kontak" placeholder="Nomor WhatsApp / Telepon">

    <label>Tanggal Masuk</label>
    <input type="date" name="tanggal_masuk" required>

    <button class="btn" type="submit">💾 Simpan Data</button>
  </form>
</div>

<script src="../../assets/js/main.js"></script>
</body>
</html>
