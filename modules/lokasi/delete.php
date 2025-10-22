<?php
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<script>alert('ID tidak ditemukan!');window.location='read.php';</script>";
  exit;
}

// Cek apakah lokasi masih dipakai oleh aset
$cek = mysqli_query($conn, "SELECT COUNT(*) AS total FROM aset_barang WHERE lokasi_id='$id'");
$row = mysqli_fetch_assoc($cek);
if ($row['total'] > 0) {
  echo "<script>alert('❌ Tidak bisa dihapus, lokasi masih digunakan di tabel aset!');window.location='read.php';</script>";
  exit;
}

if (mysqli_query($conn, "DELETE FROM lokasi_barang WHERE id='$id'")) {
  echo "<script>alert('✅ Lokasi berhasil dihapus!');window.location='read.php';</script>";
} else {
  echo "<script>alert('❌ Gagal menghapus data: " . mysqli_error($conn) . "');window.location='read.php';</script>";
}
?>
