-- GamerWiki Database Schema
-- Tạo database và các bảng cho hệ thống quản lý đội tuyển esport

-- Tạo database
CREATE DATABASE IF NOT EXISTS gamerwiki CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamerwiki;

-- Bảng users - Quản lý tài khoản
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng teams - Quản lý đội tuyển
CREATE TABLE teams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    game VARCHAR(50) NOT NULL,
    region VARCHAR(50),
    founded_date DATE,
    logo_url VARCHAR(255),
    description TEXT,
    website VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_game (game),
    INDEX idx_region (region),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng players - Quản lý tuyển thủ
CREATE TABLE players (
    id INT PRIMARY KEY AUTO_INCREMENT,
    team_id INT,
    nickname VARCHAR(50) NOT NULL,
    real_name VARCHAR(100),
    role_position VARCHAR(50),
    nationality VARCHAR(50),
    birth_date DATE,
    photo_url VARCHAR(255),
    biography TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL,
    INDEX idx_team_id (team_id),
    INDEX idx_nickname (nickname),
    INDEX idx_nationality (nationality)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng tournaments - Quản lý giải đấu
CREATE TABLE tournaments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    game VARCHAR(50) NOT NULL,
    start_date DATE,
    end_date DATE,
    prize_pool DECIMAL(15,2),
    location VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_game (game),
    INDEX idx_start_date (start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng team_tournaments - Liên kết đội tuyển và giải đấu
CREATE TABLE team_tournaments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    team_id INT NOT NULL,
    tournament_id INT NOT NULL,
    placement INT,
    prize_money DECIMAL(12,2),
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    INDEX idx_team_id (team_id),
    INDEX idx_tournament_id (tournament_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu

-- Tạo tài khoản admin và user mẫu
-- Password: admin123 và user123 (đã hash bằng password_hash)
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@gamerwiki.com', 'admin'),
('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user@gamerwiki.com', 'user');

-- Thêm đội tuyển mẫu
INSERT INTO teams (name, game, region, founded_date, logo_url, description, website, created_by) VALUES
('T1', 'League of Legends', 'Korea', '2012-12-01', 'https://via.placeholder.com/150', 'T1 (trước đây là SK Telecom T1) là một trong những đội tuyển LoL thành công nhất mọi thời đại với 3 chức vô địch thế giới.', 'https://t1.gg', 1),
('Team Liquid', 'Dota 2', 'Europe', '2015-01-01', 'https://via.placeholder.com/150', 'Team Liquid là tổ chức esports hàng đầu với đội tuyển Dota 2 vô địch The International 2017.', 'https://teamliquid.com', 1),
('Sentinels', 'Valorant', 'North America', '2020-04-01', 'https://via.placeholder.com/150', 'Sentinels là đội tuyển Valorant hàng đầu thế giới, vô địch Masters Reykjavik 2021.', 'https://sentinels.gg', 1),
('Fnatic', 'League of Legends', 'Europe', '2011-07-23', 'https://via.placeholder.com/150', 'Fnatic là một trong những tổ chức esports lâu đời nhất với nhiều thành công trong League of Legends.', 'https://fnatic.com', 1),
('OpTic Gaming', 'Valorant', 'North America', '2006-01-01', 'https://via.placeholder.com/150', 'OpTic Gaming là tổ chức esports nổi tiếng với đội tuyển Valorant mạnh mẽ.', 'https://optic.gg', 1);

-- Thêm tuyển thủ mẫu
INSERT INTO players (team_id, nickname, real_name, role_position, nationality, birth_date, photo_url, biography) VALUES
(1, 'Faker', 'Lee Sang-hyeok', 'Mid Lane', 'South Korea', '1996-05-07', 'https://via.placeholder.com/200', 'Lee "Faker" Sang-hyeok được coi là tuyển thủ LoL vĩ đại nhất mọi thời đại với 3 chức vô địch thế giới.'),
(1, 'Gumayusi', 'Lee Min-hyeong', 'ADC', 'South Korea', '2002-02-06', 'https://via.placeholder.com/200', 'Gumayusi là xạ thủ tài năng của T1, nổi bật với kỹ năng cơ học xuất sắc.'),
(1, 'Keria', 'Ryu Min-seok', 'Support', 'South Korea', '2002-10-14', 'https://via.placeholder.com/200', 'Keria là support đỉnh cao với lối chơi sáng tạo và tầm nhìn chiến thuật tuyệt vời.'),
(1, 'Zeus', 'Choi Woo-je', 'Top Lane', 'South Korea', '2004-11-31', 'https://via.placeholder.com/200', 'Zeus là tuyển thủ top lane trẻ tuổi tài năng của T1.'),
(1, 'Oner', 'Moon Hyeon-joon', 'Jungle', 'South Korea', '2002-12-24', 'https://via.placeholder.com/200', 'Oner là tuyển thủ rừng xuất sắc với khả năng đọc game tốt.'),
(2, 'MATUMBAMAN', 'Lasse Urpalainen', 'Carry', 'Finland', '1995-08-03', 'https://via.placeholder.com/200', 'Matumbaman là carry huyền thoại, vô địch TI7 cùng Team Liquid.'),
(2, 'Nisha', 'Michał Jankowski', 'Mid', 'Poland', '1999-09-01', 'https://via.placeholder.com/200', 'Nisha là mid laner tài năng với cơ học xuất sắc.'),
(3, 'TenZ', 'Tyson Ngo', 'Duelist', 'Canada', '2001-05-05', 'https://via.placeholder.com/200', 'TenZ là một trong những tuyển thủ Valorant giỏi nhất thế giới với aim cực kỳ chính xác.'),
(3, 'Zekken', 'Zachary Patrone', 'Duelist', 'USA', '2005-03-05', 'https://via.placeholder.com/200', 'Zekken là duelist trẻ tuổi tài năng của Sentinels.'),
(3, 'Sacy', 'Gustavo Rossi', 'Initiator', 'Brazil', '1997-09-04', 'https://via.placeholder.com/200', 'Sacy là tuyển thủ initiator kinh nghiệm, vô địch Masters với LOUD.'),
(4, 'Rekkles', 'Martin Larsson', 'ADC', 'Sweden', '1996-09-20', 'https://via.placeholder.com/200', 'Rekkles là một trong những ADC huyền thoại của Châu Âu.'),
(4, 'Humanoid', 'Marek Brázda', 'Mid Lane', 'Czech Republic', '2000-04-02', 'https://via.placeholder.com/200', 'Humanoid là mid laner xuất sắc với khả năng carry team.'),
(5, 'yay', 'Jaccob Whiteaker', 'Duelist', 'USA', '2000-09-01', 'https://via.placeholder.com/200', 'yay là Chamber specialist và được mệnh danh là El Diablo trong cộng đồng Valorant.'),
(5, 'Marved', 'Jimmy Nguyen', 'Controller', 'USA', '2000-02-05', 'https://via.placeholder.com/200', 'Marved là controller xuất sắc với biệt danh "The Iceman".'),
(5, 'crashies', 'Austin Roberts', 'Initiator', 'USA', '1999-08-14', 'https://via.placeholder.com/200', 'crashies là initiator kinh nghiệm với game sense tuyệt vời.');

-- Thêm giải đấu mẫu
INSERT INTO tournaments (name, game, start_date, end_date, prize_pool, location, description) VALUES
('World Championship 2023', 'League of Legends', '2023-10-10', '2023-11-19', 2250000.00, 'Seoul, South Korea', 'Giải vô địch thế giới League of Legends 2023, sự kiện esports lớn nhất năm với 22 đội tuyển hàng đầu thế giới.'),
('The International 2023', 'Dota 2', '2023-10-12', '2023-10-29', 3000000.00, 'Seattle, USA', 'The International là giải đấu Dota 2 uy tín nhất với phần thưởng khổng lồ.'),
('VCT Masters Tokyo', 'Valorant', '2023-06-11', '2023-06-25', 1000000.00, 'Tokyo, Japan', 'VCT Masters Tokyo là giải đấu Valorant quốc tế với các đội tuyển mạnh nhất thế giới.'),
('LCK Spring 2024', 'League of Legends', '2024-01-10', '2024-04-14', 300000.00, 'Seoul, South Korea', 'Giải đấu mùa xuân LCK 2024 - giải đấu khu vực mạnh nhất thế giới.'),
('VCT Champions 2023', 'Valorant', '2023-08-06', '2023-08-26', 2250000.00, 'Los Angeles, USA', 'VCT Champions là giải đấu Valorant cuối năm quy tụ các đội tuyển xuất sắc nhất.');

-- Thêm kết quả giải đấu
INSERT INTO team_tournaments (team_id, tournament_id, placement, prize_money) VALUES
(1, 1, 4, 150000.00),
(1, 4, 1, 150000.00),
(2, 2, 8, 100000.00),
(3, 3, 3, 80000.00),
(3, 5, 1, 1000000.00),
(4, 1, 12, 50000.00),
(4, 4, 3, 50000.00),
(5, 3, 5, 40000.00),
(5, 5, 4, 120000.00);
