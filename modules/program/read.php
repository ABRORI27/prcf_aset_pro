<?php
include '../../includes/header.php';
include '../../config/db.php';
?>

<div class="page">
  <div class="header">
    <h2>Data Program Pendanaan</h2>
    <a href="create.php" class="btn">+ Tambah Program</a>
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
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
