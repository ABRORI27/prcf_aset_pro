<?php
include '../../includes/header.php';
include '../../config/db.php';

// --- Ambil data dropdown ---
$kategoriList = $conn->query("SELECT id, nama_kategori FROM kategori_barang ORDER BY nama_kategori ASC");
$lokasiList = $conn->query("SELECT id, nama_lokasi FROM lokasi_barang ORDER BY nama_lokasi ASC");
$programList = $conn->query("SELECT id, nama_program FROM program_pendanaan ORDER BY nama_program ASC");

// --- Proses form ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Ambil data input
  $nama_barang       = trim($_POST['nama_barang'] ?? '');
  $deskripsi         = $_POST['deskripsi'] ?? null;
  $jumlah_unit       = (int) ($_POST['jumlah_unit'] ?? 1);
  $nomor_seri        = $_POST['nomor_seri'] ?? null;
  $harga_pembelian   = $_POST['harga_pembelian'] ?? null;
  $waktu_perolehan   = $_POST['waktu_perolehan'] ?? null;
  $kondisi_barang    = $_POST['kondisi_barang'] ?? 'Baik';
  $kode_barang       = $_POST['kode_barang'] ?? null;
  $kategori_barang   = (int) ($_POST['kategori_barang'] ?? 0);
  $nomor_plat        = $_POST['nomor_plat'] ?? null;
  $program_pendanaan = !empty($_POST['program_pendanaan']) ? (int)$_POST['program_pendanaan'] : null;
  $lokasi_barang     = (int) ($_POST['lokasi_barang'] ?? 0);
  $foto_barang       = $_POST['foto_barang'] ?? null;
  $status_penggunaan = 'Aktif';
  $tanggal_pajak     = $_POST['tanggal_pajak'] ?? null;
  $penanggung_jawab  = $_POST['penanggung_jawab'] ?? null;
  $user_input        = $_SESSION['user_id'] ?? null;

  // Ambil nama kategori
  $kategoriCheck = $conn->query("SELECT nama_kategori FROM kategori_barang WHERE id = $kategori_barang")->fetch_assoc();
  $namaKategori = strtolower($kategoriCheck['nama_kategori'] ?? '');

  // --- Tentukan apakah kategori ini kendaraan ---
  $isKendaraan = str_contains($namaKategori, 'kendaraan');

  // --- Nomor seri opsional untuk semua kategori ---
  if (empty($nomor_seri)) {
      $nomor_seri = null;
  }

  // --- Nomor urut otomatis ---
  $queryUrut = $conn->prepare("SELECT MAX(nomor_urut_barang) AS max_urut FROM aset_barang WHERE nama_barang = ?");
  $queryUrut->bind_param("s", $nama_barang);
  $queryUrut->execute();
  $resultUrut = $queryUrut->get_result()->fetch_assoc();
  $nomor_urut_barang = ($resultUrut['max_urut'] ?? 0) + 1;

  // --- Query INSERT utama ---
  $sql = "INSERT INTO aset_barang (
    nama_barang, deskripsi, jumlah_unit, nomor_seri, nomor_urut_barang, 
    harga_pembelian, waktu_perolehan, kondisi_barang, kode_barang, 
    kategori_barang, nomor_plat, program_pendanaan, lokasi_barang, 
    user_input, foto_barang, status_penggunaan, tanggal_pajak, penanggung_jawab
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "ssissdssssissiiiss",
    $nama_barang,
    $deskripsi,
    $jumlah_unit,
    $nomor_seri,
    $nomor_urut_barang,
    $harga_pembelian,
    $waktu_perolehan,
    $kondisi_barang,
    $kode_barang,
    $kategori_barang,
    $nomor_plat,
    $program_pendanaan,
    $lokasi_barang,
    $user_input,
    $foto_barang,
    $status_penggunaan,
    $tanggal_pajak,
    $penanggung_jawab
  );

  if ($stmt->execute()) {
    $aset_id = $conn->insert_id;

    // Jika kategori kendaraan, masukkan ke tabel kendaraan
    if ($isKendaraan) {
        $insertKendaraan = $conn->prepare("
          INSERT INTO kendaraan (aset_id, nomor_seri, nomor_plat, tanggal_pajak, penanggung_jawab)
          VALUES (?, ?, ?, ?, ?)
        ");
        $insertKendaraan->bind_param("issss", $aset_id, $nomor_seri, $nomor_plat, $tanggal_pajak, $penanggung_jawab);
        $insertKendaraan->execute();
    }

    echo "<script>alert('✅ Data aset berhasil ditambahkan!'); window.location='read.php';</script>";
    exit;
  } else {
    echo "<div class='alert red'>❌ Gagal menambah data: " . $stmt->error . "</div>";
  }
}
?>

<div class="page">
  <h2>Tambah Aset Baru</h2>
  <form method="post" class="form-section">

    <label>Nama Barang <span style="color:red">*</span></label>
    <input type="text" name="nama_barang" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi"></textarea>

    <label>Jumlah Unit <span style="color:red">*</span></label>
    <input type="number" name="jumlah_unit" min="1" value="1" required>

    <label>Nomor Seri</label>
    <input type="text" name="nomor_seri" id="nomor_seri" placeholder="(Opsional, isi jika ada)">

    <label>Nomor Urut Barang (otomatis)</label>
    <input type="text" name="nomor_urut_barang" readonly placeholder="Akan diisi otomatis">

    <label>Harga Pembelian (Rp)</label>
    <input type="number" name="harga_pembelian" step="0.01">

    <label>Tanggal / Waktu Perolehan</label>
    <input type="date" name="waktu_perolehan">

    <label>Kondisi Barang <span style="color:red">*</span></label>
    <select name="kondisi_barang" required>
      <option value="Baik">Baik</option>
      <option value="Rusak">Rusak</option>
      <option value="Rusak-perlu diservis, butuh dana besar">rusak, perlu diservis, butuh dana besar</option>
      <option value="Rusak-kaki kursi patah">Rusak-kaki kursi patah</option>
      <option value="Hilang">Hilang</option>
      <option value="Rusak-Habis Masa Pakai">Rusak-Habis Masa Pakai</option>
      <option value="Rusak-Sedang diservis">Rusak-Sedang diservis</option>
    </select>

    <label>Kode Barang</label>
    <input type="text" name="kode_barang">

    <label>Kategori Barang <span style="color:red">*</span></label>
    <select name="kategori_barang" id="kategori_barang" required onchange="handleKategoriChange()">
      <option value="">-- Pilih Kategori --</option>
      <?php while ($row = $kategoriList->fetch_assoc()): ?>
        <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['nama_kategori']); ?></option>
      <?php endwhile; ?>
    </select>

    <!-- Field tambahan kendaraan -->
    <div id="kendaraanFields" style="display:none;">
      <label>Nomor Plat</label>
      <input type="text" name="nomor_plat">
      <label>Tanggal Pajak Berlaku Sampai</label>
      <input type="date" name="tanggal_pajak">
      <label>Penanggung Jawab</label>
      <input type="text" name="penanggung_jawab">
    </div>

    <label>Program Pendanaan</label>
    <select name="program_pendanaan">
      <option value="">-- Pilih Program Pendanaan --</option>
      <?php while ($row = $programList->fetch_assoc()): ?>
        <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['nama_program']); ?></option>
      <?php endwhile; ?>
    </select>

    <label>Lokasi Barang <span style="color:red">*</span></label>
    <select name="lokasi_barang" required>
      <option value="">-- Pilih Lokasi --</option>
      <?php while ($row = $lokasiList->fetch_assoc()): ?>
        <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['nama_lokasi']); ?></option>
      <?php endwhile; ?>
    </select>

    <label>Foto Barang</label>
    <input type="text" name="foto_barang" placeholder="Nama file atau URL gambar">

    <button type="submit" class="btn">💾 Simpan Data</button>
  </form>
</div>

<script>
function handleKategoriChange() {
  const kategoriSelect = document.getElementById('kategori_barang');
  const kendaraanFields = document.getElementById('kendaraanFields');

  const selectedText = kategoriSelect.options[kategoriSelect.selectedIndex].text.toLowerCase();

  // Jika kategori mengandung "kendaraan", tampilkan field kendaraan
  if (selectedText.includes('kendaraan')) {
    kendaraanFields.style.display = 'block';
  } else {
    kendaraanFields.style.display = 'none';
  }
}
</script>

<?php include '../../includes/footer.php'; ?>
