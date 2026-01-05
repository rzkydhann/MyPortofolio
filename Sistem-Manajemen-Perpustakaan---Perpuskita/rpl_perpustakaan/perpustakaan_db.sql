-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2025 at 08:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `nama`, `alamat`, `no_telp`) VALUES
(1, 'Rizky Ramadhan', 'Jl. Merdeka No. 10', '081234567890'),
(2, 'Sari Melati', 'Jl. Kenanga Indah 5', '085678901234'),
(5, 'Budi Santoso', 'Jl. Merdeka No. 1, Surabaya', '08123456701'),
(6, 'Citra Dewi', 'Jl. Kenanga Indah No. 2, Malang', '08567890102'),
(7, 'Denny Wijaya', 'Perumahan Griya Asri C-3, Sidoarjo', '08781234503'),
(8, 'Eka Putra', 'Jl. Pahlawan No. 4, Gresik', '08212345604'),
(9, 'Fitriani', 'Komplek Permata Biru Blok D-5, Kediri', '08134567805'),
(10, 'Guntur Alam', 'Jl. Mawar No. 6, Blitar', '08578901206'),
(11, 'Hendra Kusuma', 'Perumahan Indah Permai E-7, Pasuruan', '08962345607'),
(12, 'Indah Permata', 'Jl. Melati No. 8, Mojokerto', '08156789008'),
(13, 'Joko Susilo', 'Gg. Kucing No. 9, Probolinggo', '08190123409'),
(14, 'Kartika Sari', 'Jl. Raya Utama No. 10, Banyuwangi', '08123456710'),
(15, 'Lestari Putri', 'Dusun Damai RT01 RW02, Jember', '08567890111'),
(16, 'Maria Angelina', 'Desa Sejahtera No. 12, Lumajang', '08781234512'),
(17, 'Naufal Rizky', 'Jl. Cempaka No. 13, Situbondo', '08212345613'),
(18, 'Olivia Wijaya', 'Blok Cempaka Wangi 14, Bondowoso', '08134567814'),
(19, 'Putra Bahari', 'Jl. Anggrek No. 15, Sumenep', '08578901215'),
(20, 'Qori Aini', 'Perkampungan Asri G-16, Pamekasan', '08962345616'),
(21, 'Rahmat Hidayat', 'Jl. Raya Barat No. 17, Sampang', '08156789017'),
(22, 'Siska Amelia', 'Dusun Timur RT03 RW04, Bangkalan', '08190123418'),
(23, 'Taufik Rahman', 'Jl. Pelajar No. 19, Tuban', '08123456719'),
(24, 'Umar Said', 'Komplek Cendana Blok J-20, Lamongan', '08567890120'),
(25, 'Vina Lestari', 'Jl. Merak No. 21, Bojonegoro', '08781234521'),
(26, 'Wayan Surya', 'Perumahan Griya Damai K-22, Ngawi', '08212345622'),
(27, 'Xavier Pratama', 'Jl. Kenari No. 23, Madiun', '08134567823'),
(28, 'Yuni Kartika', 'Desa Makmur L-24, Magetan', '08578901224'),
(29, 'Zaki Mubarak', 'Jl. Teratai No. 25, Ponorogo', '08962345625'),
(30, 'Alfa Romeo', 'Komplek Bunga No. 26, Pacitan', '08156789026'),
(31, 'Beta Gamma', 'Jl. Manggis No. 27, Trenggalek', '08190123427'),
(32, 'Charlie Delta', 'Perumahan Sejahtera M-28, Tulungagung', '08123456728'),
(33, 'Echo Fox', 'Jl. Cempedak No. 29, Blitar', '08567890129'),
(34, 'Golf Hotel', 'Desa Permai N-30, Malang', '08781234530'),
(35, 'India Juliet', 'Jl. Durian No. 31, Surabaya', '08212345631'),
(36, 'Kilo Lima', 'Komplek Elite O-32, Sidoarjo', '08134567832'),
(37, 'Mike November', 'Jl. Rambutan No. 33, Gresik', '08578901233'),
(38, 'Oscar Papa', 'Perumahan Harmoni P-34, Kediri', '08962345634'),
(39, 'Quebec Romeo', 'Jl. Sawo No. 35, Pasuruan', '08156789035'),
(40, 'Sierra Tango', 'Desa Damai Q-36, Mojokerto', '08190123436'),
(41, 'Uniform Victor', 'Jl. Nangka No. 37, Probolinggo', '08123456737'),
(42, 'Whiskey Xray', 'Komplek Asri R-38, Banyuwangi', '08567890138'),
(43, 'Yankee Zulu', 'Jl. Alpukat No. 39, Jember', '08781234539'),
(44, 'Anna Luthfi', 'Perumahan Puri Indah S-40, Lumajang', '08212345640'),
(45, 'Benny Susanto', 'Jl. Salak No. 41, Situbondo', '08134567841'),
(46, 'Clara Jessica', 'Desa Makmur T-42, Bondowoso', '08578901242'),
(47, 'Doni Saputra', 'Jl. Mangga No. 43, Sumenep', '08962345643'),
(48, 'Eva Susanti', 'Komplek Delima U-44, Pamekasan', '08156789044'),
(49, 'Fauzan Akbar', 'Jl. Jambu No. 45, Sampang', '08190123445'),
(50, 'Gita Cahaya', 'Desa Sentosa V-46, Bangkalan', '08123456746'),
(51, 'Harry Wijoyo', 'Jl. Jeruk No. 47, Tuban', '08567890147'),
(52, 'Imam Budiman', 'Perumahan Cendekia W-48, Lamongan', '08781234548');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL,
  `judul_buku` varchar(255) NOT NULL,
  `pengarang` varchar(100) DEFAULT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `jumlah_halaman` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `judul_buku`, `pengarang`, `penerbit`, `tahun_terbit`, `jumlah_halaman`) VALUES
(1, 'The Hobbit', 'J.R.R. Tolkien', 'Gramedia Pustaka Utama', '1937', 310),
(2, 'Pemrograman Web dengan PHP dan MySQL', 'Andi Hermawan', 'Penerbit Informatika', '2022', 450),
(3, 'Dasar-Dasar Aljabar', 'Dr. Matematika', 'Erlangga', '2010', 200),
(5, 'Pemrograman Web dengan PHP dan MySQL', 'Andi Hermawan', 'Penerbit Informatika', '2022', 450),
(6, 'Dasar-Dasar Aljabar', 'Dr. Matematika', 'Erlangga', '2010', 200),
(7, 'Dasar Pemrograman Python', 'Andi Nugraha', 'Gramedia', '2020', 350),
(8, 'Algoritma dan Struktur Data', 'Budi Santoso', 'Erlangga', '2018', 420),
(9, 'Belajar Web Development dengan PHP', 'Citra Dewi', 'Informatika', '2021', 500),
(10, 'Database Design with SQL', 'Denny Wijaya', 'Pustaka Jaya', '2019', 280),
(11, 'Jaringan Komputer Fundamental', 'Eka Putra', 'Mitra Media', '2017', 310),
(12, 'Matematika Diskrit untuk Ilmu Komputer', 'Fitriani', 'Cerdas', '2016', 400),
(13, 'Pengantar Kecerdasan Buatan', 'Guntur Alam', 'Teknosain', '2022', 550),
(14, 'Manajemen Proyek IT', 'Hendra Kusuma', 'Kencana', '2019', 320),
(15, 'Sistem Operasi Modern', 'Indah Permata', 'Deepublish', '2020', 480),
(16, 'Keamanan Siber untuk Pemula', 'Joko Susilo', 'Andi Offset', '2023', 380),
(17, 'Fisika Dasar I', 'Kartika Sari', 'Universitas Press', '2015', 600),
(18, 'Kimia Umum', 'Lestari Putri', 'Adipura', '2014', 580),
(19, 'Biologi Molekuler', 'Maria Angelina', 'Medica', '2016', 620),
(20, 'Sejarah Dunia Kuno', 'Naufal Rizky', 'Serambi Ilmu', '2018', 450),
(21, 'Ekonomi Makro', 'Olivia Wijaya', 'FE UI Press', '2021', 390),
(22, 'Sosiologi Pedesaan', 'Putra Bahari', 'Sinar Harapan', '2017', 300),
(23, 'Psikologi Perkembangan', 'Qori Aini', 'Psikologi Press', '2019', 340),
(24, 'Pengantar Akuntansi', 'Rahmat Hidayat', 'Salemba Empat', '2020', 410),
(25, 'Manajemen Keuangan', 'Siska Amelia', 'Penerbit Mitra', '2022', 470),
(26, 'Pemasaran Digital', 'Taufik Rahman', 'Media Kita', '2023', 360),
(27, 'Hukum Perdata', 'Umar Said', 'Ghalia Indonesia', '2018', 520),
(28, 'Statistika Terapan', 'Vina Lestari', 'Graha Ilmu', '2021', 430),
(29, 'Geografi Regional', 'Wayan Surya', 'Bumi Aksara', '2017', 330),
(30, 'Desain Grafis untuk Pemula', 'Xavier Pratama', 'Elex Media', '2020', 290),
(31, 'Animasi 3D Fundamental', 'Yuni Kartika', 'Kompas', '2022', 490),
(32, 'Fotografi Digital Lanjutan', 'Zaki Mubarak', 'Penerbit Falcon', '2019', 370),
(33, 'Dasar Jaringan Komputer', 'Alfa Romeo', 'Informatika', '2020', 310),
(34, 'Machine Learning: Konsep dan Aplikasi', 'Beta Gamma', 'Teknik Press', '2023', 650),
(35, 'Pengembangan Aplikasi Mobile dengan Kotlin', 'Charlie Delta', 'Media Komputindo', '2022', 450),
(36, 'Data Science for Business', 'Echo Fox', 'Business Books', '2021', 480),
(37, 'Robotika Industri', 'Golf Hotel', 'Penerbit Industri', '2018', 400),
(38, 'Pengantar Kecerdasan Komputasional', 'India Juliet', 'Sains Tech', '2019', 530),
(39, 'Blockchain Technology Explained', 'Kilo Lima', 'Innovatech', '2023', 390),
(40, 'Augmented Reality & Virtual Reality', 'Mike November', 'Creative Media', '2022', 350),
(41, 'Cyber Forensics Guide', 'Oscar Papa', 'Security Books', '2021', 420),
(42, 'Cloud Computing Architectures', 'Quebec Romeo', 'Tech Publications', '2020', 460),
(43, 'Big Data Analytics', 'Sierra Tango', 'Data Insight', '2019', 510),
(44, 'Internet of Things (IoT) Handbook', 'Uniform Victor', 'Digital Media', '2023', 370),
(45, 'Quantum Computing Principles', 'Whiskey Xray', 'Future Tech', '2022', 600),
(46, 'Etika Komputer', 'Yankee Zulu', 'Humaniora', '2018', 250),
(47, 'Sejarah Indonesia Modern', 'Agus Salim', 'Pustaka Rakyat', '2015', 380),
(48, 'Pendidikan Kewarganegaraan', 'Bunga Melati', 'Penerbit Edukasi', '2017', 290),
(49, 'Seni Rupa Indonesia', 'Candra Kirana', 'Penerbit Seni', '2016', 220),
(50, 'Arsitektur Tradisional', 'Dini Astuti', 'Desain Buku', '2019', 330),
(51, 'Kuliner Nusantara', 'Endang Suci', 'Makan Enak', '2020', 200),
(52, 'Batik: Warisan Budaya Indonesia', 'Fajar Gemilang', 'Budaya Press', '2021', 180),
(53, 'Musik Tradisional Jawa', 'Gilang Ramadhan', 'Seni Budaya', '2018', 270),
(54, 'Teater Kontemporer', 'Hasna Nurul', 'Panggung Seni', '2022', 310),
(55, 'Cerita Rakyat Nusantara', 'Intan Permata', 'Dongeng Indah', '2017', 240),
(56, 'Puisi Modern Indonesia', 'Jaka Samudra', 'Sastra Indonesia', '2019', 150),
(57, 'Novel Romantis Terbaik', 'Kania Dewi', 'Pustaka Fiksi', '2020', 360),
(58, 'Misteri Rumah Tua', 'Lukman Hakim', 'Horor Books', '2021', 290),
(59, 'Petualangan di Pulau Terpencil', 'Maya Sari', 'Anak Hebat', '2018', 210),
(60, 'Ensiklopedia Hewan Langka', 'Nia Kurnia', 'Fauna Dunia', '2022', 400),
(61, 'Tumbuhan Obat Indonesia', 'Oscar Setiawan', 'Herbal Nusantara', '2019', 320),
(62, 'Burung Endemik Indonesia', 'Putri Ayu', 'Ornithology', '2023', 280),
(63, 'Resep Masakan Rumahan', 'Rina Wati', 'Dapurku', '2020', 250),
(64, 'Kue Tradisional', 'Santi Dewi', 'Buku Resep', '2021', 190),
(65, 'Minuman Segar', 'Tony Wijaya', 'Minuman Sehat', '2018', 160),
(66, 'Kerajinan Tangan Kreatif', 'Umi Hani', 'Ide Kreatif', '2017', 230),
(67, 'Membuat Taman Minimalis', 'Vivi Rahayu', 'Berkebun', '2019', 270),
(68, 'Panduan Merawat Kucing', 'Wawan Gunawan', 'Hewan Peliharaan', '2020', 180),
(69, 'Melatih Anjing Pintar', 'Xena Kusuma', 'Dog Lover', '2022', 200),
(70, 'Ikan Hias Air Tawar', 'Yoga Pratama', 'Akuatik', '2021', 260),
(71, 'Budidaya Tanaman Hidroponik', 'Zahra Indah', 'Green Farm', '2023', 300),
(72, 'Teknik Menulis Kreatif', 'Anna Luthfi', 'Penerbit Karya', '2017', 240),
(73, 'Belajar Bahasa Inggris Cepat', 'Benny Susanto', 'Edukasi Bahasa', '2019', 280),
(74, 'Panduan Public Speaking', 'Clara Jessica', 'Komunikasi Efektif', '2020', 310),
(75, 'Matematika Finansial', 'Doni Saputra', 'Investasi Cerdas', '2021', 390),
(76, 'Ekonometrika Dasar', 'Eva Susanti', 'Statistika Ekonomi', '2018', 420),
(77, 'Pengantar Hukum Internasional', 'Fauzan Akbar', 'Hukum Global', '2017', 350),
(78, 'Etika Profesi', 'Gita Cahaya', 'Moralitas', '2019', 260),
(79, 'Psikologi Industri dan Organisasi', 'Harry Wijoyo', 'SDM Unggul', '2020', 410),
(80, 'Manajemen Strategi', 'Imam Budiman', 'Bisnis Modern', '2022', 480),
(81, 'Operasi Produksi', 'Jeni Susanti', 'Manufaktur', '2021', 330),
(82, 'Sistem Informasi Manajemen', 'Kevin Pratama', 'Teknologi Bisnis', '2018', 440),
(83, 'Analisis Data dengan R', 'Linda Anggraini', 'Data Analytics', '2023', 500),
(84, 'Visualisasi Data Interaktif', 'Marta Indah', 'Desain Data', '2022', 370),
(85, 'Web Desain Responsif', 'Nina Sari', 'UI/UX Desain', '2020', 300),
(86, 'Pengembangan Game 2D', 'Oliver Gunawan', 'Game Dev', '2021', 320),
(87, 'Desain UI/UX Fundamental', 'Putri Diana', 'User Experience', '2019', 290),
(88, 'Pengantar Komputasi Awan', 'Rudi Haryanto', 'Cloud Services', '2023', 400),
(89, 'DevOps Praktis', 'Siti Fatimah', 'Automation', '2022', 350),
(90, 'Cyber Security Essentials', 'Toni Akbar', 'Network Security', '2021', 410),
(91, 'Data Mining Concepts', 'Uli Lestari', 'Knowledge Discovery', '2020', 450),
(92, 'Artificial Intelligence', 'Vina Sari', 'AI Basics', '2019', 580),
(93, 'Computer Vision', 'Willy Wijaya', 'Image Processing', '2023', 520),
(94, 'Natural Language Processing', 'Xanti Clara', 'NLP Funda', '2022', 470),
(95, 'Embedded Systems', 'Yanto Adi', 'Mikrokontroler', '2021', 300),
(96, 'Dasar Elektronika', 'Zulkifli Hasan', 'Sirkuit Elektronik', '2020', 340),
(97, 'Mikroekonomi Terapan', 'Amelia Putri', 'Ekonomi Tingkat Lanjut', '2018', 380),
(98, 'Makroekonomi Lanjutan', 'Bima Sakti', 'Teori Ekonomi', '2019', 420),
(99, 'Ekonomi Pembangunan', 'Cahaya Ilahi', 'Negara Berkembang', '2020', 300),
(100, 'Ekonomi Lingkungan', 'Dini Lestari', 'Sustainable Econ', '2021', 280),
(101, 'Kebijakan Publik', 'Edo Saputra', 'Pemerintahan', '2017', 330);

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tipe_aktivitas` varchar(50) NOT NULL,
  `deskripsi_aktivitas` text NOT NULL,
  `referensi_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`log_id`, `user_id`, `tipe_aktivitas`, `deskripsi_aktivitas`, `referensi_id`, `timestamp`) VALUES
(1, NULL, 'Login', 'User Dwi Annisa berhasil login.', NULL, '2025-06-19 03:49:58'),
(2, NULL, 'Login', 'User Dwi Annisa berhasil login.', NULL, '2025-06-19 04:30:27'),
(3, 2506001, 'Login', 'Petugas dwi_annisa berhasil login.', NULL, '2025-06-19 04:35:24'),
(4, 2506001, 'Logout', 'Petugas dwi_annisa berhasil logout.', NULL, '2025-06-19 04:37:08'),
(5, 2506001, 'Login', 'Petugas dwi_annisa berhasil login.', NULL, '2025-06-19 04:37:16'),
(6, 2506001, 'Hapus Buku', 'Buku \"The Hobbit\" (ID: #0004) telah dihapus.', 4, '2025-06-19 04:40:46'),
(7, 2506001, 'Hapus Anggota', 'Anggota \"Rizky Ramadhan\" (ID: #0003) telah dihapus.', 3, '2025-06-19 04:47:23'),
(8, 2506001, 'Hapus Anggota', 'Anggota \"Sari Melati\" (ID: #0004) telah dihapus.', 4, '2025-06-19 04:47:34'),
(13, 2506001, 'Login', 'Petugas dwi_annisa (ID: 2506001) berhasil login.', NULL, '2025-06-19 05:07:49'),
(14, 2506001, 'Peminjaman', 'Peminjaman baru dicatat. Buku ID: 0002, Anggota ID: 0001', 3, '2025-06-19 05:51:07'),
(15, 2506001, 'Login', 'Petugas dwi_annisa (ID: 2506001) berhasil login.', NULL, '2025-06-19 05:54:05'),
(16, 2506001, 'Pengembalian', 'Buku dikembalikan. ID Peminjaman: 3', 3, '2025-06-19 05:58:44'),
(17, 2506001, 'Hapus Buku', 'Buku \"Hukum Bisnis\" (ID: #0105) telah dihapus.', 105, '2025-06-19 06:04:21'),
(18, 2506001, 'Hapus Buku', 'Buku \"Hukum Pidana\" (ID: #0104) telah dihapus.', 104, '2025-06-19 06:04:26'),
(19, 2506001, 'Hapus Buku', 'Buku \"Hukum Tata Negara\" (ID: #0103) telah dihapus.', 103, '2025-06-19 06:04:30'),
(20, 2506001, 'Hapus Buku', 'Buku \"Administrasi Negara\" (ID: #0102) telah dihapus.', 102, '2025-06-19 06:04:33'),
(21, 2506001, 'Hapus Anggota', 'Anggota \"Kevin Pratama\" (ID: #0054) telah dihapus.', 54, '2025-06-19 06:05:48'),
(22, 2506001, 'Hapus Anggota', 'Anggota \"Jeni Susanti\" (ID: #0053) telah dihapus.', 53, '2025-06-19 06:05:52'),
(23, 2506001, 'Peminjaman', 'Peminjaman baru dicatat. Buku ID: 0010, Anggota ID: 0005', 4, '2025-06-19 06:07:07'),
(24, 2506001, 'Login', 'Petugas dwi_annisa (ID: 2506001) berhasil login.', NULL, '2025-06-19 06:28:34'),
(25, 2506001, 'Login', 'Petugas dwi_annisa (ID: 2506001) berhasil login.', NULL, '2025-06-19 06:32:21'),
(26, 2506001, 'Login', 'Petugas dwi_annisa (ID: 2506001) berhasil login.', NULL, '2025-06-19 06:34:31'),
(27, 2506001, 'Login', 'Petugas dwi_annisa (ID: 2506001) berhasil login.', NULL, '2025-06-19 06:35:07'),
(28, 2506001, 'Login', 'Petugas dwi_annisa (ID: 2506001) berhasil login.', NULL, '2025-06-19 06:37:11');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  `tgl_pinjam` date NOT NULL,
  `tgl_kembali` date NOT NULL,
  `tgl_dikembalikan` date DEFAULT NULL,
  `status` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_buku`, `id_anggota`, `id_petugas`, `tgl_pinjam`, `tgl_kembali`, `tgl_dikembalikan`, `status`) VALUES
(3, 2, 1, 2506001, '2025-06-19', '2025-06-26', '2025-06-19', 'Dikembalikan'),
(4, 10, 5, 2506001, '2025-06-19', '2025-06-26', NULL, 'Dipinjam');

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int(11) NOT NULL,
  `nama_petugas` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Administrator','Petugas') DEFAULT 'Petugas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `nama_petugas`, `password`, `role`) VALUES
(2506001, 'dwi_annisa', '$2y$10$DFsWN4a/vAEmNp1zWvywHudcdq7RiYPCh1/AVV69rKH7vvV2ZEUtm', 'Administrator');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_buku` (`id_buku`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indexes for table `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`),
  ADD UNIQUE KEY `username` (`nama_petugas`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `petugas` (`id_petugas`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_3` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
