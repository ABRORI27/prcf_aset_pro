<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_program = trim($_POST['nama_program']);
  $tahun_anggaran = $_POST['tahun_anggaran'];
  $keterangan = trim($_POST['keterangan']);

  if ($nama_program == '' || $tahun_anggaran == '') {
    echo "<div class='alert red'>❌ Nama program dan tahun wajib diisi!</div>";
  } else {
    $sql = "INSERT INTO program_pendanaan (nama_program, tahun_anggaran, keterangan)
            VALUES ('$nama_program', '$tahun_anggaran', '$keterangan')";
    if (mysqli_query($conn, $sql)) {
      echo "<script>alert('✅ Program pendanaan berhasil ditambahkan!');window.location='read.php';</script>";
    } else {
      echo "<div class='alert red'>❌ Gagal menambah data: " . mysqli_error($conn) . "</div>";
    }
  }
}
?>

<div class="page">
  <h2>Tambah Program Pendanaan</h2>
  <form method="post" class="form-section">
    <label>Nama Program</label>
    <input type="text" name="nama_program" required>

    <label>Tahun Anggaran</label>
    <input type="number" name="tahun_anggaran" min="2000" max="<?= date('Y')+5 ?>" required>

    <label>Keterangan</label>
    <textarea name="keterangan" rows="4"></textarea>

    <button type="submit" class="btn">Simpan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
