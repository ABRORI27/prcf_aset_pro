<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // --- Wajib diisi ---
  $nama_barang     = trim($_POST['nama_barang']);
  $jumlah_unit     = (int) $_POST['jumlah_unit'];
  $kategori_id     = (int) $_POST['kategori_id'];
  $lokasi_id       = (int) $_POST['lokasi_id'];
  $kondisi_barang  = $_POST['kondisi_barang'];

  // --- Opsional (boleh kosong) ---
  $deskripsi        = !empty($_POST['deskripsi']) ? $_POST['deskripsi'] : null;
  $nomor_seri       = !empty($_POST['nomor_seri']) ? $_POST['nomor_seri'] : null;
  $harga_pembelian  = !empty($_POST['harga_pembelian']) ? $_POST['harga_pembelian'] : null;
  $tanggal_perolehan = !empty($_POST['tanggal_perolehan']) ? $_POST['tanggal_perolehan'] : null;
  $kode_barang      = !empty($_POST['kode_barang']) ? $_POST['kode_barang'] : null;
  $program_id       = !empty($_POST['program_id']) ? $_POST['program_id'] : null;
  $foto_barang      = !empty($_POST['foto_barang']) ? $_POST['foto_barang'] : null;
  $status_penggunaan = 'Aktif'; // default aktif
  $user_input       = 1; // TODO: ganti nanti dengan $_SESSION['user_id'] kalau sistem login sudah aktif

  // --- Field khusus kendaraan (optional) ---
  $nomor_plat       = !empty($_POST['nomor_plat']) ? $_POST['nomor_plat'] : null;
  $tanggal_pajak    = !empty($_POST['tanggal_pajak']) ? $_POST['tanggal_pajak'] : null;
  $penanggung_jawab = !empty($_POST['penanggung_jawab']) ? $_POST['penanggung_jawab'] : null;

  // Validasi minimal: wajib diisi
  if (!$nama_barang || !$jumlah_unit || !$kategori_id || !$lokasi_id || !$kondisi_barang) {
    echo "<div class='alert red'>⚠️ Harap isi semua field wajib!</div>";
  } else {
    // --- SQL: siapin prepared statement ---
    $sql = "INSERT INTO aset_barang 
      (nama_barang, deskripsi, jumlah_unit, nomor_seri, harga_pembelian, tanggal_perolehan, kondisi_barang, kode_barang, kategori_id, program_id, lokasi_id, foto_barang, status_penggunaan, user_input, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
      "ssisssssiiissi",
      $nama_barang,
      $deskripsi,
      $jumlah_unit,
      $nomor_seri,
      $harga_pembelian,
      $tanggal_perolehan,
      $kondisi_barang,
      $kode_barang,
      $kategori_id,
      $program_id,
      $lokasi_id,
      $foto_barang,
      $status_penggunaan,
      $user_input
    );

    if ($stmt->execute()) {
      echo "<script>alert('✅ Data aset berhasil ditambahkan!');window.location='output_aset.php';</script>";
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
    <input type="number" name="jumlah_unit" required min="1">

    <label>Nomor Seri</label>
    <input type="text" name="nomor_seri" placeholder="(Opsional)">

    <label>Harga Pembelian (Rp)</label>
    <input type="number" name="harga_pembelian" placeholder="(Opsional)">

    <label>Tanggal Perolehan</label>
    <input type="date" name="tanggal_perolehan">

    <label>Kondisi Barang <span style="color:red">*</span></label>
    <select name="kondisi_barang" required>
      <option value="Baik">Baik</option>
      <option value="Rusak">Rusak</option>
      <option value="Hilang">Hilang</option>
    </select>

    <label>Kode Barang</label>
    <input type="text" name="kode_barang" placeholder="Kode internal (opsional)">

    <label>Kategori Barang <span style="color:red">*</span></label>
    <select name="kategori_id" id="kategori_barang" required onchange="toggleKendaraanFields()">
      <option value="">-- Pilih Kategori --</option>
      <option value="1">Peralatan Kantor</option>
      <option value="2">Furniture</option>
      <option value="3">Peralatan Lapangan</option>
      <option value="4">Kendaraan</option>
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
    <input type="number" name="program_id" placeholder="ID program (opsional)">

    <label>Lokasi Barang <span style="color:red">*</span></label>
    <input type="number" name="lokasi_id" required placeholder="ID lokasi">

    <label>Foto Barang</label>
    <input type="text" name="foto_barang" placeholder="URL atau nama file">

    <button type="submit" class="btn">Simpan Data</button>
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
