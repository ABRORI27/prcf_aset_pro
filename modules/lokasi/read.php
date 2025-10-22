<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';
?>

<div class="page">
  <div class="header">
    <h2>Data Lokasi Barang</h2>
    <a href="create.php" class="btn">+ Tambah Lokasi</a>
  </div>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Lokasi</th>
          <th>Alamat</th>
          <th>Penanggung Jawab</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $result = mysqli_query($conn, "SELECT * FROM lokasi_barang ORDER BY id ASC");
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
                  <td>{$no}</td>
                  <td>{$row['nama_lokasi']}</td>
                  <td>{$row['alamat']}</td>
                  <td>{$row['penanggung_jawab']}</td>
                  <td>
                    <a href='update.php?id={$row['id']}' class='btn'>Edit</a>
                    <a href='delete.php?id={$row['id']}' class='btn red' onclick='return confirm(\"Hapus lokasi ini?\")'>Hapus</a>
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
