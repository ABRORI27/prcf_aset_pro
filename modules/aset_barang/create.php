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
  $kode_barang    = $_POST['kode_barang'] ?? null;
  $kategori_barang   = (int) ($_POST['kategori_barang'] ?? 0);
  $nomor_plat        = $_POST['nomor_plat'] ?? null;
  $program_pendanaan = !empty($_POST['program_pendanaan']) ? (int)$_POST['program_pendanaan'] : null;
  $lokasi_barang     = (int) ($_POST['lokasi_barang'] ?? 0);
  $foto_barang       = $_POST['foto_barang'] ?? null;
  $status_penggunaan = 'Aktif';
  $tanggal_pajak     = $_POST['tanggal_pajak'] ?? null;
  $penanggung_jawab  = $_POST['penanggung_jawab'] ?? null;
  $user_input        = $_SESSION['user_id'] ?? null;

  // Ambil nama kategori untuk cek apakah furniture
  $kategoriCheck = $conn->query("SELECT nama_kategori FROM kategori_barang WHERE id = $kategori_barang")->fetch_assoc();
  $namaKategori = strtolower($kategoriCheck['nama_kategori'] ?? '');

  // --- Validasi umum ---
  if (empty($nama_barang) || empty($jumlah_unit) || empty($kategori_barang) || empty($lokasi_barang)) {
    echo "<div class='alert red'>⚠️ Harap isi semua field wajib!</div>";
  } else {

// --- Atur aturan khusus nomor seri (pengecualian kategori tertentu) ---
$namaKategoriLower = strtolower(trim($namaKategori));

// Cek apakah kategori termasuk pengecualian (tanpa peduli variasi nama)
if (
    str_contains($namaKategoriLower, 'furniture') ||
    str_contains($namaKategoriLower, 'office equipment') ||
    str_contains($namaKategoriLower, 'fire equipment') ||
    str_contains($namaKategoriLower, 'field equipment (non')
) {
    // kategori tertentu boleh kosong nomor seri
    if (empty($nomor_seri)) {
        $nomor_seri = null;
    }
} else {
    // kategori lain wajib isi nomor seri
    if (empty($nomor_seri)) {
        echo "<div class='alert red'>⚠️ Nomor seri wajib diisi untuk kategori selain Office Furniture, Furniture Kantor, Office Equipment, Field Equipment (Non-Kendaraan), dan Fire Equipment!</div>";
        exit;
    }
}




    // --- Tentukan nomor urut barang otomatis ---
    $queryUrut = $conn->prepare("SELECT MAX(nomor_urut_barang) AS max_urut FROM aset_barang WHERE nama_barang = ?");
    $queryUrut->bind_param("s", $nama_barang);
    $queryUrut->execute();
    $resultUrut = $queryUrut->get_result()->fetch_assoc();
    $nomor_urut_barang = ($resultUrut['max_urut'] ?? 0) + 1;

    // --- Query INSERT (18 kolom bind_param) ---
    $sql = "INSERT INTO aset_barang (
      nama_barang, deskripsi, jumlah_unit, nomor_seri, nomor_urut_barang, 
      harga_pembelian, waktu_perolehan, kondisi_barang, kode_barang, 
      kategori_barang, nomor_plat, program_pendanaan, lokasi_barang, 
      user_input, foto_barang, status_penggunaan, tanggal_pajak, penanggung_jawab
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
      echo "<div class='alert red'>❌ Gagal menyiapkan query: " . $conn->error . "</div>";
      exit;
    }

    $stmt->bind_param(
      "ssissdssssissiiiss",
      $nama_barang,        // s
      $deskripsi,          // s
      $jumlah_unit,        // i
      $nomor_seri,         // s
      $nomor_urut_barang,  // i
      $harga_pembelian,    // d
      $waktu_perolehan,    // s
      $kondisi_barang,     // s
      $kode_barang,     // s
      $kategori_barang,    // i
      $nomor_plat,         // s
      $program_pendanaan,  // i
      $lokasi_barang,      // i
      $user_input,         // i
      $foto_barang,        // s
      $status_penggunaan,  // s
      $tanggal_pajak,      // s
      $penanggung_jawab    // s
    );

if ($stmt->execute()) {
  // --- Ambil ID aset yang baru saja dimasukkan ---
  $aset_id = $conn->insert_id;

  // --- Cek apakah kategori adalah kendaraan atau subkategori kendaraan lapangan ---
  $isKendaraan = false;

  // Cek dari nama kategori utama
  if (str_contains($namaKategoriLower, 'kendaraan')) {
      $isKendaraan = true;
  }

  // Cek juga jika sub_kategori_lapangan dipilih 'kendaraan'
  if (isset($_POST['sub_kategori_lapangan']) && $_POST['sub_kategori_lapangan'] === 'kendaraan') {
      $isKendaraan = true;
  }

  // --- Jika aset termasuk kendaraan, otomatis insert ke tabel kendaraan ---
  if ($isKendaraan) {
      $insertKendaraan = $conn->prepare("
        INSERT INTO kendaraan (aset_id, nomor_seri, nomor_plat, tanggal_pajak, penanggung_jawab)
        VALUES (?, ?, ?, ?, ?)
      ");
      $insertKendaraan->bind_param(
        "issss",
        $aset_id,
        $nomor_seri,
        $nomor_plat,
        $tanggal_pajak,
        $penanggung_jawab
      );
      $insertKendaraan->execute();
  }

  // --- Tampilkan notifikasi sukses dan kembali ke halaman daftar aset ---
  echo "<script>alert('✅ Data aset berhasil ditambahkan!'); window.location='read.php';</script>";
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
    <textarea name="deskripsi"></textarea>

    <label>Jumlah Unit <span style="color:red">*</span></label>
    <input type="number" name="jumlah_unit" min="1" value="1" required>

    <label>Nomor Seri</label>
    <input type="text" name="nomor_seri" placeholder="(Kosongkan jika kategori Office Furniture, Peralatan Kantor, Fire Equipment, atau Field Equipment)">

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
      <option value="Rusak-kaki kursi patah">Rusak-kaki kursi patah</option>
      <option value="Hilang">Hilang</option>
      <option value="Habis masa pakai">Habis masa pakai</option>
      <option value="Sedang diservis">Sedang diservis</option>
    </select>

    <label>Kode Barang</label>
    <input type="text" name="kode_barang">

        <label>Kategori Barang <span style="color:red">*</span></label>
    <select name="kategori_barang" id="kategori_barang" required onchange="toggleSubKategori()">
      <option value="">-- Pilih Kategori --</option>
      <?php while ($row = $kategoriList->fetch_assoc()): ?>
        <?php if ($row['nama_kategori'] != 'Kendaraan'): // 🚫 Kendaraan disembunyikan ?>
          <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['nama_kategori']); ?></option>
        <?php endif; ?>
      <?php endwhile; ?>
    </select>

    <!-- Subkategori khusus Peralatan Lapangan -->
    <div id="subKategoriLapangan" style="display:none; margin-top:10px;">
      <label>Sub Kategori Peralatan Lapangan</label>
      <select name="sub_kategori_lapangan" id="sub_kategori_lapangan" onchange="toggleKendaraanFields()">
        <option value="">-- Pilih Sub Kategori --</option>
        <option value="kendaraan">Kendaraan Lapangan</option>
        <option value="non">Non-Kendaraan Lapangan</option>
      </select>
    </div>

    <!-- Field tambahan untuk kategori kendaraan -->
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
function toggleKendaraanFields() {
  const kategori = document.getElementById('kategori_barang').options[document.getElementById('kategori_barang').selectedIndex].text.toLowerCase();
  const kendaraanFields = document.getElementById('kendaraanFields');
  kendaraanFields.style.display = kategori === 'kendaraan' ? 'block' : 'none';
}
</script>

<?php include '../../includes/footer.php'; ?>
