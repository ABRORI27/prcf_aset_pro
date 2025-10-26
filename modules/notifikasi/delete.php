<?php
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;

if (!$id) {
  echo "<script>alert('❌ ID notifikasi tidak ditemukan!');window.location='read.php';</script>";
  exit;
}

if (mysqli_query($conn, "DELETE FROM notifikasi WHERE id='$id'")) {
  echo "<script>alert('✅ Notifikasi berhasil dihapus!');window.location='read.php';</script>";
} else {
  echo "<script>alert('❌ Gagal menghapus notifikasi: " . mysqli_error($conn) . "');window.location='read.php';</script>";
}
?>
