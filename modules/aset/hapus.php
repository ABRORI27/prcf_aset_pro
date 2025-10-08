<?php include '../../includes/koneksi.php';
if (isset($_GET['id'])) { $id = intval($_GET['id']); $conn->query("DELETE FROM aset_barang WHERE id=$id"); }
header('Location: output_aset.php');