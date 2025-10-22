<?php
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<script>alert('ID tidak ditemukan!');window.location='read.php';</script>";
  exit;
}

// Cegah penghapusan jika program masih dipakai oleh aset
$cek = mysqli_query($conn, "SELECT COUNT(*) AS total FROM aset_barang WHERE program_id='$id'");
$row = mysqli_fetch_assoc($cek);
if ($row['total'] > 0) {
  echo "<script>alert('❌ Program ini masih digunakan oleh aset, tidak bisa dihapus!');window.location='read.php';</script>";
  exit;
}

if (mysqli_query($conn, "DELETE FROM program_pendanaan WHERE id='$id'")) {
  echo "<script>alert('✅ Program berhasil dihapus!');window.location='read.php';</script>";
} else {
  echo "<script>alert('❌ Gagal menghapus data: " . mysqli_error($conn) . "');window.location='read.php';</script>";
}
?>
