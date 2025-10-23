<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$query = "
  SELECT 
    k.nomor_seri,
    k.aset_id,
    a.nama_barang AS nama_aset,
    k.nomor_plat,
    k.tanggal_pajak,
    k.penanggung_jawab
  FROM kendaraan k
  LEFT JOIN aset_barang a ON k.aset_id = a.id
  ORDER BY k.nomor_seri ASC
";

$result = mysqli_query($conn, $query);
?>

<div class="page">
  <div class="header">
    <h2>Data Kendaraan</h2>
    <a href="create.php" class="btn">+ Tambah Kendaraan</a>
  </div>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nomor Seri</th>
          <th>Nama Barang</th>
          <th>Nomor Plat</th>
          <th>Tanggal Pajak</th>
          <th>Penanggung Jawab</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
            <td><?= htmlspecialchars($row['nama_aset'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['nomor_plat'] ?? '-') ?></td>
            <td><?= $row['tanggal_pajak'] ? date('d-m-Y', strtotime($row['tanggal_pajak'])) : '-' ?></td>
            <td><?= htmlspecialchars($row['penanggung_jawab'] ?? '-') ?></td>
            <td>
              <a href="update.php?nomor_seri=<?= $row['nomor_seri'] ?>" class="btn-edit">Edit</a>
              <a href="delete.php?nomor_seri=<?= $row['nomor_seri'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>  
      <?php else: ?>
        <tr><td colspan="7" style="text-align:center;">Belum ada data kendaraan.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
