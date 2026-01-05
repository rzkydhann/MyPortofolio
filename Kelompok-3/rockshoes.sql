-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 11 Jun 2025 pada 22.02
-- Versi server: 10.6.21-MariaDB-cll-lve
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rockshoes`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('admin','super_admin') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `nama`, `username`, `password`, `level`, `created_at`) VALUES
(1, 'Super Administrator', 'admin', 'admin123', 'super_admin', '2025-06-01 07:54:12'),
(2, 'Administrator', 'administrator', '123', 'admin', '2025-06-01 07:54:12'),
(3, 'Manager', 'manager', '123', 'admin', '2025-06-01 07:54:12'),
(4, 'admin2', 'admin2', '123', 'admin', '2025-06-02 04:02:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orderperbaikan`
--

CREATE TABLE `orderperbaikan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `hp` varchar(16) NOT NULL,
  `layananperbaikan` varchar(100) NOT NULL,
  `merk` varchar(100) NOT NULL,
  `jenisperbaikan` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` varchar(8) NOT NULL,
  `alamat` varchar(300) NOT NULL,
  `status` varchar(100) NOT NULL,
  `teknisi` varchar(100) NOT NULL,
  `catatan_admin` text DEFAULT NULL,
  `biaya` int(11) DEFAULT 0,
  `pembayaran` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orderperbaikan`
--

INSERT INTO `orderperbaikan` (`id`, `nama`, `username`, `hp`, `layananperbaikan`, `merk`, `jenisperbaikan`, `tanggal`, `waktu`, `alamat`, `status`, `teknisi`, `catatan_admin`, `biaya`, `pembayaran`) VALUES
(38, 'Budianto Raharjo', 'Budi10', '081123123123', 'Deep Cleaning', 'New Balance', 'Cat Kusam', '2025-06-23', '11:27', 'JL. Gunung Anyar No 12, Surabaya, Jawa Timur', 'Complete', 'Doni', 'Terima Kasih', 50000, 'Transfer'),
(39, 'Budianto Raharjo', 'Budi10', '081123123123', 'Hard Cleaning', 'New Balance', 'Cat Kusam', '2025-06-23', '09:43', 'JL. Gunung Anyar No 12, Surabaya, Jawa Timur', 'Pembayaranmu Terkonfirmasi', '', NULL, 60000, 'COD'),
(40, 'Budianto Raharjo', 'Budi10', '081123123123', 'Reglue', 'Nike', 'Sobek', '2025-06-12', '10:31', 'JL. Gunung Anyar No 12, Surabaya, Jawa Timur', 'Menunggu Teknisi', '', NULL, 45000, 'Transfer'),
(41, 'Adreian Alexander', 'ian10', '081234241111', 'Reglue', 'Airwalk', 'Sobek sedikit', '2025-06-11', '09:07', 'rungkut tengah no 10, surabaya', 'Menunggu Teknisi', '', NULL, 45000, 'COD'),
(42, 'rizky', 'rizky', '0853774499874', 'Deep Cleaning', 'NIKE', 'cuci', '2025-06-10', '11:09', 'sidoarjo', 'Sepatu Akan Segera Dijemput', 'budi', 'otw mas', 50000, 'E-Wallet'),
(43, 'Zahrotulll', '23081010085_zahrotul', '0858-5656-5199', 'Deep Cleaning', 'Ortuseight', 'Dry Cleaning', '2025-06-10', '08:00', 'surabaya', 'Menunggu Teknisi', '', NULL, 50000, 'COD'),
(44, 'Budianto Raharjo', 'Budi10', '081123123123', 'Reglue', 'NewBalance', 'Kotor', '2025-06-12', '09:01', 'JL. Gunung Anyar No 12, Surabaya, Jawa Timur', 'Menunggu Teknisi', '', NULL, 45000, 'E-Wallet');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `hp` varchar(16) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(225) NOT NULL,
  `alamat` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `username`, `hp`, `email`, `password`, `alamat`) VALUES
(1, 'Budianto Raharjo', 'Budi10', '081123123123', 'budianto@gmail.com', '$2y$10$KKfDOpl60.4ksbPZlMWxauiRIxJZVrm.OJ/vW9BsIokiL0ee9hZIy', 'JL. Gunung Anyar No 12, Surabaya, Jawa Timur'),
(3, 'Adreian Alexander', 'ian10', '081234241111', 'adreian@gmail.com', '$2y$10$xm4pptYW3indicn5W1y08e76cwDyPbBtE6ZlPsFL4Cq8bDQmEntFy', 'rungkut tengah no 10, surabaya'),
(4, 'rizkydhn', 'rizky', '0853774499874', 'rizky@gmail.com', '$2y$10$NjiBBHsnGLobyNUJ4zhBwexJ6VcIzaDNlMIDyyJkLkmGywasJOtgW', 'sidoarjo'),
(5, 'Zahrotulll', '23081010085_zahrotul', '0858-5656-5199', '23081010085@student.upnjatim.ac.id', '$2y$10$TxyOOvAbDQgK17xa0.KUjOhHKHH6p8B3WRXtGVWoxkIMd8.3iuCB6', 'surabaya');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `orderperbaikan`
--
ALTER TABLE `orderperbaikan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `orderperbaikan`
--
ALTER TABLE `orderperbaikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
