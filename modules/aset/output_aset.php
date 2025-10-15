<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';
$result = mysqli_query($conn, "SELECT * FROM aset_barang ORDER BY id DESC");
?>

<div class="page">
  <div class="header">
    <h2>Data Aset Barang</h2>
    <a href="input_aset.php" class="btn">+ Tambah Aset</a>
  </div>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Barang</th>
          <th>Kategori</th>
          <th>Lokasi</th>
          <th>Kondisi</th>
          <th>Penanggung Jawab</th>
          <th>Pajak</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($row=mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= $row['nama_barang']; ?></td>
          <td><?= $row['kategori_barang']; ?></td>
          <td><?= $row['lokasi_barang']; ?></td>
          <td><?= $row['kondisi_barang']; ?></td>
          <td><?= $row['penanggung_jawab']; ?></td>
          <td><?= $row['tanggal_pajak']; ?></td>
          <td>
            <a href="edit_aset.php?id=<?= $row['id']; ?>" class="btn">Edit</a>
            <a href="delete_aset.php?id=<?= $row['id']; ?>" class="btn" style="background:red;">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
