<?php
include '../../includes/header.php';
include '../../config/db.php';
?>

<div class="page">
  <div class="header">
    <h2>📢 Data Notifikasi Aset</h2>
    <a href="create.php" class="btn">+ Tambah Notifikasi</a>
  </div>
<style>
      .icon-btn {
      text-decoration: none;
      font-size: 16px;
      margin-right: 8px;
      padding: 6px;
      border-radius: 4px;
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
    }

    .icon-btn:hover {
      background-color: #e9ecef;
      transform: scale(1.1);
    }

    .edit-btn { color: #28a745; }
    .delete-btn { color: #dc3545; }
    /* .detail-btn { color: #17a2b8; } */
</style>
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
                      <a href='update.php?id={$row['id']}' class='icon-btn edit-btn' title='Edit Data'>
                  <i class='fas fa-edit'></i>
                </a>
                <a href='delete.php?id={$row['id']}' class='icon-btn delete-btn' title='Hapus Data' onclick='return confirm(\"Hapus data ini?\")'>
                  <i class='fas fa-trash'></i>
                </a>
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
