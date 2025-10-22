<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID kendaraan tidak ditemukan.</div>";
  exit;
}

$q = mysqli_query($conn, "SELECT * FROM kendaraan WHERE id='$id'");
$kendaraan = mysqli_fetch_assoc($q);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nomor_plat = $_POST['nomor_plat'];
  $tanggal_pajak = $_POST['tanggal_pajak'];
  $penanggung_jawab = $_POST['penanggung_jawab'];

  $sql = "UPDATE kendaraan 
          SET nomor_plat='$nomor_plat', tanggal_pajak='$tanggal_pajak', penanggung_jawab='$penanggung_jawab'
          WHERE id='$id'";

  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('✅ Data kendaraan berhasil diperbarui!');window.location='read.php';</script>";
  } else {
    echo "<div class='alert red'>❌ Gagal update: " . mysqli_error($conn) . "</div>";
  }
}
?>

<div class="page">
  <h2>Edit Data Kendaraan</h2>
  <form method="post" class="form-section">
    <label>Nomor Plat</label>
    <input type="text" name="nomor_plat" value="<?= $kendaraan['nomor_plat'] ?>" required>

    <label>Tanggal Pajak</label>
    <input type="date" name="tanggal_pajak" value="<?= $kendaraan['tanggal_pajak'] ?>" required>

    <label>Penanggung Jawab</label>
    <input type="text" name="penanggung_jawab" value="<?= $kendaraan['penanggung_jawab'] ?>" required>

    <button type="submit" class="btn">Simpan Perubahan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
