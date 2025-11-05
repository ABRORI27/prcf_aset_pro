-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2025 at 03:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_prcf`
--

-- --------------------------------------------------------

--
-- Table structure for table `aset_barang`
--

CREATE TABLE `aset_barang` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `jumlah_unit` int(11) DEFAULT 1,
  `nomor_seri` varchar(100) DEFAULT NULL,
  `harga_pembelian` decimal(15,2) DEFAULT NULL,
  `waktu_perolehan` date DEFAULT NULL,
  `kondisi_barang` enum('Baik','Rusak','Hilang') DEFAULT 'Baik',
  `kode_penomoran` varchar(50) DEFAULT NULL,
  `kategori_barang` int(11) DEFAULT NULL,
  `nomor_plat` varchar(50) DEFAULT NULL,
  `program_pendanaan` int(11) DEFAULT NULL,
  `lokasi_barang` int(11) DEFAULT NULL,
  `user_input` int(11) DEFAULT NULL,
  `foto_barang` varchar(255) DEFAULT NULL,
  `status_penggunaan` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif',
  `created_at` datetime DEFAULT current_timestamp(),
  `tanggal_pajak` date DEFAULT NULL,
  `penanggung_jawab` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aset_barang`
--

INSERT INTO `aset_barang` (`id`, `nama_barang`, `deskripsi`, `jumlah_unit`, `nomor_seri`, `harga_pembelian`, `waktu_perolehan`, `kondisi_barang`, `kode_penomoran`, `kategori_barang`, `nomor_plat`, `program_pendanaan`, `lokasi_barang`, `user_input`, `foto_barang`, `status_penggunaan`, `created_at`, `tanggal_pajak`, `penanggung_jawab`) VALUES
(15, 'Printer Canon G3010', 'Printer warna multifungsi untuk administrasi kantor.', 2, 'SN-CN12345', 2300000.00, '2024-02-15', 'Baik', 'PRNT-001', 1, NULL, 1, 1, 1, 'printer.jpg', 'Aktif', '2025-10-26 12:55:49', NULL, 'Budi Santoso'),
(16, 'Kursi Ergonomis', 'Kursi kerja ergonomis dengan sandaran kepala.', 10, 'SN-FR98765', 750000.00, '2023-11-10', 'Baik', 'FUR-002', 2, NULL, 1, 1, 1, 'kursi.jpg', 'Aktif', '2025-10-26 12:55:49', NULL, 'Ratna Dewi'),
(17, 'Genset Honda 3000W', 'Genset portabel untuk kegiatan di lapangan.', 1, 'SN-GN8888', 8500000.00, '2023-07-22', 'Baik', 'GEN-003', 3, NULL, 2, 2, 1, 'genset.jpg', 'Aktif', '2025-10-26 12:55:49', NULL, 'Andi Prasetyo'),
(18, 'Mobil Hilux Double Cabin', 'Kendaraan operasional lapangan untuk tim survey.', 1, 'SN-MB1122', 550000000.00, '2022-05-18', 'Baik', 'VEH-004', 4, 'KB1234YZ', 3, 2, 1, 'hilux.jpg', 'Aktif', '2025-10-26 12:55:49', '2025-12-31', 'Rudi Hartono'),
(19, 'Laptop Lenovo ThinkPad', 'Laptop utama untuk operator data.', 3, 'SN-LT9988', 12000000.00, '2024-04-05', 'Baik', 'LTP-005', 1, NULL, 2, 1, 1, 'thinkpad.jpg', 'Aktif', '2025-10-26 12:55:49', NULL, 'Dina Marlina'),
(22, 'Beat Street', 'Motor operasional lapangan', 1, 'BT-SRT-001', 21000000.00, '0000-00-00', 'Baik', 'AST-MTR-001', 4, 'KB 4131 GAA', 17, 2, NULL, NULL, 'Aktif', '2025-11-04 15:43:10', '2028-01-01', 'Anas');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_barang`
--

INSERT INTO `kategori_barang` (`id`, `nama_kategori`, `deskripsi`) VALUES
(1, 'Peralatan Kantor', 'Barang-barang operasional di kantor'),
(2, 'Furniture', 'Meja, kursi, lemari, dan sejenisnya'),
(3, 'Peralatan Lapangan', 'Peralatan untuk kegiatan di lapangan'),
(4, 'Kendaraan', 'Kendaraan roda dua dan roda empat');

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id` int(11) NOT NULL,
  `nomor_seri` varchar(100) DEFAULT NULL,
  `aset_id` int(11) NOT NULL,
  `nomor_plat` varchar(50) DEFAULT NULL,
  `tanggal_pajak` date DEFAULT NULL,
  `penanggung_jawab` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id`, `nomor_seri`, `aset_id`, `nomor_plat`, `tanggal_pajak`, `penanggung_jawab`) VALUES
(4, 'SN-MB1122', 18, 'KB1234YZ', '2025-10-31', 'Rudi Hartono'),
(7, 'BT-SRT-001', 22, 'KB 4131 GAA', '2028-01-01', 'Anas');

-- --------------------------------------------------------

--
-- Table structure for table `lokasi_barang`
--

CREATE TABLE `lokasi_barang` (
  `id` int(11) NOT NULL,
  `nama_lokasi` varchar(150) NOT NULL,
  `alamat` text DEFAULT NULL,
  `penanggung_jawab` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lokasi_barang`
--

INSERT INTO `lokasi_barang` (`id`, `nama_lokasi`, `alamat`, `penanggung_jawab`) VALUES
(1, 'Kantor Pusat', 'Jl. Ahmad Yani No.10, Pontianak', ''),
(2, 'Gudang Lapangan', 'Jl. Tanjung Raya II No.5, Pontianak', NULL),
(3, 'Workshop Perawatan', 'Jl. Imam Bonjol No.88, Pontianak', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `aset_id` int(11) NOT NULL,
  `tipe_notifikasi` enum('Pajak Kendaraan','Perawatan','Audit') NOT NULL,
  `tanggal_notifikasi` date NOT NULL,
  `status` enum('Terkirim','Belum Terkirim') DEFAULT 'Belum Terkirim'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `aset_id`, `tipe_notifikasi`, `tanggal_notifikasi`, `status`) VALUES
(2, 18, 'Pajak Kendaraan', '2025-11-01', 'Belum Terkirim'),
(3, 22, 'Pajak Kendaraan', '2028-01-01', 'Terkirim');

-- --------------------------------------------------------

--
-- Table structure for table `program_pendanaan`
--

CREATE TABLE `program_pendanaan` (
  `id` int(11) NOT NULL,
  `nama_program` varchar(150) NOT NULL,
  `tahun_anggaran` year(4) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_pendanaan`
--

INSERT INTO `program_pendanaan` (`id`, `nama_program`, `tahun_anggaran`, `keterangan`) VALUES
(1, 'Program Hutan Lestari', '2023', 'Pendanaan kegiatan konservasi hutan'),
(2, 'Program Energi Bersih', '2024', 'Pendanaan instalasi panel surya'),
(3, 'Program Air Bersih', '2023', 'Pendanaan sarana air bersih pedesaan'),
(17, 'Pengadaan Kendaraan Roda 2', '2025', 'Motor Pribadi');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','operator','auditor') NOT NULL DEFAULT 'operator',
  `nama_lengkap` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `nama_lengkap`, `email`, `created_at`) VALUES
(1, 'abrori', '$2y$10$wZpwb/fJNZ8ZermgcnVREObXvaYzST3BOZs4QpSlrc4purFxIpJ9i', 'operator', 'ABRORI ANAS ANJI', 'aaxabr127@gmail.com', '2025-10-23 22:17:40'),
(2, 'aku', '$2y$10$poXrSSzVnKpB5iusNomZ4ejuZw9OPI5elz85QL/FsPnqXkhUkb4WO', 'admin', 'saya manusia', 'manusia27@gmail.com', '2025-10-24 09:17:07'),
(3, 'anas', '$2y$10$qUc5SSHwnkgqR5LDk1MiP.GUlgsjTYg9EPib4k1IlpMIlJzrkM/VO', 'admin', 'Anas Anji Abrori', 'anasanji27@gmail.com', '2025-10-26 04:54:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aset_barang`
--
ALTER TABLE `aset_barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_seri` (`nomor_seri`),
  ADD UNIQUE KEY `kode_barang` (`kode_penomoran`),
  ADD KEY `user_input` (`user_input`),
  ADD KEY `fk_kategori` (`kategori_barang`),
  ADD KEY `fk_program` (`program_pendanaan`),
  ADD KEY `fk_lokasi` (`lokasi_barang`);

--
-- Indexes for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_plat` (`nomor_plat`),
  ADD KEY `aset_id` (`aset_id`);

--
-- Indexes for table `lokasi_barang`
--
ALTER TABLE `lokasi_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aset_id` (`aset_id`);

--
-- Indexes for table `program_pendanaan`
--
ALTER TABLE `program_pendanaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aset_barang`
--
ALTER TABLE `aset_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lokasi_barang`
--
ALTER TABLE `lokasi_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `program_pendanaan`
--
ALTER TABLE `program_pendanaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aset_barang`
--
ALTER TABLE `aset_barang`
  ADD CONSTRAINT `aset_barang_ibfk_4` FOREIGN KEY (`user_input`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kategori` FOREIGN KEY (`kategori_barang`) REFERENCES `kategori_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lokasi` FOREIGN KEY (`lokasi_barang`) REFERENCES `lokasi_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_program` FOREIGN KEY (`program_pendanaan`) REFERENCES `program_pendanaan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD CONSTRAINT `kendaraan_ibfk_1` FOREIGN KEY (`aset_id`) REFERENCES `aset_barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`aset_id`) REFERENCES `aset_barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
