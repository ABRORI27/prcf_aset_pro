<?php
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
$id = (int)$id;

if (!$id) {
  echo "<script>alert('❌ ID program tidak ditemukan!');window.location='read.php';</script>";
  exit;
}

// 1️⃣ Kosongkan relasi program_id di aset_barang
$clear_query = "UPDATE aset_barang SET program_id=NULL WHERE program_id='$id'";
if (!mysqli_query($conn, $clear_query)) {
  echo "<script>alert('⚠️ Gagal mengosongkan relasi aset: " . mysqli_error($conn) . "');window.location='read.php';</script>";
  exit;
}

// 2️⃣ Hapus data dari program_pendanaan
$delete_query = "DELETE FROM program_pendanaan WHERE id='$id'";
if (mysqli_query($conn, $delete_query)) {
  echo "<script>alert('✅ Program pendanaan berhasil dihapus!');window.location='read.php';</script>";
} else {
  echo "<script>alert('❌ Gagal menghapus program: " . mysqli_error($conn) . "');window.location='read.php';</script>";
}
?>
