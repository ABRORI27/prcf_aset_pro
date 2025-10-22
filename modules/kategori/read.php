<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';
?>

<div class="page">
  <div class="header">
    <h2>Data Kategori Barang</h2>
    <a href="create.php" class="btn">+ Tambah Kategori</a>
  </div>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Kategori</th>
          <th>Deskripsi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $no = 1;
        $query = mysqli_query($conn, "SELECT * FROM kategori_barang ORDER BY kategori_id ASC");
        while ($row = mysqli_fetch_assoc($query)) {
          echo "<tr>
                  <td>{$no}</td>
                  <td>{$row['nama_kategori']}</td>
                  <td>{$row['deskripsi']}</td>
                  <td>
                    <a href='update.php?id={$row['kategori_id']}' class='btn'>Edit</a>
                    <a href='delete.php?id={$row['kategori_id']}' class='btn red' onclick='return confirm(\"Hapus kategori ini?\")'>Hapus</a>
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
