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

// QUERY YANG SESUAI - ambil deskripsi dari aset_barang
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
          <th>Deskripsi</th>
          <th>Tanggal Pajak</th>
          <th>Penanggung Jawab</th>
          <th width="80">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0): ?>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td>
              <strong><?= htmlspecialchars($row['nama_aset'] ?? 'Tidak Terdaftar') ?></strong>
              <?php if ($row['nomor_plat']): ?>
                <br><small style="color: #666;">Plat: <?= htmlspecialchars($row['nomor_plat']) ?></small>
              <?php endif; ?>
            </td>
            <td>
              <?php 
                $deskripsi = $row['deskripsi'] ?? '';
                if (empty($deskripsi)) {
                  echo '<span style="color: #999; font-style: italic;">Tidak ada deskripsi</span>';
                } elseif (strlen($deskripsi) > 80) {
                  echo '<span title="' . htmlspecialchars($deskripsi) . '">' . 
                       htmlspecialchars(substr($deskripsi, 0, 80)) . '...</span>';
                } else {
                  echo htmlspecialchars($deskripsi);
                }
              ?>
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
            <td><?= htmlspecialchars($row['penanggung_jawab'] ?? '-') ?></td>
            <td>
              <div class="dropdown">
                <button class="dropdown-toggle" type="button">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu">
                  <!-- TOMBOL DETAIL - SEMUA ROLE BISA AKSES -->
                  <a href="detail.php?id=<?= $row['id'] ?>" class="dropdown-item">
                    <i class="fas fa-eye"></i> Lihat Detail
                  </a>
                  
                  <?php if (!$is_auditor): ?>
                    <!-- TOMBOL EDIT & HAPUS HANYA UNTUK ADMIN & OPERATOR -->
                    <a href="update.php?id=<?= $row['id'] ?>" class="dropdown-item">
                      <i class="fas fa-edit"></i> Edit Data
                    </a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="dropdown-item dropdown-item-danger" onclick="return confirm('Yakin ingin menghapus data kendaraan ini?')">
                      <i class="fas fa-trash"></i> Hapus
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>  
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center; padding: 2rem;">
            <div style="color: #666; font-style: italic;">
              <i class="fas fa-car" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
              Belum ada data kendaraan.
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
        Informasi lengkap seperti nomor seri dan nomor plat tersedia di halaman detail.
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
  vertical-align: top;
}

.table tr:hover {
  background: #f8f9fa;
}

/* Dropdown Styles */
.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-toggle {
  background: none;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 0.4rem 0.6rem;
  cursor: pointer;
  color: #666;
  transition: all 0.3s ease;
}

.dropdown-toggle:hover {
  background: #f8f9fa;
  border-color: #999;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  min-width: 160px;
  z-index: 1000;
  display: none;
  margin-top: 0.25rem;
}

.dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  text-decoration: none;
  color: #333;
  border-bottom: 1px solid #f0f0f0;
  transition: background 0.2s;
  font-size: 0.85rem;
}

.dropdown-item:last-child {
  border-bottom: none;
}

.dropdown-item:hover {
  background: #f8f9fa;
  color: #2b6b4f;
}

.dropdown-item i {
  width: 16px;
  text-align: center;
}

.dropdown-item-danger {
  color: #dc3545;
}

.dropdown-item-danger:hover {
  background: #f8d7da;
  color: #721c24;
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
  
  .dropdown-menu {
    position: fixed;
    top: auto;
    right: 1rem;
    left: 1rem;
    min-width: auto;
  }
  
  .dropdown-item {
    padding: 1rem;
    font-size: 0.9rem;
  }
}
</style>

<script>
// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
  const dropdowns = document.querySelectorAll('.dropdown');
  dropdowns.forEach(function(dropdown) {
    if (!dropdown.contains(event.target)) {
      const menu = dropdown.querySelector('.dropdown-menu');
      if (menu) menu.style.display = 'none';
    }
  });
});

// Toggle dropdown on click (for mobile)
document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
  toggle.addEventListener('click', function(e) {
    e.stopPropagation();
    const menu = this.parentNode.querySelector('.dropdown-menu');
    const isVisible = menu.style.display === 'block';
    
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(function(otherMenu) {
      otherMenu.style.display = 'none';
    });
    
    // Toggle current dropdown
    menu.style.display = isVisible ? 'none' : 'block';
  });
});
</script>

<?php include '../../includes/footer.php'; ?>