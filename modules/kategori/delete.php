<?php
include '../../includes/koneksi.php';

// Pastikan ada parameter ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  echo "<script>alert('❌ ID kategori tidak valid!'); window.location='read.php';</script>";
  exit;
}

$id = (int) $_GET['id'];

// Cek apakah data kategori ada
$cek = mysqli_query($conn, "SELECT * FROM kategori_barang WHERE id = $id");
if (mysqli_num_rows($cek) == 0) {
  echo "<script>alert('⚠️ Data kategori tidak ditemukan!'); window.location='read.php';</script>";
  exit;
}

// Hapus data
$hapus = mysqli_query($conn, "DELETE FROM kategori_barang WHERE id = $id");

if ($hapus) {
  echo "<script>alert('✅ Kategori berhasil dihapus!'); window.location='read.php';</script>";
} else {
  echo "<script>alert('❌ Gagal menghapus kategori: " . mysqli_error($conn) . "'); window.location='read.php';</script>";
}
?>
