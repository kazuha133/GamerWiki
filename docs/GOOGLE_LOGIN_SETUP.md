# Google Login Setup Guide

Hướng dẫn cài đặt tính năng đăng nhập bằng Google OAuth 2.0 cho GamerWiki.

## 📋 Yêu cầu

- PHP >= 7.4
- Composer (PHP dependency manager)
- MySQL/MariaDB database
- Tài khoản Google (Google Cloud Console)

## 🔧 Bước 1: Cài đặt Composer Dependencies

1. Đảm bảo Composer đã được cài đặt trên hệ thống:
```bash
composer --version
```

2. Nếu chưa có Composer, tải và cài đặt từ: https://getcomposer.org/download/

3. Chạy lệnh cài đặt dependencies trong thư mục gốc của project:
```bash
cd /path/to/GamerWiki
composer install
```

4. Sau khi cài đặt xong, sẽ có thư mục `vendor/` chứa Google API Client Library.

## 🔐 Bước 2: Tạo Google OAuth Credentials

### 2.1. Truy cập Google Cloud Console

1. Truy cập: https://console.cloud.google.com/
2. Đăng nhập bằng tài khoản Google của bạn
3. Tạo project mới hoặc chọn project hiện có

### 2.2. Kích hoạt Google OAuth2 API

1. Vào **APIs & Services** > **Library**
2. Tìm kiếm "Google OAuth2 API" hoặc "Google Identity API"
3. Click vào và chọn **Enable**

### 2.3. Tạo OAuth 2.0 Client ID

1. Vào **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **OAuth client ID**
3. Nếu chưa có OAuth consent screen, bạn cần tạo trước:
   - Click **Configure Consent Screen**
   - Chọn **External** (cho testing) hoặc **Internal** (nếu có Google Workspace)
   - Điền thông tin:
     - App name: `GamerWiki`
     - User support email: email của bạn
     - Developer contact email: email của bạn
   - Click **Save and Continue**
   - Trong **Scopes**, click **Add or Remove Scopes** và thêm:
     - `../auth/userinfo.email`
     - `../auth/userinfo.profile`
   - Click **Save and Continue**
   - Thêm test users (nếu app ở mode Testing)
   - Click **Save and Continue**

4. Quay lại **Create OAuth client ID**:
   - Application type: **Web application**
   - Name: `GamerWiki OAuth Client`
   - **Authorized JavaScript origins**:
     ```
     http://localhost
     http://localhost:8080
     http://localhost/GamerWiki
     ```
   - **Authorized redirect URIs**:
     ```
     http://localhost/GamerWiki/auth/google_callback.php
     ```
     
     ⚠️ **Quan trọng**: Đường dẫn phải khớp chính xác với `GOOGLE_REDIRECT_URI` trong config!
     
   - Click **Create**

5. Copy **Client ID** và **Client Secret** được tạo ra

## ⚙️ Bước 3: Cấu hình Application

1. Mở file `config/google_config.php`

2. Thay thế các giá trị placeholder bằng credentials thực tế:
```php
define('GOOGLE_CLIENT_ID', 'your-actual-client-id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'your-actual-client-secret');
define('GOOGLE_REDIRECT_URI', 'http://localhost/GamerWiki/auth/google_callback.php');
```

3. **Lưu ý về REDIRECT_URI**:
   - Nếu chạy trên domain khác localhost, cập nhật URI cho phù hợp
   - Ví dụ production: `https://yourdomain.com/auth/google_callback.php`
   - Phải khớp chính xác với URI đã đăng ký trong Google Cloud Console

## 💾 Bước 4: Cập nhật Database Schema

Chạy SQL migration để thêm cột `google_id`:

```bash
mysql -u root -p gamerwiki < database/add_google_id.sql
```

Hoặc chạy trực tiếp trong MySQL:
```sql
ALTER TABLE nguoi_dung ADD COLUMN google_id VARCHAR(255) DEFAULT NULL;
ALTER TABLE nguoi_dung ADD UNIQUE INDEX idx_google_id (google_id);
```

## ✅ Bước 5: Kiểm tra

1. Truy cập trang đăng nhập: `http://localhost/GamerWiki/auth/login.php`

2. Click nút **"Đăng nhập bằng Google"**

3. Chọn tài khoản Google và cho phép quyền truy cập

4. Sau khi xác thực thành công, bạn sẽ được redirect về trang chủ và đăng nhập

## 🔍 Troubleshooting

### Lỗi: "Error 400: redirect_uri_mismatch"
- **Nguyên nhân**: Redirect URI không khớp với URI đã đăng ký
- **Giải pháp**: 
  - Kiểm tra `GOOGLE_REDIRECT_URI` trong `config/google_config.php`
  - Đảm bảo URI trong Google Cloud Console khớp chính xác (bao gồm cả http/https, domain, port, và path)

### Lỗi: "Error 401: invalid_client"
- **Nguyên nhân**: Client ID hoặc Client Secret không đúng
- **Giải pháp**: Kiểm tra lại credentials trong `config/google_config.php`

### Lỗi: "This app isn't verified"
- **Nguyên nhân**: App đang ở chế độ Testing trong Google Cloud Console
- **Giải pháp**: 
  - Để development: Click **Advanced** > **Go to [App Name] (unsafe)**
  - Để production: Submit app để verify hoặc chuyển sang Internal user type

### Lỗi: "Composer not installed"
- **Nguyên nhân**: Chưa cài đặt dependencies
- **Giải pháp**: Chạy `composer install` trong thư mục project

### Không thấy nút Google Login
- **Nguyên nhân**: 
  - Chưa cài đặt Composer dependencies
  - Có lỗi trong `google_config.php`
- **Giải pháp**: 
  - Kiểm tra thư mục `vendor/` có tồn tại không
  - Kiểm tra PHP error log để xem lỗi cụ thể

## 🔒 Bảo mật

### Production Checklist:

- [ ] Đổi `GOOGLE_REDIRECT_URI` từ localhost sang domain thực
- [ ] Cập nhật Authorized redirect URIs trong Google Cloud Console
- [ ] Bảo mật file `config/google_config.php` (không commit credentials vào Git)
- [ ] Sử dụng HTTPS cho production
- [ ] Giới hạn scope chỉ lấy email và profile (đã config sẵn)
- [ ] Regular update Google API Client library: `composer update google/apiclient`

### Best Practices:

1. **Không commit credentials**: Thêm `google_config.php` vào `.gitignore` hoặc sử dụng environment variables
2. **Sử dụng .env file**: Cân nhắc dùng package như `vlucas/phpdotenv` để quản lý credentials
3. **Error logging**: Trong production, log errors vào file thay vì hiển thị cho user
4. **Rate limiting**: Implement rate limiting cho callback endpoint để tránh abuse

## 📚 Tài liệu tham khảo

- Google OAuth 2.0 Documentation: https://developers.google.com/identity/protocols/oauth2
- Google API PHP Client: https://github.com/googleapis/google-api-php-client
- Google Cloud Console: https://console.cloud.google.com/

## 💡 Ghi chú

- User đăng nhập lần đầu bằng Google sẽ tự động tạo tài khoản với vai trò `user`
- Username được tạo tự động từ email (phần trước @)
- Nếu username đã tồn tại, sẽ thêm số vào cuối (vd: john1, john2)
- User hiện có có thể link Google account bằng cách đăng nhập Google với cùng email
- Đăng nhập thường (username/password) vẫn hoạt động bình thường
