<?php
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<script>alert('ID tidak ditemukan!');window.location='read.php';</script>";
  exit;
}

$sql = "DELETE FROM kategori_barang WHERE id='$id'";
if (mysqli_query($conn, $sql)) {
  echo "<script>alert('✅ Kategori berhasil dihapus!');window.location='read.php';</script>";
} else {
  echo "<script>alert('❌ Gagal menghapus kategori: " . mysqli_error($conn) . "');window.location='read.php';</script>";
}
?>
