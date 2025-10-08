<?php include '../../includes/header.php'; include '../../includes/koneksi.php';
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $nama=$_POST['nama']; $nik=$_POST['nik']; $jab=$_POST['jabatan']; $unit=$_POST['unit']; $kont=$_POST['kontak']; $tgl=$_POST['tanggal_masuk'];
    $chk = $conn->prepare('SELECT id FROM employees WHERE nik=?'); $chk->bind_param('s',$nik); $chk->execute(); $chk->store_result();
    if ($chk->num_rows>0) $error='NIK sudah terdaftar'; else { $stmt=$conn->prepare('INSERT INTO employees (nama,nik,jabatan,unit,kontak,tanggal_masuk) VALUES (?,?,?,?,?,?)'); $stmt->bind_param('ssssss',$nama,$nik,$jab,$unit,$kont,$tgl); if ($stmt->execute()) header('Location: output_employee.php'); else $error=$stmt->error; }
}
?>
<div class="page">
<h2>Tambah Pegawai</h2>
<?php if(!empty($error)) echo '<div class="alert">'.$error.'</div>'; ?>
<form method="post" class="form">
<label>Nama<input name="nama" required></label>
<label>NIK<input name="nik" required></label>
<label>Jabatan<input name="jabatan" required></label>
<label>Unit<input name="unit" required></label>
<label>Kontak<input name="kontak"></label>
<label>Tanggal Masuk<input type="date" name="tanggal_masuk"></label>
<button class="btn" type="submit">Simpan</button>
</form>
</div>
<?php include '../../includes/footer.php'; ?>