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

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_barang = trim($_POST['nama_barang']);
  $deskripsi = trim($_POST['deskripsi']);
  $jumlah_unit = (int) $_POST['jumlah_unit'];
  $nomor_seri_input = trim($_POST['nomor_seri']);
  $harga_pembelian = (float) $_POST['harga_pembelian'];
  $waktu_perolehan = $_POST['waktu_perolehan'];
  $lokasi_barang = trim($_POST['lokasi_barang']);
  $kondisi_barang = trim($_POST['kondisi_barang']);
  $kode_penomoran = trim($_POST['kode_penomoran']);
  $program_pendanaan = trim($_POST['program_pendanaan']);
  $kategori_barang = (int) $_POST['kategori_barang']; // ID kategori
  $nomor_plat = trim($_POST['nomor_plat'] ?? '');
  $tanggal_pajak = !empty($_POST['tanggal_pajak']) ? $_POST['tanggal_pajak'] : null;
  $penanggung_jawab = trim($_POST['penanggung_jawab'] ?? '');

  // Tentukan kategori yang wajib isi nomor seri
  $kategoriWajibNomorSeri = [1, 3, 4]; // contoh: 1=Peralatan Kantor, 3=Peralatan Lapangan, 4=Kendaraan
  $nomor_seri = ($nomor_seri_input !== '') ? $nomor_seri_input : null;

  // Validasi field wajib
  if (empty($nama_barang) || empty($jumlah_unit) || empty($lokasi_barang) || empty($kondisi_barang)) {
    echo "<div class='alert red'>⚠️ Harap isi semua field wajib yang bertanda *!</div>";
  } elseif (in_array($kategori_barang, $kategoriWajibNomorSeri) && empty($nomor_seri)) {
    echo "<div class='alert red'>⚠️ Nomor seri wajib diisi untuk kategori ini!</div>";
  } else {
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
      if (str_contains($stmtUpdate->error, 'Duplicate entry') && str_contains($stmtUpdate->error, 'nomor_seri')) {
        echo "<div class='alert red'>❌ Nomor seri sudah digunakan pada aset lain!</div>";
      } else {
        echo "<div class='alert red'>❌ Gagal memperbarui data: " . htmlspecialchars($stmtUpdate->error) . "</div>";
      }
    }
  }
}
?>

<div class="page">
  <h2>Edit Data Aset</h2>
  <form method="post" class="form-section">

    <label>Nama Barang <span style="color:red">*</span></label>
    <input type="text" name="nama_barang" value="<?= htmlspecialchars($aset['nama_barang']) ?>" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi"><?= htmlspecialchars($aset['deskripsi']) ?></textarea>

    <label>Jumlah Unit <span style="color:red">*</span></label>
    <input type="number" name="jumlah_unit" value="<?= $aset['jumlah_unit'] ?>" required>

    <label>Nomor Seri</label>
    <input type="text" name="nomor_seri" value="<?= htmlspecialchars($aset['nomor_seri']) ?>" placeholder="Boleh dikosongkan untuk Furniture">

    <label>Harga Pembelian</label>
    <input type="number" name="harga_pembelian" value="<?= $aset['harga_pembelian'] ?>">

    <label>Waktu Perolehan</label>
    <input type="date" name="waktu_perolehan" value="<?= $aset['waktu_perolehan'] ?>">

    <label>Lokasi Barang <span style="color:red">*</span></label>
    <input type="text" name="lokasi_barang" value="<?= htmlspecialchars($aset['lokasi_barang']) ?>" required>

    <label>Kondisi Barang <span style="color:red">*</span></label>
    <select name="kondisi_barang" required>
      <option value="Baik" <?= $aset['kondisi_barang']=='Baik'?'selected':'' ?>>Baik</option>
      <option value="Rusak" <?= $aset['kondisi_barang']=='Rusak'?'selected':'' ?>>Rusak</option>
      <option value="Hilang" <?= $aset['kondisi_barang']=='Hilang'?'selected':'' ?>>Hilang</option>
    </select>

    <label>Kode Penomoran</label>
    <input type="text" name="kode_penomoran" value="<?= htmlspecialchars($aset['kode_penomoran']) ?>">

    <label>Program Pendanaan</label>
    <input type="text" name="program_pendanaan" value="<?= htmlspecialchars($aset['program_pendanaan']) ?>">

    <label>Kategori Barang <span style="color:red">*</span></label>
    <select name="kategori_barang" id="kategori_barang" onchange="toggleKendaraanFields()" required>
      <option value="1" <?= $aset['kategori_barang']==1?'selected':'' ?>>Peralatan Kantor</option>
      <option value="2" <?= $aset['kategori_barang']==2?'selected':'' ?>>Furniture</option>
      <option value="3" <?= $aset['kategori_barang']==3?'selected':'' ?>>Peralatan Lapangan</option>
      <option value="4" <?= $aset['kategori_barang']==4?'selected':'' ?>>Kendaraan</option>
    </select>

    <!-- Field tambahan untuk kategori Kendaraan -->
    <div id="kendaraanFields" style="<?= ($aset['kategori_barang']==4) ? 'display:block;' : 'display:none;' ?>">
      <label>Nomor Plat</label>
      <input type="text" name="nomor_plat" value="<?= htmlspecialchars($aset['nomor_plat']) ?>">

      <label>Tanggal Pajak</label>
      <input type="date" name="tanggal_pajak" value="<?= $aset['tanggal_pajak'] ?>">

      <label>Penanggung Jawab</label>
      <input type="text" name="penanggung_jawab" value="<?= htmlspecialchars($aset['penanggung_jawab']) ?>">
    </div>

    <button type="submit" class="btn">💾 Simpan Perubahan</button>
  </form>
</div>

<script>
function toggleKendaraanFields() {
  const kategori = document.getElementById('kategori_barang').value;
  document.getElementById('kendaraanFields').style.display = (kategori == 4) ? 'block' : 'none';
}
</script>

<?php include '../../includes/footer.php'; ?>
