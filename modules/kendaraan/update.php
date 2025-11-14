<?php
// ==============================================
// PROSES FORM DAN REDIRECT HARUS DI SINI - SEBELUM INCLUDE HEADER
// ==============================================

// Include file yang diperlukan untuk fungsi has_access()
include '../../config/db.php';
include '../../config/init.php'; // File ini yang berisi fungsi has_access()

// 🔐 CEK AKSES - Hanya Admin & Operator
if (!has_access([ROLE_ADMIN, ROLE_OPERATOR])) {
    $_SESSION['error'] = "Anda tidak memiliki akses ke modul ini.";
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

// Ambil data kendaraan berdasarkan ID
$id = $_GET['id'] ?? '';
if (empty($id) || !is_numeric($id)) {
    $_SESSION['error'] = "ID kendaraan tidak valid.";
    header('Location: read.php');
    exit();
}

// Query untuk mengambil data kendaraan berdasarkan ID
$query = "
  SELECT 
    k.*,
    a.nama_barang AS nama_aset,
    a.deskripsi,
    a.nomor_seri
  FROM kendaraan k
  LEFT JOIN aset_barang a ON k.aset_id = a.id
  WHERE k.id = ?
";

$stmt = mysqli_prepare($conn, $query);

// CEK JIKA QUERY PREPARE GAGAL
if (!$stmt) {
    die("Error dalam menyiapkan query: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);
$execute_success = mysqli_stmt_execute($stmt);

if (!$execute_success) {
    die("Error dalam mengeksekusi query: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);
$kendaraan = mysqli_fetch_assoc($result);

if (!$kendaraan) {
    $_SESSION['error'] = "Data kendaraan tidak ditemukan.";
    header('Location: read.php');
    exit();
}

// PROSES UPDATE DATA - HARUS DI SINI SEBELUM HEADER
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_plat = $_POST['nomor_plat'] ?? '';
    $tanggal_pajak = $_POST['tanggal_pajak'] ?? '';
    $penanggung_jawab = $_POST['penanggung_jawab'] ?? '';
    
    // Validasi input
    if (empty($nomor_plat)) {
        $error = "Nomor plat harus diisi.";
    } else {
        // Update data kendaraan
        $update_query = "UPDATE kendaraan SET nomor_plat = ?, tanggal_pajak = ?, penanggung_jawab = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        
        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "sssi", $nomor_plat, $tanggal_pajak, $penanggung_jawab, $id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                $_SESSION['success'] = "Data kendaraan berhasil diperbarui.";
                header('Location: read.php');
                exit();
            } else {
                $error = "Gagal memperbarui data: " . mysqli_stmt_error($update_stmt);
            }
        } else {
            $error = "Error dalam menyiapkan query update: " . mysqli_error($conn);
        }
    }
}

// ==============================================
// SETELAH INI BARU INCLUDE HEADER DAN OUTPUT HTML
// ==============================================

include '../../includes/header.php';
?>

<div class="page">
  <div class="header">
    <h2>Edit Data Kendaraan</h2>
    <a href="read.php" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Kembali
    </a>
  </div>

  <div class="card">
    <?php if (isset($error)): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="form">
      <div class="form-group">
        <label for="nama_aset">Nama Kendaraan:</label>
        <input type="text" id="nama_aset" value="<?= htmlspecialchars($kendaraan['nama_aset'] ?? '') ?>" readonly class="form-control" style="background: #f8f9fa;">
        <small>Nama kendaraan tidak dapat diubah (diambil dari data aset)</small>
      </div>

      <div class="form-group">
        <label for="nomor_seri">Nomor Seri:</label>
        <input type="text" id="nomor_seri" value="<?= htmlspecialchars($kendaraan['nomor_seri'] ?? '') ?>" readonly class="form-control" style="background: #f8f9fa;">
        <small>Nomor seri tidak dapat diubah (diambil dari data aset)</small>
      </div>

      <div class="form-group">
        <label for="nomor_plat">Nomor Plat *</label>
        <input type="text" id="nomor_plat" name="nomor_plat" value="<?= htmlspecialchars($kendaraan['nomor_plat'] ?? '') ?>" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="tanggal_pajak">Tanggal Pajak</label>
        <input type="date" id="tanggal_pajak" name="tanggal_pajak" value="<?= htmlspecialchars($kendaraan['tanggal_pajak'] ?? '') ?>" class="form-control">
      </div>

      <div class="form-group">
        <label for="penanggung_jawab">Penanggung Jawab</label>
        <input type="text" id="penanggung_jawab" name="penanggung_jawab" value="<?= htmlspecialchars($kendaraan['penanggung_jawab'] ?? '') ?>" class="form-control">
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Simpan Perubahan
        </button>
        <a href="read.php" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<style>
.form {
  max-width: 600px;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
  color: #333;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-control:focus {
  outline: none;
  border-color: #2b6b4f;
}

.form-control[readonly] {
  background-color: #f8f9fa;
  color: #666;
}

.form-group small {
  display: block;
  margin-top: 0.25rem;
  color: #666;
  font-size: 0.8rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid #eee;
}

.alert-error {
  background: #f8d7da;
  color: #721c24;
  padding: 1rem;
  border-radius: 4px;
  border-left: 4px solid #dc3545;
  margin-bottom: 1.5rem;
}

.alert-error i {
  margin-right: 0.5rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.btn-primary {
  background: #2b6b4f;
  color: white;
}

.btn-primary:hover {
  background: #1c4835;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
}
</style>

<?php include '../../includes/footer.php'; ?>