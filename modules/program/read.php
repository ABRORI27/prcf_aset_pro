<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';
?>

<div class="page">
  <div class="header">
    <h2>Data Program Pendanaan</h2>
    <a href="create.php" class="btn">+ Tambah Program</a>
  </div>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Program</th>
          <th>Tahun Anggaran</th>
          <th>Keterangan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $result = mysqli_query($conn, "SELECT * FROM program_pendanaan ORDER BY tahun_anggaran DESC");
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
                  <td>{$no}</td>
                  <td>{$row['nama_program']}</td>
                  <td>{$row['tahun_anggaran']}</td>
                  <td>{$row['keterangan']}</td>
                  <td>
                    <a href='update.php?id={$row['id']}' class='btn'>Edit</a>
                    <a href='delete.php?id={$row['id']}' class='btn red' onclick='return confirm(\"Hapus program ini?\")'>Hapus</a>
                  </td>
                </tr>";
          $no++;
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
