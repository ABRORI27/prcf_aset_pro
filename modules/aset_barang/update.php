<?php
include '../../includes/header.php';
include '../../config/db.php';

// Ambil ID dari URL
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

// === UPDATE PROSES ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang       = $_POST['nama_barang'];
    $deskripsi         = $_POST['deskripsi'];
    $jumlah_unit       = $_POST['jumlah_unit'];
    $nomor_seri        = trim($_POST['nomor_seri']);
    $nomor_urut_barang = $_POST['nomor_urut_barang'];
    $harga_pembelian   = $_POST['harga_pembelian'];
    $waktu_perolehan   = $_POST['waktu_perolehan'];
    $lokasi_barang     = $_POST['lokasi_barang'];
    $kondisi_barang    = $_POST['kondisi_barang'];
    $kode_penomoran    = $_POST['kode_penomoran'];
    $program_pendanaan = !empty($_POST['program_pendanaan']) ? $_POST['program_pendanaan'] : null;
    $kategori_barang   = $_POST['kategori_barang'];
    $nomor_plat        = $_POST['nomor_plat'] ?? null;
    $tanggal_pajak     = $_POST['tanggal_pajak'] ?? null;
    $penanggung_jawab  = $_POST['penanggung_jawab'] ?? null;

    // Hanya kategori Kendaraan (4) dan Field Equipment (5) yang simpan field kendaraan
    if (!in_array($kategori_barang, [4,5])) {
        $nomor_plat = null;
        $tanggal_pajak = null;
        $penanggung_jawab = null;
    }

    if ($nomor_seri === '') $nomor_seri = null;

    // Update database
    $stmtUpdate = $conn->prepare("
        UPDATE aset_barang SET 
            nama_barang = ?, deskripsi = ?, jumlah_unit = ?, nomor_seri = ?,
            nomor_urut_barang = ?, harga_pembelian = ?, waktu_perolehan = ?, lokasi_barang = ?,
            kondisi_barang = ?, kode_penomoran = ?, program_pendanaan = ?,
            kategori_barang = ?, nomor_plat = ?, tanggal_pajak = ?, penanggung_jawab = ?
        WHERE id = ?
    ");

    $stmtUpdate->bind_param(
        "ssississssissssi",
        $nama_barang, $deskripsi, $jumlah_unit, $nomor_seri,
        $nomor_urut_barang, $harga_pembelian, $waktu_perolehan, $lokasi_barang,
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

        <label>No Urut Barang</label>
        <input type="text" name="nomor_urut_barang" value="<?= $aset['nomor_urut_barang'] ?>">

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
            <option value="Habis Masa Pakai" <?= $aset['kondisi_barang']=='Habis Masa Pakai'?'selected':'' ?>>Habis Masa Pakai</option>
            <option value="Sedang Diservis" <?= $aset['kondisi_barang']=='Sedang Diservis'?'selected':'' ?>>Sedang Diservis</option>
        </select>

        <label>Kode Penomoran</label>
        <input type="text" name="kode_penomoran" value="<?= $aset['kode_penomoran'] ?>">

        <label>Program Pendanaan</label>
        <select name="program_pendanaan">
            <option value="">-- Pilih Program --</option>
            <?php
            $programList = $conn->query("SELECT id, nama_program FROM program_pendanaan ORDER BY nama_program ASC");
            while ($p = $programList->fetch_assoc()) {
                $selected = ($aset['program_pendanaan'] == $p['id']) ? 'selected' : '';
                echo "<option value='{$p['id']}' $selected>{$p['nama_program']}</option>";
            }
            ?>
        </select>

        <label>Kategori Barang</label>
        <select name="kategori_barang" id="kategori_barang" onchange="toggleKendaraanFields()">
            <option value="1" <?= $aset['kategori_barang']==1?'selected':'' ?>>Office Furniture</option>
            <option value="2" <?= $aset['kategori_barang']==2?'selected':'' ?>>Furniture Kantor</option>
            <option value="3" <?= $aset['kategori_barang']==3?'selected':'' ?>>Field Equipment (Non-Kendaraan)</option>
            <option value="4" <?= $aset['kategori_barang']==4?'selected':'' ?>>Kendaraan</option>
            <option value="5" <?= $aset['kategori_barang']==5?'selected':'' ?>>Field Equipment (Kendaraan)</option>
            <option value="6" <?= $aset['kategori_barang']==6?'selected':'' ?>>Fire Equipment</option>
            <option value="7" <?= $aset['kategori_barang']==7?'selected':'' ?>>Office Equipment</option>
        </select>

        <!-- Field tambahan Kendaraan / Field Equipment -->
        <div id="kendaraanFields" style="<?= in_array($aset['kategori_barang'], [4,5]) ? 'display:block;' : 'display:none;' ?>">
            <label>Nomor Plat</label>
            <input type="text" name="nomor_plat" value="<?= $aset['nomor_plat'] ?>">

            <label>Tanggal Pajak</label>
            <input type="date" name="tanggal_pajak" value="<?= $aset['tanggal_pajak'] ?>">

            <label>Penanggung Jawab</label>
            <input type="text" name="penanggung_jawab" value="<?= $aset['penanggung_jawab'] ?>">
        </div>

        <button type="submit" class="btn">💾 Simpan Perubahan</button>
    </form>
</div>

<script>
function toggleKendaraanFields() {
    const kategori = document.getElementById('kategori_barang').value;
    document.getElementById('kendaraanFields').style.display = (kategori == 4 || kategori == 5) ? 'block' : 'none';
}
</script>

<?php include '../../includes/footer.php'; ?>
