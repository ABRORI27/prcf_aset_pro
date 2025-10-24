<?php 
include '../../includes/koneksi.php';
include '../../includes/auth_check.php';

if (!has_access(['Admin', 'Operator'])) {
  echo "<script>alert('Anda tidak memiliki izin!');window.location='output_employee.php';</script>";
  exit;
}

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $conn->query("DELETE FROM employees WHERE id=$id");
}
header('Location: output_employee.php');
