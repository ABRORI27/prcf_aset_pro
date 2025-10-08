<?php include '../../includes/header.php'; include '../../includes/koneksi.php';
$res = $conn->query('SELECT * FROM employees ORDER BY id DESC');
?>
<div class="page">
<h2>Data Pegawai</h2>
<a class="btn" href="input_employee.php">+ Tambah</a>
<input id="searchEmp" placeholder="Cari..." oninput="filterTable('empTable', this.value)">
<table id="empTable" class="table"><thead><tr><th>No</th><th>Nama</th><th>NIK</th><th>Jabatan</th><th>Unit</th><th>Aksi</th></tr></thead><tbody>
<?php $i=1; while($r=$res->fetch_assoc()): ?>
<tr><td><?= $i ?></td><td><?= htmlspecialchars($r['nama']) ?></td><td><?= htmlspecialchars($r['nik']) ?></td><td><?= htmlspecialchars($r['jabatan']) ?></td><td><?= htmlspecialchars($r['unit']) ?></td><td><a href="delete_employee.php?id=<?= $r['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a></td></tr>
<?php $i++; endwhile; ?>
</tbody></table>
</div>
<?php include '../../includes/footer.php'; ?>