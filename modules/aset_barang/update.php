<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID aset tidak ditemukan.</div>";
  exit;
}

// Ambil data aset
$stmt = $conn->prepare("SELECT * FROM aset_barang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$aset = $stmt->get_result()->fetch_assoc();

if (!$aset) {
  echo "<div class='alert red'>Data aset tidak ditemukan!</div>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_barang = $_POST['nama_barang'];
  $deskripsi = $_POST['deskripsi'];
  $jumlah_unit = $_POST['jumlah_unit'];
  $nomor_seri = $_POST['nomor_seri'];
  $harga_pembelian = $_POST['harga_pembelian'];
  $waktu_perolehan = $_POST['waktu_perolehan'];
  $lokasi_barang = $_POST['lokasi_barang'];
  $kondisi_barang = $_POST['kondisi_barang'];
  $kode_penomoran = $_POST['kode_penomoran'];
  $program_pendanaan = $_POST['program_pendanaan'];
  $kategori_barang = $_POST['kategori_barang']; // ID kategori
  $nomor_plat = $_POST['nomor_plat'] ?? null;
  $tanggal_pajak = $_POST['tanggal_pajak'] ?? null;
  $penanggung_jawab = $_POST['penanggung_jawab'] ?? null;

  // Jika bukan kategori Kendaraan (ID != 4), kosongkan fields kendaraan
  if ($kategori_barang != 4) {
    $nomor_plat = null;
    $tanggal_pajak = null;
    $penanggung_jawab = null;
  }

  $stmtUpdate = $conn->prepare("
    UPDATE aset_barang SET 
      nama_barang = ?, deskripsi = ?, jumlah_unit = ?, nomor_seri = ?, 
      harga_pembelian = ?, waktu_perolehan = ?, lokasi_barang = ?, 
      kondisi_barang = ?, kode_penomoran = ?, program_pendanaan = ?, 
      kategori_barang = ?, nomor_plat = ?, tanggal_pajak = ?, penanggung_jawab = ?
    WHERE id = ?
  ");

  $stmtUpdate->bind_param(
    "ssississsissssi",
    $nama_barang, $deskripsi, $jumlah_unit, $nomor_seri, 
    $harga_pembelian, $waktu_perolehan, $lokasi_barang, 
    $kondisi_barang, $kode_penomoran, $program_pendanaan, 
    $kategori_barang, $nomor_plat, $tanggal_pajak, $penanggung_jawab, $id
  );

  if ($stmtUpdate->execute()) {
    echo "<script>alert('✅ Data aset berhasil diperbarui!');window.location='read.php';</script>";
    exit;
  } else {
    echo "<div class='alert red'>❌ Gagal memperbarui data: " . $stmtUpdate->error . "</div>";
  }
}
?>

<div class="page">
  <h2>Edit Data Aset</h2>
  <form method="post" class="form-section">

    <label>Nama Barang</label>
    <input type="text" name="nama_barang" value="<?= htmlspecialchars($aset['nama_barang']) ?>" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi"><?= htmlspecialchars($aset['deskripsi']) ?></textarea>

    <label>Jumlah Unit</label>
    <input type="number" name="jumlah_unit" value="<?= $aset['jumlah_unit'] ?>">

    <label>Nomor Seri</label>
    <input type="text" name="nomor_seri" value="<?= $aset['nomor_seri'] ?>">

    <label>Harga Pembelian</label>
    <input type="number" name="harga_pembelian" value="<?= $aset['harga_pembelian'] ?>">

    <label>Waktu Perolehan</label>
    <input type="date" name="waktu_perolehan" value="<?= $aset['waktu_perolehan'] ?>">

    <label>Lokasi Barang</label>
    <input type="text" name="lokasi_barang" value="<?= $aset['lokasi_barang'] ?>">

    <label>Kondisi Barang</label>
    <select name="kondisi_barang" required>
      <option value="Baik" <?= $aset['kondisi_barang']=='Baik'?'selected':'' ?>>Baik</option>
      <option value="Rusak" <?= $aset['kondisi_barang']=='Rusak'?'selected':'' ?>>Rusak</option>
      <option value="Hilang" <?= $aset['kondisi_barang']=='Hilang'?'selected':'' ?>>Hilang</option>
    </select>

    <label>Kode Penomoran</label>
    <input type="text" name="kode_penomoran" value="<?= $aset['kode_penomoran'] ?>">

    <label>Program Pendanaan</label>
    <input type="text" name="program_pendanaan" value="<?= $aset['program_pendanaan'] ?>">

    <label>Kategori Barang</label>
    <select name="kategori_barang" id="kategori_barang" onchange="toggleKendaraanFields()">
      <option value="1" <?= $aset['kategori_barang']==1?'selected':'' ?>>Peralatan Kantor</option>
      <option value="2" <?= $aset['kategori_barang']==2?'selected':'' ?>>Furniture</option>
      <option value="3" <?= $aset['kategori_barang']==3?'selected':'' ?>>Peralatan Lapangan</option>
      <option value="4" <?= $aset['kategori_barang']==4?'selected':'' ?>>Kendaraan</option>
    </select>

    <!-- Field tambahan untuk kategori Kendaraan -->
    <div id="kendaraanFields" style="<?= ($aset['kategori_barang']==4) ? 'display:block;' : 'display:none;' ?>">
      <label>Nomor Plat</label>
      <input type="text" name="nomor_plat" value="<?= $aset['nomor_plat'] ?>">

      <label>Tanggal Pajak</label>
      <input type="date" name="tanggal_pajak" value="<?= $aset['tanggal_pajak'] ?>">

      <label>Penanggung Jawab</label>
      <input type="text" name="penanggung_jawab" value="<?= $aset['penanggung_jawab'] ?>">
    </div>

    <button type="submit" class="btn">Simpan Perubahan</button>
  </form>
</div>

<script>
function toggleKendaraanFields() {
  const kategori = document.getElementById('kategori_barang').value;
  document.getElementById('kendaraanFields').style.display = (kategori == 4) ? 'block' : 'none';
}
</script>

<?php include '../../includes/footer.php'; ?>
