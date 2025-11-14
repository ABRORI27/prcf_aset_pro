<?php
include '../../includes/header.php';
include '../../config/db.php';

// 🔐 CEK AKSES - Semua role bisa akses
if (!has_access([ROLE_ADMIN, ROLE_OPERATOR, ROLE_AUDITOR])) {
    $_SESSION['error'] = "Anda tidak memiliki akses ke modul ini.";
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

// Cek role user untuk menentukan akses
$user_role = $_SESSION['user']['role'] ?? '';
$is_auditor = ($user_role === ROLE_AUDITOR);

// QUERY YANG SESUAI DENGAN STRUKTUR TABEL
$query = "
  SELECT 
    k.id,
    k.nomor_plat,
    k.tanggal_pajak,
    k.penanggung_jawab,
    a.nama_barang AS nama_aset,
    a.deskripsi,
    a.kondisi_barang
  FROM kendaraan k
  LEFT JOIN aset_barang a ON k.aset_id = a.id
  ORDER BY a.nama_barang ASC
";

$result = mysqli_query($conn, $query);

// CEK JIKA QUERY GAGAL
if (!$result) {
    echo "<div class='alert alert-error'>Error: " . mysqli_error($conn) . "</div>";
    $result = []; // Set sebagai array kosong untuk menghindari error
}

// Hitung total rows dengan aman
$total_rows = ($result instanceof mysqli_result) ? mysqli_num_rows($result) : 0;
?>

<div class="page">
  <div class="header">
    <h2>Data Kendaraan</h2>
    <?php if (!$is_auditor): ?>
      <!-- TOMBOL TAMBAH HANYA UNTUK ADMIN & OPERATOR -->
      <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Kendaraan
      </a>
    <?php else: ?>
      <!-- INFO UNTUK AUDITOR -->
      <span style="color: #666; font-style: italic;">
        <i class="fas fa-eye"></i> Mode Lihat Saja
      </span>
    <?php endif; ?>
  </div>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Barang</th>
          <th>Tanggal Pajak</th>
          <th>Kondisi</th>
          <th>Deskripsi</th>
          <th>Penanggung Jawab</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0): ?>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td>
              <strong><?= htmlspecialchars($row['nama_aset'] ?? 'Tidak Terdaftar') ?></strong>
            </td>
            <td>
              <?php 
                if ($row['tanggal_pajak']) {
                  $pajak_date = new DateTime($row['tanggal_pajak']);
                  $today = new DateTime();
                  $diff = $today->diff($pajak_date)->days;
                  
                  if ($pajak_date < $today) {
                    echo '<span class="badge badge-danger" title="Pajak Terlambat">' . 
                         $pajak_date->format('d-m-Y') . '</span>';
                  } elseif ($diff <= 30) {
                    echo '<span class="badge badge-warning" title="Pajak Akan Jatuh Tempo">' . 
                         $pajak_date->format('d-m-Y') . '</span>';
                  } else {
                    echo '<span class="badge badge-success" title="Pajak Masih Berlaku">' . 
                         $pajak_date->format('d-m-Y') . '</span>';
                  }
                } else {
                  echo '<span style="color: #666;">-</span>';
                }
              ?>
            </td>
            <td>
              <?php 
                $kondisi = $row['kondisi_barang'] ?? 'Tidak Diketahui';
                // Sederhanakan kondisi untuk tampilan yang lebih bersih
                $kondisi_simple = match(true) {
                  str_contains($kondisi, 'Baik') => 'Baik',
                  str_contains($kondisi, 'Rusak') => 'Rusak',
                  str_contains($kondisi, 'Hilang') => 'Hilang',
                  str_contains($kondisi, 'Kurang baik') => 'Perlu Servis',
                  default => $kondisi
                };
                
                $badge_class = match($kondisi_simple) {
                  'Baik' => 'badge-success',
                  'Rusak' => 'badge-danger', 
                  'Hilang' => 'badge-warning',
                  'Perlu Servis' => 'badge-warning',
                  default => 'badge-secondary'
                };
                echo '<span class="badge ' . $badge_class . '" title="' . htmlspecialchars($kondisi) . '">' . 
                     htmlspecialchars($kondisi_simple) . '</span>';
              ?>
            </td>
            <td>
              <?php 
                $deskripsi = $row['deskripsi'] ?? '';
                if (strlen($deskripsi) > 50) {
                  echo '<span title="' . htmlspecialchars($deskripsi) . '">' . 
                       htmlspecialchars(substr($deskripsi, 0, 50)) . '...</span>';
                } else {
                  echo htmlspecialchars($deskripsi ?: '-');
                }
              ?>
            </td>
            <td><?= htmlspecialchars($row['penanggung_jawab'] ?? '-') ?></td>
            <td>
              <div class="action-buttons">
                <!-- TOMBOL DETAIL - SEMUA ROLE BISA AKSES -->
                <a href="detail.php?id=<?= $row['id'] ?>" 
                   class="btn btn-info btn-sm" 
                   title="Lihat Detail">
                  <i class="fas fa-eye"></i>
                </a>
                
                <?php if (!$is_auditor): ?>
                  <!-- TOMBOL EDIT & HAPUS HANYA UNTUK ADMIN & OPERATOR -->
                  <a href="update.php?id=<?= $row['id'] ?>" 
                     class="btn btn-warning btn-sm" 
                     title="Edit Data">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="delete.php?id=<?= $row['id'] ?>" 
                     class="btn btn-danger btn-sm" 
                     onclick="return confirm('Yakin ingin menghapus data kendaraan ini?')"
                     title="Hapus Data">
                    <i class="fas fa-trash"></i>
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>  
      <?php else: ?>
        <tr>
          <td colspan="7" style="text-align:center; padding: 2rem;">
            <div style="color: #666; font-style: italic;">
              <i class="fas fa-car" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
              <?= ($result === false) ? 'Error dalam mengambil data' : 'Belum ada data kendaraan.' ?>
            </div>
          </td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($is_auditor): ?>
    <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #2b6b4f;">
      <p style="margin: 0; color: #666; font-size: 0.9rem;">
        <i class="fas fa-info-circle"></i> 
        <strong>Role Auditor:</strong> Anda hanya dapat melihat data kendaraan. 
        Informasi sensitif seperti nomor seri dan nomor plat hanya ditampilkan di halaman detail.
      </p>
    </div>
  <?php endif; ?>
</div>

<style>
/* Button Styles */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: #2b6b4f;
  color: white;
}

.btn-primary:hover {
  background: #1c4835;
}

.btn-sm {
  padding: 0.3rem 0.6rem;
  font-size: 0.8rem;
}

.btn-info {
  background: #17a2b8;
  color: white;
}

.btn-info:hover {
  background: #138496;
}

.btn-warning {
  background: #ffc107;
  color: black;
}

.btn-warning:hover {
  background: #e0a800;
}

.btn-danger {
  background: #dc3545;
  color: white;
}

.btn-danger:hover {
  background: #c82333;
}

/* Badge Styles */
.badge {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-success {
  background: #d4edda;
  color: #155724;
}

.badge-danger {
  background: #f8d7da;
  color: #721c24;
}

.badge-warning {
  background: #fff3cd;
  color: #856404;
}

.badge-secondary {
  background: #e2e3e5;
  color: #383d41;
}

/* Table Styles */
.table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
}

.table th {
  background: #2b6b4f;
  color: white;
  padding: 0.75rem;
  text-align: left;
  font-weight: 600;
}

.table td {
  padding: 0.75rem;
  border-bottom: 1px solid #eee;
  vertical-align: middle;
}

.table tr:hover {
  background: #f8f9fa;
}

/* Action Buttons Container */
.action-buttons {
  display: flex;
  gap: 0.3rem;
  justify-content: center;
}

/* Alert Styles */
.alert-error {
  background: #f8d7da;
  color: #721c24;
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid #f5c6cb;
  margin-bottom: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .table {
    font-size: 0.8rem;
  }
  
  .table th,
  .table td {
    padding: 0.5rem;
  }
  
  .action-buttons {
    flex-direction: column;
    gap: 0.2rem;
  }
  
  .btn-sm {
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
  }
}
</style>

<?php include '../../includes/footer.php'; ?>