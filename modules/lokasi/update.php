<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID lokasi tidak ditemukan!</div>";
  exit;
}

$q = mysqli_query($conn, "SELECT * FROM lokasi_barang WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
  echo "<div class='alert red'>Data lokasi tidak ditemukan!</div>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_lokasi = trim($_POST['nama_lokasi']);
  $alamat = trim($_POST['alamat']);
  $penanggung_jawab = trim($_POST['penanggung_jawab']);

  $sql = "UPDATE lokasi_barang 
          SET nama_lokasi='$nama_lokasi', alamat='$alamat', penanggung_jawab='$penanggung_jawab'
          WHERE id='$id'";

  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('✅ Data lokasi berhasil diperbarui!');window.location='read.php';</script>";
  } else {
    echo "<div class='alert red'>❌ Gagal update: " . mysqli_error($conn) . "</div>";
  }
}
?>

<div class="page">
  <h2>Edit Data Lokasi</h2>
  <form method="post" class="form-section">
    <label>Nama Lokasi</label>
    <input type="text" name="nama_lokasi" value="<?= htmlspecialchars($data['nama_lokasi']) ?>" required>

    <label>Alamat Lengkap</label>
    <textarea name="alamat" rows="3"><?= htmlspecialchars($data['alamat']) ?></textarea>

    <label>Penanggung Jawab</label>
    <input type="text" name="penanggung_jawab" value="<?= htmlspecialchars($data['penanggung_jawab']) ?>">

    <button type="submit" class="btn">Simpan Perubahan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
