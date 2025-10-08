<?php include '../../includes/header.php'; include '../../includes/koneksi.php';
$res = $conn->query('SELECT * FROM aset_barang ORDER BY id DESC');
?>
<div class="page">
<h2>Daftar Aset</h2>
<a class="btn" href="input_aset.php">+ Tambah</a>
<input id="searchAset" placeholder="Cari..." oninput="filterTable('asetTable', this.value)">
<table id="asetTable" class="table"><thead><tr><th>No</th><th>Nama</th><th>Kategori</th><th>Jumlah</th><th>Kondisi</th><th>Lokasi</th><th>Aksi</th></tr></thead><tbody>
<?php $i=1; while($r=$res->fetch_assoc()): ?>
<tr>
<td><?= $i ?></td>
<td><?= htmlspecialchars($r['nama_barang']) ?></td>
<td><?= htmlspecialchars($r['kategori_barang']) ?></td>
<td><?= $r['jumlah_unit'] ?></td>
<td><?= $r['kondisi_barang'] ?></td>
<td><?= htmlspecialchars($r['lokasi_barang']) ?></td>
<td><a href="hapus.php?id=<?= $r['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a></td>
</tr>
<?php $i++; endwhile; ?>
</tbody></table>
</div>
<?php include '../../includes/footer.php'; ?>