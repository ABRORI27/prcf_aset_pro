<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_lokasi = trim($_POST['nama_lokasi']);
  $alamat = trim($_POST['alamat']);
  $penanggung_jawab = trim($_POST['penanggung_jawab']);

  if ($nama_lokasi == '') {
    echo "<div class='alert red'>❌ Nama lokasi wajib diisi!</div>";
  } else {
    $sql = "INSERT INTO lokasi_barang (nama_lokasi, alamat, penanggung_jawab)
            VALUES ('$nama_lokasi', '$alamat', '$penanggung_jawab')";
    if (mysqli_query($conn, $sql)) {
      echo "<script>alert('✅ Lokasi berhasil ditambahkan!');window.location='read.php';</script>";
    } else {
      echo "<div class='alert red'>❌ Gagal menambah lokasi: " . mysqli_error($conn) . "</div>";
    }
  }
}
?>

<div class="page">
  <h2>Tambah Lokasi Baru</h2>
  <form method="post" class="form-section">
    <label>Nama Lokasi</label>
    <input type="text" name="nama_lokasi" required>

    <label>Alamat Lengkap</label>
    <textarea name="alamat" rows="3"></textarea>

    <label>Penanggung Jawab</label>
    <input type="text" name="penanggung_jawab">

    <button type="submit" class="btn">Simpan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
