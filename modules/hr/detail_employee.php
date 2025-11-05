<?php
include '../../includes/auth_check.php';
include '../../includes/koneksi.php';

// Akses role
if (!has_access(['Admin', 'Operator', 'Auditor'])) {
    echo "<script>alert('Akses ditolak!'); window.location='output_employee.php';</script>";
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>alert('Data tidak ditemukan'); window.location='output_employee.php';</script>";
    exit;
}

$id = intval($_GET['id']);
$q = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$q->bind_param("i", $id);
$q->execute();
$result = $q->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Pegawai tidak ditemukan'); window.location='output_employee.php';</script>";
    exit;
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Pegawai - PRCF</title>
  <link rel="stylesheet" href="/prcf_aset_pro/assets/css/dashboard.css">
  <style>
    /* ======== DETAIL EMPLOYEE CUSTOM STYLE ======== */
    .detail-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      border-radius: 10px;
      overflow: hidden;
      background: var(--panel-dark);
    }
    .detail-table th, .detail-table td {
      padding: 12px 16px;
      text-align: left;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .detail-table th {
      width: 30%;
      background-color: #1a2435;
      font-weight: 600;
      color: var(--text-dark);
    }
    .detail-table tr:last-child td {
      border-bottom: none;
    }
    body.light-mode .detail-table {
      background: var(--panel-light);
    }
    body.light-mode .detail-table th {
      background: #f0f2f5;
      color: var(--text-light);
    }

    /* Tombol sejajar kanan bawah */
    .action-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 18px;
    }
    .btn-edit {
      background-color: #2b6b4f;
    }
    .btn-delete {
      background-color: #b3261e;
    }
    .btn-delete:hover {
      background-color: #8e1a14;
    }
  </style>
</head>
<body>
<div class="page">
  <div class="header">
    <h2>Detail Pegawai</h2>
    <a href="output_employee.php" class="btn">← Kembali</a>
  </div>

  <div class="card">
    <table class="detail-table">
      <tr><th>Nama Lengkap</th><td><?= htmlspecialchars($data['nama']); ?></td></tr>
      <tr><th>NIK</th><td><?= htmlspecialchars($data['nik']); ?></td></tr>
      <tr><th>Jabatan</th><td><?= htmlspecialchars($data['jabatan']); ?></td></tr>
      <tr><th>Unit / Divisi</th><td><?= htmlspecialchars($data['unit']); ?></td></tr>
      <tr><th>Kontak</th><td><?= htmlspecialchars($data['kontak']); ?></td></tr>
      <tr><th>Tanggal Masuk</th><td><?= htmlspecialchars($data['tanggal_masuk']); ?></td></tr>
    </table>

    <?php if (has_access(['Admin', 'Operator'])): ?>
      <div class="action-buttons">
        <a href="edit_employee.php?id=<?= $data['id']; ?>" class="btn btn-edit">Edit</a>
        <a href="delete_employee.php?id=<?= $data['id']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
