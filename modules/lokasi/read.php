<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';
?>

<div class="page">
  <div class="header">
    <h2>📦 Data Lokasi Barang</h2>
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
          <!-- <th>Barang Tersimpan</th> -->
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;

        // ✅ Perbaikan di sini
        $sql = "
          SELECT l.*, 
                 COUNT(a.id) AS total_barang,
                 GROUP_CONCAT(a.nama_barang SEPARATOR ', ') AS daftar_barang
          FROM lokasi_barang l
          LEFT JOIN aset_barang a ON a.lokasi_barang = l.id
          GROUP BY l.id
          ORDER BY l.id ASC
        ";

        $result = mysqli_query($conn, $sql);

        if (!$result) {
          die('Query gagal: ' . mysqli_error($conn));
        }

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['nama_lokasi']}</td>
                    <td>{$row['alamat']}</td>
                    <td>{$row['penanggung_jawab']}</td>
                    <td>";
            // if ($row['total_barang'] > 0) {
            //   echo "<span class='badge green'>{$row['total_barang']} barang</span><br>
            //         <small>" . htmlspecialchars($row['daftar_barang']) . "</small>";
            // } else {
            //   echo "<span class='badge gray'>Belum ada barang</span>";
            // }
            echo "</td>
                    <td>
                      <a href='update.php?id={$row['id']}' class='btn'>Edit</a>
                      <a href='delete.php?id={$row['id']}' class='btn red' onclick='return confirm(\"Yakin hapus lokasi ini?\")'>Hapus</a>
                    </td>
                  </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='6' class='text-center'>Belum ada data lokasi.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
