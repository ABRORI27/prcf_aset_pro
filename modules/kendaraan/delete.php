<?php
include '../../includes/header.php';
include '../../config/db.php';

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

// Query untuk menghapus berdasarkan ID
$query = "DELETE FROM kendaraan WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Data kendaraan berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus data kendaraan.";
}

header('Location: read.php');
exit();
?>