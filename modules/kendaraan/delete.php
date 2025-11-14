<?php
include '../../includes/header.php';
include '../../config/db.php';

// 🔐 CEK AKSES - Hanya Admin & Operator
if (!has_access([ROLE_ADMIN, ROLE_OPERATOR])) {
    $_SESSION['error'] = "Anda tidak memiliki akses ke modul ini.";
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}


$nomor_seri = $_GET['nomor_seri'] ?? null;

if (!$nomor_seri) {
  echo "<div class='alert red'>❌ Nomor seri kendaraan tidak ditemukan di URL.</div>";
  exit;
}

// Ambil data kendaraan dulu buat ambil aset_id
$stmt = $conn->prepare("SELECT aset_id FROM kendaraan WHERE nomor_seri = ? LIMIT 1");
$stmt->bind_param("s", $nomor_seri);
$stmt->execute();
$result = $stmt->get_result();
$kendaraan = $result->fetch_assoc();

if (!$kendaraan) {
  echo "<div class='alert red'>⚠️ Data kendaraan dengan nomor seri <b>$nomor_seri</b> tidak ditemukan.</div>";
  exit;
}

$aset_id = $kendaraan['aset_id'];

// Hapus dari kendaraan dulu
$stmtDelKendaraan = $conn->prepare("DELETE FROM kendaraan WHERE nomor_seri = ?");
$stmtDelKendaraan->bind_param("s", $nomor_seri);

if ($stmtDelKendaraan->execute()) {
  // Lanjut hapus dari aset_barang
  $stmtDelAset = $conn->prepare("DELETE FROM aset_barang WHERE id = ?");
  $stmtDelAset->bind_param("i", $aset_id);
  $stmtDelAset->execute();

  echo "<script>alert('✅ Data kendaraan dan aset terkait berhasil dihapus!');window.location='read.php';</script>";
  exit;
} else {
  echo "<div class='alert red'>❌ Gagal menghapus data kendaraan: " . $stmtDelKendaraan->error . "</div>";
}
?>
