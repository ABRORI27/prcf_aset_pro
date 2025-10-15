<?php
include '../../includes/header.php';
include '../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_barang = $_POST['nama_barang'];
  $deskripsi = $_POST['deskripsi'];
  $jumlah_unit = $_POST['jumlah_unit'];
  $nomor_seri = $_POST['nomor_seri'];
  $harga_pembelian = $_POST['harga_pembelian'];
  $waktu_perolehan = $_POST['waktu_perolehan'];
  $lokasi_barang = $_POST['lokasi_barang'];
  $kondisi_barang = $_POST['kondisi_barang'];
  $kode_penomoran = $_POST['kode_penomoran'];
  $program_pendanaan = $_POST['program_pendanaan'];
  $kategori_barang = $_POST['kategori_barang'];
  $nomor_plat = $_POST['nomor_plat'];
  $tanggal_pajak = $_POST['tanggal_pajak'];
  $penanggung_jawab = $_POST['penanggung_jawab'];

  $sql = "INSERT INTO aset_barang 
  (nama_barang, deskripsi, jumlah_unit, nomor_seri, harga_pembelian, waktu_perolehan, lokasi_barang, kondisi_barang, kode_penomoran, program_pendanaan, kategori_barang, nomor_plat, tanggal_pajak, penanggung_jawab)
  VALUES 
  ('$nama_barang','$deskripsi','$jumlah_unit','$nomor_seri','$harga_pembelian','$waktu_perolehan','$lokasi_barang','$kondisi_barang','$kode_penomoran','$program_pendanaan','$kategori_barang','$nomor_plat','$tanggal_pajak','$penanggung_jawab')";

  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Data aset berhasil ditambahkan!');window.location='output_aset.php';</script>";
  } else {
    echo "<div class='alert red'>Gagal menambah data: " . mysqli_error($conn) . "</div>";
  }
}
?>

<div class="page">
  <h2>Tambah Aset Baru</h2>
  <form method="post" class="form-section">

    <label>Nama Barang</label>
    <input type="text" name="nama_barang" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi"></textarea>

    <label>Jumlah Unit</label>
    <input type="number" name="jumlah_unit" required>

    <label>Nomor Seri (unik)</label>
    <input type="text" name="nomor_seri">

    <label>Harga Pembelian (Rp)</label>
    <input type="number" name="harga_pembelian">

    <label>Tanggal Perolehan</label>
    <input type="date" name="waktu_perolehan">

    <label>Lokasi Barang</label>
    <input type="text" name="lokasi_barang">

    <label>Kondisi Barang</label>
    <select name="kondisi_barang">
      <option value="Baik">Baik</option>
      <option value="Rusak">Rusak</option>
      <option value="Hilang">Hilang</option>
    </select>

    <label>Kode Penomoran</label>
    <input type="text" name="kode_penomoran">

    <label>Program Pendanaan</label>
    <input type="text" name="program_pendanaan">

    <label>Kategori Barang</label>
    <select name="kategori_barang">
      <option value="Peralatan Kantor">Peralatan Kantor</option>
      <option value="Furniture">Furniture</option>
      <option value="Peralatan Lapangan">Peralatan Lapangan</option>
      <option value="Kendaraan">Kendaraan</option>
    </select>

    <label>Nomor Plat (jika kendaraan)</label>
    <input type="text" name="nomor_plat">

    <label>Tanggal Pajak Berlaku Sampai</label>
    <input type="date" name="tanggal_pajak">

    <label>Penanggung Jawab</label>
    <input type="text" name="penanggung_jawab">

    <button type="submit" class="btn">Simpan Data</button>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>
