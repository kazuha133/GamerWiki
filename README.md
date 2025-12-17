# GamerWiki - Hệ thống quản lý đội tuyển Esport

GamerWiki là một hệ thống quản lý đội tuyển esport chuyên nghiệp, lấy cảm hứng từ Liquipedia. Website cho phép quản lý thông tin về đội tuyển, tuyển thủ, và giải đấu esport.

## 🎮 Tính năng chính

### Hệ thống phân quyền
- **Admin**: Toàn quyền quản trị, CRUD đội tuyển, tuyển thủ, giải đấu, và tài khoản
- **User**: Xem thông tin, tìm kiếm và lọc dữ liệu

### Quản lý Đội tuyển
- Thêm/Sửa/Xóa đội tuyển
- Thông tin: Tên đội, Logo, Quốc gia, Năm thành lập, Thành tích
- Hiển thị danh sách tuyển thủ trong đội
- Kết quả giải đấu

### Quản lý Tuyển thủ
- Thêm/Sửa/Xóa tuyển thủ
- Thông tin: Tên thật, Nickname, Vai trò, Quốc tịch, Ngày sinh, Đội tuyển
- Lịch sử chuyển đội

### Quản lý Giải đấu
- Thêm/Sửa/Xóa giải đấu
- Thông tin: Tên giải, Game, Thời gian, Địa điểm, Giải thưởng
- Các đội tham gia và kết quả

### Quản lý Tài khoản (Admin)
- Thêm/Sửa/Xóa/Khóa tài khoản
- Phân quyền Admin/User

### Dashboard & Trang chủ
- Thống kê tổng quan
- Đội tuyển, tuyển thủ, giải đấu nổi bật
- Responsive design

## 🛠️ Yêu cầu hệ thống

- **Server**: WampServer 3.4.0 64bit (hoặc tương đương)
- **PHP**: 7.4 trở lên
- **MySQL**: 5.7 trở lên
- **Web Server**: Apache
- **Browser**: Chrome, Firefox, Edge (phiên bản mới nhất)

## 📦 Cài đặt

### Bước 1: Clone hoặc tải project

```bash
git clone https://github.com/kazuha133/GamerWiki.git
cd GamerWiki
```

### Bước 2: Cấu hình WampServer

1. Khởi động WampServer
2. Copy thư mục project vào `C:\wamp64\www\GamerWiki`
3. Truy cập phpMyAdmin: `http://localhost/phpmyadmin`

### Bước 3: Tạo database

1. Mở phpMyAdmin
2. Click "New" để tạo database mới
3. Import file `database/gamerwiki.sql`:
   - Click vào database vừa tạo
   - Chọn tab "Import"
   - Chọn file `gamerwiki.sql`
   - Click "Go"

**Hoặc** chạy lệnh SQL trực tiếp:

```bash
mysql -u root -p < database/gamerwiki.sql
```

### Bước 4: Cấu hình kết nối database

Mở file `config/database.php` và cập nhật thông tin kết nối nếu cần:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gamerwiki');
```

### Bước 5: Truy cập website

Mở trình duyệt và truy cập:

```
http://localhost/GamerWiki
```

## 👤 Tài khoản mặc định

### Admin
- **Username**: `admin`
- **Password**: `admin123`

**Lưu ý**: Nên đổi mật khẩu ngay sau khi đăng nhập lần đầu!

## 📁 Cấu trúc thư mục

```
GamerWiki/
├── admin/              # Module quản trị (chỉ Admin)
│   ├── index.php      # Dashboard
│   ├── doi_tuyen.php  # Quản lý đội tuyển
│   ├── tuyen_thu.php  # Quản lý tuyển thủ
│   ├── giai_dau.php   # Quản lý giải đấu
│   └── tai_khoan.php  # Quản lý tài khoản
├── auth/              # Xác thực
│   ├── login.php      # Đăng nhập
│   ├── register.php   # Đăng ký
│   └── logout.php     # Đăng xuất
├── config/            # Cấu hình
│   └── database.php   # Kết nối database
├── includes/          # File dùng chung
│   ├── header.php     # Header
│   ├── footer.php     # Footer
│   ├── navbar.php     # Navigation bar
│   └── functions.php  # Các hàm tiện ích
├── pages/             # Trang người dùng
│   ├── doi_tuyen.php     # Danh sách đội tuyển
│   ├── chi_tiet_dt.php   # Chi tiết đội tuyển
│   ├── tuyen_thu.php     # Danh sách tuyển thủ
│   ├── chi_tiet_tt.php   # Chi tiết tuyển thủ
│   ├── giai_dau.php      # Danh sách giải đấu
│   └── chi_tiet_gd.php   # Chi tiết giải đấu
├── assets/            # Tài nguyên
│   ├── css/          # CSS files
│   ├── js/           # JavaScript files
│   └── img/          # Hình ảnh
├── database/         # Database schema
│   └── gamerwiki.sql # File SQL
└── index.php         # Trang chủ
```

## 🔒 Bảo mật

- **Password Hashing**: Sử dụng `password_hash()` và `password_verify()`
- **SQL Injection Prevention**: Prepared Statements với PDO
- **CSRF Protection**: Token validation cho các form
- **XSS Prevention**: HTML escaping cho output
- **Session Management**: Secure session handling

## 🎨 Giao diện

- **Framework CSS**: Bootstrap 5
- **Icons**: Bootstrap Icons
- **Theme**: Màu xanh dương chủ đạo (tương tự Liquipedia)
- **Responsive**: Tương thích với mobile, tablet và desktop

## 📝 Quy tắc code

### Đặt tên
- **Functions/Variables**: Tiếng Việt không dấu
  - Ví dụ: `getDSDT()`, `themDoiTuyen()`, `$ds_doi_tuyen`
- **UI Text**: Tiếng Việt có dấu đầy đủ
  - Ví dụ: "Đội tuyển", "Tuyển thủ", "Giải đấu"

### Code Style
- Indent: 4 spaces
- Encoding: UTF-8
- Line endings: LF (Unix)

## 🗃️ Database Schema

### Bảng chính

1. **nguoi_dung**: Quản lý tài khoản người dùng
2. **doi_tuyen**: Thông tin đội tuyển
3. **tuyen_thu**: Thông tin tuyển thủ
4. **giai_dau**: Thông tin giải đấu
5. **doi_tham_gia**: Đội tham gia giải đấu
6. **lich_su_chuyen_doi**: Lịch sử chuyển đội của tuyển thủ

Chi tiết schema xem trong file `database/gamerwiki.sql`

## 🚀 Hướng dẫn sử dụng

### Đăng nhập
1. Truy cập `http://localhost/GamerWiki/auth/login.php`
2. Nhập username và password
3. Click "Đăng nhập"

### Quản lý đội tuyển (Admin)
1. Đăng nhập với tài khoản Admin
2. Vào menu "Quản trị" → "Quản lý đội tuyển"
3. Click "Thêm mới" để thêm đội tuyển
4. Điền thông tin và click "Thêm mới"

### Xem thông tin (User)
1. Truy cập trang chủ
2. Click vào menu "Đội tuyển", "Tuyển thủ" hoặc "Giải đấu"
3. Click "Xem chi tiết" để xem thông tin đầy đủ

## 🐛 Xử lý lỗi thường gặp

### Lỗi kết nối database
- Kiểm tra WampServer đã chạy chưa
- Kiểm tra thông tin trong `config/database.php`
- Đảm bảo database đã được import

### Lỗi 404 Not Found
- Kiểm tra đường dẫn file
- Đảm bảo Apache rewrite module đã bật
- Kiểm tra file .htaccess nếu có

### Lỗi upload file
- Kiểm tra quyền ghi thư mục `uploads/`
- Kiểm tra cấu hình PHP: `upload_max_filesize` và `post_max_size`

## 📄 License

MIT License - Tự do sử dụng cho mục đích học tập và phát triển.

## 👥 Đóng góp

Mọi đóng góp đều được hoan nghênh! Vui lòng:

1. Fork project
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📧 Liên hệ

- GitHub: [@kazuha133](https://github.com/kazuha133)
- Project Link: [https://github.com/kazuha133/GamerWiki](https://github.com/kazuha133/GamerWiki)

## 🙏 Acknowledgments

- Lấy cảm hứng từ [Liquipedia](https://liquipedia.net/)
- Bootstrap 5 Framework
- Bootstrap Icons