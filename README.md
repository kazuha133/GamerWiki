# GamerWiki - Hệ thống Quản lý Đội tuyển Esport

GamerWiki là một hệ thống quản lý đội tuyển esport chuyên nghiệp, lấy cảm hứng từ Liquipedia. Website cho phép quản lý đội tuyển, tuyển thủ và giải đấu với giao diện hiện đại và thân thiện.

## 🎮 Tính năng chính

### Quản lý Đội tuyển
- Tạo và quản lý thông tin đội tuyển (tên, logo, game, khu vực, v.v.)
- Danh sách tuyển thủ trong đội
- Lịch sử giải đấu và thành tích
- Filter và search nâng cao

### Quản lý Tuyển thủ
- Hồ sơ chi tiết tuyển thủ (nickname, tên thật, vị trí, quốc tịch)
- Ảnh đại diện và tiểu sử
- Liên kết với đội tuyển
- Filter theo đội, game, quốc tịch

### Quản lý Giải đấu
- Thông tin giải đấu (tên, game, thời gian, địa điểm)
- Prize pool
- Danh sách đội tham gia và kết quả
- Filter theo trạng thái (ongoing, upcoming, past)

### Phân quyền
- **Admin**: Toàn quyền quản lý hệ thống
- **User**: Tạo và quản lý đội tuyển của riêng mình
- **Guest**: Chỉ xem thông tin công khai

### Tính năng khác
- Trang chủ với featured teams, tournaments, players
- Tìm kiếm toàn hệ thống
- User profile và change password
- Admin dashboard với thống kê
- Responsive design (mobile-friendly)
- Dark theme gaming style

## 🚀 Cài đặt

### Yêu cầu hệ thống
- WampServer 3.4.0 64bit (hoặc XAMPP/LAMP tương tự)
- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Web browser hiện đại (Chrome, Firefox, Edge)

### Các bước cài đặt

#### 1. Download và giải nén
```bash
git clone https://github.com/kazuha133/GamerWiki.git
```

Hoặc download ZIP và giải nén vào thư mục `C:\wamp64\www\GamerWiki\`

#### 2. Import Database

1. Start WampServer
2. Truy cập phpMyAdmin: `http://localhost/phpmyadmin`
3. Tạo database mới tên `gamerwiki` (hoặc dùng SQL trong file `database.sql`)
4. Chọn database `gamerwiki`
5. Click tab "Import"
6. Chọn file `database.sql` từ thư mục project
7. Click "Go" để import

#### 3. Cấu hình Database

File config đã được cài đặt sẵn cho WampServer mặc định:
- Host: `localhost`
- Database: `gamerwiki`
- Username: `root`
- Password: `` (để trống)

Nếu cần thay đổi, chỉnh sửa file `/config/database.php`

#### 4. Truy cập Website

Mở trình duyệt và truy cập: `http://localhost/GamerWiki`

## 👤 Tài khoản mẫu

### Admin Account
- Username: `admin`
- Password: `admin123`

### User Account
- Username: `user`
- Password: `user123`

## 📁 Cấu trúc thư mục

```
/GamerWiki
├── /admin                  # Admin panel
│   ├── index.php          # Dashboard
│   └── users.php          # User management
├── /assets                # Static assets
│   ├── /css
│   │   └── style.css      # Custom CSS
│   ├── /js
│   │   └── main.js        # JavaScript
│   └── /images
├── /auth                  # Authentication
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── /config
│   └── database.php       # DB connection
├── /includes              # Shared includes
│   ├── auth.php           # Auth functions
│   ├── functions.php      # Helper functions
│   ├── header.php         # Header template
│   └── footer.php         # Footer template
├── /teams                 # Teams management
│   ├── index.php          # List teams
│   ├── view.php           # Team details
│   ├── create.php         # Create team
│   └── edit.php           # Edit team
├── /players               # Players management
│   ├── index.php          # List players
│   ├── view.php           # Player details
│   ├── create.php         # Create player
│   └── edit.php           # Edit player
├── /tournaments           # Tournaments management
│   ├── index.php          # List tournaments
│   ├── view.php           # Tournament details
│   ├── create.php         # Create tournament
│   └── edit.php           # Edit tournament
├── index.php              # Homepage
├── search.php             # Search page
├── profile.php            # User profile
├── database.sql           # Database schema & data
└── README.md              # This file
```

## 🔒 Bảo mật

Hệ thống đã implement các biện pháp bảo mật:

- **Password Hashing**: Sử dụng `password_hash()` và `password_verify()` của PHP
- **SQL Injection Prevention**: Prepared statements với PDO
- **XSS Prevention**: `htmlspecialchars()` cho mọi output
- **CSRF Protection**: CSRF tokens cho forms quan trọng
- **Session Security**: Session regeneration sau login

## 🎨 Giao diện

- **Dark Theme**: Màu tối chủ đạo với accent màu xanh/cam
- **Responsive**: Tương thích mobile, tablet, desktop
- **Bootstrap 5**: Framework CSS hiện đại
- **Font Awesome**: Icons đẹp mắt
- **Smooth Animations**: Transitions và hover effects

## 🧪 Testing

### Checklist
- [x] Đăng nhập với admin account
- [x] Tạo/Sửa/Xóa teams, players, tournaments
- [x] Đăng nhập với user account và kiểm tra quyền
- [x] Tạo team với user account
- [x] Kiểm tra guest không thể tạo/sửa
- [x] Test search functionality
- [x] Test responsive design
- [x] Kiểm tra tất cả links và navigation

## 🐛 Troubleshooting

### Database connection error
- Kiểm tra WampServer đã chạy chưa
- Xác nhận database `gamerwiki` đã được tạo
- Kiểm tra thông tin kết nối trong `config/database.php`

### Page not found (404)
- Đảm bảo đã copy đúng vào thư mục `C:\wamp64\www\GamerWiki`
- Kiểm tra WampServer đang chạy
- Truy cập đúng URL: `http://localhost/GamerWiki`

### Cannot create team/player
- Đảm bảo đã đăng nhập
- Kiểm tra quyền của tài khoản

## 📝 License

This project is open source and available for educational purposes.

## 👨‍💻 Phát triển

Được phát triển với:
- PHP 7.4+
- MySQL
- Bootstrap 5
- Font Awesome 6
- JavaScript ES6

## 📞 Liên hệ

Repository: https://github.com/kazuha133/GamerWiki

---

Made with ❤️ for esports community