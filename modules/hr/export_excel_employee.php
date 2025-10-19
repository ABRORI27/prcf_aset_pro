<?php
include '../../includes/koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Data_Pegawai_PRCF.xls");
header("Pragma: no-cache");
header("Expires: 0");

// ambil kata kunci pencarian dari URL (?search=...)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql = "SELECT * FROM employees";
if (!empty($search)) {
  $sql .= " WHERE nama LIKE '%$search%' 
            OR nik LIKE '%$search%' 
            OR jabatan LIKE '%$search%' 
            OR unit LIKE '%$search%'";
}
$sql .= " ORDER BY id DESC";

$q = mysqli_query($conn, $sql);

echo "
<h3 style='text-align:center;'>Data Pegawai PRCF Indonesia</h3>
<p style='text-align:center;'>Export hasil pencarian: <b>" . ($search ?: 'Semua Data') . "</b></p>

<table border='1' style='border-collapse:collapse; width:100%; text-align:left;'>
  <thead style='background-color:#d9ead3; font-weight:bold;'>
    <tr>
      <th>No</th>
      <th>Nama</th>
      <th>NIK</th>
      <th>Jabatan</th>
      <th>Unit</th>
      <th>Kontak</th>
      <th>Tanggal Masuk</th>
    </tr>
  </thead>
  <tbody>
";

$no = 1;
while ($d = mysqli_fetch_assoc($q)) {
  echo "
    <tr>
      <td>{$no}</td>
      <td>{$d['nama']}</td>
      <td>{$d['nik']}</td>
      <td>{$d['jabatan']}</td>
      <td>{$d['unit']}</td>
      <td>{$d['kontak']}</td>
      <td>{$d['tanggal_masuk']}</td>
    </tr>
  ";
  $no++;
}

echo "</tbody></table>";
exit;
?>
