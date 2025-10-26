<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$sql = "SELECT n.*, a.nama_barang, a.kode_barang 
        FROM notifikasi n
        LEFT JOIN aset_barang a ON n.aset_id = a.id
        ORDER BY n.tanggal_notifikasi ASC";
$result = mysqli_query($conn, $sql);
?>

<div class="page">
  <div class="header">
    <h2>Daftar Notifikasi Aset</h2>
    <a href="create.php" class="btn">+ Tambah Notifikasi</a>
  </div>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Kode Barang</th>
          <th>Nama Barang</th>
          <th>Tipe Notifikasi</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $today = new DateTime();

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $tgl = new DateTime($row['tanggal_notifikasi']);
            $diff = $today->diff($tgl)->days;
            $color = ($tgl < $today) ? 'red' : (($diff <= 7) ? 'yellow' : 'green');

            $nama_barang = $row['nama_barang'] ?? '<i>Data aset dihapus</i>';
            $kode_barang = $row['id'] ?? '-';

            echo "<tr>
                    <td>{$no}</td>
                    <td>{$kode_barang}</td>
                    <td>{$nama_barang}</td>
                    <td>{$row['tipe_notifikasi']}</td>
                    <td>{$row['tanggal_notifikasi']}</td>
                    <td><span class='alert {$color}'>{$row['status']}</span></td>
                    <td>
                      <a href='update.php?id={$row['id']}' class='btn'>Edit</a>
                      <a href='delete.php?id={$row['id']}' class='btn red' onclick='return confirm(\"Hapus notifikasi ini?\")'>Hapus</a>
                    </td>
                  </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='7' style='text-align:center;'>Belum ada notifikasi.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
