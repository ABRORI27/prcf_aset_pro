<?php include '../../includes/koneksi.php';
if (isset($_GET['id'])) { $id = intval($_GET['id']); $conn->query("DELETE FROM employees WHERE id=$id"); }
header('Location: output_employee.php');