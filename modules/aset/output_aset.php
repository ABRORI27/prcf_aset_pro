<?php
// Pastikan session aktif dan user punya akses
include '../../includes/auth_check.php';
include '../../includes/koneksi.php';
include '../../includes/header.php';

// Cek koneksi database
if (!isset($conn) || !$conn) {
  die("<div class='alert red'>❌ Koneksi database gagal!</div>");
}
?>

<div class="page">
  <div class="header">
    <h2>Data Aset</h2>

    <?php if (has_access(['Admin'])): ?>
      <a href="input_aset.php" class="btn">+ Tambah Aset</a>
    <?php endif; ?>

    <?php if (has_access(['Admin', 'Auditor', 'Operator'])): ?>
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
        $query = "SELECT * FROM aset_barang ORDER BY id DESC";
        $result = mysqli_query($conn, $query);

        if (!$result) {
          echo "<tr><td colspan='7' class='alert red'>Query error: " . mysqli_error($conn) . "</td></tr>";
        } elseif (mysqli_num_rows($result) === 0) {
          echo "<tr><td colspan='7' class='muted'>Belum ada data aset.</td></tr>";
        } else {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
              <td>{$no}</td>
              <td>{$row['nama_barang']}</td>
              <td>{$row['kategori_barang']}</td>
              <td>{$row['kondisi_barang']}</td>
              <td>{$row['lokasi_barang']}</td>
              <td>{$row['program_pendanaan']}</td>
              <td>";

            // 🔹 Role Operator = boleh Edit + Delete (tanpa Create)
            if (has_access(['Admin', 'Operator'])) {
              echo "
                <a href='edit_aset.php?id={$row['id']}' class='btn'>Edit</a>
                <a href='delete_aset.php?id={$row['id']}' class='btn red' onclick='return confirm(\"Yakin hapus data aset ini?\")'>Hapus</a>
              ";
            }

            // 🔹 Role Auditor & Admin = bisa lihat detail
            if (has_access(['Admin', 'Auditor'])) {
              echo " <a href='detail_aset.php?id={$row['id']}' class='btn'>Detail</a>";
            }

            echo "</td></tr>";
            $no++;
          }
        }
      ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
