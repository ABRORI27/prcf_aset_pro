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
          <th>Program Pendanaan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $no = 1;
        // 🔹 Gunakan JOIN agar menampilkan nama kategori, lokasi, dan program
        $query = "
          SELECT ab.*, 
                 k.nama_kategori, 
                 l.nama_lokasi, 
                 p.nama_program
          FROM aset_barang ab
          LEFT JOIN kategori_barang k ON ab.kategori_barang = k.id
          LEFT JOIN lokasi_barang l ON ab.lokasi_barang = l.id
          LEFT JOIN program_pendanaan p ON ab.program_pendanaan = p.id
          ORDER BY ab.id DESC
        ";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
            <td>{$no}</td>
            <td>{$row['nama_barang']}</td>
            <td>" . ($row['nama_kategori'] ?? '-') . "</td>
            <td>{$row['kondisi_barang']}</td>
            <td>" . ($row['nama_lokasi'] ?? '-') . "</td>
            <td>" . ($row['nama_program'] ?? '-') . "</td>
            <td>";

          // 🔹 Aksi tergantung role pengguna
          if (has_access(['Admin', 'Operator'])) {
            echo "
              <a href='update.php?id={$row['id']}' class='btn'>Edit</a>
              <a href='delete.php?id={$row['id']}' class='btn' onclick='return confirm(\"Hapus data ini?\")'>Hapus</a>
            ";
          }

          if (has_access(['Admin', 'Auditor'])) {
            echo " <a href='detail.php?id={$row['id']}' class='btn'>Detail</a>";
          }

          echo "</td></tr>";
          $no++;
        }
      ?>
      </tbody>
    </table>
  </div>
</div>

<script src='../../assets/js/main.js'></script>
</body>
</html>
