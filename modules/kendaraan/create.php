<?php
include '../../includes/header.php';
include '../../config/db.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Cari kategori_id untuk "Kendaraan"
  $cekKat = mysqli_query($conn, "SELECT * FROM kategori_barang WHERE nama_kategori='Kendaraan' LIMIT 1");
  $kategori = mysqli_fetch_assoc($cekKat);
  
  if (!$kategori) {
    $err = "⚠️ Kategori 'Kendaraan' belum ada di tabel kategori_barang!";
  } else {

    echo "<p>Kategori ID: " . $kategori['nama_kategori'] . "</p>";

    $nomor_seri = trim($_POST['nomor_seri']);
    $nomor_plat = trim($_POST['nomor_plat']);
    $tanggal_pajak = $_POST['tanggal_pajak'] ?: null;
    $penanggung_jawab = trim($_POST['penanggung_jawab']);

    // 1️⃣ Insert otomatis ke aset_barang
    $stmtAset = $conn->prepare("INSERT INTO aset_barang (nama_barang, kategori_barang, kondisi_barang)
                                VALUES (?, ?, ?)");
    $nama_barang = $_POST['nama_kendaraan']; 
    $kategori_barang = 4; // misal ID kategori "Kendaraan"
    $kondisi = 'Baik';
    $stmtAset->bind_param("sis", $nama_barang, $kategori_barang, $kondisi);
    $stmtAset->execute();

    // 2️⃣ ambil aset_id yang baru saja di-insert
    $aset_id = $stmtAset->insert_id;

    // 3️⃣ insert ke kendaraan 
    $stmt = $conn->prepare("INSERT INTO kendaraan (nomor_seri, aset_id, nomor_plat, tanggal_pajak, penanggung_jawab)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $nomor_seri, $aset_id, $nomor_plat, $tanggal_pajak, $penanggung_jawab);

    if ($stmt->execute()) {
      echo "<script>alert('✅ Data kendaraan berhasil ditambahkan!');window.location='read.php';</script>";
      exit;
    } else {
      $err = '❌ Gagal menambah data: ' . $stmt->error;
    }

    // if (!$aset) {
    //   $err = "⚠️ Belum ada data aset untuk kategori 'Kendaraan' di tabel aset_barang!";
    // } else {
    // }
  }
}
?>


<div class="page">
  <h2>Tambah Kendaraan Baru</h2>

  <?php if ($err): ?>
    <div class="alert red"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="post" class="form-section">
    <label>Nama Kendaraan</label>
    <input type="text" name="nama_kendaraan" required placeholder="Masukkan nama kendaraan">

    <label>Nomor Seri</label>
    <input type="text" name="nomor_seri" required placeholder="Masukkan nomor seri kendaraan">

    <label>Nomor Plat</label>
    <input type="text" name="nomor_plat" required placeholder="Contoh: B 1234 CD">

    <label>Tanggal Pajak</label>
    <input type="date" name="tanggal_pajak">

    <label>Penanggung Jawab</label>
    <input type="text" name="penanggung_jawab" required placeholder="Nama orang yang bertanggung jawab">

    <button type="submit" class="btn">Simpan</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
