<?php
include '../../config/db.php';
include '../../includes/auth_check.php';


$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<script>alert('ID tidak ditemukan!');window.location='output_aset.php';</script>";
  exit;
}

$sql = "DELETE FROM aset_barang WHERE id = '$id'";
if (mysqli_query($conn, $sql)) {
  echo "<script>alert('Data aset berhasil dihapus!');window.location='read.php';</script>";
} else {
  echo "<script>alert('Gagal menghapus data: " . mysqli_error($conn) . "');window.location='output_aset.php';</script>";
}
?>
