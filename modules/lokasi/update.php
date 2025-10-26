<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID lokasi tidak ditemukan!</div>";
  exit;
}

// Ambil data lokasi
$q = mysqli_query($conn, "SELECT * FROM lokasi_barang WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
  echo "<div class='alert red'>Data lokasi tidak ditemukan!</div>";
  exit;
}

// Ambil aset yang sedang terhubung dengan lokasi ini (kalau ada)
$q_aset_terkait = mysqli_query($conn, "SELECT id FROM aset_barang WHERE lokasi_id='$id' LIMIT 1");
$aset_terkait = mysqli_fetch_assoc($q_aset_terkait);
$aset_terkait_id = $aset_terkait['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_lokasi = trim($_POST['nama_lokasi']);
  $alamat = trim($_POST['alamat']);
  $penanggung_jawab = trim($_POST['penanggung_jawab']);
  $aset_id = intval($_POST['aset_id']);

  // Update tabel lokasi_barang
  $sql = "UPDATE lokasi_barang 
          SET nama_lokasi='$nama_lokasi', alamat='$alamat', penanggung_jawab='$penanggung_jawab'
          WHERE id='$id'";

  if (mysqli_query($conn, $sql)) {
    // 1️⃣ Reset lokasi_id lama di aset yang sebelumnya terkait
    if ($aset_terkait_id && $aset_terkait_id != $aset_id) {
      mysqli_query($conn, "UPDATE aset_barang SET lokasi_id=NULL WHERE id='$aset_terkait_id'");
    }

    // 2️⃣ Update aset_barang dengan lokasi_id & kategori_id baru
    $kategori_id = 4; // contoh tetap: kategori kendaraan
    $update_aset = "UPDATE aset_barang 
                    SET lokasi_id='$id', kategori_id='$kategori_id' 
                    WHERE id='$aset_id'";
    mysqli_query($conn, $update_aset);

    echo "<script>alert('✅ Data lokasi berhasil diperbarui!');window.location='read.php';</script>";
    exit;
  } else {
    echo "<div class='alert red'>❌ Gagal update: " . mysqli_error($conn) . "</div>";
  }
}
?>

<div class="page">
  <h2>Edit Data Lokasi</h2>

  <form method="post" class="form-section">
    <label>Pilih Aset Barang</label>
    <select name="aset_id" required>
      <option value="">-- Pilih ID & Nama Aset --</option>
      <?php
      $q_aset = mysqli_query($conn, "SELECT id, nama_barang FROM aset_barang ORDER BY id ASC");
      while ($a = mysqli_fetch_assoc($q_aset)) {
        $selected = ($a['id'] == $aset_terkait_id) ? 'selected' : '';
        echo "<option value='{$a['id']}' $selected>[ID {$a['id']}] {$a['nama_barang']}</option>";
      }
      ?>
    </select>

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
