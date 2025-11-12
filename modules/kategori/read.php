<?php
include '../../includes/header.php';
include '../../config/db.php';

// Ambil filter dari URL (jika ada)
$filter = isset($_GET['filter']) ? (int)$_GET['filter'] : 0;

// Query: jika filter kategori dipilih, tampilkan kategori itu beserta aset/barangnya
if ($filter > 0) {
  $query = mysqli_query($conn, "
    SELECT k.id AS kategori_barang, k.nama_kategori, k.deskripsi, a.nama_barang
    FROM kategori_barang k
    LEFT JOIN aset_barang a ON a.kategori_barang = k.id
    WHERE k.id = '$filter'
    ORDER BY a.nama_barang ASC
  ");
} else {
  // Jika tidak ada filter, tampilkan semua kategori beserta barangnya (jika ada)
  $query = mysqli_query($conn, "
    SELECT k.id AS kategori_barang, k.nama_kategori, k.deskripsi, a.nama_barang
    FROM kategori_barang k
    LEFT JOIN aset_barang a ON a.kategori_barang = k.id
    ORDER BY k.id ASC, a.nama_barang ASC
  ");
}

if (!$query) {
  die("Query gagal: " . mysqli_error($conn));
}

// Ambil semua hasil dalam array
$data = [];
while ($row = mysqli_fetch_assoc($query)) {
  $data[$row['kategori_barang']]['nama_kategori'] = $row['nama_kategori'];
  $data[$row['kategori_barang']]['barang'][] = $row['nama_barang'] ?: "-";
  $data[$row['kategori_barang']]['deskripsi'] = $row['deskripsi'];
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
          <th>Nama Barang / Aset</th>
          <th>Deskripsi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
      if (!empty($data)) {
        $no = 1;
        foreach ($data as $id => $kategori) {
          echo "<tr>
                  <td>{$no}</td>
                  <td>{$kategori['nama_kategori']}</td>
                  <td>";
                  
                  // tampilkan semua barang di kategori ini
                  if (!empty($kategori['barang'])) {
                    echo "<ul style='margin:0; padding-left:18px;'>";
                    foreach ($kategori['barang'] as $barang) {
                      echo "<li>{$barang}</li>";
                    }
                    echo "</ul>";
                  } else {
                    echo "-";
                  }
                  
                  echo "</td>
                  <td>{$kategori['deskripsi']}</td>
                  <td>
                  <a href='update.php?id={$id}' class='btn'>Edit</a>
                  <a href='delete.php?id={$id}' class='btn red' onclick='return confirm(\"Hapus kategori ini?\")'>Hapus</a>
                </td>
              </tr>";
          $no++;
        }
      } else {
        echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data kategori atau aset terkait</td></tr>";
      }
      ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
