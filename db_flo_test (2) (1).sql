-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 01 Sep 2026 pada 10.22
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_flo_test`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_riwayat`
--

CREATE TABLE `detail_riwayat` (
  `id_detail_riwayat` int(11) NOT NULL,
  `id_riwayat_tes` int(11) NOT NULL,
  `id_soal` int(11) NOT NULL,
  `jawaban_siswa` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_riwayat`
--

INSERT INTO `detail_riwayat` (`id_detail_riwayat`, `id_riwayat_tes`, `id_soal`, `jawaban_siswa`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'B', '2026-08-24 02:54:31', '2026-08-24 02:54:31', NULL),
(2, 1, 2, 'C', '2026-08-24 02:54:31', '2026-08-24 02:54:31', NULL),
(3, 2, 1, 'B', '2026-08-24 03:25:09', '2026-08-24 03:25:09', NULL),
(4, 2, 2, 'C', '2026-08-24 03:25:09', '2026-08-24 03:25:09', NULL),
(5, 3, 3, 'B', '2026-08-24 03:31:29', '2026-08-24 03:31:29', NULL),
(6, 3, 4, 'B', '2026-08-24 03:31:29', '2026-08-24 03:31:29', NULL),
(7, 4, 3, 'B', '2026-08-24 04:17:59', '2026-08-24 04:17:59', NULL),
(8, 4, 4, 'B', '2026-08-24 04:17:59', '2026-08-24 04:17:59', NULL),
(9, 5, 3, 'B', '2026-08-24 23:21:54', '2026-08-24 23:21:54', NULL),
(10, 5, 4, 'B', '2026-08-24 23:21:54', '2026-08-24 23:21:54', NULL),
(11, 6, 3, 'B', '2026-08-24 23:32:59', '2026-08-24 23:32:59', NULL),
(12, 6, 4, 'B', '2026-08-24 23:32:59', '2026-08-24 23:32:59', NULL),
(13, 7, 3, 'A', '2026-08-24 23:36:04', '2026-08-24 23:36:04', NULL),
(14, 7, 4, 'B', '2026-08-24 23:36:04', '2026-08-24 23:36:04', NULL),
(15, 15, 8, '', '2026-08-28 08:21:52', '2026-08-28 08:21:52', NULL),
(16, 16, 9, 'A', '2026-08-28 09:23:14', '2026-08-28 09:23:14', NULL),
(17, 16, 10, 'A', '2026-08-28 09:23:14', '2026-08-28 09:23:14', NULL),
(18, 14, 5, 'A', '2026-08-31 08:25:45', '2026-08-31 08:25:45', NULL),
(19, 14, 6, '', '2026-08-31 08:25:45', '2026-08-31 08:25:45', NULL),
(20, 18, 11, 'D', '2026-08-31 08:56:11', '2026-08-31 08:56:11', NULL),
(21, 18, 12, '', '2026-08-31 08:56:11', '2026-08-31 08:56:11', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_08_24_053533_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_tes`
--

CREATE TABLE `riwayat_tes` (
  `id_riwayat_tes` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_test` int(11) NOT NULL,
  `tanggal_masuk` datetime NOT NULL,
  `skor_akhir` decimal(5,2) DEFAULT NULL,
  `jumlah_benar` int(11) DEFAULT 0,
  `jumlah_salah` int(11) DEFAULT 0,
  `status_pengerjaan` enum('draft','selesai') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `riwayat_tes`
--

INSERT INTO `riwayat_tes` (`id_riwayat_tes`, `id_user`, `id_test`, `tanggal_masuk`, `skor_akhir`, `jumlah_benar`, `jumlah_salah`, `status_pengerjaan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-08-24 09:54:31', 100.00, 2, 0, 'selesai', '2026-08-24 02:54:31', '2026-08-24 02:54:31', NULL),
(2, 1, 1, '2026-08-24 10:17:51', 100.00, 2, 0, 'selesai', '2026-08-24 03:17:51', '2026-08-24 03:25:09', NULL),
(3, 1, 2, '2026-08-24 10:30:54', 100.00, 2, 0, 'selesai', '2026-08-24 03:30:54', '2026-08-24 03:31:29', NULL),
(4, 1, 2, '2026-08-24 10:37:25', 100.00, 2, 0, 'selesai', '2026-08-24 03:37:25', '2026-08-24 04:17:59', NULL),
(5, 1, 2, '2026-08-24 11:18:29', 100.00, 2, 0, 'selesai', '2026-08-24 04:18:29', '2026-08-24 23:21:54', NULL),
(6, 1, 2, '2026-08-25 06:32:49', 100.00, 2, 0, 'selesai', '2026-08-24 23:32:49', '2026-08-24 23:32:59', NULL),
(7, 1, 2, '2026-08-25 06:35:39', 50.00, 1, 1, 'selesai', '2026-08-24 23:35:39', '2026-08-24 23:36:04', NULL),
(8, 1, 8, '2026-08-27 13:15:37', NULL, 0, 0, 'draft', '2026-08-27 06:15:37', '2026-08-27 06:15:37', NULL),
(9, 1, 12, '2026-08-27 13:15:58', NULL, 0, 0, 'draft', '2026-08-27 06:15:58', '2026-08-27 06:15:58', NULL),
(10, 3, 8, '2026-08-27 13:42:18', NULL, 0, 0, 'draft', '2026-08-27 06:42:18', '2026-08-27 06:42:18', NULL),
(11, 3, 11, '2026-08-27 13:42:51', NULL, 0, 0, 'draft', '2026-08-27 06:42:51', '2026-08-27 06:42:51', NULL),
(12, 3, 13, '2026-08-27 13:43:08', NULL, 0, 0, 'draft', '2026-08-27 06:43:08', '2026-08-27 06:43:08', NULL),
(13, 3, 9, '2026-08-27 13:43:29', NULL, 0, 0, 'draft', '2026-08-27 06:43:29', '2026-08-27 06:43:29', NULL),
(14, 1, 9, '2026-08-27 13:59:52', 50.00, 1, 1, 'selesai', '2026-08-27 06:59:52', '2026-08-31 08:25:45', NULL),
(15, 1, 14, '2026-08-27 14:19:19', 0.00, 0, 1, 'selesai', '2026-08-27 07:19:19', '2026-08-28 08:21:52', NULL),
(16, 2, 20, '2026-08-28 16:22:28', 50.00, 1, 1, 'selesai', '2026-08-28 09:22:28', '2026-08-28 09:23:14', NULL),
(17, 2, 17, '2026-08-28 16:23:38', NULL, 0, 0, 'draft', '2026-08-28 09:23:38', '2026-08-28 09:23:38', NULL),
(18, 2, 22, '2026-08-31 15:55:50', 50.00, 1, 1, 'selesai', '2026-08-31 08:55:50', '2026-08-31 08:56:11', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `soal`
--

CREATE TABLE `soal` (
  `id_soal` int(11) NOT NULL,
  `id_test` int(11) NOT NULL,
  `nomor_soal` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `pilihan_a` varchar(255) NOT NULL,
  `pilihan_b` varchar(255) NOT NULL,
  `pilihan_c` varchar(255) NOT NULL,
  `pilihan_d` varchar(255) NOT NULL,
  `kunci_jawaban` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `soal`
--

INSERT INTO `soal` (`id_soal`, `id_test`, `nomor_soal`, `pertanyaan`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `kunci_jawaban`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Hasil dari 2 + 2 adalah?', '3', '4', '5', '6', 'B', '2026-08-23 22:46:02', '2026-08-23 22:46:02', NULL),
(2, 1, 2, 'Hasil dari 5 x 2 adalah?', '7', '8', '10', '12', 'C', '2026-08-23 22:46:02', '2026-08-23 22:46:02', NULL),
(3, 2, 1, 'Berapakah 2 + 2?', '3', '4', '5', '6', 'B', '2026-08-24 03:29:40', '2026-08-24 03:29:40', NULL),
(4, 2, 2, 'Berapakah 3 + 3?', '5', '6', '7', '8', 'B', '2026-08-24 03:29:40', '2026-08-24 03:29:40', NULL),
(5, 9, 1, 'hasil dari 99 x 99 adalah', '1801', '1808', '1811', '1818', 'A', '2026-08-27 04:13:50', '2026-08-27 04:13:50', NULL),
(6, 9, 2, 'hasil dari 5 x 5 adalah', '30', '25', '20', '35', 'B', '2026-08-27 04:13:50', '2026-08-27 04:13:50', NULL),
(7, 12, 1, 'hasil dari 1 + 1 adalah', '2', '4', '6', '8', 'A', '2026-08-27 04:26:01', '2026-08-27 04:26:01', NULL),
(8, 14, 1, 'apa arti dari \"what\"', '1', '2', '3', '4', 'C', '2026-08-27 07:19:05', '2026-08-27 07:19:05', NULL),
(9, 20, 1, 'Nama ibukota Riau adalah...', 'Pekanbaru', 'Panam', 'Pontianak', 'Palangkaraya', 'A', '2026-08-28 09:02:55', '2026-08-28 09:02:55', NULL),
(10, 20, 2, 'Hasil dari gabungan warna biru dan merah adalah warna...', 'Hijau', 'Ungu', 'Kuning', 'Oranye', 'B', '2026-08-28 09:02:55', '2026-08-28 09:02:55', NULL),
(11, 22, 1, 'Pancasila sebagai dasar negara Indonesia secara resmi disahkan pada tanggal...', '1 Juni 1945', '22 Juni 1945', '17 Agustus 1945', '18 Agustus 1945', 'D', '2026-08-31 08:55:06', '2026-08-31 08:55:06', NULL),
(12, 22, 2, 'Pengakuan hak asasi manusia di Indonesia secara tegas diatur dalam UUD 1945 pasal...', '27 sampai 34', '28A sampai 28J', '29 ayat 1 dan 2', '30 ayat 1 sampai 5', 'B', '2026-08-31 08:55:06', '2026-08-31 08:55:06', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `test`
--

CREATE TABLE `test` (
  `id_test` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `judul_test` varchar(255) NOT NULL,
  `kode_test` varchar(100) NOT NULL,
  `tanggal_dibuat` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `test`
--

INSERT INTO `test` (`id_test`, `id_user`, `judul_test`, `kode_test`, `tanggal_dibuat`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Test Matematika', 'WZGBRK', '2026-08-24', '2026-08-23 22:46:02', '2026-08-23 22:46:02', NULL),
(2, 1, 'Test Integrasi FloTest', 'YIPN0N', '2026-08-24', '2026-08-24 03:29:40', '2026-08-24 03:29:40', NULL),
(3, 1, 'ujian pengetahuan umum', 'TEST-44B9EA', '2026-08-26', '2026-08-26 14:34:54', '2026-08-26 14:34:54', NULL),
(4, 1, 'ujian pengetahuan umum', 'TEST-4892E3', '2026-08-26', '2026-08-26 14:35:12', '2026-08-26 14:35:12', NULL),
(5, 1, 'Test Matematika', 'TEST-05DC38', '2026-08-26', '2026-08-26 14:40:03', '2026-08-26 14:40:03', NULL),
(6, 1, 'ujian pengetahuan umum', 'TEST-A1EECE', '2026-08-26', '2026-08-26 14:42:57', '2026-08-26 14:42:57', NULL),
(7, 1, 'ujian pengetahuan umum', 'TEST-AF8EEE', '2026-08-27', '2026-08-27 02:56:51', '2026-08-27 02:56:51', NULL),
(8, 1, 'ujian pengetahuan umum', '840', '2026-08-27', '2026-08-27 03:00:55', '2026-08-27 03:00:55', NULL),
(9, 1, 'Tes Matematika', '277', '2026-08-27', '2026-08-27 04:11:55', '2026-08-27 04:11:55', NULL),
(10, 1, 'Test Matematika', '444', '2026-08-27', '2026-08-27 04:15:09', '2026-08-27 04:15:09', NULL),
(11, 1, 'ujian pengetahuan umum', '865', '2026-08-27', '2026-08-27 04:21:05', '2026-08-27 04:21:05', NULL),
(12, 1, 'Test Matematika', '958', '2026-08-27', '2026-08-27 04:25:31', '2026-08-27 04:25:31', NULL),
(13, 1, 'ujian pengetahuan umum', '594', '2026-08-27', '2026-08-27 06:16:38', '2026-08-27 06:16:38', NULL),
(14, 1, 'Bahasa Inggris', '587', '2026-08-27', '2026-08-27 07:18:28', '2026-08-27 07:18:28', NULL),
(15, 1, 'Bahasa Inggris', '498', '2026-08-27', '2026-08-27 07:43:48', '2026-08-27 07:43:48', NULL),
(16, 1, 'Bahasa Inggris', '462', '2026-08-27', '2026-08-27 08:15:38', '2026-08-27 08:15:38', NULL),
(17, 2, 'Test Matematika', '852', '2026-08-27', '2026-08-27 08:18:48', '2026-08-27 08:18:48', NULL),
(19, 1, 'Test Pengetahuan Umum', '193', '2026-08-28', '2026-08-28 08:42:21', '2026-08-28 08:42:21', NULL),
(20, 1, 'Test Pengetahuan Umum', '667', '2026-08-28', '2026-08-28 09:00:52', '2026-08-28 09:00:52', NULL),
(21, 1, 'Test Pengetahuan Umum', '936', '2026-08-31', '2026-08-31 08:24:45', '2026-08-31 08:24:45', NULL),
(22, 1, 'Test PKN', '872', '2026-08-31', '2026-08-31 08:52:53', '2026-08-31 08:52:53', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Azzkia', '$2y$10$0dSMm2x9fqRT2m4qNoylIOnXlF8yIJiFI9/8A9OcI1udTgMrvvHjm', '2026-08-23 21:38:33', '2026-08-28 08:21:28', NULL),
(2, 'Fuji', '$2y$10$4AbQ5ObK.lCOSwKiqUwR4.HmoqZrqnJ/KGm5S2/pDngdiLyxv6hRm', '2026-08-27 06:39:54', '2026-08-28 08:25:07', NULL),
(3, 'Vira', '0697', '2026-08-27 06:41:42', '2026-08-27 06:41:42', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `detail_riwayat`
--
ALTER TABLE `detail_riwayat`
  ADD PRIMARY KEY (`id_detail_riwayat`),
  ADD KEY `id_riwayat_tes` (`id_riwayat_tes`),
  ADD KEY `id_soal` (`id_soal`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indeks untuk tabel `riwayat_tes`
--
ALTER TABLE `riwayat_tes`
  ADD PRIMARY KEY (`id_riwayat_tes`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_test` (`id_test`);

--
-- Indeks untuk tabel `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id_soal`),
  ADD KEY `id_test` (`id_test`);

--
-- Indeks untuk tabel `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id_test`),
  ADD UNIQUE KEY `kode_test` (`kode_test`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_riwayat`
--
ALTER TABLE `detail_riwayat`
  MODIFY `id_detail_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `riwayat_tes`
--
ALTER TABLE `riwayat_tes`
  MODIFY `id_riwayat_tes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `soal`
--
ALTER TABLE `soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `test`
--
ALTER TABLE `test`
  MODIFY `id_test` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_riwayat`
--
ALTER TABLE `detail_riwayat`
  ADD CONSTRAINT `detail_riwayat_ibfk_1` FOREIGN KEY (`id_riwayat_tes`) REFERENCES `riwayat_tes` (`id_riwayat_tes`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_riwayat_ibfk_2` FOREIGN KEY (`id_soal`) REFERENCES `soal` (`id_soal`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `riwayat_tes`
--
ALTER TABLE `riwayat_tes`
  ADD CONSTRAINT `riwayat_tes_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `riwayat_tes_ibfk_2` FOREIGN KEY (`id_test`) REFERENCES `test` (`id_test`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `soal`
--
ALTER TABLE `soal`
  ADD CONSTRAINT `soal_ibfk_1` FOREIGN KEY (`id_test`) REFERENCES `test` (`id_test`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
