<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

// Ambil daftar aset dari tabel aset_barang untuk dropdown
$aset_result = mysqli_query($conn, "SELECT id, nama_barang FROM aset_barang ORDER BY id ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_program = mysqli_real_escape_string($conn, trim($_POST['nama_program']));
  $tahun_anggaran = (int)$_POST['tahun_anggaran'];
  $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan']));
  $aset_id = (int)$_POST['aset_id']; // ambil aset yang dipilih user

  if ($nama_program == '' || $tahun_anggaran == '' || !$aset_id) {
    echo "<div class='alert red'>❌ Semua field wajib diisi termasuk pilihan aset!</div>";
  } else {
    // 1️⃣ Tambahkan program baru ke tabel program_pendanaan
    $sql = "INSERT INTO program_pendanaan (nama_program, tahun_anggaran, keterangan)
            VALUES ('$nama_program', '$tahun_anggaran', '$keterangan')";
    if (mysqli_query($conn, $sql)) {
      // Ambil ID program yang baru saja dimasukkan
      $program_id = mysqli_insert_id($conn);

      // 2️⃣ Update aset_barang agar kolom program_id diisi dengan ID program baru
      $update_aset = "UPDATE aset_barang SET program_id='$program_id' WHERE id='$aset_id'";
      if (mysqli_query($conn, $update_aset)) {
        echo "<script>alert('✅ Program berhasil dibuat dan aset berhasil dikaitkan!');window.location='read.php';</script>";
      } else {
        echo "<div class='alert red'>⚠️ Program tersimpan, tapi gagal update aset: " . mysqli_error($conn) . "</div>";
      }
    } else {
      echo "<div class='alert red'>❌ Gagal menambah program: " . mysqli_error($conn) . "</div>";
    }
  }
}
?>

<div class="page">
  <h2>Tambah Program Pendanaan</h2>
  <form method="post" class="form-section">
    <label>Nama Program</label>
    <input type="text" name="nama_program" placeholder="Contoh: Pengadaan Komputer 2025" required>

    <label>Tahun Anggaran</label>
    <input type="number" name="tahun_anggaran" min="2000" max="<?= date('Y') + 5 ?>" value="<?= date('Y') ?>" required>

    <label>Keterangan</label>
    <textarea name="keterangan" rows="3" placeholder="Tuliskan keterangan tambahan jika ada..."></textarea>

    <label>Pilih Aset Barang</label>
    <select name="aset_id" required>
      <option value="">-- Pilih Aset --</option>
      <?php
      while ($aset = mysqli_fetch_assoc($aset_result)) {
        echo "<option value='{$aset['id']}'>[{$aset['id']}] {$aset['nama_barang']}</option>";
      }
      ?>
    </select>

    <button type="submit" class="btn">Simpan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
