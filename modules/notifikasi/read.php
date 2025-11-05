<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';
?>

<div class="page">
  <div class="header">
    <h2>📢 Data Notifikasi Aset</h2>
    <a href="create.php" class="btn">+ Tambah Notifikasi</a>
  </div>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Aset</th>
          <th>Tipe Notifikasi</th>
          <th>Tanggal Notifikasi</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;

        // ✅ Query ambil notifikasi + nama aset
        $sql = "
          SELECT n.*, a.nama_barang 
          FROM notifikasi n
          LEFT JOIN aset_barang a ON n.aset_id = a.id
          ORDER BY n.tanggal_notifikasi DESC
        ";

        $result = mysqli_query($conn, $sql);

        if (!$result) {
          die('Query gagal: ' . mysqli_error($conn));
        }

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>" . htmlspecialchars($row['nama_barang']) . "</td>
                    <td>{$row['tipe_notifikasi']}</td>
                    <td>{$row['tanggal_notifikasi']}</td>
                    <td>
                      <span class='badge " . ($row['status'] == 'Terkirim' ? 'green' : 'gray') . "'>
                        {$row['status']}
                      </span>
                    </td>
                    <td>
                      <a href='update.php?id={$row['id']}' class='btn'>Edit</a>
                      <a href='delete.php?id={$row['id']}' class='btn red' onclick='return confirm(\"Hapus notifikasi ini?\")'>Hapus</a>
                    </td>
                  </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='6' class='text-center'>Belum ada data notifikasi.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
