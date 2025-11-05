<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';
include '../../includes/auth_check.php';

if (!has_access(['Admin', 'Auditor'])) {
  echo "<script>alert('Anda tidak memiliki izin untuk melihat detail aset!');window.location='" . BASE_URL . "modules/aset_barang/read.php';</script>";
  exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='alert red'>ID aset tidak ditemukan.</div>";
  exit;
}

// Ambil data aset dengan JOIN ke kategori, lokasi, program
$q = mysqli_query($conn, "
    SELECT ab.*, 
           k.nama_kategori, 
           l.nama_lokasi, 
           p.nama_program
    FROM aset_barang ab
    LEFT JOIN kategori_barang k ON ab.kategori_barang = k.id
    LEFT JOIN lokasi_barang l ON ab.lokasi_barang = l.id
    LEFT JOIN program_pendanaan p ON ab.program_pendanaan = p.id
    WHERE ab.id = '$id'
");
$aset = mysqli_fetch_assoc($q);

if (!$aset) {
  echo "<div class='alert red'>Data aset tidak ditemukan!</div>";
  exit;
}
?>

<div class="page">
  <div class="header">
    <h2>Detail Data Aset</h2>
    <!-- Link Kembali menggunakan BASE_URL -->
    <a href="<?= BASE_URL ?>modules/aset_barang/read.php" class="btn">← Kembali</a>
  </div>

  <div class="card">
    <table class="table">
      <tr><th width="220">Nama Barang</th><td><?= htmlspecialchars($aset['nama_barang']) ?></td></tr>
      <tr><th>Deskripsi</th><td><?= nl2br(htmlspecialchars($aset['deskripsi'])) ?></td></tr>
      <tr><th>Kategori Barang</th><td><?= htmlspecialchars($aset['nama_kategori'] ?? '-') ?></td></tr>
      <tr><th>Jumlah Unit</th><td><?= htmlspecialchars($aset['jumlah_unit']) ?></td></tr>
      <tr><th>Nomor Seri</th><td><?= htmlspecialchars($aset['nomor_seri']) ?></td></tr>
      <tr><th>Kode Penomoran</th><td><?= htmlspecialchars($aset['kode_penomoran']) ?></td></tr>
      <tr><th>Harga Pembelian</th><td>Rp <?= number_format($aset['harga_pembelian'], 0, ',', '.') ?></td></tr>
      <tr><th>Waktu Perolehan</th><td><?= htmlspecialchars($aset['waktu_perolehan']) ?></td></tr>
      <tr><th>Lokasi Barang</th><td><?= htmlspecialchars($aset['nama_lokasi'] ?? '-') ?></td></tr>
      <tr><th>Kondisi Barang</th>
          <td>
            <?php
              $color = [
                'Baik' => 'green',
                'Rusak' => 'red',
                'Hilang' => 'yellow'
              ][$aset['kondisi_barang']] ?? 'muted';
              echo "<span class='alert {$color}' style='display:inline-block;padding:4px 10px;border-radius:6px;font-weight:600;'>{$aset['kondisi_barang']}</span>";
            ?>
          </td>
      </tr>
      <tr><th>Program Pendanaan</th><td><?= htmlspecialchars($aset['nama_program'] ?? '-') ?></td></tr>
      <tr><th>Penanggung Jawab</th><td><?= htmlspecialchars($aset['penanggung_jawab']) ?></td></tr>
      
      <?php if ($aset['nama_kategori'] === 'Kendaraan'): ?>
      <tr><th>Nomor Plat</th><td><?= htmlspecialchars($aset['nomor_plat']) ?></td></tr>
      <tr><th>Tanggal Pajak</th>
          <td>
            <?= htmlspecialchars($aset['tanggal_pajak']) ?>
            <?php
              if (!empty($aset['tanggal_pajak'])) {
                $today = new DateTime();
                $due = new DateTime($aset['tanggal_pajak']);
                $diff = $today->diff($due)->days;

                if ($today < $due && $diff <= 30) {
                  echo "<div class='alert yellow' style='margin-top:6px;'>⚠ Pajak akan jatuh tempo dalam {$diff} hari.</div>";
                } elseif ($today > $due) {
                  echo "<div class='alert red' style='margin-top:6px;'>⛔ Pajak kendaraan sudah lewat jatuh tempo!</div>";
                } else {
                  echo "<div class='alert green' style='margin-top:6px;'>✅ Pajak kendaraan masih aktif.</div>";
                }
              }
            ?>
          </td>
      </tr>
      <?php endif; ?>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>