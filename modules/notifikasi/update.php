<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>❌ ID notifikasi tidak ditemukan!</div>";
  exit;
}

// Ambil data notifikasi
$q = mysqli_query($conn, "SELECT * FROM notifikasi WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
  echo "<div class='alert red'>❌ Data notifikasi tidak ditemukan!</div>";
  exit;
}

// Ambil data aset untuk dropdown
$asetList = mysqli_query($conn, "SELECT id, nama_barang, kode_barang FROM aset_barang ORDER BY nama_barang ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $aset_id = $_POST['aset_id'];
  $tipe_notifikasi = $_POST['tipe_notifikasi'];
  $tanggal_notifikasi = $_POST['tanggal_notifikasi'];
  $status = $_POST['status'];

  if ($aset_id == '' || $tipe_notifikasi == '' || $tanggal_notifikasi == '') {
    echo "<div class='alert red'>❌ Semua field wajib diisi!</div>";
  } else {
    $sql = "UPDATE notifikasi 
            SET aset_id='$aset_id', tipe_notifikasi='$tipe_notifikasi', 
                tanggal_notifikasi='$tanggal_notifikasi', status='$status'
            WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
      echo "<script>alert('✅ Notifikasi berhasil diperbarui!');window.location='read.php';</script>";
    } else {
      echo "<div class='alert red'>❌ Gagal update: " . mysqli_error($conn) . "</div>";
    }
  }
}
?>

<div class="page">
  <h2>Edit Notifikasi</h2>
  <form method="post" class="form-section">

    <label>Pilih Aset Barang</label>
    <select name="aset_id" required>
      <option value="">-- Pilih Barang --</option>
      <?php
      while ($a = mysqli_fetch_assoc($asetList)) {
        $selected = ($a['id'] == $data['aset_id']) ? 'selected' : '';
        echo "<option value='{$a['id']}' $selected>[{$a['kode_barang']}] {$a['nama_barang']}</option>";
      }
      ?>
    </select>

    <label>Tipe Notifikasi</label>
    <select name="tipe_notifikasi" required>
      <option value="">-- Pilih Tipe --</option>
      <?php
      $tipeList = ['Pajak Kendaraan', 'Perawatan', 'Audit'];
      foreach ($tipeList as $t) {
        $sel = ($t == $data['tipe_notifikasi']) ? 'selected' : '';
        echo "<option value='$t' $sel>$t</option>";
      }
      ?>
    </select>

    <label>Tanggal Notifikasi</label>
    <input type="date" name="tanggal_notifikasi" value="<?= $data['tanggal_notifikasi'] ?>" required>

    <label>Status</label>
    <select name="status">
      <option value="Belum Terkirim" <?= ($data['status'] == 'Belum Terkirim') ? 'selected' : '' ?>>Belum Terkirim</option>
      <option value="Terkirim" <?= ($data['status'] == 'Terkirim') ? 'selected' : '' ?>>Terkirim</option>
    </select>

    <button type="submit" class="btn">Simpan Perubahan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
