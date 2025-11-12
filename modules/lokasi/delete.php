<?php
include '../../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
  echo "<script>alert('⚠️ ID lokasi tidak valid!');window.location='read.php';</script>";
  exit;
}

// Cek apakah data lokasi ada
$cek = mysqli_query($conn, "SELECT * FROM lokasi_barang WHERE id='$id'");
if (mysqli_num_rows($cek) === 0) {
  echo "<script>alert('❌ Data lokasi tidak ditemukan!');window.location='read.php';</script>";
  exit;
}

// 1️⃣ Kosongkan lokasi_id pada aset_barang yang pakai lokasi ini
mysqli_query($conn, "UPDATE aset_barang SET lokasi_id=NULL WHERE lokasi_id='$id'");

// 2️⃣ Hapus data lokasi dari tabel lokasi_barang
if (mysqli_query($conn, "DELETE FROM lokasi_barang WHERE id='$id'")) {
  echo "<script>alert('✅ Lokasi berhasil dihapus dan aset terkait sudah dilepaskan dari lokasi!');window.location='read.php';</script>";
} else {
  $error = addslashes(mysqli_error($conn));
  echo "<script>alert('❌ Gagal menghapus lokasi: {$error}');window.location='read.php';</script>";
}
?>
