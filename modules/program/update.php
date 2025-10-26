<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

// Ambil ID program dari URL
$id = $_GET['id'] ?? 0;
$id = (int)$id;

// Ambil data program berdasarkan ID
$program_result = mysqli_query($conn, "SELECT * FROM program_pendanaan WHERE id='$id'");
$program = mysqli_fetch_assoc($program_result);

if (!$program) {
  echo "<div class='alert red'>❌ Data program tidak ditemukan!</div>";
  include '../../includes/footer.php';
  exit;
}

// Ambil daftar aset untuk dropdown
$aset_result = mysqli_query($conn, "SELECT id, nama_barang, program_id FROM aset_barang ORDER BY id ASC");

// Ambil aset yang saat ini terkait dengan program ini (jika ada)
$current_aset = mysqli_query($conn, "SELECT id FROM aset_barang WHERE program_id='$id'");
$current_aset_row = mysqli_fetch_assoc($current_aset);
$current_aset_id = $current_aset_row ? $current_aset_row['id'] : null;

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_program = mysqli_real_escape_string($conn, trim($_POST['nama_program']));
  $tahun_anggaran = (int)$_POST['tahun_anggaran'];
  $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan']));
  $aset_id_baru = (int)$_POST['aset_id'];

  if ($nama_program == '' || $tahun_anggaran == '') {
    echo "<div class='alert red'>❌ Nama program dan tahun anggaran wajib diisi!</div>";
  } else {
    // 1️⃣ Update data program
    $update_program = "
      UPDATE program_pendanaan
      SET nama_program='$nama_program', tahun_anggaran='$tahun_anggaran', keterangan='$keterangan'
      WHERE id='$id'
    ";
    if (mysqli_query($conn, $update_program)) {

      // 2️⃣ Kosongkan aset lama (jika ada)
      if ($current_aset_id && $current_aset_id != $aset_id_baru) {
        mysqli_query($conn, "UPDATE aset_barang SET program_id=NULL WHERE id='$current_aset_id'");
      }

      // 3️⃣ Update aset baru agar terkait dengan program ini
      mysqli_query($conn, "UPDATE aset_barang SET program_id='$id' WHERE id='$aset_id_baru'");

      echo "<script>alert('✅ Program berhasil diperbarui!');window.location='read.php';</script>";
    } else {
      echo "<div class='alert red'>❌ Gagal memperbarui data: " . mysqli_error($conn) . "</div>";
    }
  }
}
?>

<div class="page">
  <h2>Edit Program Pendanaan</h2>
  <form method="post" class="form-section">
    <label>Nama Program</label>
    <input type="text" name="nama_program" value="<?= htmlspecialchars($program['nama_program']) ?>" required>

    <label>Tahun Anggaran</label>
    <input type="number" name="tahun_anggaran" min="2000" max="<?= date('Y') + 5 ?>" value="<?= htmlspecialchars($program['tahun_anggaran']) ?>" required>

    <label>Keterangan</label>
    <textarea name="keterangan" rows="3"><?= htmlspecialchars($program['keterangan']) ?></textarea>

    <label>Pilih Aset Barang</label>
    <select name="aset_id" required>
      <option value="">-- Pilih Aset --</option>
      <?php
      while ($aset = mysqli_fetch_assoc($aset_result)) {
        $selected = ($aset['id'] == $current_aset_id) ? 'selected' : '';
        echo "<option value='{$aset['id']}' $selected>[{$aset['id']}] {$aset['nama_barang']}</option>";
      }
      ?>
    </select>

    <button type="submit" class="btn">Update</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
