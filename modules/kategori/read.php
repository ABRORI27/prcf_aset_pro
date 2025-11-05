<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

// Ambil filter dari URL (jika ada)
$filter = isset($_GET['filter']) ? (int)$_GET['filter'] : 0;

// Tentukan query sesuai filter
if ($filter > 0) {
  $query = mysqli_query($conn, "SELECT * FROM kategori_barang WHERE id = '$filter'");
} else {
  $query = mysqli_query($conn, "SELECT * FROM kategori_barang ORDER BY id ASC");
}

if (!$query) {
  die("Query gagal: " . mysqli_error($conn));
}
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
        while ($row = mysqli_fetch_assoc($query)) {
          echo "<tr>
                  <td>{$no}</td>
                  <td>{$row['nama_kategori']}</td>
                  <td>{$row['deskripsi']}</td>
                  <td>
                    <a href='update.php?id={$row['id']}' class='btn'>Edit</a>
                    <a href='delete.php?id={$row['id']}' class='btn red' onclick='return confirm(\"Hapus kategori ini?\")'>Hapus</a>
                  </td>
                </tr>";
          $no++;
        }

        if (mysqli_num_rows($query) == 0) {
          echo "<tr><td colspan='4' style='text-align:center;'>Tidak ada data kategori</td></tr>";
        }
      ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
