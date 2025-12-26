# 🎮 GamerWiki - Hệ thống quản lý đội tuyển Esport

[![PHP Version](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> **Tác giả**: NGUYỄN QUỐC TIẾN - DH52201555  
> **Đề tài**: Xây dựng hệ thống quản lý thông tin đội tuyển Esport  
> **Lấy cảm hứng từ**: [Liquipedia](https://liquipedia.net/)

---

## 📋 Mục lục

- [Giới thiệu](#-giới-thiệu)
- [Tính năng](#-tính-năng)
- [Công nghệ sử dụng](#️-công-nghệ-sử-dụng)
- [Yêu cầu hệ thống](#-yêu-cầu-hệ-thống)
- [Cài đặt](#-cài-đặt)
- [Cấu trúc thư mục](#-cấu-trúc-thư-mục)
- [Database Schema](#️-database-schema)
- [Tài khoản mặc định](#-tài-khoản-mặc-định)
- [Hướng dẫn sử dụng](#-hướng-dẫn-sử-dụng)
- [Bảo mật](#-bảo-mật)
- [Screenshots](#-screenshots)
- [Documentation](#-documentation)
- [License](#-license)
- [Liên hệ](#-liên-hệ)

---

## 📖 Giới thiệu

**GamerWiki** là một hệ thống quản lý đội tuyển esport chuyên nghiệp, được xây dựng với mục tiêu cung cấp một nền tảng tổ chức và tra cứu thông tin về:

- 🏆 **Đội tuyển Esport**: Quản lý thông tin các đội tuyển chuyên nghiệp
- 👤 **Tuyển thủ**: Hồ sơ chi tiết các game thủ, lịch sử chuyển đội
- 🎯 **Giải đấu**: Thông tin các giải đấu lớn nhỏ và kết quả thi đấu
- 📊 **Thống kê**: Phân tích thành tích, xếp hạng

### 🎯 Mục tiêu của đề tài

1. **Tổ chức thông tin**: Tập trung hóa dữ liệu về đội tuyển, tuyển thủ và giải đấu esport
2. **Dễ dàng tra cứu**: Giao diện thân thiện, tìm kiếm nhanh chóng
3. **Quản lý phân quyền**: Hệ thống 3 cấp độ quyền (admin, customer, user)
4. **Bảo mật cao**: Áp dụng các chuẩn bảo mật hiện đại (OAuth 2.0, prepared statements, password hashing)

### 🔍 Phân tích cơ sở

- **Nhu cầu thực tế**: Cộng đồng esport Việt Nam đang phát triển mạnh mẽ nhưng thiếu nền tảng quản lý thông tin chuyên nghiệp
- **Tham khảo**: Liquipedia (quốc tế) - nền tảng wiki lớn nhất về esports
- **Giải pháp**: Xây dựng hệ thống tương tự với tính năng CRUD đầy đủ, hỗ trợ tiếng Việt và Google OAuth

---

## ✨ Tính năng

### 🔐 Hệ thống xác thực & phân quyền

- ✅ **Đăng nhập/Đăng ký truyền thống**: Username + Password với bcrypt hashing
- ✅ **Google OAuth 2.0**: Đăng nhập nhanh bằng tài khoản Google
- ✅ **3 cấp độ quyền**:
  - **Admin**: Toàn quyền CRUD + Quản lý tài khoản người dùng
  - **Customer**: CRUD đội tuyển, tuyển thủ, giải đấu
  - **User**: Chỉ xem thông tin (Read-only)
- ✅ **Session Management**: Bảo mật session với HttpOnly cookies

### 👥 Quản lý Đội tuyển

- ➕ Thêm/Sửa/Xóa đội tuyển
- 🏷️ Thông tin đầy đủ: Tên, Logo, Quốc gia, Năm thành lập, Thành tích, Mô tả
- 📋 Danh sách tuyển thủ trong đội
- 📊 Lịch sử tham gia giải đấu và kết quả
- 🔍 Tìm kiếm và lọc theo quốc gia

### 🎮 Quản lý Tuyển thủ

- ➕ Thêm/Sửa/Xóa tuyển thủ
- 🏷️ Thông tin: Tên thật, Nickname/IGN, Vai trò (Top, Mid, ADC...), Quốc tịch, Ngày sinh
- 🖼️ Upload avatar/ảnh đại diện
- 🔄 Gán tuyển thủ vào đội tuyển
- 📜 Lịch sử chuyển đội (transfer history)
- 🔍 Tìm kiếm theo nickname, đội, vai trò

### 🏆 Quản lý Giải đấu

- ➕ Thêm/Sửa/Xóa giải đấu
- 🏷️ Thông tin: Tên giải, Game (LoL, Dota 2, CS:GO...), Thời gian, Địa điểm, Giải thưởng
- 👥 Quản lý đội tham gia
- 🥇 Ghi nhận kết quả và xếp hạng
- 📊 Thống kê thành tích các đội

### 🛠️ Quản lý Tài khoản (Admin only)

- ➕ Thêm/Sửa/Xóa/Khóa tài khoản
- 👤 Phân quyền: admin, customer, user
- 🔒 Kích hoạt/Vô hiệu hóa tài khoản
- 🔑 Đổi mật khẩu (hash lại với bcrypt)

### 🎨 Giao diện & UX

- 📱 **Responsive Design**: Tương thích mọi thiết bị (mobile, tablet, desktop)
- 🎨 **Bootstrap 5**: Giao diện hiện đại, professional
- 🔵 **Theme xanh dương**: Tương tự Liquipedia
- ⚡ **Fast Loading**: Tối ưu performance
- 🔍 **Search & Filter**: Tìm kiếm và lọc nhanh chóng

---

## 🛠️ Công nghệ sử dụng

### Backend

- **PHP 8.3+**: Server-side scripting language
- **MySQL 5.7+**: Relational database với InnoDB engine
- **PDO**: PHP Data Objects cho database abstraction
- **Composer**: Dependency management
- **Google API Client**: OAuth 2.0 integration

### Frontend

- **Bootstrap 5.3**: CSS framework
- **Bootstrap Icons**: Icon library
- **JavaScript**: Client-side interactivity
- **HTML5 & CSS3**: Markup và styling

### Security

- **Password Hashing**: bcrypt với `password_hash()`
- **Prepared Statements**: SQL injection prevention
- **XSS Prevention**: HTML escaping với `htmlspecialchars()`
- **CSRF Protection**: Token validation
- **Session Security**: HttpOnly và Secure cookies
- **Google OAuth 2.0**: Trusted authentication provider

### Development Tools

- **Git**: Version control
- **WampServer / XAMPP**: Local development environment
- **phpMyAdmin**: Database management

---

## 💻 Yêu cầu hệ thống

### Server Requirements

- **PHP**: 7.4 hoặc cao hơn (khuyến nghị PHP 8.3+)
- **MySQL**: 5.7 hoặc cao hơn
- **Apache**: 2.4+ với mod_rewrite enabled
- **Composer**: Latest version
- **SSL Certificate**: Khuyến nghị cho production (Google OAuth yêu cầu HTTPS)

### PHP Extensions Required

- `php-pdo`
- `php-pdo_mysql`
- `php-mbstring`
- `php-json`
- `php-curl`
- `php-openssl`

### Browser Support

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Edge 90+
- ✅ Safari 14+
- ✅ Opera 76+

---

## 📦 Cài đặt

### Bước 1: Clone hoặc tải project

```bash
git clone https://github.com/kazuha133/GamerWiki.git
cd GamerWiki
```

### Bước 2: Install dependencies với Composer

```bash
composer install
```

**Lưu ý**: Nếu chưa cài Composer, tải tại [getcomposer.org](https://getcomposer.org/)

### Bước 3: Cấu hình WampServer/XAMPP

#### Với WampServer:

1. Khởi động WampServer
2. Copy thư mục project vào `C:\wamp64\www\GamerWiki`
3. Truy cập phpMyAdmin: `http://localhost/phpmyadmin`

#### Với XAMPP:

1. Khởi động XAMPP Control Panel
2. Start Apache và MySQL
3. Copy thư mục project vào `C:\xampp\htdocs\GamerWiki`
4. Truy cập phpMyAdmin: `http://localhost/phpmyadmin`

### Bước 4: Tạo database

#### Option 1: Import qua phpMyAdmin

1. Mở phpMyAdmin
2. Click **"New"** để tạo database mới, đặt tên `gamerwiki`
3. Click vào database vừa tạo
4. Chọn tab **"Import"**
5. Chọn file `database/gamerwiki_complete.sql`
6. Click **"Go"**

#### Option 2: Command line

```bash
mysql -u root -p
CREATE DATABASE gamerwiki CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamerwiki;
SOURCE database/gamerwiki_complete.sql;
```

### Bước 5: Cấu hình database connection

Mở file `config/database.php` và cập nhật thông tin kết nối:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Username MySQL
define('DB_PASS', '');              // Password MySQL (thường để trống với WAMP/XAMPP)
define('DB_NAME', 'gamerwiki');     // Tên database
```

### Bước 6: Cấu hình Google OAuth (Optional)

Nếu muốn sử dụng tính năng đăng nhập bằng Google:

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Tạo project mới hoặc chọn project hiện có
3. Enable **Google+ API**
4. Tạo **OAuth 2.0 Client ID**:
   - Application type: **Web application**
   - Authorized redirect URIs: `http://localhost/GamerWiki/auth/google_callback.php`
5. Copy **Client ID** và **Client Secret**
6. Mở `config/google_config.php` và cập nhật:

```php
define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID_HERE.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI', 'http://localhost/GamerWiki/auth/google_callback.php');
```

**Lưu ý về SSL**: Google OAuth yêu cầu HTTPS trong production. Để test local:
- Sử dụng `http://localhost` (được Google cho phép)
- Hoặc setup SSL certificate cho local development

### Bước 7: Cấu hình permissions (Linux/Mac)

```bash
chmod -R 755 GamerWiki/
chmod -R 777 GamerWiki/uploads/
```

### Bước 8: Truy cập website

Mở trình duyệt và truy cập:

```
http://localhost/GamerWiki
```

---

## 📁 Cấu trúc thư mục

```
GamerWiki/
│
├── 📂 admin/                     # Module quản trị (Admin/Customer only)
│   ├── 📄 index.php              # Dashboard với thống kê
│   ├── 📄 doi_tuyen.php          # CRUD Đội tuyển
│   ├── 📄 tuyen_thu.php          # CRUD Tuyển thủ
│   ├── 📄 giai_dau.php           # CRUD Giải đấu
│   └── 📄 tai_khoan.php          # Quản lý tài khoản (Admin only)
│
├── 📂 auth/                      # Authentication module
│   ├── 📄 login.php              # Đăng nhập (traditional + Google OAuth)
│   ├── 📄 register.php           # Đăng ký tài khoản
│   ├── 📄 logout.php             # Đăng xuất
│   └── 📄 google_callback.php    # Google OAuth callback handler
│
├── 📂 config/                    # Configuration files
│   ├── 📄 config.php             # App constants (BASE_URL, UPLOAD_DIR...)
│   ├── 📄 database.php           # PDO database connection
│   └── 📄 google_config.php      # Google OAuth credentials
│
├── 📂 includes/                  # Shared components
│   ├── 📄 header.php             # HTML head, navbar, session check
│   ├── 📄 footer.php             # HTML footer, scripts
│   ├── 📄 navbar.php             # Navigation bar với phân quyền
│   ├── 📄 functions.php          # Helper functions
│   └── 📄 upload_handler.php     # File upload utility
│
├── 📂 pages/                     # Public pages (user view)
│   ├── 📄 doi_tuyen.php          # Danh sách đội tuyển
│   ├── 📄 chi_tiet_dt.php        # Chi tiết đội tuyển
│   ├── 📄 tuyen_thu.php          # Danh sách tuyển thủ
│   ├── 📄 chi_tiet_tt.php        # Chi tiết tuyển thủ
│   ├── 📄 giai_dau.php           # Danh sách giải đấu
│   └── 📄 chi_tiet_gd.php        # Chi tiết giải đấu
│
├── 📂 assets/                    # Static resources
│   ├── 📂 css/                   # Custom stylesheets
│   │   └── 📄 style.css
│   ├── 📂 js/                    # JavaScript files
│   │   └── 📄 main.js
│   └── 📂 img/                   # Static images
│       └── 📄 logo.png
│
├── 📂 database/                  # SQL schema files
│   ├── 📄 gamerwiki_complete.sql # Schema đầy đủ với sample data
│   ├── 📄 gamerwiki.sql          # Schema cơ bản
│   ├── 📄 add_google_id.sql      # Migration: Add Google OAuth support
│   └── 📄 add_customer_role.sql  # Migration: Add customer role
│
├── 📂 docs/                      # Documentation
│   ├── 📄 CODE_EXPLANATION.md    # Giải thích code chi tiết
│   ├── 📄 GOOGLE_LOGIN_SETUP.md  # Hướng dẫn setup Google OAuth
│   └── 📄 *.pdf                  # Báo cáo đồ án
│
├── 📂 uploads/                   # User uploaded files
│   ├── 📂 logos/                 # Team logos
│   ├── 📂 avatars/               # Player avatars
│   └── 📄 .gitkeep
│
├── 📂 vendor/                    # Composer dependencies (gitignored)
│
├── 📄 index.php                  # Homepage với dashboard
├── 📄 composer.json              # Composer dependencies
├── 📄 composer.lock              # Composer lock file
├── 📄 .gitignore                 # Git ignore rules
├── 📄 .htaccess                  # Apache configuration
├── 📄 LICENSE                    # MIT License
└── 📄 README.md                  # This file
```

---

## 🗄️ Database Schema

### Entity Relationship Diagram (ERD)

```
┌─────────────────┐
│   nguoi_dung    │ (Users - Authentication & Authorization)
└────────┬────────┘
         │
         │ manages
         ↓
┌─────────────────┐
│   doi_tuyen     │ (Teams)
└────────┬────────┘
         │
         ├─────→ ┌─────────────────┐
         │       │   tuyen_thu     │ (Players)
         │       └────────┬────────┘
         │                │
         │                │ has history
         │                ↓
         │       ┌──────────────────────┐
         │       │ lich_su_chuyen_doi   │ (Transfer History)
         │       └──────────────────────┘
         │
         │ participates in
         ↓
┌──────────────────┐
│  doi_tham_gia   │ (Tournament Participants)
└────────┬─────────┘
         │ belongs to
         ↓
┌─────────────────┐
│   giai_dau      │ (Tournaments)
└─────────────────┘
```

### Bảng chính

| Bảng | Mô tả | Số cột |
|------|-------|--------|
| **nguoi_dung** | Quản lý tài khoản với Google OAuth support | 8 |
| **doi_tuyen** | Thông tin đội tuyển esport | 9 |
| **tuyen_thu** | Hồ sơ tuyển thủ | 10 |
| **giai_dau** | Giải đấu và tournament | 9 |
| **doi_tham_gia** | Đội tham gia giải + kết quả | 5 |
| **lich_su_chuyen_doi** | Lịch sử chuyển đội của tuyển thủ | 6 |

### Key Features

- ✅ **Google OAuth Support**: Cột `google_id` trong bảng `nguoi_dung`
- ✅ **Foreign Keys**: Đầy đủ constraints với ON DELETE CASCADE/SET NULL
- ✅ **Indexes**: Tối ưu performance cho search và filter
- ✅ **UTF8MB4**: Hỗ trợ emoji và ký tự đặc biệt
- ✅ **InnoDB Engine**: Transaction support

Chi tiết schema xem trong file `database/gamerwiki_complete.sql`

---

## 👤 Tài khoản mặc định

### 🔴 Admin Account

```
Username: admin
Password: admin123
```

**Quyền hạn**:
- ✅ Toàn quyền CRUD tất cả module
- ✅ Quản lý tài khoản người dùng
- ✅ Phân quyền admin/customer/user
- ✅ Xóa và khóa tài khoản

### 🟡 Customer Account

```
Username: customer
Password: customer123
```

**Quyền hạn**:
- ✅ CRUD đội tuyển, tuyển thủ, giải đấu
- ❌ Không thể quản lý tài khoản

### 🟢 User Account

```
Username: user
Password: user123
```

**Quyền hạn**:
- ✅ Xem thông tin (Read-only)
- ❌ Không thể thêm/sửa/xóa

---

## ⚠️ LƯU Ý BẢO MẬT QUAN TRỌNG

### 🔒 Security Best Practices

1. **ĐỔI MẬT KHẨU NGAY**: Mật khẩu mặc định CHỈ dùng cho môi trường phát triển/test
2. **KHÔNG sử dụng production**: KHÔNG BAO GIỜ dùng mật khẩu mặc định trong môi trường production
3. **Xóa tài khoản test**: Nên xóa hoặc vô hiệu hóa các tài khoản test sau khi deploy
4. **Tạo admin mới**: Tạo tài khoản admin mới với mật khẩu mạnh (12+ ký tự, chữ hoa, chữ thường, số, ký tự đặc biệt)
5. **Enable HTTPS**: Luôn sử dụng SSL/TLS cho production
6. **Update dependencies**: Thường xuyên cập nhật Composer packages

---

## 🚀 Hướng dẫn sử dụng

### Đăng nhập

1. Truy cập `http://localhost/GamerWiki/auth/login.php`
2. **Option 1**: Nhập username và password → Click "Đăng nhập"
3. **Option 2**: Click nút "Đăng nhập bằng Google" → Chọn tài khoản Google

### Quản lý đội tuyển (Admin/Customer)

1. Đăng nhập với tài khoản Admin hoặc Customer
2. Click menu **"Quản trị" → "Quản lý đội tuyển"**
3. Click **"Thêm mới"**
4. Điền thông tin:
   - Tên đội (bắt buộc)
   - Upload logo (JPG, PNG, GIF - max 5MB)
   - Quốc gia
   - Năm thành lập
   - Thành tích
   - Mô tả
5. Click **"Thêm mới"** hoặc **"Cập nhật"**

### Quản lý tuyển thủ (Admin/Customer)

1. Vào **"Quản trị" → "Quản lý tuyển thủ"**
2. Click **"Thêm mới"**
3. Điền thông tin:
   - Tên thật, Nickname (bắt buộc)
   - Upload avatar
   - Chọn đội tuyển từ dropdown
   - Vai trò (Top, Mid, ADC, Support, Jungle...)
   - Quốc tịch, Ngày sinh
4. Click **"Thêm mới"**

### Xem thông tin (User)

1. Truy cập trang chủ
2. Click vào menu:
   - **"Đội tuyển"**: Xem danh sách và chi tiết đội
   - **"Tuyển thủ"**: Xem hồ sơ tuyển thủ
   - **"Giải đấu"**: Xem thông tin giải đấu
3. Click **"Xem chi tiết"** để xem thông tin đầy đủ

### Quản lý tài khoản (Admin only)

1. Vào **"Quản trị" → "Quản lý tài khoản"**
2. Xem danh sách tất cả tài khoản
3. **Thêm mới**: Tạo tài khoản và phân quyền
4. **Sửa**: Đổi thông tin, phân quyền
5. **Khóa**: Đặt trạng thái `inactive`
6. **Xóa**: Xóa tài khoản (không thể xóa chính mình)

---

## 🔒 Bảo mật

### Implemented Security Measures

| Security Feature | Implementation | Status |
|------------------|----------------|--------|
| **SQL Injection Prevention** | PDO Prepared Statements | ✅ |
| **XSS Prevention** | `htmlspecialchars()` | ✅ |
| **CSRF Protection** | Token validation | ✅ |
| **Password Security** | bcrypt hashing với `password_hash()` | ✅ |
| **Session Security** | HttpOnly cookies, regenerate ID after login | ✅ |
| **Google OAuth 2.0** | Trusted authentication provider | ✅ |
| **File Upload Security** | Extension & size validation, unique filenames | ✅ |
| **Input Sanitization** | `strip_tags()`, `trim()` | ✅ |
| **HTTPS Support** | SSL/TLS ready (production) | ✅ |

### Security Code Examples

#### SQL Injection Prevention

```php
// ❌ BAD - Vulnerable
$query = "SELECT * FROM nguoi_dung WHERE ten_dang_nhap = '$username'";

// ✅ GOOD - Prepared Statement
$stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE ten_dang_nhap = ?");
$stmt->execute([$username]);
```

#### XSS Prevention

```php
// ❌ BAD - Vulnerable
echo "<h1>" . $_GET['name'] . "</h1>";

// ✅ GOOD - HTML Escaping
echo "<h1>" . escape_html($_GET['name']) . "</h1>";
```

#### Password Hashing

```php
// ✅ GOOD - Bcrypt
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Verify
if (password_verify($input_password, $stored_hash)) {
    // Login success
}
```

---

## 📸 Screenshots

### Homepage & Dashboard

![Homepage](https://via.placeholder.com/800x400.png?text=GamerWiki+Homepage)

*Trang chủ với danh sách đội tuyển, tuyển thủ nổi bật*

### Login Page

![Login](https://via.placeholder.com/600x400.png?text=Login+Page+with+Google+OAuth)

*Đăng nhập với Google OAuth 2.0*

### Admin Dashboard

![Admin](https://via.placeholder.com/800x400.png?text=Admin+Dashboard)

*Quản lý đội tuyển, tuyển thủ, giải đấu*

### Team Detail

![Team Detail](https://via.placeholder.com/800x400.png?text=Team+Detail+Page)

*Chi tiết đội tuyển với danh sách tuyển thủ*

---

## 📚 Documentation

### Available Documentation

- 📄 **[CODE_EXPLANATION.md](docs/CODE_EXPLANATION.md)**: Giải thích chi tiết code, architecture, security
- 📄 **[GOOGLE_LOGIN_SETUP.md](docs/GOOGLE_LOGIN_SETUP.md)**: Hướng dẫn setup Google OAuth 2.0
- 📄 **[Database Schema](database/gamerwiki_complete.sql)**: SQL schema đầy đủ với comments

### Code Documentation

Code được document với:
- **PHPDoc comments** cho functions
- **Inline comments** giải thích logic phức tạp
- **README comments** trong SQL files

---

## 🐛 Xử lý lỗi thường gặp

### Lỗi kết nối database

**Triệu chứng**: `Database connection failed`

**Giải pháp**:
1. Kiểm tra WampServer/XAMPP đã chạy chưa
2. Verify thông tin trong `config/database.php`
3. Đảm bảo database `gamerwiki` đã được import
4. Check MySQL service status

### Lỗi 404 Not Found

**Triệu chứng**: Page not found khi click vào link

**Giải pháp**:
1. Kiểm tra đường dẫn file
2. Enable Apache `mod_rewrite`: `a2enmod rewrite` (Linux) hoặc check `httpd.conf` (Windows)
3. Verify `.htaccess` file tồn tại

### Lỗi upload file

**Triệu chứng**: Cannot upload logo/avatar

**Giải pháp**:
1. Kiểm tra quyền ghi thư mục `uploads/` (chmod 777)
2. Check PHP config:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```
3. Restart Apache sau khi đổi config

### Lỗi Google OAuth

**Triệu chứng**: Redirect mismatch error

**Giải pháp**:
1. Check redirect URI trong Google Cloud Console
2. Verify `GOOGLE_REDIRECT_URI` trong `config/google_config.php`
3. Đảm bảo URL khớp chính xác (http vs https, trailing slash...)

### Lỗi Composer

**Triệu chứng**: `Class 'Google_Client' not found`

**Giải pháp**:
```bash
composer install
# hoặc
composer update
```

---

## 🤝 Đóng góp

Mọi đóng góp đều được hoan nghênh! Để contribute:

1. **Fork** project
2. Tạo branch mới: `git checkout -b feature/AmazingFeature`
3. Commit changes: `git commit -m 'Add some AmazingFeature'`
4. Push to branch: `git push origin feature/AmazingFeature`
5. Tạo **Pull Request**

### Coding Standards

- **PSR-12** coding style
- **PHPDoc** comments cho functions
- **Meaningful** variable names (tiếng Việt không dấu)
- **Security first**: Always sanitize input, use prepared statements

---

## 📄 License

This project is licensed under the **MIT License**.

```
MIT License

Copyright (c) 2024 NGUYỄN QUỐC TIẾN - DH52201555

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

Xem file [LICENSE](LICENSE) để biết thêm chi tiết.

---

## 📧 Liên hệ

**Tác giả**: NGUYỄN QUỐC TIẾN  
**MSSV**: DH52201555  
**Đề tài**: Xây dựng hệ thống quản lý thông tin đội tuyển Esport

- 🐙 **GitHub**: [@kazuha133](https://github.com/kazuha133)
- 🔗 **Project Link**: [https://github.com/kazuha133/GamerWiki](https://github.com/kazuha133/GamerWiki)
- 📧 **Email**: [Contact via GitHub](https://github.com/kazuha133)

---

## 🙏 Acknowledgments

- **Liquipedia**: Lấy cảm hứng về giao diện và cấu trúc
- **Bootstrap Team**: Framework CSS tuyệt vời
- **Google**: OAuth 2.0 authentication
- **PHP Community**: Tài liệu và best practices
- **Stack Overflow**: Giải đáp các vấn đề kỹ thuật

---

## 🎓 Academic Information

**Đề tài đồ án**: Xây dựng hệ thống quản lý thông tin đội tuyển Esport  
**Sinh viên thực hiện**: NGUYỄN QUỐC TIẾN  
**Mã số sinh viên**: DH52201555  
**Lớp**: [Tên lớp]  
**Khoa**: [Tên khoa]  
**Trường**: [Tên trường]  
**Năm học**: 2024

---

<div align="center">

**⭐ Nếu thấy project hữu ích, hãy cho một Star! ⭐**

Made with ❤️ by NGUYỄN QUỐC TIẾN - DH52201555

</div>
