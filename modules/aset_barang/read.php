<?php
include '../../includes/auth_check.php';
include '../../config/db.php';
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Data Aset - PRCF Indonesia</title>
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
<div class="page">
  <div class="header">
    <div class="header-left">
      <h2>Data Aset</h2>
    </div>

    <!-- Tombol aksi -->
    <div class="actions">
      <!-- Tombol Kembali ke Dashboard -->
      <a href="../../index.php" class="btn btn-secondary">⬅ Kembali ke Dashboard</a>

      <?php if (has_access(['Admin'])): ?>
        <a href="create.php" class="btn">+ Tambah Aset</a>
      <?php endif; ?>

      <?php if (has_access(['Admin', 'Auditor'])): ?>
        <a href="export_excel_aset.php" class="btn">Export Excel</a>
      <?php endif; ?>

      <input type="text" id="searchInput" onkeyup="filterTable('tabelAset', this.value)" placeholder="Cari aset...">
    </div>
  </div>

  <div class="card">
    <table class="table" id="tabelAset">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Barang</th>
          <th>Kategori</th>
          <th>Kondisi</th>
          <th>Lokasi</th>
          <th>Program</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $no = 1;
        $result = mysqli_query($conn, "SELECT * FROM aset_barang ORDER BY id DESC");
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
            <td>{$no}</td>
            <td>{$row['nama_barang']}</td>
            <td>{$row['kategori_id']}</td>
            <td>{$row['kondisi_barang']}</td>
            <td>{$row['lokasi_id']}</td>
            <td>{$row['program_pendanaan']}</td>
            <td>";

          if (has_access(['Admin', 'Operator'])) {
            echo "
              <a href='edit_aset.php?id={$row['id']}' class='btn'>Edit</a>
              <a href='delete_aset.php?id={$row['id']}' class='btn' onclick='return confirm(\"Hapus data ini?\")'>Hapus</a>
            ";
          }

          if (has_access(['Admin', 'Auditor'])) {
            echo " <a href='detail_aset.php?id={$row['id']}' class='btn'>Detail</a>";
          }

          echo "</td></tr>";
          $no++;
        }
      ?>
      </tbody>
    </table>
  </div>
</div>

<script src="../../assets/js/main.js"></script>
</body>
</html>
