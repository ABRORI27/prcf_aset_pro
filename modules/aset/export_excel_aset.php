<?php
include '../../includes/koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Data_Aset_PRCF.xls");
header("Pragma: no-cache");
header("Expires: 0");

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql = "SELECT * FROM aset_barang";
if (!empty($search)) {
  $sql .= " WHERE nama_barang LIKE '%$search%' 
            OR kategori_barang LIKE '%$search%' 
            OR lokasi_barang LIKE '%$search%' 
            OR kondisi_barang LIKE '%$search%' 
            OR program_pendanaan LIKE '%$search%'";
}
$sql .= " ORDER BY id DESC";

$q = mysqli_query($conn, $sql);

echo "
<h3 style='text-align:center;'>Data Aset Barang PRCF Indonesia</h3>
<p style='text-align:center;'>Export hasil pencarian: <b>" . ($search ?: 'Semua Data') . "</b></p>

<table border='1' style='border-collapse:collapse; width:100%; text-align:left;'>
  <thead style='background-color:#c9daf8; font-weight:bold;'>
    <tr>
      <th>No</th>
      <th>Nama Barang</th>
      <th>Kategori</th>
      <th>Kondisi</th>
      <th>Lokasi</th>
      <th>Program Pendanaan</th>
      <th>Jumlah Unit</th>
      <th>Penanggung Jawab</th>
      <th>Tanggal Perolehan</th>
    </tr>
  </thead>
  <tbody>
";

$no = 1;
while ($d = mysqli_fetch_assoc($q)) {
  echo "
    <tr>
      <td>{$no}</td>
      <td>{$d['nama_barang']}</td>
      <td>{$d['kategori_barang']}</td>
      <td>{$d['kondisi_barang']}</td>
      <td>{$d['lokasi_barang']}</td>
      <td>{$d['program_pendanaan']}</td>
      <td>{$d['jumlah_unit']}</td>
      <td>{$d['penanggung_jawab']}</td>
      <td>{$d['waktu_perolehan']}</td>
    </tr>
  ";
  $no++;
}

echo "</tbody></table>";
exit;
?>
