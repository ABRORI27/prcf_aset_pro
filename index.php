<?php
// ================================================
//  Dashboard Aset Barang PRCF Indonesia
//  Author: Philo
// ================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/includes/koneksi.php';
require_once __DIR__ . '/config/init.php';  // ⬅️  include koneksi, constants, timezone, dll
require_once __DIR__ . '/includes/header.php'; // ⬅️  sudah otomatis include auth_check

// ========================
//  QUERY DASHBOARD DATA
// ========================

// Total aset
$total_aset = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) AS total FROM aset_barang
"))['total'] ?? 0;

// Aset aktif
$aktif_aset = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) AS total FROM aset_barang WHERE status_penggunaan='Aktif'
"))['total'] ?? 0;

// Aset rusak / hilang
$rusak_aset = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) AS total FROM aset_barang WHERE kondisi_barang IN ('Rusak','Hilang')
"))['total'] ?? 0;

// Total kendaraan
$total_kendaraan = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) AS total FROM kendaraan
"))['total'] ?? 0;

// Notifikasi belum terkirim
$notif_pending = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) AS total FROM notifikasi WHERE status='Belum Terkirim'
"))['total'] ?? 0;

// Deteksi pajak kendaraan jatuh tempo < 7 hari
$today = date('Y-m-d');
$limit = date('Y-m-d', strtotime('+7 days'));

$pajak_due = mysqli_query($conn, "
  SELECT k.*, a.nama_barang 
  FROM kendaraan k 
  JOIN aset_barang a ON a.id = k.aset_id
  WHERE k.tanggal_pajak BETWEEN '$today' AND '$limit'
  ORDER BY k.tanggal_pajak ASC
");
?>

<!-- ========================
      DASHBOARD CONTENT
========================= -->
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
    <div class="card stat"><h3><?= $notif_pending ?></h3><p>Notifikasi Belum Terkirim</p></div>
  </div>

  <!-- Reminder Pajak -->
  <div class="card mt-4">
    <h3>🚗 Pajak Kendaraan Mendekati Jatuh Tempo</h3>
    <?php if (mysqli_num_rows($pajak_due) == 0): ?>
      <p>Tidak ada kendaraan dengan pajak mendekati jatuh tempo dalam 7 hari ke depan.</p>
    <?php else: ?>
      <table class="table">
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

  <!-- Navigasi Cepat -->
  <div class="card mt-4">
    <h3>📂 Navigasi Modul</h3>
    <div class="module-links">
      <a href="<?= base_url('modules/aset_barang/read.php') ?>" class="btn">Aset Barang</a>
      <a href="<?= base_url('modules/kendaraan/read.php') ?>" class="btn">Kendaraan</a>
      <a href="<?= base_url('modules/kategori/read.php') ?>" class="btn">Kategori</a>
      <a href="<?= base_url('modules/lokasi/read.php') ?>" class="btn">Lokasi</a>
      <a href="<?= base_url('modules/program/read.php') ?>" class="btn">Program Pendanaan</a>
      <a href="<?= base_url('modules/notifikasi/read.php') ?>" class="btn">Notifikasi</a>
    </div>
  </div>
</div>

<!-- ========================
          STYLE
========================= -->
<style>
.dashboard h2 { margin-bottom: .5em; }
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
}
.card.stat {
  text-align: center;
  background: var(--bg-card, #fff);
  color: #111;
  padding: 1rem;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.card.stat h3 { font-size: 2rem; margin: 0; }
.module-links {
  display: flex;
  flex-wrap: wrap;
  gap: .75rem;
}
.module-links .btn {
  background: var(--primary, #2b6b4f);
  color: white;
  padding: .6em 1.2em;
  border-radius: 6px;
  text-decoration: none;
  transition: 0.2s;
}
.module-links .btn:hover {
  background: #257a52;
}
.mt-4 { margin-top: 1.5em; }
.alert.red { background: #ff6b6b; color: white; padding: 2px 8px; border-radius: 6px; }
.alert.yellow { background: #ffd93b; color: #222; padding: 2px 8px; border-radius: 6px; }
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
