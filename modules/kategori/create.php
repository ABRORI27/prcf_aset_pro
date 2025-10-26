<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama_kategori']);
  $deskripsi = trim($_POST['deskripsi']);

  if ($nama == '') {
    echo "<div class='alert red'>❌ Nama kategori wajib diisi!</div>";
  } else {
    $sql = "INSERT INTO kategori_barang (nama_kategori, deskripsi) VALUES ('$nama', '$deskripsi')";
    if (mysqli_query($conn, $sql)) {
      echo "<script>alert('✅ Kategori berhasil ditambahkan!');window.location='read.php';</script>";
    } else {
      echo "<div class='alert red'>❌ Gagal menambah kategori: " . mysqli_error($conn) . "</div>";
    }
  }
}
?>

<div class="page">
  <h2>Tambah Kategori Baru</h2>
  <form method="post" class="form-section">
    <label>Nama Kategori</label>
    <input type="text" name="nama_kategori" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi" rows="4"></textarea>

    <button type="submit" class="btn">Simpan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
