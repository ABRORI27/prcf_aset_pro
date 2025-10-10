<?php include '../../includes/header.php'; include '../../includes/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_barang']; $des = $_POST['deskripsi']; $jumlah = intval($_POST['jumlah_unit']);
    $nomor = $_POST['nomor_seri']; $harga = floatval($_POST['harga_pembelian']); $tanggal = $_POST['waktu_perolehan'];
    $lokasi = $_POST['lokasi_barang']; $kondisi = $_POST['kondisi_barang']; $kode = $_POST['kode_penomoran'];
    $program = $_POST['program_pendanaan']; $kategori = $_POST['kategori_barang']; $plat = $_POST['nomor_plat']?:NULL;
    $tanggal_pajak = $_POST['tanggal_pajak']?:NULL; $pj = $_POST['penanggung_jawab']?:NULL;
    $chk = $conn->prepare('SELECT id FROM aset_barang WHERE nomor_seri=?'); $chk->bind_param('s',$nomor); $chk->execute(); $chk->store_result();
    if ($chk->num_rows>0) $error='Nomor seri sudah ada';
    else {
        $stmt = $conn->prepare('INSERT INTO aset_barang (nama_barang,deskripsi,jumlah_unit,nomor_seri,harga_pembelian,waktu_perolehan,lokasi_barang,kondisi_barang,kode_penomoran,program_pendanaan,kategori_barang,nomor_plat,tanggal_pajak,penanggung_jawab) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('ssisssssssssss',$nama,$des,$jumlah,$nomor,$harga,$tanggal,$lokasi,$kondisi,$kode,$program,$kategori,$plat,$tanggal_pajak,$pj);
        if ($stmt->execute()) header('Location: output_aset.php');
        else $error = $stmt->error;
    }
}
?>
<div class="page">
<h2>Tambah Aset</h2>
<?php if(!empty($error)) echo '<div class="alert">'.$error.'</div>'; ?>
<form method="post" class="form">
<label>Nama Barang<input name="nama_barang" required></label>
<label>Deskripsi<textarea name="deskripsi" required></textarea></label>
<label>Jumlah Unit<input type="number" name="jumlah_unit" required></label>
<label>Nomor Seri<input name="nomor_seri" required></label>
<label>Harga Pembelian<input type="number" step="0.01" name="harga_pembelian" required></label>
<label>Tanggal Perolehan<input type="date" name="waktu_perolehan" required></label>
<label>Lokasi<input name="lokasi_barang" required></label>
<label>Kondisi<select name="kondisi_barang"><option>Baik</option><option>Rusak</option><option>Hilang</option></select></label>
<label>Kategori<input id="kategori_barang" name="kategori_barang" oninput="toggleKendaraanFields()" required></label>
<div id="kendaraanFields" style="display:none;">
<label>Nomor Plat<input name="nomor_plat"></label>
<label>Tanggal Pajak<input type="date" name="tanggal_pajak"></label>
<label>Penanggung Jawab<input name="penanggung_jawab"></label>
</div>
<label>Kode Penomoran<input name="kode_penomoran" required></label>
<label>Program Pendanaan<input name="program_pendanaan" required></label>
<button class="btn" type="submit">Simpan</button>
</form>
</div>
<?php include '../../includes/footer.php'; ?>