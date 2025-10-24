<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
$id = (int)$id;

if (!$id) {
  echo "<script>alert('❌ ID program tidak ditemukan!');window.location='read.php';</script>";
  exit;
}

// 1️⃣ Kosongkan relasi di aset_barang
$clear_query = "UPDATE aset_barang SET program_pendanaan=NULL WHERE program_pendanaan='$id'";
if (!mysqli_query($conn, $clear_query)) {
  echo "<script>alert('⚠️ Gagal mengosongkan relasi aset: " . mysqli_error($conn) . "');window.location='read.php';</script>";
  exit;
}

// 2️⃣ Hapus data program dari tabel program_pendanaan
$delete_query = "DELETE FROM program_pendanaan WHERE id='$id'";
if (mysqli_query($conn, $delete_query)) {
  echo "<script>alert('✅ Program pendanaan berhasil dihapus!');window.location='read.php';</script>";
} else {
  echo "<script>alert('❌ Gagal menghapus program: " . mysqli_error($conn) . "');window.location='read.php';</script>";
}
?>
