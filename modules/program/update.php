<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID program tidak ditemukan!</div>";
  exit;
}

$q = mysqli_query($conn, "SELECT * FROM program_pendanaan WHERE id='$id'");
$data = mysqli_fetch_assoc($q);
if (!$data) {
  echo "<div class='alert red'>Program tidak ditemukan!</div>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_program = trim($_POST['nama_program']);
  $tahun_anggaran = $_POST['tahun_anggaran'];
  $keterangan = trim($_POST['keterangan']);

  $sql = "UPDATE program_pendanaan 
          SET nama_program='$nama_program', tahun_anggaran='$tahun_anggaran', keterangan='$keterangan'
          WHERE id='$id'";

  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('✅ Data program berhasil diperbarui!');window.location='read.php';</script>";
  } else {
    echo "<div class='alert red'>❌ Gagal memperbarui data: " . mysqli_error($conn) . "</div>";
  }
}
?>

<div class="page">
  <h2>Edit Program Pendanaan</h2>
  <form method="post" class="form-section">
    <label>Nama Program</label>
    <input type="text" name="nama_program" value="<?= htmlspecialchars($data['nama_program']) ?>" required>

    <label>Tahun Anggaran</label>
    <input type="number" name="tahun_anggaran" value="<?= htmlspecialchars($data['tahun_anggaran']) ?>" min="2000" max="<?= date('Y')+5 ?>" required>

    <label>Keterangan</label>
    <textarea name="keterangan" rows="4"><?= htmlspecialchars($data['keterangan']) ?></textarea>

    <button type="submit" class="btn">Simpan Perubahan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
