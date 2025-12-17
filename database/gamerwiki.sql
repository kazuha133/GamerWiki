-- GamerWiki Database Schema
-- Tạo database
CREATE DATABASE IF NOT EXISTS gamerwiki CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamerwiki;

-- Bảng nguoi_dung (Users)
CREATE TABLE nguoi_dung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_dang_nhap VARCHAR(50) UNIQUE NOT NULL,
    mat_khau VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    vai_tro ENUM('admin', 'user') DEFAULT 'user',
    trang_thai ENUM('active', 'inactive') DEFAULT 'active',
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ten_dang_nhap (ten_dang_nhap),
    INDEX idx_vai_tro (vai_tro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng doi_tuyen (Teams)
CREATE TABLE doi_tuyen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_doi VARCHAR(100) NOT NULL,
    logo VARCHAR(255),
    quoc_gia VARCHAR(50),
    nam_thanh_lap YEAR,
    thanh_tich TEXT,
    mo_ta TEXT,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ten_doi (ten_doi),
    INDEX idx_quoc_gia (quoc_gia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng tuyen_thu (Players)
CREATE TABLE tuyen_thu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_that VARCHAR(100) NOT NULL,
    nickname VARCHAR(50) NOT NULL,
    anh_dai_dien VARCHAR(255),
    vai_tro VARCHAR(50),
    quoc_tich VARCHAR(50),
    ngay_sinh DATE,
    id_doi_tuyen INT,
    mo_ta TEXT,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_doi_tuyen) REFERENCES doi_tuyen(id) ON DELETE SET NULL,
    INDEX idx_nickname (nickname),
    INDEX idx_doi_tuyen (id_doi_tuyen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng giai_dau (Tournaments)
CREATE TABLE giai_dau (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_giai VARCHAR(150) NOT NULL,
    game VARCHAR(100) NOT NULL,
    thoi_gian_bat_dau DATE,
    thoi_gian_ket_thuc DATE,
    dia_diem VARCHAR(100),
    giai_thuong VARCHAR(100),
    mo_ta TEXT,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ten_giai (ten_giai),
    INDEX idx_game (game)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng doi_tham_gia (Tournament Participants)
CREATE TABLE doi_tham_gia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_giai_dau INT NOT NULL,
    id_doi_tuyen INT NOT NULL,
    thu_hang INT,
    ngay_tham_gia DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_giai_dau) REFERENCES giai_dau(id) ON DELETE CASCADE,
    FOREIGN KEY (id_doi_tuyen) REFERENCES doi_tuyen(id) ON DELETE CASCADE,
    INDEX idx_giai_dau (id_giai_dau),
    INDEX idx_doi_tuyen (id_doi_tuyen),
    INDEX idx_thu_hang (thu_hang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng lich_su_chuyen_doi (Transfer History)
CREATE TABLE lich_su_chuyen_doi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tuyen_thu INT NOT NULL,
    id_doi_cu INT,
    id_doi_moi INT,
    ngay_chuyen DATE NOT NULL,
    ghi_chu TEXT,
    FOREIGN KEY (id_tuyen_thu) REFERENCES tuyen_thu(id) ON DELETE CASCADE,
    FOREIGN KEY (id_doi_cu) REFERENCES doi_tuyen(id) ON DELETE SET NULL,
    FOREIGN KEY (id_doi_moi) REFERENCES doi_tuyen(id) ON DELETE SET NULL,
    INDEX idx_tuyen_thu (id_tuyen_thu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert admin mặc định (password: admin123)
-- LƯU Ý BẢO MẬT: Đây là mật khẩu mặc định chỉ dùng cho môi trường phát triển/test
-- PHẢI ĐỔI MẬT KHẨU NGAY SAU KHI ĐĂNG NHẬP LẦN ĐẦU trong môi trường production
INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro, trang_thai) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@gamerwiki.com', 'admin', 'active');

-- Insert sample teams
INSERT INTO doi_tuyen (ten_doi, logo, quoc_gia, nam_thanh_lap, thanh_tich, mo_ta) VALUES
('T1', 'logo_t1.png', 'South Korea', 2012, 'World Championship 2023, LCK Spring 2023', 'T1 là một trong những đội tuyển mạnh nhất thế giới với nhiều chức vô địch quốc tế.'),
('Gen.G', 'logo_geng.png', 'South Korea', 2017, 'LCK Summer 2023, MSI 2023', 'Gen.G là đội tuyển hàng đầu Hàn Quốc với lối chơi chiến thuật đỉnh cao.'),
('JD Gaming', 'logo_jdg.png', 'China', 2017, 'LPL Spring 2023, Worlds Runner-up 2023', 'JDG là đội tuyển hàng đầu của Trung Quốc với phong cách chơi tấn công.'),
('Cloud9', 'logo_c9.png', 'United States', 2013, 'LCS Championship, Worlds Quarterfinalist', 'Cloud9 là đội tuyển nổi tiếng nhất Bắc Mỹ với nhiều thành tích ấn tượng.'),
('G2 Esports', 'logo_g2.png', 'Germany', 2014, 'LEC Champions, MSI 2019', 'G2 Esports là đội tuyển châu Âu với lối chơi sáng tạo và phong cách độc đáo.');

-- Insert sample players
INSERT INTO tuyen_thu (ten_that, nickname, anh_dai_dien, vai_tro, quoc_tich, ngay_sinh, id_doi_tuyen, mo_ta) VALUES
('Lee Sang-hyeok', 'Faker', 'faker.jpg', 'Mid', 'South Korea', '1996-05-07', 1, 'Tuyển thủ huyền thoại của T1, được mệnh danh là "Unkillable Demon King".'),
('Ryu Min-seok', 'Keria', 'keria.jpg', 'Support', 'South Korea', '2002-10-14', 1, 'Support xuất sắc của T1 với khả năng roaming và vision control đỉnh cao.'),
('Choi Woo-je', 'Zeus', 'zeus.jpg', 'Top', 'South Korea', '2004-01-31', 1, 'Top laner trẻ tài năng của T1 với mechanics ấn tượng.'),
('Jeong Ji-hoon', 'Chovy', 'chovy.jpg', 'Mid', 'South Korea', '2001-03-03', 2, 'Mid laner đẳng cấp thế giới với kỹ năng laning xuất sắc.'),
('Son Si-woo', 'Lehends', 'lehends.jpg', 'Support', 'South Korea', '1999-03-02', 2, 'Support sáng tạo và có tầm nhìn chiến thuật tốt.'),
('Seo Jin-hyeok', 'Kanavi', 'kanavi.jpg', 'Jungle', 'South Korea', '2001-04-28', 3, 'Jungle mạnh mẽ của JDG với khả năng gank hiệu quả.'),
('Park Jae-hyuk', 'Ruler', 'ruler.jpg', 'ADC', 'South Korea', '1998-12-29', 3, 'ADC hàng đầu thế giới với positioning và kỹ năng teamfight xuất sắc.'),
('Yiliang Peng', 'Doublelift', 'doublelift.jpg', 'ADC', 'United States', '1993-07-19', 4, 'ADC huyền thoại của Bắc Mỹ, đã giải nghệ nhưng vẫn là biểu tượng.'),
('Robert Huang', 'Blaber', 'blaber.jpg', 'Jungle', 'United States', '2000-01-16', 4, 'Jungle năng động với lối chơi aggressive.'),
('Rasmus Borregaard', 'Caps', 'caps.jpg', 'Mid', 'Denmark', '1999-11-17', 5, 'Mid laner tài năng của G2 với lối chơi unpredictable.'),
('Mihael Mehle', 'Mikyx', 'mikyx.jpg', 'Support', 'Slovenia', '1999-09-02', 5, 'Support của G2 với khả năng playmaking tuyệt vời.'),
('Martin Larsson', 'Rekkles', 'rekkles.jpg', 'ADC', 'Sweden', '1996-09-20', 5, 'ADC kỳ cựu với phong cách chơi ổn định và đẳng cấp.');

-- Insert sample tournaments
INSERT INTO giai_dau (ten_giai, game, thoi_gian_bat_dau, thoi_gian_ket_thuc, dia_diem, giai_thuong, mo_ta) VALUES
('World Championship 2023', 'League of Legends', '2023-10-10', '2023-11-19', 'Seoul, South Korea', '$2,250,000', 'Giải đấu lớn nhất năm của League of Legends với sự tham gia của các đội mạnh nhất thế giới.'),
('MSI 2024', 'League of Legends', '2024-05-01', '2024-05-19', 'Shanghai, China', '$1,000,000', 'Mid-Season Invitational - giải đấu quốc tế giữa mùa giải với các đội vô địch các khu vực.'),
('The International 2023', 'Dota 2', '2023-10-12', '2023-10-29', 'Seattle, USA', '$40,000,000', 'Giải đấu Dota 2 lớn nhất với giải thưởng khổng lồ.');

-- Insert tournament participants
INSERT INTO doi_tham_gia (id_giai_dau, id_doi_tuyen, thu_hang) VALUES
(1, 1, 1), -- T1 vô địch Worlds 2023
(1, 2, 3), -- Gen.G top 3
(1, 3, 2), -- JDG á quân
(1, 4, 5), -- Cloud9 top 5
(2, 2, 1), -- Gen.G vô địch MSI
(2, 1, 4),
(2, 5, 3);

-- Insert transfer history
INSERT INTO lich_su_chuyen_doi (id_tuyen_thu, id_doi_cu, id_doi_moi, ngay_chuyen, ghi_chu) VALUES
(7, 2, 3, '2022-11-20', 'Chuyển từ Gen.G sang JDG với mức phí chuyển nhượng cao.'),
(12, 4, 5, '2023-11-15', 'Chuyển từ Cloud9 sang G2 Esports để tìm kiếm thử thách mới.');
