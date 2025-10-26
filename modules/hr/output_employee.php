<?php
include '../../includes/auth_check.php';
include '../../includes/koneksi.php';
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Data Pegawai - PRCF Indonesia</title>
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>

<div class="page">
  <div class="header">
    <h2>Data Pegawai</h2>
    <div class="action-buttons">
      <!-- Tombol Kembali ke Dashboard -->
      <a href="../../index.php" class="btn btn-secondary">⬅ Kembali ke Dashboard</a>
      

      <!-- Tombol Tambah -->
      <?php if (has_access(['Admin'])): ?>
        <a href="input_employee.php" class="btn">+ Tambah</a>
      <?php endif; ?>


      <!-- Tombol Export Excel -->
      <?php if (has_access(['Admin', 'Operator', 'Auditor'])): ?>
        <a href="export_excel_employee.php" class="btn">Export Excel</a>
      <?php endif; ?>

      <!-- Kolom Pencarian -->
      <input type="text" id="searchPegawai" placeholder="Cari pegawai..." class="search-box" onkeyup="filterTable('tabelPegawai', this.value)">
    </div>
  </div>

  <div class="card">
    <table class="table" id="tabelPegawai">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>NIK</th>
          <th>Jabatan</th>
          <th>Unit</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $q = mysqli_query($conn, "SELECT * FROM employees ORDER BY id DESC");
        while ($row = mysqli_fetch_assoc($q)) {
          echo "<tr>
            <td>{$no}</td>
            <td>{$row['nama']}</td>
            <td>{$row['nik']}</td>
            <td>{$row['jabatan']}</td>
            <td>{$row['unit']}</td>
            <td>";

          // Role-based tombol aksi
          if (has_access(['Admin', 'Operator'])) {
            echo "
              <a href='edit_employee.php?id={$row['id']}' class='btn'>Edit</a>
              <a href='delete_employee.php?id={$row['id']}' class='btn' onclick='return confirm(\"Yakin hapus data ini?\")'>Hapus</a>
            ";
          }

          if (has_access(['Admin', 'Auditor'])) {
            echo " <a href='detail_employee.php?id={$row['id']}' class='btn'>Detail</a>";
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
