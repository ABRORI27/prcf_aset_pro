<?php
// Koneksi awal ke MySQL (tanpa nama database dulu)
$servername = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// === 1. Buat database ===
$dbname = "db_prcf";
if ($conn->query("CREATE DATABASE IF NOT EXISTS $dbname") === TRUE) {
    echo "Database '$dbname' berhasil dibuat atau sudah ada.<br>";
} else {
    die("Gagal membuat database: " . $conn->error);
}

// Gunakan database
$conn->select_db($dbname);

// === 2. Tabel USERS ===
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    password_hash VARCHAR(255),
    role VARCHAR(50)
)";
if ($conn->query($sql_users) === TRUE) {
    echo "Tabel 'users' siap digunakan.<br>";
} else {
    echo "Error membuat tabel users: " . $conn->error . "<br>";
}

// === 3. Tabel EMPLOYEES ===
$sql_employees = "CREATE TABLE IF NOT EXISTS employees (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150),
    nik VARCHAR(50) UNIQUE,
    jabatan VARCHAR(100),
    unit VARCHAR(100),
    kontak VARCHAR(100),
    tanggal_masuk DATE
)";
if ($conn->query($sql_employees) === TRUE) {
    echo "Tabel 'employees' siap digunakan.<br>";
} else {
    echo "Error membuat tabel employees: " . $conn->error . "<br>";
}

// === 4. Tabel ASET_BARANG ===
$sql_aset = "CREATE TABLE IF NOT EXISTS aset_barang (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(150),
    deskripsi TEXT,
    jumlah_unit INT(11),
    nomor_seri VARCHAR(100) UNIQUE,
    harga_pembelian DECIMAL(15,2),
    waktu_perolehan DATE,
    lokasi_barang VARCHAR(150),
    kondisi_barang ENUM('Baik','Rusak','Hilang'),
    kode_penomoran VARCHAR(80),
    program_pendanaan VARCHAR(150),
    kategori_barang VARCHAR(80),
    nomor_plat VARCHAR(30),
    tanggal_pajak DATE,
    penanggung_jawab VARCHAR(120)
)";
if ($conn->query($sql_aset) === TRUE) {
    echo "Tabel 'aset_barang' siap digunakan.<br>";
} else {
    echo "Error membuat tabel aset_barang: " . $conn->error . "<br>";
}

// === 5. Tambahkan user default ===
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$auditor_pass = password_hash('audit123', PASSWORD_DEFAULT);
$operator_pass = password_hash('oper123', PASSWORD_DEFAULT);

$conn->query("INSERT IGNORE INTO users (username, password_hash, role) VALUES
('anas', '$admin_pass', 'admin'),
('dzakwan', '$auditor_pass', 'auditor'),
('fauzan', '$operator_pass', 'operator')
");

echo "<br>✅ Semua tabel dan akun awal berhasil dibuat.<br>";
echo "<a href='login.php'>Klik di sini untuk login</a>";

$conn->close();
?>
