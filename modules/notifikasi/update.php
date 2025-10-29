<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$id = (int)($_GET['id'] ?? 0);

$sql = "
  SELECT n.*, a.nama_barang 
  FROM notifikasi n
  LEFT JOIN aset_barang a ON n.aset_id = a.id
  WHERE n.id = $id
";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

if (!$data) {
  echo "<div class='alert red'>❌ Notifikasi tidak ditemukan!</div>";
  include '../../includes/footer.php';
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tipe = mysqli_real_escape_string($conn, $_POST['tipe_notifikasi']);
  $tgl = mysqli_real_escape_string($conn, $_POST['tanggal_notifikasi']);
  $status = mysqli_real_escape_string($conn, $_POST['status']);

  $update = "
    UPDATE notifikasi
    SET tipe_notifikasi='$tipe',
        tanggal_notifikasi='$tgl',
        status='$status'
    WHERE id='$id'
  ";

  if (mysqli_query($conn, $update)) {
    echo "<script>alert('✅ Notifikasi berhasil diupdate!');window.location='read.php';</script>";
  } else {
    echo "<div class='alert red'>❌ Gagal update: " . mysqli_error($conn) . "</div>";
  }
}
?>

<div class="page">
  <h2>Edit Notifikasi</h2>

  <form method="post" class="form-section">
    <label>Nama Aset</label>
    <input type="text" value="<?= htmlspecialchars($data['nama_barang']) ?>" disabled>

    <label>Tipe Notifikasi</label>
    <input type="text" name="tipe_notifikasi" value="<?= htmlspecialchars($data['tipe_notifikasi']) ?>" required>

    <label>Tanggal Notifikasi</label>
    <input type="date" name="tanggal_notifikasi" value="<?= htmlspecialchars($data['tanggal_notifikasi']) ?>" required>

    <label>Status</label>
    <select name="status" required>
      <option value="Belum Terkirim" <?= $data['status']=='Belum Terkirim'?'selected':''; ?>>
        Belum Terkirim
      </option>
      <option value="Terkirim" <?= $data['status']=='Terkirim'?'selected':''; ?>>
        Terkirim
      </option>
    </select>

    <button type="submit" class="btn">Update</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
