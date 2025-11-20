<?php
include '../../includes/header.php';
include '../../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID tidak ditemukan!</div>";
  exit;
}

$q = mysqli_query($conn, "SELECT k.*, a.nama_barang, a.kode_barang, a.harga_pembelian 
                          FROM kendaraan k 
                          JOIN aset_barang a ON k.aset_id = a.id
                          WHERE k.id='$id'");
$data = mysqli_fetch_assoc($q);
?>

<div class="page">
  <div class="header">
    <h2>Detail Kendaraan</h2>
    <a href="read.php" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Kembali
    </a>
  </div>
  <div class="card">
    <table class="table">
      <tr><th>Nama Aset</th><td><?= $data['nama_barang'] ?></td></tr>
      <tr><th>Kode Barang</th><td><?= $data['kode_barang'] ?></td></tr>
      <tr><th>Nomor Plat</th><td><?= $data['nomor_plat'] ?></td></tr>
      <tr><th>Tanggal Pajak</th><td><?= $data['tanggal_pajak'] ?></td></tr>
      <tr><th>Penanggung Jawab</th><td><?= $data['penanggung_jawab'] ?></td></tr>
      <tr><th>Harga Pembelian</th><td>Rp <?= number_format($data['harga_pembelian'], 0, ',', '.') ?></td></tr>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
