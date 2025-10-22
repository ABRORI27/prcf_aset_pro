<?php
include '../includes/koneksi.php'; // sesuaikan path-nya kalau beda

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Data kategori default
$kategori = [
    'Peralatan Kantor',
    'Furniture',
    'Peralatan Lapangan',
    'Kendaraan'
];

// Siapkan query insert
$stmt = $conn->prepare("INSERT INTO kategori_barang (nama_kategori) VALUES (?)");

if (!$stmt) {
    die("Gagal menyiapkan statement: " . $conn->error);
}

$inserted = 0;
foreach ($kategori as $nama) {
    $stmt->bind_param('s', $nama);
    if ($stmt->execute()) {
        $inserted++;
    }
}

$stmt->close();
$conn->close();

echo "<h3>Setup kategori selesai ✅</h3>";
echo "<p>$inserted kategori berhasil dimasukkan ke tabel <b>kategori_barang</b>.</p>";
echo "<a href='../aset_barang/create.php'>➡️ Kembali ke halaman input aset</a>";
?>
