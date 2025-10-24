<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';
include '../../includes/auth_check.php';

if (!has_access(['Admin', 'Operator'])) {
  echo "<script>alert('Anda tidak memiliki izin!');window.location='output_employee.php';</script>";
  exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID tidak ditemukan.</div>";
  exit;
}

$q = mysqli_query($conn, "SELECT * FROM employees WHERE id='$id'");
$emp = mysqli_fetch_assoc($q);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama'];
  $nik = $_POST['nik'];
  $jab = $_POST['jabatan'];
  $unit = $_POST['unit'];
  $kontak = $_POST['kontak'];
  $tgl = $_POST['tanggal_masuk'];

  $sql = "UPDATE employees SET nama='$nama', nik='$nik', jabatan='$jab', unit='$unit', kontak='$kontak', tanggal_masuk='$tgl' WHERE id='$id'";
  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Data pegawai berhasil diperbarui!');window.location='output_employee.php';</script>";
  } else {
    echo "<div class='alert red'>Gagal: " . mysqli_error($conn) . "</div>";
  }
}
?>

<div class="page">
  <h2>Edit Data Pegawai</h2>
  <form method="post" class="form-section">
    <label>Nama</label>
    <input type="text" name="nama" value="<?= $emp['nama'] ?>" required>

    <label>NIK</label>
    <input type="text" name="nik" value="<?= $emp['nik'] ?>" required>

    <label>Jabatan</label>
    <input type="text" name="jabatan" value="<?= $emp['jabatan'] ?>">

    <label>Unit</label>
    <input type="text" name="unit" value="<?= $emp['unit'] ?>">

    <label>Kontak</label>
    <input type="text" name="kontak" value="<?= $emp['kontak'] ?>">

    <label>Tanggal Masuk</label>
    <input type="date" name="tanggal_masuk" value="<?= $emp['tanggal_masuk'] ?>">

    <button type="submit" class="btn">Simpan Perubahan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
