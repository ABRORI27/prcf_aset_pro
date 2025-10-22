<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID notifikasi tidak ditemukan!</div>";
  exit;
}

$q = mysqli_query($conn, "SELECT * FROM notifikasi WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tipe = $_POST['tipe_notifikasi'];
  $tanggal = $_POST['tanggal_notifikasi'];
  $status = $_POST['status'];

  $sql = "UPDATE notifikasi 
          SET tipe_notifikasi='$tipe', tanggal_notifikasi='$tanggal', status='$status'
          WHERE id='$id'";

  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('✅ Data notifikasi berhasil diperbarui!');window.location='read.php';</script>";
  } else {
    echo "<div class='alert red'>❌ Gagal memperbarui data: " . mysqli_error($conn) . "</div>";
  }
}
?>

<div class="page">
  <h2>Edit Notifikasi</h2>
  <form method="post" class="form-section">

    <label>Tipe Notifikasi</label>
    <select name="tipe_notifikasi" required>
      <option value="Pajak Kendaraan" <?= $data['tipe_notifikasi']=='Pajak Kendaraan'?'selected':'' ?>>Pajak Kendaraan</option>
      <option value="Perawatan" <?= $data['tipe_notifikasi']=='Perawatan'?'selected':'' ?>>Perawatan</option>
      <option value="Audit" <?= $data['tipe_notifikasi']=='Audit'?'selected':'' ?>>Audit</option>
    </select>

    <label>Tanggal Notifikasi</label>
    <input type="date" name="tanggal_notifikasi" value="<?= $data['tanggal_notifikasi'] ?>" required>

    <label>Status</label>
    <select name="status">
      <option value="Belum Terkirim" <?= $data['status']=='Belum Terkirim'?'selected':'' ?>>Belum Terkirim</option>
      <option value="Terkirim" <?= $data['status']=='Terkirim'?'selected':'' ?>>Terkirim</option>
    </select>

    <button type="submit" class="btn">Simpan Perubahan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
