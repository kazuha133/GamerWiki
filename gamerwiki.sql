-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 17, 2025 at 04:09 PM
-- Server version: 9.4.0
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamerwiki`
--

-- --------------------------------------------------------

--
-- Table structure for table `doi_tham_gia`
--

DROP TABLE IF EXISTS `doi_tham_gia`;
CREATE TABLE IF NOT EXISTS `doi_tham_gia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_giai_dau` int NOT NULL,
  `id_doi_tuyen` int NOT NULL,
  `thu_hang` int DEFAULT NULL,
  `ngay_tham_gia` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_giai_dau` (`id_giai_dau`),
  KEY `idx_doi_tuyen` (`id_doi_tuyen`),
  KEY `idx_thu_hang` (`thu_hang`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doi_tham_gia`
--

INSERT INTO `doi_tham_gia` (`id`, `id_giai_dau`, `id_doi_tuyen`, `thu_hang`, `ngay_tham_gia`) VALUES
(1, 1, 1, 1, '2025-12-17 22:11:41'),
(2, 1, 2, 3, '2025-12-17 22:11:41'),
(3, 1, 3, 2, '2025-12-17 22:11:41'),
(4, 1, 4, 5, '2025-12-17 22:11:41'),
(5, 2, 2, 1, '2025-12-17 22:11:41'),
(6, 2, 1, 4, '2025-12-17 22:11:41'),
(7, 2, 5, 3, '2025-12-17 22:11:41');

-- --------------------------------------------------------

--
-- Table structure for table `doi_tuyen`
--

DROP TABLE IF EXISTS `doi_tuyen`;
CREATE TABLE IF NOT EXISTS `doi_tuyen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten_doi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quoc_gia` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nam_thanh_lap` year DEFAULT NULL,
  `thanh_tich` text COLLATE utf8mb4_unicode_ci,
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ten_doi` (`ten_doi`),
  KEY `idx_quoc_gia` (`quoc_gia`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doi_tuyen`
--

INSERT INTO `doi_tuyen` (`id`, `ten_doi`, `logo`, `quoc_gia`, `nam_thanh_lap`, `thanh_tich`, `mo_ta`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 'T1', 'logo_t1.png', 'South Korea', '2012', 'World Championship 2023, LCK Spring 2023', 'T1 là một trong những đội tuyển mạnh nhất thế giới với nhiều chức vô địch quốc tế.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(2, 'Gen.G', 'logo_geng.png', 'South Korea', '2017', 'LCK Summer 2023, MSI 2023', 'Gen.G là đội tuyển hàng đầu Hàn Quốc với lối chơi chiến thuật đỉnh cao.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(3, 'JD Gaming', 'logo_jdg.png', 'China', '2017', 'LPL Spring 2023, Worlds Runner-up 2023', 'JDG là đội tuyển hàng đầu của Trung Quốc với phong cách chơi tấn công.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(4, 'Cloud9', 'logo_c9.png', 'United States', '2013', 'LCS Championship, Worlds Quarterfinalist', 'Cloud9 là đội tuyển nổi tiếng nhất Bắc Mỹ với nhiều thành tích ấn tượng.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(5, 'G2 Esports', 'logo_g2.png', 'Germany', '2014', 'LEC Champions, MSI 2019', 'G2 Esports là đội tuyển châu Âu với lối chơi sáng tạo và phong cách độc đáo.', '2025-12-17 22:11:41', '2025-12-17 22:11:41');

-- --------------------------------------------------------

--
-- Table structure for table `giai_dau`
--

DROP TABLE IF EXISTS `giai_dau`;
CREATE TABLE IF NOT EXISTS `giai_dau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten_giai` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `game` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thoi_gian_bat_dau` date DEFAULT NULL,
  `thoi_gian_ket_thuc` date DEFAULT NULL,
  `dia_diem` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `giai_thuong` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ten_giai` (`ten_giai`),
  KEY `idx_game` (`game`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `giai_dau`
--

INSERT INTO `giai_dau` (`id`, `ten_giai`, `game`, `thoi_gian_bat_dau`, `thoi_gian_ket_thuc`, `dia_diem`, `giai_thuong`, `mo_ta`, `ngay_tao`) VALUES
(1, 'World Championship 2023', 'League of Legends', '2023-10-10', '2023-11-19', 'Seoul, South Korea', '$2,250,000', 'Giải đấu lớn nhất năm của League of Legends với sự tham gia của các đội mạnh nhất thế giới.', '2025-12-17 22:11:41'),
(2, 'MSI 2024', 'League of Legends', '2024-05-01', '2024-05-19', 'Shanghai, China', '$1,000,000', 'Mid-Season Invitational - giải đấu quốc tế giữa mùa giải với các đội vô địch các khu vực.', '2025-12-17 22:11:41'),
(3, 'The International 2023', 'Dota 2', '2023-10-12', '2023-10-29', 'Seattle, USA', '$40,000,000', 'Giải đấu Dota 2 lớn nhất với giải thưởng khổng lồ.', '2025-12-17 22:11:41');

-- --------------------------------------------------------

--
-- Table structure for table `lich_su_chuyen_doi`
--

DROP TABLE IF EXISTS `lich_su_chuyen_doi`;
CREATE TABLE IF NOT EXISTS `lich_su_chuyen_doi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_tuyen_thu` int NOT NULL,
  `id_doi_cu` int DEFAULT NULL,
  `id_doi_moi` int DEFAULT NULL,
  `ngay_chuyen` date NOT NULL,
  `ghi_chu` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `id_doi_cu` (`id_doi_cu`),
  KEY `id_doi_moi` (`id_doi_moi`),
  KEY `idx_tuyen_thu` (`id_tuyen_thu`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lich_su_chuyen_doi`
--

INSERT INTO `lich_su_chuyen_doi` (`id`, `id_tuyen_thu`, `id_doi_cu`, `id_doi_moi`, `ngay_chuyen`, `ghi_chu`) VALUES
(1, 7, 2, 3, '2022-11-20', 'Chuyển từ Gen.G sang JDG với mức phí chuyển nhượng cao.'),
(2, 12, 4, 5, '2023-11-15', 'Chuyển từ Cloud9 sang G2 Esports để tìm kiếm thử thách mới.');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

DROP TABLE IF EXISTS `nguoi_dung`;
CREATE TABLE IF NOT EXISTS `nguoi_dung` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten_dang_nhap` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vai_tro` enum('admin','customer','user') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `trang_thai` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  KEY `idx_ten_dang_nhap` (`ten_dang_nhap`),
  KEY `idx_vai_tro` (`vai_tro`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `ten_dang_nhap`, `mat_khau`, `email`, `vai_tro`, `trang_thai`, `ngay_tao`) VALUES
(2, 'admin', '$2y$10$.5AsrIhq0lRd/9CRd417a.58uNbtxnA3WEFzt3/GYPYypV1gf1616', 'admin@gamerwiki.com', 'admin', 'active', '2025-12-17 22:58:29'),
(4, 'customer', '$2y$10$vCzXt0keVA7wzXi2N9nTV./K9.plve3AMoSKKaEXbNqecmuHZ4OJu', 'customer@gamerwiki.com', 'customer', 'active', '2025-12-17 23:02:03');

-- --------------------------------------------------------

--
-- Table structure for table `tuyen_thu`
--

DROP TABLE IF EXISTS `tuyen_thu`;
CREATE TABLE IF NOT EXISTS `tuyen_thu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten_that` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anh_dai_dien` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vai_tro` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quoc_tich` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `id_doi_tuyen` int DEFAULT NULL,
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nickname` (`nickname`),
  KEY `idx_doi_tuyen` (`id_doi_tuyen`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tuyen_thu`
--

INSERT INTO `tuyen_thu` (`id`, `ten_that`, `nickname`, `anh_dai_dien`, `vai_tro`, `quoc_tich`, `ngay_sinh`, `id_doi_tuyen`, `mo_ta`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 'Lee Sang-hyeok', 'Faker', 'faker.jpg', 'Mid', 'South Korea', '1996-05-07', 1, 'Tuyển thủ huyền thoại của T1, được mệnh danh là \"Unkillable Demon King\".', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(2, 'Ryu Min-seok', 'Keria', 'keria.jpg', 'Support', 'South Korea', '2002-10-14', 1, 'Support xuất sắc của T1 với khả năng roaming và vision control đỉnh cao.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(3, 'Choi Woo-je', 'Zeus', 'zeus.jpg', 'Top', 'South Korea', '2004-01-31', 1, 'Top laner trẻ tài năng của T1 với mechanics ấn tượng.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(4, 'Jeong Ji-hoon', 'Chovy', 'chovy.jpg', 'Mid', 'South Korea', '2001-03-03', 2, 'Mid laner đẳng cấp thế giới với kỹ năng laning xuất sắc.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(5, 'Son Si-woo', 'Lehends', 'lehends.jpg', 'Support', 'South Korea', '1999-03-02', 2, 'Support sáng tạo và có tầm nhìn chiến thuật tốt.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(6, 'Seo Jin-hyeok', 'Kanavi', 'kanavi.jpg', 'Jungle', 'South Korea', '2001-04-28', 3, 'Jungle mạnh mẽ của JDG với khả năng gank hiệu quả.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(7, 'Park Jae-hyuk', 'Ruler', 'ruler.jpg', 'ADC', 'South Korea', '1998-12-29', 3, 'ADC hàng đầu thế giới với positioning và kỹ năng teamfight xuất sắc.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(8, 'Yiliang Peng', 'Doublelift', 'doublelift.jpg', 'ADC', 'United States', '1993-07-19', 4, 'ADC huyền thoại của Bắc Mỹ, đã giải nghệ nhưng vẫn là biểu tượng.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(9, 'Robert Huang', 'Blaber', 'blaber.jpg', 'Jungle', 'United States', '2000-01-16', 4, 'Jungle năng động với lối chơi aggressive.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(10, 'Rasmus Borregaard', 'Caps', 'caps.jpg', 'Mid', 'Denmark', '1999-11-17', 5, 'Mid laner tài năng của G2 với lối chơi unpredictable.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(11, 'Mihael Mehle', 'Mikyx', 'mikyx.jpg', 'Support', 'Slovenia', '1999-09-02', 5, 'Support của G2 với khả năng playmaking tuyệt vời.', '2025-12-17 22:11:41', '2025-12-17 22:11:41'),
(12, 'Martin Larsson', 'Rekkles', 'rekkles.jpg', 'ADC', 'Sweden', '1996-09-20', 5, 'ADC kỳ cựu với phong cách chơi ổn định và đẳng cấp.', '2025-12-17 22:11:41', '2025-12-17 22:11:41');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `doi_tham_gia`
--
ALTER TABLE `doi_tham_gia`
  ADD CONSTRAINT `doi_tham_gia_ibfk_1` FOREIGN KEY (`id_giai_dau`) REFERENCES `giai_dau` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doi_tham_gia_ibfk_2` FOREIGN KEY (`id_doi_tuyen`) REFERENCES `doi_tuyen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lich_su_chuyen_doi`
--
ALTER TABLE `lich_su_chuyen_doi`
  ADD CONSTRAINT `lich_su_chuyen_doi_ibfk_1` FOREIGN KEY (`id_tuyen_thu`) REFERENCES `tuyen_thu` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lich_su_chuyen_doi_ibfk_2` FOREIGN KEY (`id_doi_cu`) REFERENCES `doi_tuyen` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lich_su_chuyen_doi_ibfk_3` FOREIGN KEY (`id_doi_moi`) REFERENCES `doi_tuyen` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tuyen_thu`
--
ALTER TABLE `tuyen_thu`
  ADD CONSTRAINT `tuyen_thu_ibfk_1` FOREIGN KEY (`id_doi_tuyen`) REFERENCES `doi_tuyen` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
