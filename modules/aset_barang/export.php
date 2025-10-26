<?php
include '../../includes/koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Data_Aset_PRCF.xls");

echo "No\tNama Barang\tKategori\tKondisi\tLokasi\tProgram Pendanaan\n";
$no=1;
$q = mysqli_query($conn,"SELECT * FROM aset_barang ORDER BY id DESC");
while($d=mysqli_fetch_assoc($q)){
  echo "$no\t{$d['nama_barang']}\t{$d['kategori_barang']}\t{$d['kondisi_barang']}\t{$d['lokasi_barang']}\t{$d['program_pendanaan']}\n";
  $no++;
}
exit;
?>
