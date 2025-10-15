<?php
include '../../includes/koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Data_Pegawai_PRCF.xls");

echo "No\tNama\tNIK\tJabatan\tUnit\tTanggal Masuk\n";
$no=1;
$q = mysqli_query($conn,"SELECT * FROM employees ORDER BY id DESC");
while($d=mysqli_fetch_assoc($q)){
  echo "$no\t{$d['nama']}\t{$d['nik']}\t{$d['jabatan']}\t{$d['unit']}\t{$d['tanggal_masuk']}\n";
  $no++;
}
exit;
?>
