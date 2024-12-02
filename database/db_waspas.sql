-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 01, 2024 at 12:25 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_waspas`
--

-- --------------------------------------------------------

--
-- Table structure for table `hasil_seleksi`
--

CREATE TABLE `hasil_seleksi` (
  `id` bigint UNSIGNED NOT NULL,
  `warga_id` bigint UNSIGNED NOT NULL,
  `skor_akhir` double NOT NULL,
  `peringkat` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_kriteria` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kriteria` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot` float NOT NULL,
  `tipe` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id`, `kode_kriteria`, `nama_kriteria`, `bobot`, `tipe`, `created_at`, `updated_at`) VALUES
(2, 'C1', 'penghasilan', 20, 'Cost', '2024-11-27 16:38:58', NULL),
(3, 'C2', 'status_kepemilikan_rumah', 20, 'Benefit', '2024-11-27 16:39:11', NULL),
(4, 'C3', 'struktur_bangunan_rumah', 10, 'Benefit', '2024-11-27 22:48:08', NULL),
(5, 'C4', 'status_kepemilikan_lahan', 10, 'Benefit', '2024-11-27 22:48:31', NULL),
(6, 'C5', 'status_legalitas_lahan', 10, 'Benefit', '2024-11-27 22:50:19', NULL),
(7, 'C6', 'jumlah_penghuni', 10, 'Cost', '2024-11-27 22:51:20', NULL),
(8, 'C7', 'kepemilikan_sanitasi', 10, 'Benefit', '2024-11-27 22:51:53', NULL),
(9, 'C8', 'tempat_pembuangan_tinja', 10, 'Benefit', '2024-11-27 22:52:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '2024_10_26_061305_periode', 1),
(3, '2024_10_29_091057_kriteria', 1),
(4, '2024_10_29_094028_sub_kriteria', 1),
(5, '2024_11_23_152215_warga', 1),
(6, '2024_11_24_085607_hasil_seleksi', 1);

-- --------------------------------------------------------

--
-- Table structure for table `periode`
--

CREATE TABLE `periode` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('OryzaTxcyWqOLcYn9zCGRw4othj4sug9uP9idcWJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic2ZjelRkZ2FkZFJHV1JvMWhvSUpQcGdCOGJxSmltRGJBcThPMThNTSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fX0=', 1733055142);

-- --------------------------------------------------------

--
-- Table structure for table `subkriteria`
--

CREATE TABLE `subkriteria` (
  `id` bigint UNSIGNED NOT NULL,
  `kriteria_id` bigint UNSIGNED NOT NULL,
  `kode_sub` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_subkriteria` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subkriteria`
--

INSERT INTO `subkriteria` (`id`, `kriteria_id`, `kode_sub`, `nama_subkriteria`, `nilai`, `created_at`, `updated_at`) VALUES
(2, 2, 'SUB-01', '<_rp._500.000', 5, '2024-11-27 16:52:09', NULL),
(3, 2, 'SUB-02', 'rp._500.000-1.000.000', 4, '2024-11-27 22:38:47', NULL),
(4, 2, 'SUB-03', 'rp._1.000.000-2.000.000', 3, '2024-11-27 22:53:47', NULL),
(5, 2, 'SUB-04', 'rp._2.000.000-3.000.000', 2, '2024-11-27 22:55:02', NULL),
(6, 2, 'SUB-05', '>_rp._3.000.000', 1, '2024-11-27 22:55:58', NULL),
(7, 3, 'SUB-06', 'milik_sendiri', 5, '2024-11-27 22:56:21', NULL),
(8, 3, 'SUB-07', 'sewa_/_kontrak', 3, '2024-11-27 22:56:45', NULL),
(9, 3, 'SUB-08', 'bantuan_pemerintah', 1, '2024-11-27 22:57:18', NULL),
(10, 4, 'SUB-09', 'pernamen', 1, '2024-11-27 22:57:56', NULL),
(11, 4, 'SUB-10', 'semi_permanen', 3, '2024-11-27 23:44:08', NULL),
(12, 4, 'SUB-11', 'non_permanen', 5, '2024-11-27 23:44:32', NULL),
(13, 5, 'SUB-12', 'sewa/kontrak', 1, '2024-11-27 23:45:12', NULL),
(14, 5, 'SUB-13', 'milik_sendiri', 3, '2024-11-27 23:45:44', NULL),
(15, 6, 'SUB-14', 'tidak_ada/tidak_tahu', 1, '2024-11-27 23:46:01', NULL),
(16, 6, 'SUB-15', 'milik_pihak_lain_tanpa_surat_perjanjian', 2, '2024-11-27 23:46:28', NULL),
(17, 6, 'SUB-16', 'milik_pihak_lain/surat_perjanjian_lainnya_(termasuk_surat_adat)', 3, '2024-11-27 23:47:16', NULL),
(18, 6, 'SUB-17', 'surat_yang_diakui_pemerintah', 5, '2024-11-27 23:49:43', NULL),
(19, 7, 'SUB-18', '<=_2_orang', 1, '2024-11-27 23:50:21', NULL),
(20, 7, 'SUB-19', '3-5_orang', 3, '2024-11-27 23:53:19', NULL),
(21, 7, 'SUB-20', '>_6_orang', 5, '2024-11-27 23:53:32', NULL),
(22, 8, 'SUB-21', 'milik_sendiri', 1, '2024-11-27 23:54:18', NULL),
(23, 8, 'SUB-22', 'wc_umum', 3, '2024-11-27 23:55:55', NULL),
(24, 8, 'SUB-23', 'tidak_memiliki_wc', 5, '2024-11-27 23:56:09', NULL),
(25, 9, 'SUB-24', 'septictank_pribadi', 1, '2024-11-27 23:56:28', NULL),
(26, 9, 'SUB-25', 'septictank_komunal', 3, '2024-11-27 23:57:24', NULL),
(27, 9, 'SUB-26', 'kali_atau_sungai', 5, '2024-11-27 23:57:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Kepala Kampung', 'kepala', '$2y$12$1djGtoVNqV.1YdYfoxI08.xiESw0h2pG18C4W6ChKGVpyA2Br7Gxa', 'kepala', '2024-11-26 09:22:43', NULL),
(2, 'Perangkat kampung', 'perangkat1', '$2y$12$qRjVi0mc6i9r7Z5XerHBUegoCWg8kKegKYJ2.toTQjHx8O4U.CsTe', 'perangkat', '2024-11-26 09:22:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warga`
--

CREATE TABLE `warga` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun_id` bigint UNSIGNED NOT NULL,
  `nomor_kk` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kk` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provinsi` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kabupaten` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kampung` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rt` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rw` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asal_suku` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pekerjaan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `agama` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `jenis_kelamin` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `penghasilan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_kepemilikan_rumah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `struktur_bangunan_rumah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_kepemilikan_lahan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_legalitas_lahan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_penghuni` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kepemilikan_sanitasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_pembuangan_tinja` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hasil_seleksi`
--
ALTER TABLE `hasil_seleksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hasil_seleksi_warga_id_foreign` (`warga_id`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kriteria_kode_kriteria_unique` (`kode_kriteria`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `periode`
--
ALTER TABLE `periode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `periode_tahun_unique` (`tahun`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `subkriteria`
--
ALTER TABLE `subkriteria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_sub` (`kode_sub`),
  ADD KEY `subkriteria_kriteria_id_foreign` (`kriteria_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warga`
--
ALTER TABLE `warga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warga_tahun_id_foreign` (`tahun_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hasil_seleksi`
--
ALTER TABLE `hasil_seleksi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `periode`
--
ALTER TABLE `periode`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subkriteria`
--
ALTER TABLE `subkriteria`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `warga`
--
ALTER TABLE `warga`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hasil_seleksi`
--
ALTER TABLE `hasil_seleksi`
  ADD CONSTRAINT `hasil_seleksi_warga_id_foreign` FOREIGN KEY (`warga_id`) REFERENCES `warga` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subkriteria`
--
ALTER TABLE `subkriteria`
  ADD CONSTRAINT `subkriteria_kriteria_id_foreign` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warga`
--
ALTER TABLE `warga`
  ADD CONSTRAINT `warga_tahun_id_foreign` FOREIGN KEY (`tahun_id`) REFERENCES `periode` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
