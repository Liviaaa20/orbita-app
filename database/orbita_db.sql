-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 15, 2026 at 08:55 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `orbita_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `alats`
--

CREATE TABLE `alats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sub_kategori_id` bigint(20) UNSIGNED NOT NULL,
  `nama_alat` varchar(255) NOT NULL,
  `merk_type` varchar(255) DEFAULT NULL,
  `nomor_seri` varchar(255) NOT NULL,
  `tahun_pengadaan` varchar(4) DEFAULT NULL,
  `jenis` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `jadwal_hari` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `kondisi` varchar(255) NOT NULL,
  `foto_alat` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rentang_ukur` varchar(255) DEFAULT NULL,
  `resolusi` varchar(255) DEFAULT NULL,
  `akurasi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alats`
--

INSERT INTO `alats` (`id`, `sub_kategori_id`, `nama_alat`, `merk_type`, `nomor_seri`, `tahun_pengadaan`, `jenis`, `lokasi`, `jadwal_hari`, `status`, `kondisi`, `foto_alat`, `created_at`, `updated_at`, `rentang_ukur`, `resolusi`, `akurasi`) VALUES
(1, 1, 'Termometer (BB)', 'lamberjs', '00001', '1999', 'Harian', 'taman alat', NULL, 'Aktif', 'Rusak', '1778769199__ (12).jpeg', '2026-05-14 14:33:19', '2026-05-15 04:00:18', '1', '2', '0'),
(2, 2, 'AIAIAI', NULL, '00002', NULL, 'Harian', 'taman alat', NULL, 'Aktif', 'Baik', '1778769253__ (14).jpeg', '2026-05-14 14:34:13', '2026-05-15 04:00:18', NULL, '0', NULL),
(3, 1, 'Termometer (BB)', NULL, '00003', NULL, 'Mingguan', 'taman alat', NULL, 'Aktif', 'Baik', '1778773317__ (9).jpeg', '2026-05-14 15:41:57', '2026-05-14 15:41:57', NULL, NULL, NULL),
(4, 2, 'AIAIAI', NULL, '56556', NULL, 'Mingguan', 'taman alat', NULL, 'Aktif', 'Baik', '1778820798_Screen Shot 2024-11-16 at 13.54.57.png', '2026-05-15 04:53:18', '2026-05-15 04:53:18', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `histori_operasionals`
--

CREATE TABLE `histori_operasionals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `waktu` datetime NOT NULL,
  `jenis_aktivitas` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `alat_id` bigint(20) UNSIGNED NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `kondisi_fisik` enum('Baik','RR','RB') NOT NULL DEFAULT 'Baik',
  `deskripsi_hasil` text NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `dokumen` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `uraian_kerusakan` text DEFAULT NULL,
  `tindakan_perbaikan` text DEFAULT NULL,
  `nilai_koreksi` varchar(255) DEFAULT NULL,
  `nilai_ketidakpastian` varchar(255) DEFAULT NULL,
  `catatan_khusus` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `histori_operasionals`
--

INSERT INTO `histori_operasionals` (`id`, `waktu`, `jenis_aktivitas`, `kategori`, `alat_id`, `lokasi`, `kondisi_fisik`, `deskripsi_hasil`, `user_id`, `dokumen`, `created_at`, `updated_at`, `uraian_kerusakan`, `tindakan_perbaikan`, `nilai_koreksi`, `nilai_ketidakpastian`, `catatan_khusus`) VALUES
(1, '2026-05-14 21:57:29', 'Maintenance Harian', 'Automatic Weather Station (AWS)', 1, 'taman alat', 'Baik', 'Pengecekan rutin. Status: Aktif, Kondisi: Baik', 1, '1778770649_1.jpeg', '2026-05-14 14:57:29', '2026-05-14 14:57:29', NULL, NULL, NULL, NULL, NULL),
(2, '2026-05-14 21:57:29', 'Maintenance Harian', 'Digital Barometer', 2, 'taman alat', 'Baik', 'Pengecekan rutin. Status: Non-Aktif, Kondisi: RB', 1, '1778770649_2.jpeg', '2026-05-14 14:57:29', '2026-05-14 14:57:29', NULL, NULL, NULL, NULL, NULL),
(3, '2026-05-14 22:22:27', 'Maintenance Harian', 'Automatic Weather Station (AWS)', 1, 'taman alat', 'Baik', 'Pengecekan rutin. Status: Aktif, Kondisi: Baik', 1, '1778772147_1.jpeg', '2026-05-14 15:22:27', '2026-05-14 15:22:27', NULL, NULL, NULL, NULL, NULL),
(4, '2026-05-14 22:22:27', 'Maintenance Harian', 'Digital Barometer', 2, 'taman alat', 'Baik', 'Pengecekan rutin. Status: Non-Aktif, Kondisi: Baik', 1, '1778772147_2.jpeg', '2026-05-14 15:22:27', '2026-05-14 15:22:27', NULL, NULL, NULL, NULL, NULL),
(5, '2026-05-14 23:08:37', 'Maintenance Harian', 'Automatic Weather Station (AWS)', 1, 'taman alat', 'Baik', 'Pengecekan rutin. Status: Non-Aktif, Kondisi: RR', 1, NULL, '2026-05-14 16:08:37', '2026-05-14 16:08:37', NULL, NULL, NULL, NULL, NULL),
(6, '2026-05-14 23:08:37', 'Maintenance Harian', 'Digital Barometer', 2, 'taman alat', 'Baik', 'Pengecekan rutin. Status: Aktif, Kondisi: RB', 1, NULL, '2026-05-14 16:08:37', '2026-05-14 16:08:37', NULL, NULL, NULL, NULL, NULL),
(7, '2026-05-14 23:11:39', 'Maintenance Harian', 'Automatic Weather Station (AWS)', 1, 'taman alat', 'Baik', 'Pengecekan rutin. Status: Non-Aktif, Kondisi: Baik', 1, NULL, '2026-05-14 16:11:39', '2026-05-14 16:11:39', NULL, NULL, NULL, NULL, NULL),
(8, '2026-05-14 23:11:39', 'Maintenance Harian', 'Digital Barometer', 2, 'taman alat', 'Baik', 'Pengecekan rutin. Status: Aktif, Kondisi: Baik', 1, NULL, '2026-05-14 16:11:39', '2026-05-14 16:11:39', NULL, NULL, NULL, NULL, NULL),
(9, '2026-05-15 11:00:18', 'Maintenance Harian', 'Automatic Weather Station (AWS)', 1, 'taman alat', 'Baik', 'Pengecekan rutin. Status: Aktif, Kondisi: RR', 1, '1778817618_1.png', '2026-05-15 04:00:18', '2026-05-15 04:00:18', NULL, NULL, NULL, NULL, NULL),
(10, '2026-05-15 11:00:18', 'Maintenance Harian', 'Digital Barometer', 2, 'taman alat', 'Baik', 'Pengecekan rutin. Status: Aktif, Kondisi: Baik', 1, NULL, '2026-05-15 04:00:18', '2026-05-15 04:00:18', NULL, NULL, NULL, NULL, NULL),
(11, '2026-05-15 22:15:55', 'Gangguan', '-', 2, 'Stasiun Meteorologi Maritim Semarang', 'Baik', '[TEKNISI] LAPORAN KERUSAKAN: adohh', 2, 'perbaikan/QGuPaP3MSORI0knEAsbeLSiJvHLQjZg2PFeIDg1s.jpg', '2026-05-15 15:15:55', '2026-05-15 15:15:55', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_dinas`
--

CREATE TABLE `jadwal_dinas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `shift` varchar(255) NOT NULL,
  `jam` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kalibrasis`
--

CREATE TABLE `kalibrasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategoris`
--

CREATE TABLE `kategoris` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_kategori` varchar(255) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `tahun_pengadaan` year(4) NOT NULL,
  `merk` varchar(255) NOT NULL,
  `jenis` enum('Sistem','Non Sistem') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategoris`
--

INSERT INTO `kategoris` (`id`, `kode_kategori`, `nama_kategori`, `tahun_pengadaan`, `merk`, `jenis`, `created_at`, `updated_at`) VALUES
(1, 'K001', 'Automatic Weather Station (AWS)', '2024', 'Lambrecht', 'Sistem', '2026-05-14 14:32:09', '2026-05-14 14:32:09'),
(2, 'K002', 'Digital Barometer', '2023', 'Vaisala', 'Non Sistem', '2026-05-14 14:32:09', '2026-05-14 14:32:09');

-- --------------------------------------------------------

--
-- Table structure for table `maintenances`
--

CREATE TABLE `maintenances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `alat_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_maintenance` enum('harian','mingguan') NOT NULL,
  `tanggal` date NOT NULL,
  `shift` varchar(255) DEFAULT NULL,
  `status` enum('proses','selesai') NOT NULL DEFAULT 'proses',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenances`
--

INSERT INTO `maintenances` (`id`, `alat_id`, `jenis_maintenance`, `tanggal`, `shift`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'harian', '2026-05-14', 'pagi', 'selesai', '2026-05-14 14:34:38', '2026-05-14 16:08:37'),
(2, 2, 'harian', '2026-05-14', 'pagi', 'selesai', '2026-05-14 14:34:38', '2026-05-14 16:08:37'),
(3, 1, 'harian', '2026-05-14', 'Siang', 'selesai', '2026-05-14 15:52:44', '2026-05-14 16:11:39'),
(4, 2, 'harian', '2026-05-14', 'Siang', 'selesai', '2026-05-14 15:52:44', '2026-05-14 16:11:39'),
(5, 1, 'harian', '2026-05-15', 'Pagi', 'proses', '2026-05-15 03:59:38', '2026-05-15 16:19:19'),
(6, 2, 'harian', '2026-05-15', 'Pagi', 'selesai', '2026-05-15 03:59:38', '2026-05-15 04:00:18'),
(7, 3, 'mingguan', '2026-05-15', 'Jumat', 'selesai', '2026-05-15 04:25:22', '2026-05-15 07:35:11'),
(8, 3, 'mingguan', '2026-05-15', 'Senin', 'proses', '2026-05-15 04:52:35', '2026-05-15 04:52:35'),
(9, 4, 'mingguan', '2026-05-15', 'Senin', 'proses', '2026-05-15 04:53:31', '2026-05-15 04:53:31'),
(10, 1, 'harian', '2026-05-15', 'Siang', 'proses', '2026-05-15 04:55:02', '2026-05-15 04:55:02'),
(11, 2, 'harian', '2026-05-15', 'Siang', 'proses', '2026-05-15 04:55:02', '2026-05-15 04:55:02'),
(12, 4, 'mingguan', '2026-05-15', 'Jumat', 'proses', '2026-05-15 05:08:53', '2026-05-15 07:35:47'),
(13, 1, 'mingguan', '2026-05-15', 'Jumat', 'selesai', '2026-05-15 07:14:29', '2026-05-15 07:35:11'),
(14, 2, 'mingguan', '2026-05-15', 'Senin', 'proses', '2026-05-15 07:35:33', '2026-05-15 07:35:33'),
(15, 2, 'mingguan', '2026-05-15', 'Jumat', 'proses', '2026-05-15 07:35:47', '2026-05-15 07:35:47'),
(16, 3, 'harian', '2026-05-15', 'Siang', 'proses', '2026-05-15 15:49:01', '2026-05-15 15:49:01'),
(17, 3, 'harian', '2026-05-15', 'Pagi', 'proses', '2026-05-15 16:19:19', '2026-05-15 16:19:19'),
(18, 1, 'mingguan', '2026-05-16', 'Jumat', 'proses', '2026-05-15 17:09:20', '2026-05-15 17:09:20'),
(19, 3, 'mingguan', '2026-05-16', 'Jumat', 'proses', '2026-05-15 17:09:20', '2026-05-15 17:09:20'),
(20, 2, 'mingguan', '2026-05-16', 'Jumat', 'proses', '2026-05-15 17:09:20', '2026-05-15 17:09:20'),
(21, 4, 'mingguan', '2026-05-16', 'Jumat', 'proses', '2026-05-15 17:09:20', '2026-05-15 17:09:20'),
(22, 1, 'harian', '2026-05-16', 'Pagi', 'proses', '2026-05-15 17:22:31', '2026-05-15 17:22:31'),
(23, 3, 'harian', '2026-05-16', 'Pagi', 'proses', '2026-05-15 17:22:31', '2026-05-15 17:22:31'),
(24, 2, 'harian', '2026-05-16', 'Pagi', 'proses', '2026-05-15 17:22:31', '2026-05-15 17:22:31'),
(25, 4, 'harian', '2026-05-16', 'Pagi', 'proses', '2026-05-15 17:22:31', '2026-05-15 17:22:31');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0000_05_06_042259_create_roles_table', 1),
(2, '0001_01_01_000000_create_users_table', 1),
(3, '0001_01_01_000001_create_cache_table', 1),
(4, '0001_01_01_000002_create_jobs_table', 1),
(5, '2026_05_05_152032_create_kategoris_table', 1),
(6, '2026_05_05_152041_create_sub_kategoris_table', 1),
(7, '2026_05_05_152050_create_alats_table', 1),
(8, '2026_05_05_152057_create_pengecekans_table', 1),
(9, '2026_05_05_152105_create_perbaikans_table', 1),
(10, '2026_05_05_152113_create_kalibrasis_table', 1),
(11, '2026_05_06_033203_create_jadwal_dinas_table', 1),
(12, '2026_05_06_033651_create_maintenances_table', 1),
(13, '2026_05_07_062805_create_histori_operasionals_table', 1),
(14, '2026_05_14_083332_add_jadwal_hari_to_alats_table', 1),
(15, '2026_05_14_124544_add_standard_bmkg_columns_to_tables', 1),
(16, '2026_05_14_220109_add_nip_to_users_table', 2),
(17, '2026_05_15_221458_add_alat_id_to_perbaikans_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `pengecekans`
--

CREATE TABLE `pengecekans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `alat_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` varchar(50) DEFAULT NULL,
  `is_checked` tinyint(1) NOT NULL DEFAULT 0,
  `kondisi_akhir` enum('Baik','Rusak Ringan','Rusak Berat') NOT NULL DEFAULT 'Baik',
  `foto_kegiatan` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengecekans`
--

INSERT INTO `pengecekans` (`id`, `alat_id`, `user_id`, `tanggal`, `waktu`, `is_checked`, `kondisi_akhir`, `foto_kegiatan`, `catatan`, `created_at`, `updated_at`) VALUES
(2, 1, 1, '2026-05-14', 'Pagi', 1, 'Baik', NULL, 'bagus', '2026-05-14 14:57:29', '2026-05-14 14:57:29'),
(3, 2, 1, '2026-05-14', 'Pagi', 0, 'Baik', NULL, 'rusak dah', '2026-05-14 14:57:29', '2026-05-14 14:57:29'),
(4, 1, 1, '2026-05-14', 'Pagi', 1, 'Baik', NULL, 'yes yes yes', '2026-05-14 15:22:27', '2026-05-14 15:22:27'),
(5, 2, 1, '2026-05-14', 'Pagi', 1, 'Baik', NULL, 'bo no', '2026-05-14 15:22:27', '2026-05-14 15:22:27'),
(6, 1, 1, '2026-05-14', 'Pagi', 0, 'Baik', NULL, 'hhh', '2026-05-14 16:08:37', '2026-05-14 16:08:37'),
(7, 2, 1, '2026-05-14', 'Pagi', 0, 'Baik', NULL, NULL, '2026-05-14 16:08:37', '2026-05-14 16:08:37'),
(8, 1, 1, '2026-05-14', 'Siang', 1, 'Baik', NULL, NULL, '2026-05-14 16:11:39', '2026-05-14 16:11:39'),
(9, 2, 1, '2026-05-14', 'Siang', 1, 'Baik', NULL, NULL, '2026-05-14 16:11:39', '2026-05-14 16:11:39'),
(10, 1, 1, '2026-05-15', 'Pagi', 0, 'Baik', NULL, 'sedikt rusak', '2026-05-15 04:00:18', '2026-05-15 04:00:18'),
(11, 2, 1, '2026-05-15', 'Pagi', 1, 'Baik', NULL, 'ya ya', '2026-05-15 04:00:18', '2026-05-15 04:00:18'),
(12, 3, 1, '2026-05-15', 'Jumat', 1, 'Rusak Ringan', NULL, NULL, '2026-05-15 04:51:21', '2026-05-15 04:51:21'),
(13, 3, 1, '2026-05-15', 'Jumat', 1, 'Baik', NULL, NULL, '2026-05-15 05:09:12', '2026-05-15 05:09:12'),
(14, 4, 1, '2026-05-15', 'Jumat', 1, 'Rusak Ringan', NULL, NULL, '2026-05-15 05:09:12', '2026-05-15 05:09:12'),
(15, 3, 1, '2026-05-15', 'Jumat', 1, 'Baik', NULL, NULL, '2026-05-15 05:38:19', '2026-05-15 05:38:19'),
(16, 4, 1, '2026-05-15', 'Jumat', 1, 'Baik', 'uploads/maintenance/1778827262_4.png', NULL, '2026-05-15 06:41:02', '2026-05-15 06:41:02'),
(17, 3, 1, '2026-05-15', 'Jumat', 1, 'Rusak Berat', NULL, NULL, '2026-05-15 06:41:14', '2026-05-15 06:41:14'),
(18, 1, 1, '2026-05-15', 'Jumat', 1, 'Baik', NULL, NULL, '2026-05-15 07:35:11', '2026-05-15 07:35:11'),
(19, 3, 1, '2026-05-15', 'Jumat', 1, 'Rusak Ringan', NULL, NULL, '2026-05-15 07:35:11', '2026-05-15 07:35:11'),
(20, 4, 1, '2026-05-15', 'Jumat', 1, 'Baik', NULL, NULL, '2026-05-15 07:35:18', '2026-05-15 07:35:18');

-- --------------------------------------------------------

--
-- Table structure for table `perbaikans`
--

CREATE TABLE `perbaikans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_tiket` varchar(255) NOT NULL,
  `alat_id` bigint(20) UNSIGNED DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tgl_permintaan` timestamp NULL DEFAULT NULL,
  `tgl_diterima` timestamp NULL DEFAULT NULL,
  `tgl_selesai` timestamp NULL DEFAULT NULL,
  `user` varchar(255) NOT NULL,
  `kategori_perbaikan` text NOT NULL,
  `keterangan` text NOT NULL,
  `validasi` tinyint(1) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('pending','onproses','selesai') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perbaikans`
--

INSERT INTO `perbaikans` (`id`, `no_tiket`, `alat_id`, `foto`, `tgl_permintaan`, `tgl_diterima`, `tgl_selesai`, `user`, `kategori_perbaikan`, `keterangan`, `validasi`, `catatan`, `status`, `created_at`, `updated_at`) VALUES
(2, 'TKT-6A0738AB4FFE9', 2, 'perbaikan/QGuPaP3MSORI0knEAsbeLSiJvHLQjZg2PFeIDg1s.jpg', '2026-05-15 15:15:55', '2026-05-15 17:37:16', '2026-05-15 17:37:13', 'Triyono', 'tolong', 'adohh', NULL, 'aman bos', 'selesai', '2026-05-15 15:15:55', '2026-05-15 17:37:16');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_role` varchar(255) NOT NULL,
  `nama_role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `kode_role`, `nama_role`, `created_at`, `updated_at`) VALUES
(1, 'R001', 'admin', '2026-05-14 14:32:09', '2026-05-14 14:32:09'),
(2, 'R002', 'teknisi', '2026-05-14 14:32:09', '2026-05-14 14:32:09'),
(3, 'R003', 'kepala kelompok', '2026-05-15 08:01:43', '2026-05-15 08:01:43');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('kcOtDPysyniPch8ei3Aj77JlhilVwDtnU2B98xMy', 3, '192.168.1.5', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.4 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiczFJTFd4dnBHY0dzVUl6ek1DbVZRb1EwTHBSRWZteFVVdTRFQ054eSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xOTIuMTY4LjEuNjo4MDAwL3BlcmJhaWthbi9jcmVhdGUiO3M6NToicm91dGUiO3M6MTY6InBlcmJhaWthbi5jcmVhdGUiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1778868890),
('tUgZrkw1qLnmb6ZV6NlH2tcenB04VaZcG5h7wZjz', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36 Edg/128.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibWJjTGNCNHF3YjVKUE9RSzlJT2lPMXZURGZXNzBsTTQxZWxoTmQ0MCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXRhLWFsYXQiO3M6NToicm91dGUiO3M6MTU6ImRhdGEtYWxhdC5pbmRleCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1778870976);

-- --------------------------------------------------------

--
-- Table structure for table `sub_kategoris`
--

CREATE TABLE `sub_kategoris` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `kode_sub_kategori` varchar(255) NOT NULL,
  `nama_sub_kategori` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_kategoris`
--

INSERT INTO `sub_kategoris` (`id`, `kategori_id`, `kode_sub_kategori`, `nama_sub_kategori`, `created_at`, `updated_at`) VALUES
(1, 1, 'SK001', 'Termometer (BB)', '2026-05-14 14:32:50', '2026-05-14 14:32:50'),
(2, 2, 'SK002', 'AIAIAI', '2026-05-14 14:33:02', '2026-05-14 14:33:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_user` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `kode_user`, `name`, `nip`, `username`, `email`, `password`, `status`, `role_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'U001', 'Hajirin Arafat', '123456789777', 'admin', 'admin@gmail.com', '$2y$12$9jDB.uxWGYzZdOTB5.8B6u7miLUWZD/3uXYcuajfPI833ESDq5OjC', 'aktif', 1, NULL, '2026-05-14 14:32:09', '2026-05-14 15:21:50'),
(2, 'U002', 'Triyono', '980829221811011', 'Triyono', 'ryandra@gmail.com', '$2y$12$8WL3.ss6gehWo3DXEKyMQOU4EEHaXWMRvztxWfI1Prd9R8Kkec3S.', 'aktif', 2, NULL, '2026-05-15 08:00:39', '2026-05-15 08:00:39'),
(3, 'U003', 'agusta', '637182819921111', 'agusta', 'agusta@gmail.com', '$2y$12$aLsNz.lHB.Tfwy6JqusYg.R.ZUlG9Ujlh7sw/mBfo/YUCkxwGtxby', 'aktif', 3, NULL, '2026-05-15 08:02:16', '2026-05-15 08:02:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alats`
--
ALTER TABLE `alats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alats_nomor_seri_unique` (`nomor_seri`),
  ADD KEY `alats_sub_kategori_id_foreign` (`sub_kategori_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `histori_operasionals`
--
ALTER TABLE `histori_operasionals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `histori_operasionals_alat_id_foreign` (`alat_id`),
  ADD KEY `histori_operasionals_user_id_foreign` (`user_id`);

--
-- Indexes for table `jadwal_dinas`
--
ALTER TABLE `jadwal_dinas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kalibrasis`
--
ALTER TABLE `kalibrasis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategoris`
--
ALTER TABLE `kategoris`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kategoris_kode_kategori_unique` (`kode_kategori`);

--
-- Indexes for table `maintenances`
--
ALTER TABLE `maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenances_alat_id_foreign` (`alat_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengecekans`
--
ALTER TABLE `pengecekans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengecekans_alat_id_foreign` (`alat_id`),
  ADD KEY `pengecekans_user_id_foreign` (`user_id`);

--
-- Indexes for table `perbaikans`
--
ALTER TABLE `perbaikans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perbaikans_alat_id_foreign` (`alat_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_kode_role_unique` (`kode_role`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sub_kategoris`
--
ALTER TABLE `sub_kategoris`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sub_kategoris_kode_sub_kategori_unique` (`kode_sub_kategori`),
  ADD KEY `sub_kategoris_kategori_id_foreign` (`kategori_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_kode_user_unique` (`kode_user`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alats`
--
ALTER TABLE `alats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `histori_operasionals`
--
ALTER TABLE `histori_operasionals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `jadwal_dinas`
--
ALTER TABLE `jadwal_dinas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kalibrasis`
--
ALTER TABLE `kalibrasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategoris`
--
ALTER TABLE `kategoris`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `maintenances`
--
ALTER TABLE `maintenances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pengecekans`
--
ALTER TABLE `pengecekans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `perbaikans`
--
ALTER TABLE `perbaikans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sub_kategoris`
--
ALTER TABLE `sub_kategoris`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alats`
--
ALTER TABLE `alats`
  ADD CONSTRAINT `alats_sub_kategori_id_foreign` FOREIGN KEY (`sub_kategori_id`) REFERENCES `sub_kategoris` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `histori_operasionals`
--
ALTER TABLE `histori_operasionals`
  ADD CONSTRAINT `histori_operasionals_alat_id_foreign` FOREIGN KEY (`alat_id`) REFERENCES `alats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `histori_operasionals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `maintenances`
--
ALTER TABLE `maintenances`
  ADD CONSTRAINT `maintenances_alat_id_foreign` FOREIGN KEY (`alat_id`) REFERENCES `alats` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengecekans`
--
ALTER TABLE `pengecekans`
  ADD CONSTRAINT `pengecekans_alat_id_foreign` FOREIGN KEY (`alat_id`) REFERENCES `alats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengecekans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `perbaikans`
--
ALTER TABLE `perbaikans`
  ADD CONSTRAINT `perbaikans_alat_id_foreign` FOREIGN KEY (`alat_id`) REFERENCES `alats` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sub_kategoris`
--
ALTER TABLE `sub_kategoris`
  ADD CONSTRAINT `sub_kategoris_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategoris` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
