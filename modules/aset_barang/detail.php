<?php
include '../../includes/header.php';
include '../../config/db.php';
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

// --- Format tanggal waktu_perolehan agar tampil seperti "11 November 2025"
$formatted_perolehan = '-';
if (!empty($aset['waktu_perolehan']) && $aset['waktu_perolehan'] !== '0000-00-00') {
  // Nama bulan Indonesia
  $bulan_indonesia = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
  ];
  $bulan = $bulan_indonesia[date('F', strtotime($aset['waktu_perolehan']))];
  $formatted_perolehan = date('d', strtotime($aset['waktu_perolehan'])) . ' ' . $bulan . ' ' . date('Y', strtotime($aset['waktu_perolehan']));
}
?>

<div class="page">
  <div class="header">
    <h2>Detail Data Aset</h2>
    <a href="<?= BASE_URL ?>modules/aset_barang/read.php" class="btn">← Kembali</a>
  </div>

  <div class="card">
    <table class="table">
      <tr><th width="220">Nama Barang</th><td><?= htmlspecialchars($aset['nama_barang']) ?></td></tr>
      <tr><th>Deskripsi</th><td><?= nl2br(htmlspecialchars($aset['deskripsi'])) ?></td></tr>
      <tr><th>Kategori Barang</th><td><?= htmlspecialchars($aset['nama_kategori'] ?? '-') ?></td></tr>
      <tr><th>Jumlah Unit</th><td><?= htmlspecialchars($aset['jumlah_unit']) ?></td></tr>
      <tr><th>Nomor Seri</th><td><?= htmlspecialchars($aset['nomor_seri'] ?? '-') ?></td></tr>

      <!-- Tambahan: Nomor Urut Barang -->
      <tr><th>Nomor Urut Barang</th><td><?= htmlspecialchars($aset['nomor_urut_barang'] ?? '-') ?></td></tr>

      <tr><th>Kode Penomoran</th><td><?= htmlspecialchars($aset['kode_penomoran']) ?></td></tr>
      <tr><th>Harga Pembelian</th><td>Rp <?= number_format($aset['harga_pembelian'], 0, ',', '.') ?></td></tr>

      <!-- Format tanggal sesuai input manual -->
      <tr><th>Waktu Perolehan</th><td><?= htmlspecialchars($formatted_perolehan) ?></td></tr>

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
      <tr><th>Nomor Plat</th><td><?= htmlspecialchars($aset['nomor_plat'] ?? '-') ?></td></tr>
      <tr><th>Tanggal Pajak</th>
          <td>
            <?php
              if (!empty($aset['tanggal_pajak'])) {
                $bulan_pajak = $bulan_indonesia[date('F', strtotime($aset['tanggal_pajak']))];
                $tanggal_pajak = date('d', strtotime($aset['tanggal_pajak'])) . ' ' . $bulan_pajak . ' ' . date('Y', strtotime($aset['tanggal_pajak']));
                echo htmlspecialchars($tanggal_pajak);

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
              } else {
                echo "-";
              }
            ?>
          </td>
      </tr>
      <?php endif; ?>
    </table>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
