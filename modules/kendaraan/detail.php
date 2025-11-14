<?php
include '../../includes/header.php';
include '../../config/db.php';

// 🔐 CEK AKSES - Semua role bisa akses detail
if (!has_access([ROLE_ADMIN, ROLE_OPERATOR, ROLE_AUDITOR])) {
    $_SESSION['error'] = "Anda tidak memiliki akses ke modul ini.";
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

// Cek role user untuk menentukan akses
$user_role = $_SESSION['user']['role'] ?? '';
$is_auditor = ($user_role === ROLE_AUDITOR);

// Ambil data kendaraan berdasarkan nomor_seri
$nomor_seri = $_GET['nomor_seri'] ?? '';
$query = "
  SELECT 
    k.*,
    a.nama_barang AS nama_aset,
    a.deskripsi_barang,
    a.jumlah_unit,
    a.harga_pembelian,
    a.waktu_perolehan,
    a.lokasi_barang,
    a.kondisi_barang,
    a.kode_penomoran,
    a.program_dana,
    a.kategori_barang
  FROM kendaraan k
  LEFT JOIN aset_barang a ON k.aset_id = a.id
  WHERE k.nomor_seri = ?
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $nomor_seri);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$kendaraan = mysqli_fetch_assoc($result);

if (!$kendaraan) {
    $_SESSION['error'] = "Data kendaraan tidak ditemukan.";
    header('Location: read.php');
    exit();
}
?>

<div class="page">
  <div class="header">
    <h2>Detail Kendaraan</h2>
    <div class="header-actions">
      <a href="read.php" class="btn btn-secondary">← Kembali</a>
      <?php if (!$is_auditor): ?>
        <!-- TOMBOL EDIT HANYA UNTUK ADMIN & OPERATOR -->
        <a href="update.php?nomor_seri=<?= $kendaraan['nomor_seri'] ?>" class="btn btn-edit">✏️ Edit Data</a>
      <?php else: ?>
        <!-- INFO UNTUK AUDITOR -->
        <span style="color: #666; font-style: italic;">
          <i class="fas fa-eye"></i> Mode Lihat Saja
        </span>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="detail-section">
      <h3>Informasi Kendaraan</h3>
      <div class="detail-grid">
        <div class="detail-item">
          <label>Nomor Seri:</label>
          <span><?= htmlspecialchars($kendaraan['nomor_seri']) ?></span>
        </div>
        <div class="detail-item">
          <label>Nama Kendaraan:</label>
          <span><?= htmlspecialchars($kendaraan['nama_aset'] ?? '-') ?></span>
        </div>
        <div class="detail-item">
          <label>Nomor Plat:</label>
          <span><?= htmlspecialchars($kendaraan['nomor_plat'] ?? '-') ?></span>
        </div>
        <div class="detail-item">
          <label>Tanggal Pajak:</label>
          <span>
            <?php 
              if ($kendaraan['tanggal_pajak']) {
                $pajak_date = new DateTime($kendaraan['tanggal_pajak']);
                $today = new DateTime();
                $diff = $today->diff($pajak_date)->days;
                
                if ($pajak_date < $today) {
                  echo '<span style="color: red; font-weight: bold;">' . $pajak_date->format('d-m-Y') . ' (TERLAMBAT)</span>';
                } elseif ($diff <= 30) {
                  echo '<span style="color: orange; font-weight: bold;">' . $pajak_date->format('d-m-Y') . ' (SEGERA)</span>';
                } else {
                  echo $pajak_date->format('d-m-Y');
                }
              } else {
                echo '-';
              }
            ?>
          </span>
        </div>
        <div class="detail-item">
          <label>Penanggung Jawab:</label>
          <span><?= htmlspecialchars($kendaraan['penanggung_jawab'] ?? '-') ?></span>
        </div>
      </div>
    </div>

    <!-- Informasi Aset Barang (jika ada) -->
    <?php if ($kendaraan['aset_id']): ?>
    <div class="detail-section">
      <h3>Informasi Aset Terkait</h3>
      <div class="detail-grid">
        <div class="detail-item">
          <label>Deskripsi Barang:</label>
          <span><?= htmlspecialchars($kendaraan['deskripsi_barang'] ?? '-') ?></span>
        </div>
        <div class="detail-item">
          <label>Jumlah Unit:</label>
          <span><?= htmlspecialchars($kendaraan['jumlah_unit'] ?? '-') ?></span>
        </div>
        <div class="detail-item">
          <label>Harga Pembelian:</label>
          <span>Rp <?= number_format($kendaraan['harga_pembelian'] ?? 0, 0, ',', '.') ?></span>
        </div>
        <div class="detail-item">
          <label>Waktu Perolehan:</label>
          <span><?= $kendaraan['waktu_perolehan'] ? date('d-m-Y', strtotime($kendaraan['waktu_perolehan'])) : '-' ?></span>
        </div>
        <div class="detail-item">
          <label>Lokasi Barang:</label>
          <span><?= htmlspecialchars($kendaraan['lokasi_barang'] ?? '-') ?></span>
        </div>
        <div class="detail-item">
          <label>Kondisi Barang:</label>
          <span>
            <?php 
              $kondisi = $kendaraan['kondisi_barang'] ?? '';
              $color = match($kondisi) {
                'Baik' => 'green',
                'Rusak' => 'red', 
                'Hilang' => 'orange',
                default => 'gray'
              };
              echo '<span style="color: ' . $color . '; font-weight: bold;">' . htmlspecialchars($kondisi) . '</span>';
            ?>
          </span>
        </div>
        <div class="detail-item">
          <label>Kode Penomoran:</label>
          <span><?= htmlspecialchars($kendaraan['kode_penomoran'] ?? '-') ?></span>
        </div>
        <div class="detail-item">
          <label>Program Dana:</label>
          <span><?= htmlspecialchars($kendaraan['program_dana'] ?? '-') ?></span>
        </div>
        <div class="detail-item">
          <label>Kategori Barang:</label>
          <span><?= htmlspecialchars($kendaraan['kategori_barang'] ?? '-') ?></span>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <?php if ($is_auditor): ?>
    <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #2b6b4f;">
      <p style="margin: 0; color: #666; font-size: 0.9rem;">
        <i class="fas fa-info-circle"></i> 
        <strong>Role Auditor:</strong> Anda hanya dapat melihat detail kendaraan. 
        Untuk perubahan data, hubungi Admin atau Operator.
      </p>
    </div>
  <?php endif; ?>
</div>

<style>
.detail-section {
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #eee;
}

.detail-section h3 {
  color: #2b6b4f;
  margin-bottom: 1rem;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1rem;
}

.detail-item {
  display: flex;
  flex-direction: column;
}

.detail-item label {
  font-weight: bold;
  color: #555;
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
}

.detail-item span {
  padding: 0.5rem;
  background: #f8f9fa;
  border-radius: 4px;
  border-left: 3px solid #2b6b4f;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  padding: 0.5rem 1rem;
  text-decoration: none;
  border-radius: 4px;
}

.btn-secondary:hover {
  background: #5a6268;
}
</style>

<?php include '../../includes/footer.php'; ?>