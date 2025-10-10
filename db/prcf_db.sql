CREATE DATABASE db_prcf;
USE db_prcf;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE,
  password_hash VARCHAR(255),
  role VARCHAR(50)
);

CREATE TABLE aset_barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(150),
    deskripsi TEXT,
    jumlah_unit INT,
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
);

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150),
    nik VARCHAR(50) UNIQUE,
    jabatan VARCHAR(100),
    unit VARCHAR(100),
    kontak VARCHAR(100),
    tanggal_masuk DATE
);