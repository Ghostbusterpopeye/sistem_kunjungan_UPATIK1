-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 06, 2026 at 12:56 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `database_upatik`
--

-- --------------------------------------------------------

--
-- Table structure for table `akun_admin`
--

CREATE TABLE `akun_admin` (
  `id_admin` int NOT NULL,
  `nip` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `akun_admin`
--

INSERT INTO `akun_admin` (`id_admin`, `nip`, `nama_lengkap`, `password`) VALUES
(1, '123456789', 'Admin1', '$2a$12$rw8gPK3JNfP3VVzcENbF0OAgExl6rD4sRfvTPBewX1sUTEzx9xoxK');

-- --------------------------------------------------------

--
-- Table structure for table `akun_pengguna`
--

CREATE TABLE `akun_pengguna` (
  `id_pengguna` int NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nim_nip` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('mahasiswa','dosen') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `akun_pengguna`
--

INSERT INTO `akun_pengguna` (`id_pengguna`, `nama_lengkap`, `nim_nip`, `email`, `password`, `status`, `is_active`) VALUES
(1, 'Maba Unej', '3213213212', 'MabaUnej@gmail.com', '$2y$10$UMuIZpx7KuHMdFWSUJKFl.5KnMaB.uykPW7xffDn4pf.5MSDIWXIy', 'mahasiswa', 1);

-- --------------------------------------------------------

--
-- Table structure for table `formulir_layanan`
--

CREATE TABLE `formulir_layanan` (
  `id_formulir` int NOT NULL,
  `id_pengguna` int NOT NULL,
  `id_layanan` int NOT NULL,
  `tanggal_isi` timestamp NOT NULL,
  `detail_layanan` text NOT NULL,
  `status_layanan` enum('menunggu','diproses','selesai') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `formulir_layanan`
--

INSERT INTO `formulir_layanan` (`id_formulir`, `id_pengguna`, `id_layanan`, `tanggal_isi`, `detail_layanan`, `status_layanan`) VALUES
(1, 1, 1, '2026-05-04 12:00:23', 'Akun Kuliah error', 'selesai'),
(2, 1, 2, '2026-05-04 13:14:01', 'Aplikasi E-Learning Tidak bisa di buka dan lag', 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id_layanan` int NOT NULL,
  `nama_layanan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `detail_layanan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bg_0900_ai_ci NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id_layanan`, `nama_layanan`, `is_active`, `detail_layanan`, `status`) VALUES
(1, 'SSO Email', 1, '', 'aktif'),
(2, 'E-Learning', 1, '', 'aktif'),
(3, 'Pemasangan VPN', 0, '', 'aktif'),
(4, 'Reset Password', 1, '', 'aktif'),
(5, 'Keluhan IT', 1, '', 'aktif'),
(6, 'Instalasi Software', 1, '', 'aktif'),
(7, 'Jaringan & Infrastru', 1, '', 'aktif'),
(8, 'Jaringan & Infrastruktur', 1, '', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `tindakan_layanan`
--

CREATE TABLE `tindakan_layanan` (
  `id_tindakan` int NOT NULL,
  `id_formulir` int NOT NULL,
  `id_admin` int NOT NULL,
  `tanggal_tindakan` date NOT NULL,
  `detail_tindakan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tindakan_layanan`
--

INSERT INTO `tindakan_layanan` (`id_tindakan`, `id_formulir`, `id_admin`, `tanggal_tindakan`, `detail_tindakan`) VALUES
(1, 1, 1, '2026-05-04', 'Formulir ditandai selesai oleh admin melalui dashboard.'),
(2, 2, 1, '2026-05-04', 'Formulir ditandai selesai oleh admin melalui dashboard.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun_admin`
--
ALTER TABLE `akun_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `akun_pengguna`
--
ALTER TABLE `akun_pengguna`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indexes for table `formulir_layanan`
--
ALTER TABLE `formulir_layanan`
  ADD PRIMARY KEY (`id_formulir`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_layanan` (`id_layanan`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id_layanan`);

--
-- Indexes for table `tindakan_layanan`
--
ALTER TABLE `tindakan_layanan`
  ADD PRIMARY KEY (`id_tindakan`),
  ADD KEY `id_formulir` (`id_formulir`),
  ADD KEY `id_admin` (`id_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akun_admin`
--
ALTER TABLE `akun_admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `akun_pengguna`
--
ALTER TABLE `akun_pengguna`
  MODIFY `id_pengguna` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `formulir_layanan`
--
ALTER TABLE `formulir_layanan`
  MODIFY `id_formulir` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id_layanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tindakan_layanan`
--
ALTER TABLE `tindakan_layanan`
  MODIFY `id_tindakan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `formulir_layanan`
--
ALTER TABLE `formulir_layanan`
  ADD CONSTRAINT `formulir_layanan_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `akun_pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formulir_layanan_ibfk_2` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tindakan_layanan`
--
ALTER TABLE `tindakan_layanan`
  ADD CONSTRAINT `tindakan_layanan_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `akun_admin` (`id_admin`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tindakan_layanan_ibfk_2` FOREIGN KEY (`id_formulir`) REFERENCES `formulir_layanan` (`id_formulir`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
