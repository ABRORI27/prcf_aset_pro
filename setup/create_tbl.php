<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_prcf";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully <br>";

try {
    // Pastikan koneksi aktif ke database db_prcf
    $conn->select_db("db_prcf");

    // SQL untuk membuat tabel USERS
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('admin','operator','auditor') NOT NULL DEFAULT 'operator',
        nama_lengkap VARCHAR(150) NOT NULL,
        email VARCHAR(150),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;";

    // SQL untuk tabel KATEGORI_BARANG
    $sql_kategori = "CREATE TABLE IF NOT EXISTS kategori_barang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_kategori VARCHAR(100) NOT NULL,
        deskripsi TEXT
    ) ENGINE=InnoDB;";

    // SQL untuk tabel PROGRAM_PENDANAAN
    $sql_program = "CREATE TABLE IF NOT EXISTS program_pendanaan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_program VARCHAR(150) NOT NULL,
        tahun_anggaran YEAR NOT NULL,
        keterangan TEXT
    ) ENGINE=InnoDB;";

    // SQL untuk tabel LOKASI_BARANG
    $sql_lokasi = "CREATE TABLE IF NOT EXISTS lokasi_barang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_lokasi VARCHAR(150) NOT NULL,
        alamat TEXT,
        penanggung_jawab VARCHAR(150)
    ) ENGINE=InnoDB;";

    // SQL untuk tabel ASET_BARANG
    $sql_aset = "CREATE TABLE IF NOT EXISTS aset_barang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_barang VARCHAR(150) NOT NULL,
        deskripsi TEXT,
        jumlah_unit INT DEFAULT 1,
        nomor_seri VARCHAR(100) UNIQUE,
        harga_pembelian DECIMAL(15,2),
        tanggal_perolehan DATE,
        kondisi_barang ENUM('Baik','Rusak','Hilang') DEFAULT 'Baik',
        kode_barang VARCHAR(50) UNIQUE,
        kategori_id INT,
        program_id INT,
        lokasi_id INT,
        user_input INT,
        foto_barang VARCHAR(255),
        status_penggunaan ENUM('Aktif','Tidak Aktif') DEFAULT 'Aktif',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (kategori_id) REFERENCES kategori_barang(id) ON DELETE SET NULL ON UPDATE CASCADE,
        FOREIGN KEY (program_id) REFERENCES program_pendanaan(id) ON DELETE SET NULL ON UPDATE CASCADE,
        FOREIGN KEY (lokasi_id) REFERENCES lokasi_barang(id) ON DELETE SET NULL ON UPDATE CASCADE,
        FOREIGN KEY (user_input) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB;";

    // SQL untuk tabel KENDARAAN
    $sql_kendaraan = "CREATE TABLE IF NOT EXISTS kendaraan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        aset_id INT NOT NULL,
        nomor_plat VARCHAR(50) UNIQUE,
        tanggal_pajak DATE,
        penanggung_jawab VARCHAR(150),
        FOREIGN KEY (aset_id) REFERENCES aset_barang(id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB;";

    // SQL untuk tabel NOTIFIKASI
    $sql_notifikasi = "CREATE TABLE IF NOT EXISTS notifikasi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        aset_id INT NOT NULL,
        tipe_notifikasi ENUM('Pajak Kendaraan','Perawatan','Audit') NOT NULL,
        tanggal_notifikasi DATE NOT NULL,
        status ENUM('Terkirim','Belum Terkirim') DEFAULT 'Belum Terkirim',
        FOREIGN KEY (aset_id) REFERENCES aset_barang(id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB;";

    // Eksekusi semua query
    $tables = [
        'users' => $sql_users,
        'kategori_barang' => $sql_kategori,
        'program_pendanaan' => $sql_program,
        'lokasi_barang' => $sql_lokasi,
        'aset_barang' => $sql_aset,
        'kendaraan' => $sql_kendaraan,
        'notifikasi' => $sql_notifikasi
    ];

    foreach ($tables as $name => $query) {
        if ($conn->query($query) === TRUE) {
            echo "✅ Tabel '$name' berhasil dibuat.<br>";
        } else {
            echo "❌ Gagal membuat tabel '$name': " . $conn->error . "<br>";
        }
    }

    echo "<br>🎉 Semua tabel berhasil diproses.";
    
} catch (Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}

$conn->close();
?>
?>