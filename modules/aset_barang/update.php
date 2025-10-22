<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID aset tidak ditemukan.</div>";
  exit;
}

// Ambil data aset berdasarkan ID
$q = mysqli_query($conn, "SELECT * FROM aset_barang WHERE id = '$id'");
$aset = mysqli_fetch_assoc($q);
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
  $kategori_barang = $_POST['kategori_barang'];
  $nomor_plat = $_POST['nomor_plat'] ?? '';
  $tanggal_pajak = $_POST['tanggal_pajak'] ?? '';
  $penanggung_jawab = $_POST['penanggung_jawab'] ?? '';

  $sql = "UPDATE aset_barang SET 
            nama_barang='$nama_barang',
            deskripsi='$deskripsi',
            jumlah_unit='$jumlah_unit',
            nomor_seri='$nomor_seri',
            harga_pembelian='$harga_pembelian',
            waktu_perolehan='$waktu_perolehan',
            lokasi_barang='$lokasi_barang',
            kondisi_barang='$kondisi_barang',
            kode_penomoran='$kode_penomoran',
            program_pendanaan='$program_pendanaan',
            kategori_barang='$kategori_barang',
            nomor_plat='$nomor_plat',
            tanggal_pajak='$tanggal_pajak',
            penanggung_jawab='$penanggung_jawab'
          WHERE id='$id'";

  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('✅ Data aset berhasil diperbarui!');window.location='output_aset.php';</script>";
  } else {
    echo "<div class='alert red'>❌ Gagal memperbarui data: " . mysqli_error($conn) . "</div>";
  }
}
?>

<div class="page">
  <h2>Edit Data Aset</h2>
  <form method="post" class="form-section">

    <label>Nama Barang</label>
    <input type="text" name="nama_barang" value="<?= $aset['nama_barang'] ?>" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi"><?= $aset['deskripsi'] ?></textarea>

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
      <option value="Peralatan Kantor" <?= $aset['kategori_barang']=='Peralatan Kantor'?'selected':'' ?>>Peralatan Kantor</option>
      <option value="Furniture" <?= $aset['kategori_barang']=='Furniture'?'selected':'' ?>>Furniture</option>
      <option value="Peralatan Lapangan" <?= $aset['kategori_barang']=='Peralatan Lapangan'?'selected':'' ?>>Peralatan Lapangan</option>
      <option value="Kendaraan" <?= $aset['kategori_barang']=='Kendaraan'?'selected':'' ?>>Kendaraan</option>
    </select>

    <!-- Field tambahan khusus kendaraan -->
    <div id="kendaraanFields" style="<?= ($aset['kategori_barang']=='Kendaraan') ? 'display:block;' : 'display:none;' ?>">
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

<!-- JS dinamis untuk tampil/sembunyi field kendaraan -->
<script>
function toggleKendaraanFields() {
  const kategori = document.getElementById('kategori_barang').value;
  const kendaraanFields = document.getElementById('kendaraanFields');
  kendaraanFields.style.display = (kategori === 'Kendaraan') ? 'block' : 'none';
}
</script>

<?php include '../../includes/footer.php'; ?>
