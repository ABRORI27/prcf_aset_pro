<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../includes/header.php';
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $aset_id = mysqli_real_escape_string($conn, $_POST['aset_id']);
    $tipe_notifikasi = mysqli_real_escape_string($conn, $_POST['tipe_notifikasi']);
    $tanggal_notifikasi = mysqli_real_escape_string($conn, $_POST['tanggal_notifikasi']);
    $status = 'Belum Terkirim';

    if (empty($aset_id) || empty($tipe_notifikasi) || empty($tanggal_notifikasi)) {

        echo "<div class='alert red'>Semua field wajib diisi!</div>";

    } else {

        $sql = "INSERT INTO notifikasi (aset_id, tipe_notifikasi, tanggal_notifikasi, status)
                VALUES ('$aset_id', '$tipe_notifikasi', '$tanggal_notifikasi', '$status')";

        if (mysqli_query($conn, $sql)) {

            echo "<script>alert('Notifikasi berhasil ditambahkan!');window.location='read.php';</script>";

        } else {
            echo "<div class='alert red'>Gagal menyimpan: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<div class="page">
  <h2>Tambah Notifikasi</h2>

  <form method="post" class="form-section">

    <label>Pilih Aset</label>
    <select name="aset_id" required>
      <option value="">-- Pilih Aset --</option>
      <?php
      $aset = mysqli_query($conn, "SELECT id, nama_barang FROM aset_barang ORDER BY nama_barang ASC");
      while ($row = mysqli_fetch_assoc($aset)) {
        echo "<option value='{$row['id']}'>[{$row['id']}] {$row['nama_barang']}</option>";
      }
      ?>
    </select>

    <label>Tipe Notifikasi</label>
    <select name="tipe_notifikasi" required>
      <option value="">-- Pilih Tipe --</option>
      <option value="Pajak Kendaraan">Pajak Kendaraan</option>
      <option value="Perawatan">Perawatan</option>
      <option value="Audit">Audit</option>
    </select>

    <label>Tanggal Notifikasi</label>
    <input type="date" name="tanggal_notifikasi" required>

    <button type="submit" class="btn">Simpan</button>

  </form>
</div>

<?php include '../../includes/footer.php'; ?>
