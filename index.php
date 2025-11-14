<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/includes/header.php';

// === QUERY DATA DASHBOARD ===
$total_aset = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM aset_barang"))['total'] ?? 0;
$aktif_aset = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM aset_barang WHERE status_penggunaan='Aktif'"))['total'] ?? 0;
$rusak_aset = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM aset_barang WHERE kondisi_barang IN ('Rusak','Hilang')"))['total'] ?? 0;
$total_kendaraan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM kendaraan"))['total'] ?? 0;
$notif_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM notifikasi WHERE status='Belum Terkirim'"))['total'] ?? 0;

$today = date('Y-m-d');
$limit = date('Y-m-d', strtotime('+7 days'));
$pajak_due = mysqli_query($conn, "
  SELECT k.*, a.nama_barang 
  FROM kendaraan k 
  JOIN aset_barang a ON a.id = k.aset_id
  WHERE k.tanggal_pajak BETWEEN '$today' AND '$limit'
  ORDER BY k.tanggal_pajak ASC
");

// Cek role user
$user_role = $_SESSION['user']['role'] ?? '';
$is_auditor = ($user_role === 'Auditor');
?>

<div class="page dashboard">
  <h2>Dashboard Aset Barang PRCF Indonesia</h2>
  <p>
    Halo <strong><?= e($_SESSION['user']['nama_lengkap'] ?? 'User') ?></strong>!  
    Role Anda: <b><?= e($_SESSION['user']['role'] ?? '-') ?></b>.
  </p>

  <!-- Statistik Utama -->
  <div class="stats-grid">
    <div class="card stat"><h3><?= $total_aset ?></h3><p>Total Aset</p></div>
    <div class="card stat"><h3><?= $aktif_aset ?></h3><p>Aset Aktif</p></div>
    <div class="card stat"><h3><?= $rusak_aset ?></h3><p>Aset Rusak / Hilang</p></div>
    <div class="card stat"><h3><?= $total_kendaraan ?></h3><p>Kendaraan Terdaftar</p></div>
    <?php if (!$is_auditor): ?>
      <div class="card stat"><h3><?= $notif_pending ?></h3><p>Notifikasi Belum Terkirim</p></div>
    <?php endif; ?>
  </div>

  <!-- Reminder Pajak -->
  <?php if (!$is_auditor): ?>
    <div class="card mt-4 reminder-card">
      <h3>🚗 Pajak Kendaraan Mendekati Jatuh Tempo</h3>
      <div class="reminder-content">
        <?php if (mysqli_num_rows($pajak_due) == 0): ?>
          <p>Tidak ada kendaraan dengan pajak mendekati jatuh tempo dalam 7 hari ke depan.</p>
        <?php else: ?>
          <table class="table pajak-table">
            <thead>
              <tr>
                <th>Nama Kendaraan</th>
                <th>Nomor Plat</th>
                <th>Tanggal Pajak</th>
                <th>Penanggung Jawab</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($pajak_due)):
                $diff = (new DateTime())->diff(new DateTime($row['tanggal_pajak']))->days;
                $color = ($diff <= 3) ? 'red' : 'yellow';
              ?>
              <tr>
                <td><?= e($row['nama_barang']) ?></td>
                <td><?= e($row['nomor_plat']) ?></td>
                <td><span class="alert <?= $color ?>"><?= e($row['tanggal_pajak']) ?></span></td>
                <td><?= e($row['penanggung_jawab']) ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Navigasi Modul - TAMPILKAN SELALU, tapi konten berbeda berdasarkan role -->
  <div class="card mt-4">
    <h3>📂 Navigasi Modul</h3>
    <div class="module-links">
      <!-- ASET BARANG - TAMPIL UNTUK SEMUA ROLE -->
      <a href="<?= base_url('modules/aset_barang/read.php') ?>" class="btn">
        <i class="fas fa-boxes"></i> Aset Barang
      </a>
      
      <!-- MODUL LAINNYA - HANYA UNTUK ADMIN & OPERATOR -->
      <?php if (!$is_auditor): ?>
        <a href="<?= base_url('modules/kendaraan/read.php') ?>" class="btn">
          <i class="fas fa-car"></i> Kendaraan
        </a>
        <a href="<?= base_url('modules/kategori/read.php') ?>" class="btn">
          <i class="fas fa-tags"></i> Kategori
        </a>
        <a href="<?= base_url('modules/lokasi/read.php') ?>" class="btn">
          <i class="fas fa-map-marker-alt"></i> Lokasi
        </a>
        <a href="<?= base_url('modules/program/read.php') ?>" class="btn">
          <i class="fas fa-money-bill-wave"></i> Program Pendanaan
        </a>
        <a href="<?= base_url('modules/notifikasi/read.php') ?>" class="btn">
          <i class="fas fa-bell"></i> Notifikasi
        </a>
      <?php else: ?>
        <!-- TAMBAHAN UNTUK AUDITOR JIKA PERLU -->
        <a href="<?= base_url('modules/aset_barang/export.php') ?>" class="btn">
          <i class="fas fa-file-pdf"></i> Export Laporan
        </a>
      <?php endif; ?>
    </div>
    
    <?php if ($is_auditor): ?>
      <p style="margin-top: 15px; color: #666; font-style: italic; font-size: 0.9rem;">
        <i class="fas fa-info-circle"></i> 
        Role Auditor hanya dapat mengakses modul Aset Barang untuk melihat data dan laporan.
      </p>
    <?php endif; ?>
  </div>
</div>

<!-- ========================
          STYLE
========================= -->
<style>
:root {
  --primary: #2b6b4f;
  --primary-dark: #1c4835;
  --text-dark: #222;
  --text-light: #f5f5f5;
  --bg-dark: #1e1e1e;
  --bg-light: #ffffff;
  --card-dark: #2c2c2c;
  --card-light: #ffffff;
  --card-highlight: #fdfdfd;
}

/* GRID UTAMA */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
}

.card.stat {
  text-align: center;
  padding: 1rem;
  border-radius: 12px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.15);
  transition: 0.3s ease-in-out;
  background: var(--card-light);
  color: var(--text-dark);
}
.card.stat h3 {
  font-size: 2rem;
  margin: 0;
}

/* DARK MODE – semua card tetap terang */
body.dark-mode .card.stat,
body:not(.light-mode) .card.stat {
  background: var(--card-highlight);
  color: #111;
  box-shadow: 0 3px 12px rgba(255,255,255,0.05);
}

/* Pajak & Navigasi Modul Card */
.card {
  background: var(--card-light);
  padding: 1rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
body.dark-mode .card,
body:not(.light-mode) .card {
  background: var(--card-highlight);
  color: #111;
}

/* Tabel Pajak */
.table {
  width: 100%;
  border-collapse: collapse;
}
.table th, .table td {
  padding: .6rem;
  border-bottom: 1px solid #ccc;
  text-align: left;
}
body.dark-mode .table th,
body.dark-mode .table td {
  border-color: #ddd;
}

/* Modul Navigasi Buttons */
.module-links {
  display: flex;
  flex-wrap: wrap;
  gap: .75rem;
}
.module-links .btn {
  background: var(--primary);
  color: white;
  padding: .6em 1.2em;
  border-radius: 8px;
  text-decoration: none;
  transition: 0.25s;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
}
.module-links .btn:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
}

/* Alert Colors */
.alert.red {
  background: #ff6b6b;
  color: white;
  padding: 3px 8px;
  border-radius: 6px;
}
.alert.yellow {
  background: #ffe16b;
  color: #111;
  padding: 3px 8px;
  border-radius: 6px;
}

/* Warna Global Background */
body.light-mode {
  background: var(--bg-light);
  color: var(--text-dark);
}
body.dark-mode,
body:not(.light-mode) {
  background: var(--bg-dark);
  color: var(--text-light);
}

/* Info text untuk Auditor */
.card p i {
  margin-right: 5px;
  color: #2b6b4f;
}

</style>

<?php include __DIR__ . '/includes/footer.php'; ?>