<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

$err = '';
$nomor_seri = $_GET['nomor_seri'] ?? null;

if (!$nomor_seri) {
  echo "<div class='alert red'>❌ Nomor seri kendaraan tidak ditemukan di URL.</div>";
  exit;
}

// Ambil data kendaraan + nama aset
$query = "
  SELECT 
    k.nomor_seri,
    k.aset_id,
    a.nama_barang AS nama_kendaraan,
    k.nomor_plat,
    k.tanggal_pajak,
    k.penanggung_jawab
  FROM kendaraan k
  LEFT JOIN aset_barang a ON k.aset_id = a.id
  WHERE k.nomor_seri = ?
  LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nomor_seri);
$stmt->execute();
$result = $stmt->get_result();
$kendaraan = $result->fetch_assoc();

if (!$kendaraan) {
  echo "<div class='alert red'>❌ Data kendaraan dengan nomor seri <b>$nomor_seri</b> tidak ditemukan.</div>";
  exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_kendaraan = trim($_POST['nama_kendaraan']);
  $nomor_plat = trim($_POST['nomor_plat']);
  $tanggal_pajak = $_POST['tanggal_pajak'] ?: null;
  $penanggung_jawab = trim($_POST['penanggung_jawab']);

  // Update aset_barang (nama_kendaraan)
  $stmtAset = $conn->prepare("UPDATE aset_barang SET nama_barang = ? WHERE id = ?");
  $stmtAset->bind_param("si", $nama_kendaraan, $kendaraan['aset_id']);
  $stmtAset->execute();

  // Update kendaraan
  $stmtKendaraan = $conn->prepare("
    UPDATE kendaraan 
    SET nomor_plat = ?, tanggal_pajak = ?, penanggung_jawab = ?
    WHERE nomor_seri = ?
  ");
  $stmtKendaraan->bind_param("ssss", $nomor_plat, $tanggal_pajak, $penanggung_jawab, $nomor_seri);

  if ($stmtKendaraan->execute()) {
    echo "<script>alert('✅ Data kendaraan berhasil diperbarui!');window.location='read.php';</script>";
    exit;
  } else {
    $err = "❌ Gagal update: " . $stmtKendaraan->error;
  }
}
?>

<div class="page">
  <h2>Edit Data Kendaraan</h2>

  <?php if ($err): ?>
    <div class="alert red"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="post" class="form-section">
    <label>Nama Kendaraan</label>
    <input type="text" name="nama_kendaraan" value="<?= htmlspecialchars($kendaraan['nama_kendaraan']) ?>" required>

    <label>Nomor Plat</label>
    <input type="text" name="nomor_plat" value="<?= htmlspecialchars($kendaraan['nomor_plat']) ?>" required>

    <label>Tanggal Pajak</label>
    <input type="date" name="tanggal_pajak" value="<?= htmlspecialchars($kendaraan['tanggal_pajak']) ?>">

    <label>Penanggung Jawab</label>
    <input type="text" name="penanggung_jawab" value="<?= htmlspecialchars($kendaraan['penanggung_jawab']) ?>" required>

    <button type="submit" class="btn">Simpan Perubahan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
