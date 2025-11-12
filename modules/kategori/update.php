<?php
include '../../includes/header.php';
include '../../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID kategori tidak ditemukan!</div>";
  exit;
}

$q = mysqli_query($conn, "SELECT * FROM kategori_barang WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
  echo "<div class='alert red'>Kategori tidak ditemukan!</div>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama_kategori']);
  $deskripsi = trim($_POST['deskripsi']);

  $sql = "UPDATE kategori_barang SET nama_kategori='$nama', deskripsi='$deskripsi' WHERE id='$id'";
  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('✅ Kategori berhasil diperbarui!');window.location='read.php';</script>";
  } else {
    echo "<div class='alert red'>❌ Gagal memperbarui data: " . mysqli_error($conn) . "</div>";
  }
}
?>

<div class="page">
  <h2>Edit Kategori Barang</h2>
  <form method="post" class="form-section">
    <label>Nama Kategori</label>
    <input type="text" name="nama_kategori" value="<?= htmlspecialchars($data['nama_kategori']) ?>" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi" rows="4"><?= htmlspecialchars($data['deskripsi']) ?></textarea>

    <button type="submit" class="btn">Simpan Perubahan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
