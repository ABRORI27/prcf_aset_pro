<?php
include '../../includes/koneksi.php';
include '../../includes/auth_check.php';

if (!has_access(['Admin', 'Auditor'])) {
  echo "<script>alert('Anda tidak memiliki izin untuk mengekspor data!');window.location='output_aset.php';</script>";
  exit;
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Data_Aset_PRCF.xls");

echo "<table border='1'>
<tr>
  <th>No</th>
  <th>Nama Barang</th>
  <th>Deskripsi</th>
  <th>Kategori</th>
  <th>Kondisi</th>
  <th>Lokasi</th>
  <th>Program Pendanaan</th>
  <th>Jumlah Unit</th>
  <th>Harga Pembelian</th>
  <th>Penanggung Jawab</th>
</tr>";

$no = 1;
$q = mysqli_query($conn, "SELECT * FROM aset_barang ORDER BY id DESC");
while ($d = mysqli_fetch_assoc($q)) {
    echo "<tr>
    <td>{$no}</td>
    <td>{$d['nama_barang']}</td>
    <td>{$d['deskripsi']}</td>
    <td>{$d['kategori_barang']}</td>
    <td>{$d['kondisi_barang']}</td>
    <td>{$d['lokasi_barang']}</td>
    <td>{$d['program_pendanaan']}</td>
    <td>{$d['jumlah_unit']}</td>
    <td>{$d['harga_pembelian']}</td>
    <td>{$d['penanggung_jawab']}</td>
    </tr>";
$no++;
}
echo "</table>";
?>
