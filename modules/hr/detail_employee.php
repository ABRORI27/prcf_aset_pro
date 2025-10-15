<?php
include '../../includes/auth_check.php';
include '../../includes/koneksi.php';

// Pastikan hanya role tertentu yang bisa akses
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
        <a href="edit_employee.php?id=<?= $data['id']; ?>" class="btn">Edit</a>
        <a href="delete_employee.php?id=<?= $data['id']; ?>" class="btn" onclick="return confirm('Hapus data ini?')">Hapus</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
