<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

// --- Ambil data untuk dropdown ---
$kategoriList = $conn->query("SELECT id, nama_kategori FROM kategori_barang ORDER BY nama_kategori ASC");
$lokasiList = $conn->query("SELECT id, nama_lokasi FROM lokasi_barang ORDER BY nama_lokasi ASC");
$programList = $conn->query("SELECT id, nama_program FROM program_pendanaan ORDER BY nama_program ASC");

// --- Proses jika form disubmit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // --- Ambil semua input dengan aman ---
  $nama_barang       = trim($_POST['nama_barang'] ?? '');
  $deskripsi         = $_POST['deskripsi'] ?? null;
  $jumlah_unit       = (int) ($_POST['jumlah_unit'] ?? 1);
  $nomor_seri        = $_POST['nomor_seri'] ?? null;
  $harga_pembelian   = $_POST['harga_pembelian'] ?? null;
  $waktu_perolehan   = $_POST['waktu_perolehan'] ?? null;
  $kondisi_barang    = $_POST['kondisi_barang'] ?? 'Baik';
  $kode_penomoran    = $_POST['kode_penomoran'] ?? null;
  $kategori_barang   = (int) ($_POST['kategori_barang'] ?? 0);
  $nomor_plat        = $_POST['nomor_plat'] ?? null;
  $program_pendanaan = !empty($_POST['program_pendanaan']) ? (int)$_POST['program_pendanaan'] : null;
  $lokasi_barang     = (int) ($_POST['lokasi_barang'] ?? 0);
  $foto_barang       = $_POST['foto_barang'] ?? null;
  $status_penggunaan = 'Aktif';
  $tanggal_pajak     = $_POST['tanggal_pajak'] ?? null;
  $penanggung_jawab  = $_POST['penanggung_jawab'] ?? null;
  $user_input        = $_SESSION['user_id'] ?? null; // Pastikan session user sudah aktif

  // --- Validasi field wajib ---
  if (empty($nama_barang) || empty($jumlah_unit) || empty($kategori_barang) || empty($lokasi_barang) || empty($kondisi_barang)) {
    echo "<div class='alert red'>⚠️ Harap isi semua field wajib yang bertanda *!</div>";
  } else {
    // --- Query INSERT aman ---
    $sql = "INSERT INTO aset_barang 
      (nama_barang, deskripsi, jumlah_unit, nomor_seri, harga_pembelian, waktu_perolehan, kondisi_barang, kode_penomoran, kategori_barang, nomor_plat, program_pendanaan, lokasi_barang, user_input, foto_barang, status_penggunaan, tanggal_pajak, penanggung_jawab, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
      die("<div class='alert red'>❌ Query prepare gagal: " . $conn->error . "</div>");
    }

    $stmt->bind_param(
      "ssisssssissiiisss",
      $nama_barang,
      $deskripsi,
      $jumlah_unit,
      $nomor_seri,
      $harga_pembelian,
      $waktu_perolehan,
      $kondisi_barang,
      $kode_penomoran,
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
      echo "<script>alert('✅ Data aset berhasil ditambahkan!'); window.location='output_aset.php';</script>";
      exit;
    } else {
      echo "<div class='alert red'>❌ Gagal menambah data: " . $stmt->error . "</div>";
    }
  }
}
?>

<div class="page">
  <h2>Tambah Aset Baru</h2>
  <form method="post" class="form-section">

    <label>Nama Barang <span style="color:red">*</span></label>
    <input type="text" name="nama_barang" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi" placeholder="Keterangan tambahan"></textarea>

    <label>Jumlah Unit <span style="color:red">*</span></label>
    <input type="number" name="jumlah_unit" required min="1" value="1">

    <label>Nomor Seri</label>
    <input type="text" name="nomor_seri" placeholder="(Opsional)">

    <label>Harga Pembelian (Rp)</label>
    <input type="number" name="harga_pembelian" step="0.01" placeholder="(Opsional)">

    <label>Tanggal / Waktu Perolehan</label>
    <input type="date" name="waktu_perolehan">

    <label>Kondisi Barang <span style="color:red">*</span></label>
    <select name="kondisi_barang" required>
      <option value="Baik">Baik</option>
      <option value="Rusak">Rusak</option>
      <option value="Hilang">Hilang</option>
    </select>

    <label>Kode Penomoran</label>
    <input type="text" name="kode_penomoran" placeholder="Kode internal (opsional)">

    <label>Kategori Barang <span style="color:red">*</span></label>
    <select name="kategori_barang" id="kategori_barang" required onchange="toggleKendaraanFields()">
      <option value="">-- Pilih Kategori --</option>
      <?php while ($row = $kategoriList->fetch_assoc()): ?>
        <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['nama_kategori']); ?></option>
      <?php endwhile; ?>
    </select>

    <div id="kendaraanFields" style="display:none;">
      <label>Nomor Plat</label>
      <input type="text" name="nomor_plat" placeholder="(Khusus kendaraan)">

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
function toggleKendaraanFields() {
  const kategori = document.getElementById('kategori_barang').value;
  const kendaraanFields = document.getElementById('kendaraanFields');
  kendaraanFields.style.display = (kategori === '4') ? 'block' : 'none';
}
</script>

<?php include '../../includes/footer.php'; ?>
