<?php
include '../../includes/header.php';
include '../../config/db.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_lokasi = trim($_POST['nama_lokasi']);
  $alamat = trim($_POST['alamat']);
  $penanggung_jawab = trim($_POST['penanggung_jawab']);
  $aset_id = intval($_POST['aset_id']); // ambil dari dropdown

  if ($nama_lokasi == '' || $aset_id == 0) {
    $err = "❌ Nama lokasi dan ID aset wajib diisi!";
  } else {
    // 1️⃣ Insert lokasi baru
    $sql = "INSERT INTO lokasi_barang (nama_lokasi, alamat, penanggung_jawab)
            VALUES ('$nama_lokasi', '$alamat', '$penanggung_jawab')";
    if (mysqli_query($conn, $sql)) {
      $lokasi_id = mysqli_insert_id($conn); // ambil ID lokasi baru

      // 2️⃣ Update aset_barang dengan lokasi_id + kategori_id
      $kategori_id = 4; // contoh: kategori kendaraan
      $update_aset = "UPDATE aset_barang 
                      SET lokasi_barang = '$lokasi_id', kategori_barang = '$kategori_id' 
                      WHERE id = '$aset_id'";

      if (mysqli_query($conn, $update_aset)) {
        echo "<script>alert('✅ Lokasi dan aset berhasil dikaitkan!');window.location='read.php';</script>";
        exit;
      } else {
        $err = "⚠️ Gagal mengupdate aset: " . mysqli_error($conn);
      }
    } else {
      $err = "❌ Gagal menambah lokasi: " . mysqli_error($conn);
    }
  }
}
?>

<div class="page">
  <h2>Tambah Lokasi Barang</h2>

  <?php if ($err): ?>
    <div class="alert red"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="post" class="form-section">
    <label>Pilih Aset Barang</label>
    <select name="aset_id" required>
      <option value="">-- Pilih ID & Nama Aset --</option>
      <?php
      $q_aset = mysqli_query($conn, "SELECT id, nama_barang FROM aset_barang ORDER BY id ASC");
      while ($a = mysqli_fetch_assoc($q_aset)) {
        echo "<option value='{$a['id']}'>[ID {$a['id']}] {$a['nama_barang']}</option>";
      }
      ?>
    </select>

    <label>Nama Lokasi</label>
    <input type="text" name="nama_lokasi" required placeholder="Contoh: Gudang Utama">

    <label>Alamat Lengkap</label>
    <textarea name="alamat" rows="3" placeholder="Masukkan alamat lengkap lokasi"></textarea>

    <label>Penanggung Jawab</label>
    <input type="text" name="penanggung_jawab" placeholder="Nama orang yang bertanggung jawab">

    <button type="submit" class="btn">Simpan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
