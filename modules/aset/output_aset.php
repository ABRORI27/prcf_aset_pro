<?php include '../../includes/auth_check.php'; ?>

<div class="page">
  <div class="header">
    <h2>Data Aset</h2>
    <?php if (has_access(['Admin'])): ?>
      <a href="input_aset.php" class="btn">+ Tambah Aset</a>
    <?php endif; ?>

    <?php if (has_access(['Admin', 'Auditor'])): ?>
      <a href="export_excel.php" class="btn">Export Excel</a>
    <?php endif; ?>
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
        $q = mysqli_query($conn, "SELECT * FROM aset_barang ORDER BY id DESC");
        while ($row = mysqli_fetch_assoc($q)) {
          echo "<tr>
            <td>{$no}</td>
            <td>{$row['nama_barang']}</td>
            <td>{$row['kategori_barang']}</td>
            <td>{$row['kondisi_barang']}</td>
            <td>{$row['lokasi_barang']}</td>
            <td>{$row['program_pendanaan']}</td>
            <td>";

          // Tampilkan tombol hanya jika role mengizinkan
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
