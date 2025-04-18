-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20250222.063555083e
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 15, 2025 at 05:00 PM
-- Server version: 5.7.33
-- PHP Version: 8.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_tesis_disertasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id_dosen` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `bidang_keahlian` varchar(100) DEFAULT NULL,
  `max` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id_dosen`, `nama`, `nidn`, `email`, `telepon`, `bidang_keahlian`, `max`) VALUES
(1, 'Prof. Dr. H. Babun Suharto, S.E., M.M.', '2022036601', NULL, NULL, 'Ilmu Manajemen', 30),
(2, 'Dr. Abdul Wadud Nafis, Lc., MEI.', '2006076901', NULL, NULL, 'Makro Ekonomi Islam', 30),
(3, 'Prof. Dr. Moch. Chotib, S.Ag., M.M.', '2027077102', NULL, NULL, 'Manajemen Pemasaran', 30),
(4, 'Dr. Khairunnisa Musari, S.T.,M.MT.', '503107803', NULL, NULL, 'Ilmu Ekonomi', 30),
(5, 'Dr. Misbahul Munir, M.M.', '2025125401', NULL, NULL, 'Manajemen', 30),
(6, 'Dr. Hersa Farida Qoriani, S.Kom., M.E.I.', '729118601', NULL, NULL, 'Manajemen Keuangan Bank Syari`ah', 30),
(7, 'Dr. Ishaq, M.Ag.', '2013027101', NULL, NULL, 'Qowaid Al-Fiqhiyah', 30),
(8, 'Dr. Abdullah, S.Ag., M.H.I .', '2003027603', NULL, NULL, 'Ulumul Qur\'an', 30),
(9, 'Dr. Hamam, M.H.I.', '2005056005', NULL, NULL, 'Fiqh', 30),
(10, 'Dr. Wildani Hefni, S.H.I., M.A.', '2107119101', NULL, NULL, 'Pemikiran Islam', 30),
(11, 'Dr. Sri Lumatus Sa\'adah, S.Ag., M.H.I.', '2008107401', NULL, NULL, 'Hukum Perdata Islam', 30),
(12, 'Dr. Kun Wazis, S.Sos., M.I.Kom.', '2003107404', NULL, NULL, 'Komunikasi Massa', 30),
(13, 'Prof. Dr. Hepni, S.Ag., M.M.', '2003026903', NULL, NULL, 'Metodologi Penelitian', 30),
(14, 'Dr. Siti Raudhatul Jannah, S.Ag, M.Med.Kom.', '2015077205', NULL, NULL, 'Komunikasi Jurnalistik', 30),
(15, 'Dr. Minan Jauhari, S.Sos.I. M.Si.', '2010087802', NULL, NULL, 'Public Relation', 30),
(16, 'Dr. Win Usuluddin, M.Hum.', '2018017001', NULL, NULL, 'Filsafat', 30),
(17, 'Dr. Zainuddin Al Haj Zaini, Lc., M.Pd.I.', '2020037404', NULL, NULL, 'Bahasa Arab', 30),
(18, 'Dr. Ach Faridul  Ilmi, M.Ag.', '2006086002', NULL, NULL, 'Manajemen Pendidikan', 30),
(19, 'Dr. Abdul Mu\'is, S.Ag., M.Si.', '2024047303', NULL, NULL, 'Kepemimpinan Pendidikan Islam', 30),
(20, 'Dr. Khotibul Umam, MA.', '2004067501', NULL, NULL, 'Manajemen Mutu Pendidikan', 30),
(21, 'Dr. Moh. Anwar, S.Pd., M.Pd.', '2025026802', NULL, NULL, 'Supervisi Pendidikan', 30),
(22, 'Dr. Gunawan, S.Pd.I., M.Pd.I.', '710088202', NULL, NULL, 'Manajemen Kurikulum', 30),
(23, 'Dr. Dyah Nawangsari, M.Ag.', '2012017302', NULL, NULL, 'Filsafat Pendidikan Islam', 30),
(24, 'Dr. Mustajab, M.Pd.I.', '2005097402', NULL, NULL, 'Ilmu Pendidikan Islam', 30),
(25, 'Dr. Saihan, S.Ag., M.Pd.I.', '2017027203', NULL, NULL, 'Isu-isu Kontemporer Pendidikan Umum dan Islam', 30),
(26, 'Dr. Moh. Sahlan, M.Ag.', '2011036301', NULL, NULL, 'Evaluasi Pendidikan', 30),
(27, 'Dr. Mas\'ud, S.Ag., M.Pd.I.', '2019127201', NULL, NULL, 'Akhlak Tasawuf', 30),
(28, 'Dr. Subakri, M.Pd.I.', '2121077501', NULL, NULL, 'Ilmu Pendidikan Islam', 30),
(29, 'Dr. Syamsul Anam, S.Ag., M.Pd.', '2021087103', NULL, NULL, 'Bahasa Arab', 30),
(30, 'Prof. Dr. Faisol Nasar Bin Madi, M.A.', '2002085801', NULL, NULL, 'Ilmu Kalam', 30),
(31, 'Dr. Bambang Irawan, M.Ed.', '2002057601', NULL, NULL, 'Bahasa Arab (Insya\')', 30),
(32, 'Dr. Maskud, S.Ag., M.Si.', '2010027402', NULL, NULL, 'Bahasa Arab', 30),
(33, 'Dr. Abdul Haris, M.Ag.', '2007017101', NULL, NULL, 'Qowaid', 30),
(34, 'Dr. Abdur Rosid, M.Pd.', '2002128501', NULL, NULL, 'Bahasa Arab', 30),
(35, 'Dr. ST. Mislikhah, M.Ag.', '2013066803', NULL, NULL, 'Bahasa Indonesia', 30),
(36, 'Dr. Abd. Muhith, S.Ag., M.Pd.I.', '2016107204', NULL, NULL, 'Manajemen Mutu Terpadu dalam Pendidikan', 30),
(37, 'Dr. Mu\'alimin, S.Ag., M.Pd.I.', '2004027505', NULL, NULL, 'Psikologi Pendidikan', 30),
(38, 'Dr. Erma Fatmawati, M.Pd.', '2026077101', NULL, NULL, 'Manajemen Pendidikan', 30),
(39, 'Dr. Moh. Sutomo, M.Pd.', '2015107102', NULL, NULL, 'Pengembangan Kurikulum Ilmu Pengetahuan Sosial', 30),
(40, 'Dr. Imron Fauzi, M.Pd.I.', '2022058701', NULL, NULL, 'Manajemen Kurikulum', 30),
(41, 'Dr. H. Mursalim, M.Ag', '2026037002', NULL, NULL, 'Ilmu Pendidikan', 30),
(42, 'Dr. Safrudin Edi Wibowo, Lc, M.Ag.', '2010037302', NULL, NULL, 'Tafsir Hadits', 30),
(43, 'Dr. Fawaizul Umam, S.Ag., M.Ag.', '2027027301', NULL, NULL, 'Filsafat Umum', 30),
(44, 'Dr. Rafid Abbas, M.A.', '2014056101', NULL, NULL, 'Ulumul Hadits', 30),
(45, 'Dr. Khoirul Faizin, M.Ag.', '2012067104', NULL, NULL, 'Sejarah Peradaban Islam', 30),
(46, 'Dr. H. Matkur, S.Pd.I., M.Si.', '2002068102', NULL, NULL, 'Sejarah Pendidikan Islam', 30),
(47, 'Prof. Dr. Abd. Mu\'is, M.M.', '9990309105', NULL, NULL, 'Ilmu Pendidikan', 30),
(48, 'Prof. Dr. Moh. Khusnuridlo, M.Pd.', '2020076503', NULL, NULL, 'Manajemen Pendidikan', 30),
(49, 'Dr. Ainur Rafik, M.Ag .', '2005056503', NULL, NULL, 'Ilmu Pendidikan Islam', 30),
(50, 'Dr. St. Rodliyah, M.Pd.', '2011096802', NULL, NULL, 'Ilmu Pendidikan', 30),
(51, 'Prof. Dr. Suhadi Winoto, M.Pd.', '2008125902', NULL, NULL, 'Administrasi dan Supervisi Pendidikan', 30),
(52, 'Prof.   Dr. Sofyan Tsauri, M.M.', '2011115801', NULL, NULL, 'Manajemen SDM', 30),
(53, 'Prof. Dr. Mundir, M.Pd.', '2003116301', NULL, NULL, 'Metodologi Penelitian', 30),
(54, 'Prof. Dr. Abd. Halim Soebahar, MA.', '2004016101', NULL, NULL, 'Ilmu Pendidikan Islam', 30),
(55, 'Prof. Dr. Mukni\'ah, M.Pd.I.', '2011056401', NULL, NULL, 'Perencanaan Pembelajaran', 30),
(56, 'Dr. Ubaidillah, M.Ag.', '2026126801', NULL, NULL, 'Ilmu  Tasawuf', 30),
(57, 'Prof. Dr. Mashudi, M.Pd.', '2103068801', NULL, NULL, 'Strategi Pembelajaran Pendidikan Agama Islam', 30),
(58, 'Dr. Sofyan Hadi, M.Pd.', '2014057505', NULL, NULL, 'Evaluasi Pembelajaran PAI', 30),
(59, 'Prof. Dr. Miftah Arifin, M.Ag.', '2003017501', NULL, NULL, 'Sejarah Peradaban Islam', 30),
(60, 'Prof. Dr. Moh. Dahlan, M.Ag.', '2017037802', NULL, NULL, 'Ushul Fiqh', 30),
(61, 'Prof. Dr. Aminullah, M.Ag.', '2016116002', NULL, NULL, 'Pemikiran Islam', 30),
(62, 'Dr. Pujiono, M.Ag.', '2001047001', NULL, NULL, 'Filsafat Umum', 30),
(63, 'Dra. Sofkhatin Khumaidah, M.Pd, M.Ed., Ph.D.', '2020076502', NULL, NULL, 'Bahasa Inggris', 30),
(64, 'Dr. Abdul Rokhim, S.Ag., M.E.I.', '2030087301', NULL, NULL, 'Hadits', 30);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_sidang`
--

CREATE TABLE `jadwal_sidang` (
  `id_jadwal` int(11) NOT NULL,
  `id_tesis` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu` time DEFAULT NULL,
  `tempat` varchar(100) DEFAULT NULL,
  `status` enum('terjadwal','selesai') DEFAULT 'terjadwal'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jadwal_sidang`
--

INSERT INTO `jadwal_sidang` (`id_jadwal`, `id_tesis`, `tanggal`, `waktu`, `tempat`, `status`) VALUES
(1, 1, '2025-03-11', '01:52:00', 'GOR', 'terjadwal');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id_mahasiswa` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id_mahasiswa`, `nama`, `nim`, `email`, `telepon`) VALUES
(1, 'Budi Santoso', '123456789', 'budi@example.com', '081234567890'),
(2, 'Nama Mahasiswa 2', '123456788', 'mahasiswa2@example.com', '081234567890'),
(3, 'Paijo', '123456787', 'john.doe@example.com', '081234567890'),
(4, 'joni', '08043046', 'john.doel@example.com', '081234567890'),
(5, 'Jack', '10988393', 'aaaa@email.com', '081234567890'),
(6, 'Jeki', '09234567', 'john1.doe@example.com', '081234567890');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `id_nilai` int(11) NOT NULL,
  `id_tesis` int(11) DEFAULT NULL,
  `id_dosen` int(11) DEFAULT NULL,
  `nilai` decimal(4,2) DEFAULT NULL,
  `catatan` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`id_nilai`, `id_tesis`, `id_dosen`, `nilai`, `catatan`) VALUES
(1, 1, 1, 90.00, 'anmh');

-- --------------------------------------------------------

--
-- Table structure for table `prodi`
--

CREATE TABLE `prodi` (
  `id` int(11) NOT NULL,
  `Program` varchar(100) NOT NULL,
  `strata` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prodi`
--

INSERT INTO `prodi` (`id`, `Program`, `strata`) VALUES
(1, 'Magister Pendidikan Agama Islam', 2),
(2, 'Magister Manajemen Pendidikan Islam', 2),
(3, 'Magister Pendidikan Bahasa Arab', 2),
(4, 'Magister Pendidikan Guru Madrasah Ibtidaiyah', 2),
(5, 'Magister Hukum Keluarga', 2),
(6, 'Magister Komunikasi dan Penyiaran Islam', 2),
(7, 'Magister Ekonomi Syariah', 2),
(8, 'Magister Studi Islam', 2),
(9, 'Doktor Pendidikan Agama Islam', 3),
(10, 'Doktor Manajemen Pendidikan Islam', 3),
(11, 'Doktor Studi Islam', 3),
(12, 'Doktor Ekonomi Syariah', 3),
(13, 'Doktor Hukum Keluarga', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tesis`
--

CREATE TABLE `tesis` (
  `id_tesis` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `abstrak` text,
  `tahun` year(4) DEFAULT NULL,
  `id_mahasiswa` int(11) DEFAULT NULL,
  `id_prodi` int(11) DEFAULT NULL,
  `status` enum('diajukan','diterima','ditolak','selesai','lulus') DEFAULT 'diajukan'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tesis`
--

INSERT INTO `tesis` (`id_tesis`, `judul`, `abstrak`, `tahun`, `id_mahasiswa`, `id_prodi`, `status`) VALUES
(1, 'Sistem Deteksi Plagiarisme Berbasis AI', 'Abstrak tesis tentang sistem deteksi plagiarisme menggunakan kecerdasan buatan...', '2023', 1, 1, 'diajukan'),
(4, 'Analisis Sentimen Media Sosial Menggunakan NLP', 'Abstrak tesis tentang analisis sentimen di media sosial dengan teknik pemrosesan bahasa alami...', '2022', 2, 2, 'diterima'),
(5, 'Desertasi', 'bla', '2024', 3, 9, 'diajukan'),
(6, 'Desertasi2', 'Desertasi2', '2024', 4, 12, 'diajukan'),
(7, 'Desertasi 4', 'zczczv', '2004', 5, 12, 'diajukan'),
(8, 'Desertasi 5', 'xxx', '2004', 6, 11, 'diajukan'),
(9, 'Pengaruh Gaya Belajar terhadap Prestasi Akademik Mahasiswa', 'Tesis ini membahas hubungan antara gaya belajar mahasiswa dengan pencapaian akademik.', '2021', 12, 4, 'ditolak'),
(10, 'Optimalisasi Produksi Pertanian dengan Teknologi IoT', 'Penelitian ini mengembangkan solusi berbasis Internet of Things (IoT) untuk meningkatkan efisiensi pertanian.', '2023', 13, 5, 'diajukan'),
(11, 'Analisis Sentimen Media Sosial terhadap Keputusan Konsumen', 'Penelitian ini mengeksplorasi pengaruh sentimen media sosial terhadap keputusan pembelian konsumen.', '2023', 9, 2, 'diajukan'),
(12, 'Implementasi Blockchain dalam Sistem Keamanan Data', 'Studi ini membahas bagaimana teknologi blockchain dapat meningkatkan keamanan data.', '2022', 10, 1, 'diterima'),
(13, 'Efektivitas Hukum Cyber dalam Menangani Kejahatan Digital', 'Penelitian ini mengkaji sejauh mana hukum siber efektif dalam menangani kasus kejahatan digital.', '2023', 11, 3, 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `ujian_disertasi`
--

CREATE TABLE `ujian_disertasi` (
  `id_ujian` int(11) NOT NULL,
  `id_tesis` int(11) DEFAULT NULL,
  `jenis_ujian` enum('ujian_kualifikasi','ujian_proposal','seminar_hasil','ujian_tertutup','ujian_terbuka') DEFAULT NULL,
  `tanggal_ujian` date DEFAULT NULL,
  `id_dosen` int(11) DEFAULT NULL,
  `peran_dosen` enum('promotor','copromotor','penguji_utama','sekretaris_penguji','penguji_1','penguji_2','penguji_3','penguji_4') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ujian_disertasi`
--

INSERT INTO `ujian_disertasi` (`id_ujian`, `id_tesis`, `jenis_ujian`, `tanggal_ujian`, `id_dosen`, `peran_dosen`) VALUES
(2, 5, 'ujian_kualifikasi', '2025-03-14', 1, 'promotor'),
(3, 6, 'ujian_kualifikasi', '2025-03-15', 2, 'copromotor'),
(4, 8, 'ujian_kualifikasi', '2025-03-15', 3, 'promotor'),
(5, 5, 'ujian_kualifikasi', '2025-03-15', 3, 'copromotor'),
(6, 5, 'ujian_kualifikasi', '2025-03-15', 4, 'penguji_utama');

-- --------------------------------------------------------

--
-- Table structure for table `ujian_tesis`
--

CREATE TABLE `ujian_tesis` (
  `id_ujian` int(11) NOT NULL,
  `id_tesis` int(11) DEFAULT NULL,
  `jenis_ujian` enum('ujian_kualifikasi','ujian_proposal','seminar_hasil','ujian_tertutup','ujian_terbuka') DEFAULT NULL,
  `tanggal_ujian` date DEFAULT NULL,
  `id_dosen` int(11) DEFAULT NULL,
  `peran_dosen` enum('nama_ketua_sidang','pembimbing_penguji_1','pembimbing_penguji_2','penguji') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ujian_tesis`
--

INSERT INTO `ujian_tesis` (`id_ujian`, `id_tesis`, `jenis_ujian`, `tanggal_ujian`, `id_dosen`, `peran_dosen`) VALUES
(1, 4, 'ujian_kualifikasi', '2025-03-15', 4, 'pembimbing_penguji_1'),
(2, 4, 'ujian_kualifikasi', '2025-03-15', 1, 'pembimbing_penguji_2'),
(3, 4, 'ujian_kualifikasi', '2025-03-15', 3, 'penguji');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','dosen','mahasiswa') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `nama`, `role`, `created_at`) VALUES
(1, 'prof.babun.suharto', '1', 'Prof. Dr. H. Babun Suharto, S.E., M.M.', 'dosen', '2025-03-15 05:20:00'),
(2, 'abdul.wadud.nafis', 'user', 'Dr. Abdul Wadud Nafis, Lc., MEI.', 'dosen', '2025-03-15 05:20:00'),
(3, 'moch.chotib', 'user', 'Prof. Dr. Moch. Chotib, S.Ag., M.M.', 'dosen', '2025-03-15 05:20:00'),
(4, 'khairunnisa.musari', 'user', 'Dr. Khairunnisa Musari, S.T.,M.MT.', 'dosen', '2025-03-15 05:20:00'),
(5, 'misbahul.munir', 'user', 'Dr. Misbahul Munir, M.M.', 'dosen', '2025-03-15 05:20:00'),
(6, 'hersa.farida.qoriani', 'user', 'Dr. Hersa Farida Qoriani, S.Kom., M.E.I.', 'dosen', '2025-03-15 05:20:00'),
(7, 'ishaq', 'user', 'Dr. Ishaq, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(8, 'abdullah', 'user', 'Dr. Abdullah, S.Ag., M.H.I.', 'dosen', '2025-03-15 05:20:00'),
(9, 'hamam', 'user', 'Dr. Hamam, M.H.I.', 'dosen', '2025-03-15 05:20:00'),
(10, 'wildani.hefni', 'user', 'Dr. Wildani Hefni, S.H.I., M.A.', 'dosen', '2025-03-15 05:20:00'),
(11, 'sri.lumatus.saadah', 'user', 'Dr. Sri Lumatus Sa\'adah, S.Ag., M.H.I.', 'dosen', '2025-03-15 05:20:00'),
(12, 'kun.wazis', 'user', 'Dr. Kun Wazis, S.Sos., M.I.Kom.', 'dosen', '2025-03-15 05:20:00'),
(13, 'hepni', 'user', 'Prof. Dr. Hepni, S.Ag., M.M.', 'dosen', '2025-03-15 05:20:00'),
(14, 'siti.raudhatul.jannah', 'user', 'Dr. Siti Raudhatul Jannah, S.Ag, M.Med.Kom.', 'dosen', '2025-03-15 05:20:00'),
(15, 'minan.jauhari', 'user', 'Dr. Minan Jauhari, S.Sos.I. M.Si.', 'dosen', '2025-03-15 05:20:00'),
(16, 'win.usuluddin', 'user', 'Dr. Win Usuluddin, M.Hum.', 'dosen', '2025-03-15 05:20:00'),
(17, 'zainuddin.al.haj.zaini', 'user', 'Dr. Zainuddin Al Haj Zaini, Lc., M.Pd.I.', 'dosen', '2025-03-15 05:20:00'),
(18, 'ach.faridul.ilmi', 'user', 'Dr. Ach Faridul Ilmi, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(19, 'abdul.muis', 'user', 'Dr. Abdul Mu\'is, S.Ag., M.Si.', 'dosen', '2025-03-15 05:20:00'),
(20, 'khotibul.umam', 'user', 'Dr. Khotibul Umam, MA.', 'dosen', '2025-03-15 05:20:00'),
(21, 'moh.anwar', 'user', 'Dr. Moh. Anwar, S.Pd., M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(22, 'gunawan', 'user', 'Dr. Gunawan, S.Pd.I., M.Pd.I.', 'dosen', '2025-03-15 05:20:00'),
(23, 'dyah.nawangsari', 'user', 'Dr. Dyah Nawangsari, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(24, 'mustajab', 'user', 'Dr. Mustajab, M.Pd.I.', 'dosen', '2025-03-15 05:20:00'),
(25, 'saihan', 'user', 'Dr. Saihan, S.Ag., M.Pd.I.', 'dosen', '2025-03-15 05:20:00'),
(26, 'moh.sahlan', 'user', 'Dr. Moh. Sahlan, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(27, 'masud', 'user', 'Dr. Mas\'ud, S.Ag., M.Pd.I.', 'dosen', '2025-03-15 05:20:00'),
(28, 'subakri', 'user', 'Dr. Subakri, M.Pd.I.', 'dosen', '2025-03-15 05:20:00'),
(29, 'syamsul.anam', 'user', 'Dr. Syamsul Anam, S.Ag., M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(30, 'faisol.nasar.bin.madi', 'user', 'Prof. Dr. Faisol Nasar Bin Madi, M.A.', 'dosen', '2025-03-15 05:20:00'),
(31, 'bambang.irawan', 'user', 'Dr. Bambang Irawan, M.Ed.', 'dosen', '2025-03-15 05:20:00'),
(32, 'maskud', 'user', 'Dr. Maskud, S.Ag., M.Si.', 'dosen', '2025-03-15 05:20:00'),
(33, 'abdul.haris', 'user', 'Dr. Abdul Haris, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(34, 'abdur.rosid', 'user', 'Dr. Abdur Rosid, M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(35, 'st.mislikhah', 'user', 'Dr. ST. Mislikhah, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(36, 'abd.muhith', 'user', 'Dr. Abd. Muhith, S.Ag., M.Pd.I.', 'dosen', '2025-03-15 05:20:00'),
(37, 'mualimin', 'user', 'Dr. Mu\'alimin, S.Ag., M.Pd.I.', 'dosen', '2025-03-15 05:20:00'),
(38, 'erma.fatmawati', 'user', 'Dr. Erma Fatmawati, M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(39, 'moh.sutomo', 'user', 'Dr. Moh. Sutomo, M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(40, 'imron.fauzi', 'user', 'Dr. Imron Fauzi, M.Pd.I.', 'dosen', '2025-03-15 05:20:00'),
(41, 'h.mursalim', 'user', 'Dr. H. Mursalim, M.Ag', 'dosen', '2025-03-15 05:20:00'),
(42, 'safrudin.edi.wibowo', 'user', 'Dr. Safrudin Edi Wibowo, Lc, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(43, 'fawaizul.umam', 'user', 'Dr. Fawaizul Umam, S.Ag., M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(44, 'rafid.abbas', 'user', 'Dr. Rafid Abbas, M.A.', 'dosen', '2025-03-15 05:20:00'),
(45, 'khoirul.faizin', 'user', 'Dr. Khoirul Faizin, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(46, 'h.matkur', 'user', 'Dr. H. Matkur, S.Pd.I., M.Si.', 'dosen', '2025-03-15 05:20:00'),
(47, 'abd.muis', 'user', 'Prof. Dr. Abd. Mu\'is, M.M.', 'dosen', '2025-03-15 05:20:00'),
(48, 'moh.khusnuridlo', 'user', 'Prof. Dr. Moh. Khusnuridlo, M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(49, 'ainur.rafik', 'user', 'Dr. Ainur Rafik, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(50, 'st.rodliyah', 'user', 'Dr. St. Rodliyah, M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(51, 'suhadi.winoto', 'user', 'Prof. Dr. Suhadi Winoto, M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(52, 'sofyan.tsauri', 'user', 'Prof. Dr. Sofyan Tsauri, M.M.', 'dosen', '2025-03-15 05:20:00'),
(53, 'mundir', 'user', 'Prof. Dr. Mundir, M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(54, 'abd.halim.soebahar', 'user', 'Prof. Dr. Abd. Halim Soebahar, MA.', 'dosen', '2025-03-15 05:20:00'),
(55, 'mukniah', 'user', 'Prof. Dr. Mukni\'ah, M.Pd.I.', 'dosen', '2025-03-15 05:20:00'),
(56, 'ubaidillah', 'user', 'Dr. Ubaidillah, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(57, 'mashudi', 'user', 'Prof. Dr. Mashudi, M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(58, 'sofyan.hadi', 'user', 'Dr. Sofyan Hadi, M.Pd.', 'dosen', '2025-03-15 05:20:00'),
(59, 'miftah.arifin', 'user', 'Prof. Dr. Miftah Arifin, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(60, 'moh.dahlan', 'user', 'Prof. Dr. Moh. Dahlan, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(61, 'aminullah', 'user', 'Prof. Dr. Aminullah, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(62, 'pujiono', 'user', 'Dr. Pujiono, M.Ag.', 'dosen', '2025-03-15 05:20:00'),
(63, 'sofkhatin.khumaidah', 'user', 'Dra. Sofkhatin Khumaidah, M.Pd, M.Ed., Ph.D.', 'dosen', '2025-03-15 05:20:00'),
(64, 'abdul.rokhim', 'user', 'Dr. Abdul Rokhim, S.Ag., M.E.I.', 'dosen', '2025-03-15 05:20:00'),
(65, 'Admin', '1', 'Dimas', 'admin', '2025-03-15 05:21:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id_dosen`),
  ADD UNIQUE KEY `nidn` (`nidn`);

--
-- Indexes for table `jadwal_sidang`
--
ALTER TABLE `jadwal_sidang`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_tesis` (`id_tesis`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id_mahasiswa`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id_nilai`),
  ADD KEY `id_tesis` (`id_tesis`),
  ADD KEY `id_dosen` (`id_dosen`);

--
-- Indexes for table `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tesis`
--
ALTER TABLE `tesis`
  ADD PRIMARY KEY (`id_tesis`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`),
  ADD KEY `id_prodi` (`id_prodi`);

--
-- Indexes for table `ujian_disertasi`
--
ALTER TABLE `ujian_disertasi`
  ADD PRIMARY KEY (`id_ujian`);

--
-- Indexes for table `ujian_tesis`
--
ALTER TABLE `ujian_tesis`
  ADD PRIMARY KEY (`id_ujian`),
  ADD KEY `fk_tesis` (`id_tesis`),
  ADD KEY `fk_dosen` (`id_dosen`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id_dosen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `jadwal_sidang`
--
ALTER TABLE `jadwal_sidang`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id_mahasiswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tesis`
--
ALTER TABLE `tesis`
  MODIFY `id_tesis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ujian_disertasi`
--
ALTER TABLE `ujian_disertasi`
  MODIFY `id_ujian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ujian_tesis`
--
ALTER TABLE `ujian_tesis`
  MODIFY `id_ujian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jadwal_sidang`
--
ALTER TABLE `jadwal_sidang`
  ADD CONSTRAINT `jadwal_sidang_ibfk_1` FOREIGN KEY (`id_tesis`) REFERENCES `tesis` (`id_tesis`);

--
-- Constraints for table `nilai`
--
ALTER TABLE `nilai`
  ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`id_tesis`) REFERENCES `tesis` (`id_tesis`),
  ADD CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id_dosen`);

--
-- Constraints for table `tesis`
--
ALTER TABLE `tesis`
  ADD CONSTRAINT `tesis_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`),
  ADD CONSTRAINT `tesis_ibfk_2` FOREIGN KEY (`id_prodi`) REFERENCES `prodi` (`id`);

--
-- Constraints for table `ujian_tesis`
--
ALTER TABLE `ujian_tesis`
  ADD CONSTRAINT `fk_dosen` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id_dosen`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tesis` FOREIGN KEY (`id_tesis`) REFERENCES `tesis` (`id_tesis`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
