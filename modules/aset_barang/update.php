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
    $kode_barang       = $_POST['kode_barang'];
    $program_pendanaan = !empty($_POST['program_pendanaan']) ? $_POST['program_pendanaan'] : null;
    $kategori_barang   = $_POST['kategori_barang'];
    
    // ✅ PERBAIKAN: Ambil field kendaraan dengan kondisi yang benar
    $nomor_plat        = ($_POST['nomor_plat'] && $kategori_barang == 4) ? $_POST['nomor_plat'] : null;
    $tanggal_pajak     = ($_POST['tanggal_pajak'] && $kategori_barang == 4) ? $_POST['tanggal_pajak'] : null;
    $penanggung_jawab  = ($_POST['penanggung_jawab'] && $kategori_barang == 4) ? $_POST['penanggung_jawab'] : null;
    
    // Ambil periode dari form
    $periode_tahun     = $_POST['periode_tahun'] ?? null;
    $periode_bulan     = $_POST['periode_bulan'] ?? null;

    // Handle empty values
    if ($nomor_seri === '') $nomor_seri = null;
    if ($periode_tahun === '') $periode_tahun = null;
    if ($periode_bulan === '') $periode_bulan = null;

    // Update database
    $stmtUpdate = $conn->prepare("
        UPDATE aset_barang SET 
            nama_barang = ?, deskripsi = ?, jumlah_unit = ?, nomor_seri = ?,
            nomor_urut_barang = ?, harga_pembelian = ?, waktu_perolehan = ?, lokasi_barang = ?,
            kondisi_barang = ?, kode_barang = ?, program_pendanaan = ?,
            kategori_barang = ?, nomor_plat = ?, tanggal_pajak = ?, penanggung_jawab = ?,
            periode_tahun = ?, periode_bulan = ?
        WHERE id = ?
    ");

    $stmtUpdate->bind_param(
        "ssississssissssiii",
        $nama_barang, $deskripsi, $jumlah_unit, $nomor_seri,
        $nomor_urut_barang, $harga_pembelian, $waktu_perolehan, $lokasi_barang,
        $kondisi_barang, $kode_barang, $program_pendanaan,
        $kategori_barang, $nomor_plat, $tanggal_pajak, $penanggung_jawab,
        $periode_tahun, $periode_bulan,
        $id
    );

    if ($stmtUpdate->execute()) {
        echo "<script>alert('✅ Data aset berhasil diperbarui!');window.location='read.php';</script>";
        exit;
    } else {
        echo "<div class='alert red'>❌ Gagal memperbarui data: " . $stmtUpdate->error . "</div>";
    }
}

// Ambil data dropdown untuk lokasi
$lokasiList = $conn->query("SELECT id, nama_lokasi FROM lokasi_barang ORDER BY nama_lokasi ASC");
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
        <input type="text" name="nomor_seri" value="<?= htmlspecialchars($aset['nomor_seri'] ?? '') ?>">

        <label>No Urut Barang</label>
        <input type="text" name="nomor_urut_barang" value="<?= $aset['nomor_urut_barang'] ?>">

        <label>Harga Pembelian</label>
        <input type="number" name="harga_pembelian" step="0.01" value="<?= $aset['harga_pembelian'] ?>">

        <label>Waktu Perolehan</label>
        <input type="date" name="waktu_perolehan" value="<?= $aset['waktu_perolehan'] ?>">

        <!-- Field Periode -->
        <label>Periode Tahun</label>
        <input type="number" name="periode_tahun" value="<?= $aset['periode_tahun'] ?>" min="2000" max="2030">

        <label>Periode Bulan</label>
        <select name="periode_bulan">
            <option value="">-- Pilih Bulan --</option>
            <?php
                $bulan_list = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                foreach ($bulan_list as $num => $nama) {
                    $selected = $aset['periode_bulan'] == $num ? 'selected' : '';
                    echo "<option value='$num' $selected>$nama</option>";
                }
            ?>
        </select>

        <!-- Lokasi Barang -->
        <label>Lokasi Barang</label>
        <select name="lokasi_barang" required>
            <option value="">-- Pilih Lokasi --</option>
            <?php 
            while ($lokasi = $lokasiList->fetch_assoc()) {
                $selected = ($aset['lokasi_barang'] == $lokasi['id']) ? 'selected' : '';
                echo "<option value='{$lokasi['id']}' $selected>{$lokasi['nama_lokasi']}</option>";
            }
            ?>
        </select>

        <label>Kondisi Barang</label>
        <select name="kondisi_barang" required>
            <option value="Baik" <?= $aset['kondisi_barang']=='Baik'?'selected':'' ?>>Baik</option>
            <option value="Rusak" <?= $aset['kondisi_barang']=='Rusak'?'selected':'' ?>>Rusak</option>
            <option value="Rusak-perlu diservis, butuh dana besar" <?= $aset['kondisi_barang']=='Rusak-perlu diservis, butuh dana besar'?'selected':'' ?>>rusak, perlu diservis, butuh dana besar</option>
            <option value="Rusak-kaki kursi patah" <?= $aset['kondisi_barang']=='Rusak-kaki kursi patah'?'selected':'' ?>>Rusak-kaki kursi patah</option>
            <option value="Hilang" <?= $aset['kondisi_barang']=='Hilang'?'selected':'' ?>>Hilang</option>
            <option value="Rusak-Habis Masa Pakai" <?= $aset['kondisi_barang']=='Rusak-Habis Masa Pakai'?'selected':'' ?>>Rusak-Habis Masa Pakai</option>
            <option value="Rusak-Sedang Diservis" <?= $aset['kondisi_barang']=='Rusak-Sedang Diservis'?'selected':'' ?>>Rusak-Sedang Diservis</option>
            <option value="Kurang baik-Perlu diservis" <?= $aset['kondisi_barang']=='Kurang baik-Perlu diservis'?'selected':'' ?>>Kurang baik-Perlu diservis</option>
        </select>

        <label>Kode Barang</label>
        <input type="text" name="kode_barang" value="<?= $aset['kode_barang'] ?>">

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
            <option value="3" <?= $aset['kategori_barang']==3?'selected':'' ?>>Field Equipment</option>
            <option value="4" <?= $aset['kategori_barang']==4?'selected':'' ?>>Kendaraan</option>
            <option value="5" <?= $aset['kategori_barang']==5?'selected':'' ?>>Fire Equipment</option>
            <option value="6" <?= $aset['kategori_barang']==6?'selected':'' ?>>Office Equipment</option>
            <option value="7" <?= $aset['kategori_barang']==7?'selected':'' ?>>Peralatan Lapangan</option>
        </select>

        <!-- ✅ PERBAIKAN: Field tambahan Kendaraan - Hanya untuk kategori 4 -->
        <div id="kendaraanFields" style="<?= $aset['kategori_barang'] == 4 ? 'display:block;' : 'display:none;' ?>">
            <label>Nomor Plat</label>
            <input type="text" name="nomor_plat" value="<?= htmlspecialchars($aset['nomor_plat'] ?? '') ?>">

            <label>Tanggal Pajak</label>
            <input type="date" name="tanggal_pajak" value="<?= $aset['tanggal_pajak'] ?>">

            <label>Penanggung Jawab</label>
            <input type="text" name="penanggung_jawab" value="<?= htmlspecialchars($aset['penanggung_jawab'] ?? '') ?>">
        </div>

        <button type="submit" class="btn">💾 Simpan Perubahan</button>
    </form>
</div>

<script>
function toggleKendaraanFields() {
    const kategori = document.getElementById('kategori_barang').value;
    // ✅ PERBAIKAN: Hanya kategori 4 (Kendaraan) yang tampilkan field kendaraan
    document.getElementById('kendaraanFields').style.display = (kategori == 4) ? 'block' : 'none';
    
    // ✅ PERBAIKAN: Jika bukan kendaraan, clear nilai field kendaraan
    if (kategori != 4) {
        document.querySelector('input[name="nomor_plat"]').value = '';
        document.querySelector('input[name="tanggal_pajak"]').value = '';
        document.querySelector('input[name="penanggung_jawab"]').value = '';
    }
}
</script>

<?php include '../../includes/footer.php'; ?>