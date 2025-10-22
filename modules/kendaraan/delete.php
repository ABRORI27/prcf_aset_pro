<?php
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<script>alert('ID tidak ditemukan!');window.location='read.php';</script>";
  exit;
}

if (mysqli_query($conn, "DELETE FROM kendaraan WHERE id='$id'")) {
  echo "<script>alert('✅ Kendaraan berhasil dihapus!');window.location='read.php';</script>";
} else {
  echo "<script>alert('❌ Gagal menghapus data!');window.location='read.php';</script>";
}
?>
