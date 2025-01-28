-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 28, 2025 at 02:10 AM
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
  `skor_akhir` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_kriteria` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kriteria` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot` float DEFAULT NULL,
  `tipe` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Tidak') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Tidak',
  `keterangan` enum('Penting','Tidak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id`, `kode_kriteria`, `nama_kriteria`, `bobot`, `tipe`, `status`, `keterangan`) VALUES
(1, 'C01', 'penghasilan_perbulan', 10, 'Cost', 'Aktif', 'Penting'),
(2, 'C02', 'status_kepemilikan_rumah', 10, 'Benefit', 'Aktif', 'Tidak'),
(3, 'C03', 'struktur_bangunan_rumah', 25, 'Benefit', 'Aktif', 'Penting'),
(4, 'C04', 'status_kepemilikan_lahan', 10, 'Benefit', 'Aktif', 'Tidak'),
(5, 'C05', 'status_legalitas_lahan', 10, 'Benefit', 'Aktif', 'Penting'),
(6, 'C06', 'jumlah_penghuni', 10, 'Cost', 'Aktif', 'Tidak'),
(7, 'C07', 'kepemilikan_sanitasi', 15, 'Benefit', 'Aktif', 'Penting'),
(9, 'C08', 'tempat_pembuangan_tinja', 10, 'Cost', 'Aktif', 'Penting'),
(10, 'C09', 'bangunan_diatas_lahan_sempadan_sungai_atau_kali', NULL, NULL, 'Tidak', 'Tidak'),
(11, 'C10', 'bangunan_berada_di_daerah_buangan_limbah_pabrik_atau_sutet', NULL, NULL, 'Tidak', 'Tidak'),
(13, 'C11', 'luas_lantai_bangunan_rumah_jiwa', NULL, NULL, 'Tidak', 'Tidak'),
(14, 'C12', 'kondisi_atap_terluas', NULL, NULL, 'Tidak', 'Tidak'),
(15, 'C13', 'kondisi_dinding_terluas', NULL, NULL, 'Tidak', 'Tidak'),
(16, 'C14', 'jenis_lantai_terluas', NULL, NULL, 'Tidak', 'Tidak'),
(17, 'C15', 'sumber_air_minum_mandi_dan_cuci', NULL, NULL, 'Tidak', 'Tidak'),
(18, 'C16', 'jarak_sumber_air_dengan_penampungan_tinja_terdekat', NULL, NULL, 'Tidak', 'Tidak'),
(19, 'C17', 'kecukupan_air_minum_mandi_dan_cuci_tahunan', NULL, NULL, 'Tidak', 'Tidak'),
(20, 'C18', 'jenis_kloset', NULL, NULL, 'Tidak', 'Tidak'),
(21, 'C19', 'tempat_pembuangan_sampah', NULL, NULL, 'Tidak', 'Tidak'),
(22, 'C20', 'jumlah_pengangkutan_sampah', NULL, NULL, 'Tidak', 'Tidak'),
(23, 'C21', 'mata_pencaharian_kepala_keluarga', NULL, NULL, 'Tidak', 'Tidak');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `perhitungan`
--

CREATE TABLE `perhitungan` (
  `id` bigint UNSIGNED NOT NULL,
  `warga_id` bigint UNSIGNED NOT NULL,
  `tahun_id` bigint UNSIGNED NOT NULL,
  `skor_akhir` float NOT NULL,
  `status` varchar(12) NOT NULL,
  `terima` enum('Telah Menerima','Belum Menerima') NOT NULL DEFAULT 'Belum Menerima',
  `hitung` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `periode`
--

CREATE TABLE `periode` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `periode`
--

INSERT INTO `periode` (`id`, `tahun`) VALUES
(1, '2024'),
(2, '2025');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Bx0rGUuDqvxisfO3FJOFw2ODpPdWw4UxzbULQMVE', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZ01DVThYS3V6YlhMbmJzZ0ptNzdsbWgwczFkQWY4eFhZMG9POG5LRSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL1dhcmdhIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyOToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL1BlcmlvZGUiO319', 1738030212),
('WpxCYXPNJplH0V2BqY04pBa7leWcX35YFUJQCBEk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV2lUeGRTR2t1TkpEeGM5aUt2Wmt2WUQ3eVpSd2VkdWFzd0dmWTVvMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9IYXNpbCUyMFNlbGVrc2klMjBXYXJnYSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1738029563),
('YoiTwI9ELq7sb8OUtTvWov38IKaAjVFVP57xeBVt', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 Edg/132.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiU3YwZUFYTHVEZ21RU0trb0FUN2xiTzM1ZkU3RnpqRDFzTEt0VFVoUSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9XYXJnYSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1738029557);

-- --------------------------------------------------------

--
-- Table structure for table `subkriteria`
--

CREATE TABLE `subkriteria` (
  `id` bigint UNSIGNED NOT NULL,
  `kriteria_id` bigint UNSIGNED NOT NULL,
  `kode_sub` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_subkriteria` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subkriteria`
--

INSERT INTO `subkriteria` (`id`, `kriteria_id`, `kode_sub`, `nama_subkriteria`, `nilai`) VALUES
(30, 1, 'SUB-01', '<_rp._500.000', 5),
(31, 1, 'SUB-02', 'rp._500.000-1.000.000', 4),
(32, 1, 'SUB-03', 'rp._1.000.000-2.000.000', 3),
(33, 1, 'SUB-04', 'rp._2.000.000-3.000.000', 2),
(34, 1, 'SUB-05', '>_rp._3.000.000', 1),
(35, 2, 'SUB-06', 'milik_sendiri', 5),
(36, 2, 'SUB-07', 'sewa/kontrak', 3),
(37, 2, 'SUB-08', 'bantuan_pemerintah', 1),
(38, 3, 'SUB-09', 'permanen', 1),
(39, 2, 'SUB-10', 'semi_permanen', 3),
(40, 3, 'SUB-11', 'non_permanen', 5),
(41, 4, 'SUB-12', 'sewa/kontrak', 1),
(42, 4, 'SUB-13', 'milik_sendiri', 3),
(43, 5, 'SUB-14', 'tidak_ada/tidak_tahu', 1),
(44, 5, 'SUB-15', 'milik_pihak_lain_tanpa_surat_perjanjian', 2),
(45, 5, 'SUB-16', 'milik_pihak_lain/surat_perjanjian_lainnya_(termasuk_surat_adat)', 3),
(46, 5, 'SUB-17', 'surat_yang_diakui_pemerintah', 5),
(47, 6, 'SUB-18', '<=_2_orang', 1),
(48, 6, 'SUB-19', '3-5_orang', 3),
(49, 6, 'SUB-20', '>_6_orang', 5),
(50, 7, 'SUB-21', 'milik_sendiri', 1),
(51, 7, 'SUB-22', 'wc_umum', 3),
(52, 7, 'SUB-23', 'tidak_memiliki_wc', 5),
(53, 9, 'SUB-24', 'septictank_pribadi', 1),
(54, 9, 'SUB-25', 'septictank_komunal', 3),
(55, 9, 'SUB-26', 'kali_atau_sungai', 5),
(56, 10, 'SUB-27', 'ya', 0),
(57, 10, 'SUB-28', 'tidak', 0),
(58, 10, 'SUB-29', 'tidak_ada_kali/sungai', 0),
(59, 11, 'SUB-30', 'ya', 0),
(60, 11, 'SUB-31', 'tidak', 0),
(61, 13, 'SUB-32', '>=_7,2_meter2/jiwa', 0),
(62, 13, 'SUB-33', '<_7,2_meter2/jiwa', 0),
(63, 14, 'SUB-34', 'bocor', 0),
(64, 14, 'SUB-35', 'tidak_bocor', 0),
(65, 15, 'SUB-36', 'baik', 0),
(66, 15, 'SUB-37', 'rusak', 0),
(67, 16, 'SUB-38', 'tanah', 0),
(68, 16, 'SUB-39', 'bukan_tanah', 0),
(69, 17, 'SUB-40', 'ledeng_meteran/sr', 0),
(70, 17, 'SUB-41', 'ledeng_tanpa_meteran', 0),
(71, 17, 'SUB-42', 'sumber_bor/pompa', 0),
(72, 17, 'SUB-43', 'sumur_terlindung', 0),
(73, 17, 'SUB-44', 'mata_air_terlindung', 0),
(74, 17, 'SUB-45', 'air_hujan', 0),
(75, 17, 'SUB-46', 'air_kemasan/air_isi_ulang', 0),
(76, 17, 'SUB-47', 'sumber_tak_terlindung', 0),
(77, 17, 'SUB-48', 'mata_air_tak_terlindung', 0),
(78, 17, 'SUB-49', 'kali/sungai', 0),
(79, 17, 'SUB-50', 'tangki/mobil/gerobak_air', 0),
(80, 18, 'SUB-51', '>=_10_m', 0),
(81, 18, 'SUB-52', '<_10_m', 0),
(82, 19, 'SUB-53', 'tercukupi/terpenuhi_sepanjang_tahun', 0),
(83, 19, 'SUB-54', 'tercukupi_hanya_pada_bulan_tertentu', 0),
(84, 19, 'SUB-55', 'tidak_pernah_tercukupi', 0),
(85, 20, 'SUB-56', 'leher_angsa', 0),
(86, 20, 'SUB-57', 'bukan_leher_angsa', 0),
(87, 21, 'SUB-58', 'tempat_sampah_pribadi', 0),
(88, 21, 'SUB-59', 'tempat_sampah_komunal/tps/tps-3r', 0),
(89, 21, 'SUB-60', 'dalam_lubang/dibakar', 0),
(90, 21, 'SUB-61', 'ruang_terbuka/lahan_kosong/jalan', 0),
(91, 21, 'SUB-62', 'sungai/saluran_irigasi/draunase_(got/selokan)', 0),
(92, 22, 'SUB-63', '>=_2x_seminggu', 0),
(93, 22, 'SUB-64', '<=_1x_seminggu', 0),
(94, 23, 'SUB-65', 'petani,_perkebunan,_kehutanan,_peternakan', 0),
(95, 23, 'SUB-66', 'perikanan/nelayan', 0),
(96, 23, 'SUB-67', 'pertambangan/galian', 0),
(97, 23, 'SUB-68', 'industri/pabrik', 0),
(98, 23, 'SUB-69', 'konstruksi/bangunan', 0),
(99, 23, 'SUB-70', 'perdagangan/jas_(guru,_tenaga_kerja,_hotel,_dll)', 0),
(100, 23, 'SUB-71', 'pegawai_pemerintah', 0),
(101, 3, 'SUB-72', 'semi_permanen', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Kepala Kampung', 'kepala', '$2y$12$1djGtoVNqV.1YdYfoxI08.xiESw0h2pG18C4W6ChKGVpyA2Br7Gxa', 'kepala'),
(2, 'Perangkat kampung', 'perangkat1', '$2y$12$qRjVi0mc6i9r7Z5XerHBUegoCWg8kKegKYJ2.toTQjHx8O4U.CsTe', 'perangkat');

-- --------------------------------------------------------

--
-- Table structure for table `warga`
--

CREATE TABLE `warga` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun_id` bigint UNSIGNED NOT NULL,
  `nomor_kk` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kk` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `provinsi` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kabupaten` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kampung` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rt` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rw` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `asal_suku` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `agama` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `jenis_kelamin` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `penghasilan_perbulan` text COLLATE utf8mb4_unicode_ci,
  `status_kepemilikan_rumah` text COLLATE utf8mb4_unicode_ci,
  `struktur_bangunan_rumah` text COLLATE utf8mb4_unicode_ci,
  `status_kepemilikan_lahan` text COLLATE utf8mb4_unicode_ci,
  `status_legalitas_lahan` text COLLATE utf8mb4_unicode_ci,
  `jumlah_penghuni` text COLLATE utf8mb4_unicode_ci,
  `kepemilikan_sanitasi` text COLLATE utf8mb4_unicode_ci,
  `tempat_pembuangan_tinja` text COLLATE utf8mb4_unicode_ci,
  `bangunan_diatas_lahan_sempadan_sungai_atau_kali` text COLLATE utf8mb4_unicode_ci,
  `bangunan_berada_di_daerah_buangan_limbah_pabrik_atau_sutet` text COLLATE utf8mb4_unicode_ci,
  `luas_lantai_bangunan_rumah_jiwa` text COLLATE utf8mb4_unicode_ci,
  `kondisi_atap_terluas` text COLLATE utf8mb4_unicode_ci,
  `kondisi_dinding_terluas` text COLLATE utf8mb4_unicode_ci,
  `jenis_lantai_terluas` text COLLATE utf8mb4_unicode_ci,
  `sumber_air_minum_mandi_dan_cuci` text COLLATE utf8mb4_unicode_ci,
  `jarak_sumber_air_dengan_penampungan_tinja_terdekat` text COLLATE utf8mb4_unicode_ci,
  `kecukupan_air_minum_mandi_dan_cuci_tahunan` text COLLATE utf8mb4_unicode_ci,
  `jenis_kloset` text COLLATE utf8mb4_unicode_ci,
  `tempat_pembuangan_sampah` text COLLATE utf8mb4_unicode_ci,
  `jumlah_pengangkutan_sampah` text COLLATE utf8mb4_unicode_ci,
  `mata_pencaharian_kepala_keluarga` text COLLATE utf8mb4_unicode_ci
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
-- Indexes for table `perhitungan`
--
ALTER TABLE `perhitungan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_warga` (`warga_id`),
  ADD KEY `fk_tahun_perhitungan` (`tahun_id`);

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `perhitungan`
--
ALTER TABLE `perhitungan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `periode`
--
ALTER TABLE `periode`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subkriteria`
--
ALTER TABLE `subkriteria`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `warga`
--
ALTER TABLE `warga`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hasil_seleksi`
--
ALTER TABLE `hasil_seleksi`
  ADD CONSTRAINT `hasil_seleksi_warga_id_foreign` FOREIGN KEY (`warga_id`) REFERENCES `warga` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `perhitungan`
--
ALTER TABLE `perhitungan`
  ADD CONSTRAINT `fk_tahun_perhitungan` FOREIGN KEY (`tahun_id`) REFERENCES `periode` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_warga` FOREIGN KEY (`warga_id`) REFERENCES `warga` (`id`) ON DELETE CASCADE;

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
