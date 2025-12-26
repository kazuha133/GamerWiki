-- ========================================
-- GamerWiki Complete Database Schema
-- ========================================
-- Project: GamerWiki - Hệ thống quản lý đội tuyển Esport
-- Author: NGUYỄN QUỐC TIẾN - DH52201555
-- Description: Complete database schema with Google OAuth support
-- ========================================

-- Tạo database
CREATE DATABASE IF NOT EXISTS gamerwiki CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamerwiki;

-- ========================================
-- Bảng nguoi_dung (Users)
-- ========================================
-- Quản lý thông tin người dùng và xác thực
-- Hỗ trợ cả đăng nhập truyền thống và Google OAuth 2.0
CREATE TABLE nguoi_dung (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID người dùng (auto increment)',
    ten_dang_nhap VARCHAR(50) UNIQUE NOT NULL COMMENT 'Tên đăng nhập (unique)',
    mat_khau VARCHAR(255) NOT NULL COMMENT 'Mật khẩu đã hash (bcrypt)',
    email VARCHAR(100) UNIQUE NOT NULL COMMENT 'Email người dùng (unique)',
    google_id VARCHAR(255) DEFAULT NULL COMMENT 'Google ID cho OAuth (nullable)',
    vai_tro ENUM('admin', 'customer', 'user') DEFAULT 'user' COMMENT 'Vai trò: admin (toàn quyền), customer (CRUD), user (xem)',
    trang_thai ENUM('active', 'inactive') DEFAULT 'active' COMMENT 'Trạng thái tài khoản',
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo tài khoản',
    INDEX idx_ten_dang_nhap (ten_dang_nhap) COMMENT 'Index cho tìm kiếm theo username',
    INDEX idx_vai_tro (vai_tro) COMMENT 'Index cho filter theo vai trò',
    UNIQUE INDEX idx_google_id (google_id) COMMENT 'Unique index cho Google ID',
    UNIQUE INDEX idx_email (email) COMMENT 'Unique index cho email'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng quản lý người dùng với hỗ trợ Google OAuth';

-- ========================================
-- Bảng doi_tuyen (Teams)
-- ========================================
-- Quản lý thông tin đội tuyển esport
CREATE TABLE doi_tuyen (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID đội tuyển',
    ten_doi VARCHAR(100) NOT NULL COMMENT 'Tên đội tuyển',
    logo VARCHAR(255) COMMENT 'Đường dẫn logo đội',
    quoc_gia VARCHAR(50) COMMENT 'Quốc gia',
    nam_thanh_lap YEAR COMMENT 'Năm thành lập',
    thanh_tich TEXT COMMENT 'Thành tích của đội',
    mo_ta TEXT COMMENT 'Mô tả về đội',
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo record',
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật cuối',
    INDEX idx_ten_doi (ten_doi) COMMENT 'Index cho tìm kiếm theo tên đội',
    INDEX idx_quoc_gia (quoc_gia) COMMENT 'Index cho filter theo quốc gia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng quản lý đội tuyển esport';

-- ========================================
-- Bảng tuyen_thu (Players)
-- ========================================
-- Quản lý thông tin tuyển thủ
CREATE TABLE tuyen_thu (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID tuyển thủ',
    ten_that VARCHAR(100) NOT NULL COMMENT 'Tên thật của tuyển thủ',
    nickname VARCHAR(50) NOT NULL COMMENT 'Nickname/IGN',
    anh_dai_dien VARCHAR(255) COMMENT 'Đường dẫn ảnh đại diện',
    vai_tro VARCHAR(50) COMMENT 'Vai trò trong game (Top, Mid, ADC, Support, Jungle)',
    quoc_tich VARCHAR(50) COMMENT 'Quốc tịch',
    ngay_sinh DATE COMMENT 'Ngày sinh',
    id_doi_tuyen INT COMMENT 'ID đội tuyển hiện tại',
    mo_ta TEXT COMMENT 'Mô tả về tuyển thủ',
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo record',
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật cuối',
    FOREIGN KEY (id_doi_tuyen) REFERENCES doi_tuyen(id) ON DELETE SET NULL COMMENT 'FK tới bảng doi_tuyen',
    INDEX idx_nickname (nickname) COMMENT 'Index cho tìm kiếm theo nickname',
    INDEX idx_doi_tuyen (id_doi_tuyen) COMMENT 'Index cho filter theo đội'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng quản lý tuyển thủ';

-- ========================================
-- Bảng giai_dau (Tournaments)
-- ========================================
-- Quản lý thông tin giải đấu
CREATE TABLE giai_dau (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID giải đấu',
    ten_giai VARCHAR(150) NOT NULL COMMENT 'Tên giải đấu',
    game VARCHAR(100) NOT NULL COMMENT 'Tên game (LoL, Dota 2, CS:GO, etc.)',
    thoi_gian_bat_dau DATE COMMENT 'Ngày bắt đầu',
    thoi_gian_ket_thuc DATE COMMENT 'Ngày kết thúc',
    dia_diem VARCHAR(100) COMMENT 'Địa điểm tổ chức',
    giai_thuong VARCHAR(100) COMMENT 'Giải thưởng',
    mo_ta TEXT COMMENT 'Mô tả về giải đấu',
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo record',
    INDEX idx_ten_giai (ten_giai) COMMENT 'Index cho tìm kiếm theo tên giải',
    INDEX idx_game (game) COMMENT 'Index cho filter theo game'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng quản lý giải đấu esport';

-- ========================================
-- Bảng doi_tham_gia (Tournament Participants)
-- ========================================
-- Quản lý đội tuyển tham gia giải đấu
CREATE TABLE doi_tham_gia (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID record',
    id_giai_dau INT NOT NULL COMMENT 'ID giải đấu',
    id_doi_tuyen INT NOT NULL COMMENT 'ID đội tuyển',
    thu_hang INT COMMENT 'Thứ hạng đạt được (1 = vô địch)',
    ngay_tham_gia DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày đăng ký tham gia',
    FOREIGN KEY (id_giai_dau) REFERENCES giai_dau(id) ON DELETE CASCADE COMMENT 'FK tới bảng giai_dau',
    FOREIGN KEY (id_doi_tuyen) REFERENCES doi_tuyen(id) ON DELETE CASCADE COMMENT 'FK tới bảng doi_tuyen',
    INDEX idx_giai_dau (id_giai_dau) COMMENT 'Index cho filter theo giải đấu',
    INDEX idx_doi_tuyen (id_doi_tuyen) COMMENT 'Index cho filter theo đội',
    INDEX idx_thu_hang (thu_hang) COMMENT 'Index cho sort theo thứ hạng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng quản lý đội tuyển tham gia giải đấu';

-- ========================================
-- Bảng lich_su_chuyen_doi (Transfer History)
-- ========================================
-- Lịch sử chuyển đội của tuyển thủ
CREATE TABLE lich_su_chuyen_doi (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID record',
    id_tuyen_thu INT NOT NULL COMMENT 'ID tuyển thủ',
    id_doi_cu INT COMMENT 'ID đội cũ (nullable nếu là đội đầu tiên)',
    id_doi_moi INT COMMENT 'ID đội mới',
    ngay_chuyen DATE NOT NULL COMMENT 'Ngày chuyển đội',
    ghi_chu TEXT COMMENT 'Ghi chú về lý do chuyển',
    FOREIGN KEY (id_tuyen_thu) REFERENCES tuyen_thu(id) ON DELETE CASCADE COMMENT 'FK tới bảng tuyen_thu',
    FOREIGN KEY (id_doi_cu) REFERENCES doi_tuyen(id) ON DELETE SET NULL COMMENT 'FK tới bảng doi_tuyen (đội cũ)',
    FOREIGN KEY (id_doi_moi) REFERENCES doi_tuyen(id) ON DELETE SET NULL COMMENT 'FK tới bảng doi_tuyen (đội mới)',
    INDEX idx_tuyen_thu (id_tuyen_thu) COMMENT 'Index cho filter theo tuyển thủ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng lịch sử chuyển đội của tuyển thủ';

-- ========================================
-- SAMPLE DATA - Tài khoản mặc định
-- ========================================
-- NOTE: Mật khẩu đã hash bằng bcrypt (password_hash PHP function)

-- Admin account (username: admin, password: admin123)
-- ⚠️ LƯU Ý BẢO MẬT: Mật khẩu này CHỈ dùng cho môi trường phát triển/test
-- BẮT BUỘC đổi mật khẩu ngay sau khi đăng nhập lần đầu trong production
INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro, trang_thai) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@gamerwiki.com', 'admin', 'active');

-- Customer account (username: customer, password: customer123)
-- Có quyền CRUD (Create, Read, Update, Delete)
INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro, trang_thai) 
VALUES ('customer', '$2y$10$K8tCqLBqGvYlDRjLQz7P/.EzVn5wW5K4qhxE7KVYxJxoFJQWqxg3e', 'customer@gamerwiki.com', 'customer', 'active');

-- User account (username: user, password: user123)
-- Chỉ có quyền xem (Read only)
INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro, trang_thai) 
VALUES ('user', '$2y$10$vNv3iFJzQKHa8Fz5ZYyqH.LR6J9kB5K4qFqVbJjM5vKNwQxqxPxqK', 'user@gamerwiki.com', 'user', 'active');

-- ========================================
-- SAMPLE DATA - Đội tuyển (Teams)
-- ========================================
INSERT INTO doi_tuyen (ten_doi, logo, quoc_gia, nam_thanh_lap, thanh_tich, mo_ta) VALUES
('T1', 'logo_t1.png', 'South Korea', 2012, 'World Championship 2023, LCK Spring 2023', 'T1 là một trong những đội tuyển mạnh nhất thế giới với nhiều chức vô địch quốc tế. Đội được biết đến với tuyển thủ huyền thoại Faker.'),
('Gen.G', 'logo_geng.png', 'South Korea', 2017, 'LCK Summer 2023, MSI 2023', 'Gen.G là đội tuyển hàng đầu Hàn Quốc với lối chơi chiến thuật đỉnh cao và khả năng teamfight xuất sắc.'),
('JD Gaming', 'logo_jdg.png', 'China', 2017, 'LPL Spring 2023, Worlds Runner-up 2023', 'JDG là đội tuyển hàng đầu của Trung Quốc với phong cách chơi tấn công mạnh mẽ và aggressive.'),
('Cloud9', 'logo_c9.png', 'United States', 2013, 'LCS Championship, Worlds Quarterfinalist', 'Cloud9 là đội tuyển nổi tiếng nhất Bắc Mỹ với nhiều thành tích ấn tượng tại các giải đấu quốc tế.'),
('G2 Esports', 'logo_g2.png', 'Germany', 2014, 'LEC Champions, MSI 2019', 'G2 Esports là đội tuyển châu Âu với lối chơi sáng tạo, phong cách độc đáo và khả năng thích nghi cao.');

-- ========================================
-- SAMPLE DATA - Tuyển thủ (Players)
-- ========================================
INSERT INTO tuyen_thu (ten_that, nickname, anh_dai_dien, vai_tro, quoc_tich, ngay_sinh, id_doi_tuyen, mo_ta) VALUES
-- T1 Players
('Lee Sang-hyeok', 'Faker', 'faker.jpg', 'Mid', 'South Korea', '1996-05-07', 1, 'Tuyển thủ huyền thoại của T1, được mệnh danh là "Unkillable Demon King". Là tuyển thủ vĩ đại nhất lịch sử League of Legends.'),
('Ryu Min-seok', 'Keria', 'keria.jpg', 'Support', 'South Korea', '2002-10-14', 1, 'Support xuất sắc của T1 với khả năng roaming, vision control và playmaking đỉnh cao.'),
('Choi Woo-je', 'Zeus', 'zeus.jpg', 'Top', 'South Korea', '2004-01-31', 1, 'Top laner trẻ tài năng của T1 với mechanics ấn tượng và khả năng carry team.'),
('Lee Min-hyeong', 'Gumayusi', 'gumayusi.jpg', 'ADC', 'South Korea', '2002-02-06', 1, 'ADC của T1 với khả năng positioning và damage output xuất sắc trong teamfight.'),
('Mun Hyeon-jun', 'Oner', 'oner.jpg', 'Jungle', 'South Korea', '2002-12-24', 1, 'Jungle của T1 với lối chơi thông minh và khả năng gank hiệu quả.'),

-- Gen.G Players
('Jeong Ji-hoon', 'Chovy', 'chovy.jpg', 'Mid', 'South Korea', '2001-03-03', 2, 'Mid laner đẳng cấp thế giới với kỹ năng laning xuất sắc và khả năng farming ấn tượng.'),
('Son Si-woo', 'Lehends', 'lehends.jpg', 'Support', 'South Korea', '1999-03-02', 2, 'Support sáng tạo của Gen.G với tầm nhìn chiến thuật tốt và khả năng engage mạnh mẽ.'),
('Kim Dong-beom', 'Canyon', 'canyon.jpg', 'Jungle', 'South Korea', '2001-09-18', 2, 'Jungle xuất sắc với khả năng kiểm soát map và pathing tối ưu.'),

-- JDG Players
('Seo Jin-hyeok', 'Kanavi', 'kanavi.jpg', 'Jungle', 'South Korea', '2001-04-28', 3, 'Jungle mạnh mẽ của JDG với khả năng gank hiệu quả và teamfight control.'),
('Park Jae-hyuk', 'Ruler', 'ruler.jpg', 'ADC', 'South Korea', '1998-12-29', 3, 'ADC hàng đầu thế giới với positioning hoàn hảo và kỹ năng teamfight xuất sắc.'),
('Bai Jia-Hao', '369', '369.jpg', 'Top', 'China', '1999-08-27', 3, 'Top laner mạnh mẽ của JDG với khả năng carry và tank tùy theo meta.'),

-- Cloud9 Players
('Yiliang Peng', 'Doublelift', 'doublelift.jpg', 'ADC', 'United States', '1993-07-19', 4, 'ADC huyền thoại của Bắc Mỹ (đã giải nghệ), là biểu tượng và truyền cảm hứng cho nhiều tuyển thủ.'),
('Robert Huang', 'Blaber', 'blaber.jpg', 'Jungle', 'United States', '2000-01-16', 4, 'Jungle năng động với lối chơi aggressive và khả năng invade xuất sắc.'),
('Ibrahim Allami', 'Fudge', 'fudge.jpg', 'Top', 'Australia', '2000-07-17', 4, 'Top laner linh hoạt có thể chơi nhiều champion và style khác nhau.'),

-- G2 Esports Players
('Rasmus Borregaard', 'Caps', 'caps.jpg', 'Mid', 'Denmark', '1999-11-17', 5, 'Mid laner tài năng của G2 với lối chơi unpredictable và aggressive, được biết đến với nickname "Baby Faker".'),
('Mihael Mehle', 'Mikyx', 'mikyx.jpg', 'Support', 'Slovenia', '1999-09-02', 5, 'Support của G2 với khả năng playmaking tuyệt vời và mechanics cao.'),
('Martin Larsson', 'Rekkles', 'rekkles.jpg', 'ADC', 'Sweden', '1996-09-20', 5, 'ADC kỳ cựu với phong cách chơi ổn định, đẳng cấp và an toàn.');

-- ========================================
-- SAMPLE DATA - Giải đấu (Tournaments)
-- ========================================
INSERT INTO giai_dau (ten_giai, game, thoi_gian_bat_dau, thoi_gian_ket_thuc, dia_diem, giai_thuong, mo_ta) VALUES
('World Championship 2023', 'League of Legends', '2023-10-10', '2023-11-19', 'Seoul, South Korea', '$2,250,000', 'Giải đấu lớn nhất năm của League of Legends với sự tham gia của các đội mạnh nhất từ khắp nơi trên thế giới. Đây là giải đấu uy tín nhất và có giải thưởng cao nhất trong năm.'),
('MSI 2024', 'League of Legends', '2024-05-01', '2024-05-19', 'Shanghai, China', '$1,000,000', 'Mid-Season Invitational - giải đấu quốc tế giữa mùa giải quy tụ các đội vô địch từ các khu vực lớn trên thế giới.'),
('The International 2023', 'Dota 2', '2023-10-12', '2023-10-29', 'Seattle, USA', '$40,000,000', 'Giải đấu Dota 2 lớn nhất với giải thưởng khổng lồ, được tài trợ bởi cộng đồng thông qua Battle Pass.'),
('LCK Spring 2024', 'League of Legends', '2024-01-10', '2024-04-20', 'Seoul, South Korea', '$200,000', 'Giải vô địch mùa xuân Hàn Quốc - một trong những giải đấu khu vực mạnh nhất thế giới.'),
('IEM Katowice 2024', 'CS:GO', '2024-02-01', '2024-02-15', 'Katowice, Poland', '$1,000,000', 'Intel Extreme Masters - giải đấu CS:GO lâu đời và uy tín nhất thế giới.');

-- ========================================
-- SAMPLE DATA - Đội tham gia giải đấu
-- ========================================
INSERT INTO doi_tham_gia (id_giai_dau, id_doi_tuyen, thu_hang) VALUES
-- World Championship 2023
(1, 1, 1),  -- T1 vô địch Worlds 2023
(1, 2, 3),  -- Gen.G top 3
(1, 3, 2),  -- JDG á quân Worlds 2023
(1, 4, 8),  -- Cloud9 top 8
(1, 5, 5),  -- G2 top 5

-- MSI 2024
(2, 2, 1),  -- Gen.G vô địch MSI 2024
(2, 1, 4),  -- T1 top 4
(2, 5, 3),  -- G2 top 3
(2, 3, 2),  -- JDG á quân

-- LCK Spring 2024
(4, 1, 1),  -- T1 vô địch LCK Spring
(4, 2, 2);  -- Gen.G á quân LCK Spring

-- ========================================
-- SAMPLE DATA - Lịch sử chuyển đội
-- ========================================
INSERT INTO lich_su_chuyen_doi (id_tuyen_thu, id_doi_cu, id_doi_moi, ngay_chuyen, ghi_chu) VALUES
(10, 2, 3, '2022-11-20', 'Ruler chuyển từ Gen.G sang JDG với mức phí chuyển nhượng cao, tìm kiếm thử thách mới tại LPL.'),
(16, 4, 5, '2023-11-15', 'Rekkles chuyển từ Cloud9 sang G2 Esports để quay trở lại châu Âu và tái hợp với đội bóng cũ.'),
(8, NULL, 2, '2021-11-25', 'Canyon gia nhập Gen.G làm tuyển thủ đầu tiên của anh trong sự nghiệp chuyên nghiệp.');

-- ========================================
-- DATABASE OPTIMIZATION NOTES
-- ========================================
-- 1. All tables use InnoDB engine for transaction support and foreign key constraints
-- 2. Indexes are created on frequently queried columns for better performance
-- 3. ON DELETE CASCADE: When parent record is deleted, child records are automatically deleted
-- 4. ON DELETE SET NULL: When parent record is deleted, foreign key in child is set to NULL
-- 5. utf8mb4 charset supports full Unicode including emojis
-- 6. All datetime fields use CURRENT_TIMESTAMP for automatic timestamping

-- ========================================
-- SECURITY NOTES
-- ========================================
-- 1. Passwords are hashed using bcrypt (password_hash() function in PHP)
-- 2. Never store plain text passwords
-- 3. google_id column allows NULL for users who don't use Google OAuth
-- 4. Email and google_id must be unique to prevent duplicate accounts
-- 5. Default role is 'user' with minimal privileges
-- 6. Admin accounts should always use strong passwords in production

-- ========================================
-- END OF SCHEMA
-- ========================================
