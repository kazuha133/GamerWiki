# 📚 CODE EXPLANATION - GamerWiki

> **Tác giả**: NGUYỄN QUỐC TIẾN - DH52201555  
> **Mục đích**: Tài liệu giải thích chi tiết code cho đồ án Hệ thống quản lý đội tuyển Esport

---

## 📑 Mục lục

1. [Tổng quan kiến trúc](#tổng-quan-kiến-trúc)
2. [Authentication System](#authentication-system-auth)
3. [Admin Panel](#admin-panel-admin)
4. [Configuration](#configuration-config)
5. [Helper Functions](#helper-functions-includesfunctionsphp)
6. [Database Schema](#database-schema)
7. [Security Best Practices](#security-best-practices)
8. [Request Flow](#request-flow)
9. [Code Examples](#code-examples)

---

## 🏗️ Tổng quan kiến trúc

### Pattern sử dụng
GamerWiki sử dụng **MVC-like structure** (không hoàn toàn MVC nhưng có phân tách rõ ràng):

- **Model**: Tương tác database thông qua PDO trong các file PHP
- **View**: HTML template với PHP embedded
- **Controller**: Logic xử lý trong các file `.php` chính

### Công nghệ sử dụng

| Thành phần | Công nghệ | Mục đích |
|------------|-----------|----------|
| **Backend** | PHP 7.4+ | Server-side logic |
| **Database** | MySQL 5.7+ với PDO | Data persistence |
| **Authentication** | Session-based + Google OAuth 2.0 | User authentication |
| **Frontend** | Bootstrap 5 | Responsive UI |
| **Security** | CSRF tokens, XSS prevention, Password hashing | Application security |

### Cấu trúc thư mục

```
GamerWiki/
├── admin/              # Module quản trị (chỉ Admin/Customer)
├── auth/               # Authentication (Login, Register, Logout, OAuth)
├── config/             # Configuration files
├── includes/           # Shared components (header, footer, functions)
├── pages/              # Public pages (view-only)
├── assets/             # Static assets (CSS, JS, images)
├── database/           # SQL schema files
├── uploads/            # User uploaded files (logos, avatars)
└── index.php           # Homepage
```

---

## 🔐 Authentication System (auth/)

### 1. `auth/login.php` - Đăng nhập truyền thống + Google OAuth

#### Chức năng
- Hiển thị form đăng nhập
- Xử lý đăng nhập với username/password
- Tạo OAuth URL để đăng nhập bằng Google
- Redirect sau khi đăng nhập thành công

#### Flow xử lý

```php
// 1. Kiểm tra session - nếu đã login thì redirect
khoi_tao_session();
if (kiem_tra_dang_nhap()) {
    header('Location: ../index.php');
    exit();
}

// 2. Xử lý POST request (form submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input để tránh SQL injection
    $ten_dang_nhap = sanitize_input($_POST['ten_dang_nhap'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';
    
    // Prepared statement - phòng SQL injection
    $stmt = $conn->prepare("SELECT id, mat_khau, vai_tro FROM nguoi_dung WHERE ten_dang_nhap = ?");
    $stmt->execute([$ten_dang_nhap]);
    $user = $stmt->fetch();
    
    // Verify password - sử dụng bcrypt
    if ($user && password_verify($mat_khau, $user['mat_khau'])) {
        // Regenerate session ID - phòng session fixation attack
        session_regenerate_id(true);
        
        // Lưu thông tin vào session
        $_SESSION['nguoi_dung_id'] = $user['id'];
        $_SESSION['ten_dang_nhap'] = $ten_dang_nhap;
        $_SESSION['vai_tro'] = $user['vai_tro'];
        
        // Redirect về trang chủ
        header('Location: ../index.php');
        exit();
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
    }
}

// 3. Tạo Google OAuth login URL
$client = get_google_client();
$auth_url = $client->createAuthUrl();
```

#### Security measures
- ✅ **SQL Injection Prevention**: Sử dụng prepared statements với PDO
- ✅ **Password Security**: `password_verify()` để kiểm tra password đã hash
- ✅ **Session Fixation Prevention**: `session_regenerate_id(true)` sau login
- ✅ **Input Sanitization**: `sanitize_input()` trước khi xử lý

---

### 2. `auth/google_callback.php` - Google OAuth Callback Handler

#### Chức năng
- Nhận authorization code từ Google
- Exchange code for access token
- Lấy thông tin user từ Google API
- Tạo account mới hoặc link với account hiện có (qua email)

#### Flow xử lý

```php
// 1. Nhận authorization code
if (!isset($_GET['code'])) {
    die('Error: No authorization code received');
}

// 2. Exchange code for access token
$client = get_google_client();
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token);

// 3. Get user info từ Google
$google_oauth = new Google_Service_Oauth2($client);
$google_account_info = $google_oauth->userinfo->get();

$google_id = $google_account_info->id;
$email = $google_account_info->email;
$name = $google_account_info->name;

// 4. Kiểm tra user đã tồn tại chưa (qua google_id hoặc email)
$stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE google_id = ? OR email = ?");
$stmt->execute([$google_id, $email]);
$user = $stmt->fetch();

if ($user) {
    // User đã tồn tại - update google_id nếu chưa có
    if (!$user['google_id']) {
        $stmt = $conn->prepare("UPDATE nguoi_dung SET google_id = ? WHERE id = ?");
        $stmt->execute([$google_id, $user['id']]);
    }
} else {
    // Tạo user mới
    $username = strtolower(str_replace(' ', '', $name)) . rand(100, 999);
    $random_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, google_id, vai_tro) VALUES (?, ?, ?, ?, 'user')");
    $stmt->execute([$username, $random_password, $email, $google_id]);
    
    $user_id = $conn->lastInsertId();
}

// 5. Login user (tạo session)
session_regenerate_id(true);
$_SESSION['nguoi_dung_id'] = $user['id'];
$_SESSION['ten_dang_nhap'] = $user['ten_dang_nhap'];
$_SESSION['vai_tro'] = $user['vai_tro'];
```

#### Security measures
- ✅ **Secure Token Exchange**: Sử dụng Google Client Library
- ✅ **Email Verification**: Google đã verify email
- ✅ **Random Password**: Generate random secure password cho Google users
- ✅ **Account Linking**: Link Google account với existing account qua email

---

### 3. `auth/register.php` - Đăng ký tài khoản mới

#### Chức năng
- Hiển thị form đăng ký
- Validate input (email format, password strength)
- Kiểm tra duplicate username/email
- Hash password và insert vào database

#### Flow xử lý

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Lấy và sanitize input
    $ten_dang_nhap = sanitize_input($_POST['ten_dang_nhap'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'] ?? '';
    
    // 2. Validation
    if (empty($ten_dang_nhap) || empty($email) || empty($mat_khau)) {
        $error = 'Vui lòng điền đầy đủ thông tin!';
    } elseif (!validate_email($email)) {
        $error = 'Email không hợp lệ!';
    } elseif ($mat_khau !== $xac_nhan_mat_khau) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } elseif (strlen($mat_khau) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự!';
    }
    
    // 3. Kiểm tra duplicate
    if (!$error) {
        $stmt = $conn->prepare("SELECT id FROM nguoi_dung WHERE ten_dang_nhap = ? OR email = ?");
        $stmt->execute([$ten_dang_nhap, $email]);
        if ($stmt->fetch()) {
            $error = 'Tên đăng nhập hoặc email đã tồn tại!';
        }
    }
    
    // 4. Insert user mới
    if (!$error) {
        // Hash password với bcrypt
        $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$ten_dang_nhap, $mat_khau_hash, $email]);
        
        // Auto login sau khi đăng ký
        $user_id = $conn->lastInsertId();
        $_SESSION['nguoi_dung_id'] = $user_id;
        $_SESSION['ten_dang_nhap'] = $ten_dang_nhap;
        $_SESSION['vai_tro'] = 'user';
        
        header('Location: ../index.php');
        exit();
    }
}
```

#### Security measures
- ✅ **Password Hashing**: `password_hash()` với bcrypt algorithm
- ✅ **Input Validation**: Email format, password length, required fields
- ✅ **Duplicate Check**: Prevent duplicate username/email
- ✅ **Default Role**: Mặc định là 'user' (quyền thấp nhất)

---

### 4. `auth/logout.php` - Đăng xuất

#### Chức năng
- Xóa toàn bộ session data
- Destroy session
- Redirect về trang login

```php
khoi_tao_session();

// Xóa toàn bộ session variables
session_unset();

// Destroy session
session_destroy();

// Redirect về login
header('Location: login.php');
exit();
```

---

## 👨‍💼 Admin Panel (admin/)

### Phân quyền

| Vai trò | Quyền hạn |
|---------|-----------|
| **admin** | Toàn quyền CRUD + Quản lý tài khoản |
| **customer** | CRUD đội tuyển, tuyển thủ, giải đấu |
| **user** | Chỉ xem (Read-only) |

### 1. `admin/doi_tuyen.php` - CRUD Đội tuyển

#### Chức năng
- List: Hiển thị danh sách đội tuyển với pagination
- Add: Thêm đội tuyển mới với upload logo
- Edit: Sửa thông tin đội tuyển
- Delete: Xóa đội tuyển (có kiểm tra foreign key)

#### Code example - List

```php
// Yêu cầu quyền admin hoặc customer
yeu_cau_admin_hoac_customer();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Count total
$count_stmt = $conn->query("SELECT COUNT(*) FROM doi_tuyen");
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $limit);

// Get data với limit/offset
$stmt = $conn->prepare("SELECT * FROM doi_tuyen ORDER BY ngay_tao DESC LIMIT ? OFFSET ?");
$stmt->execute([$limit, $offset]);
$doi_tuyen_list = $stmt->fetchAll();

// Display trong HTML table
foreach ($doi_tuyen_list as $doi_tuyen) {
    echo '<tr>';
    echo '<td>' . escape_html($doi_tuyen['ten_doi']) . '</td>';
    echo '<td>' . escape_html($doi_tuyen['quoc_gia']) . '</td>';
    // ... buttons Edit, Delete
    echo '</tr>';
}
```

#### Code example - Delete

```php
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    try {
        // Xóa với prepared statement
        $stmt = $conn->prepare("DELETE FROM doi_tuyen WHERE id = ?");
        $stmt->execute([$id]);
        
        $success = 'Xóa đội tuyển thành công!';
    } catch (PDOException $e) {
        // Foreign key constraint error
        if ($e->getCode() == 23000) {
            $error = 'Không thể xóa đội tuyển này vì có tuyển thủ hoặc giải đấu liên quan!';
        } else {
            $error = 'Lỗi: ' . $e->getMessage();
        }
    }
}
```

---

### 2. `admin/tuyen_thu.php` - CRUD Tuyển thủ

#### Code example - Join query với đội tuyển

```php
// Get tuyển thủ với JOIN để lấy tên đội
$stmt = $conn->query("
    SELECT t.*, d.ten_doi 
    FROM tuyen_thu t 
    LEFT JOIN doi_tuyen d ON t.id_doi_tuyen = d.id 
    ORDER BY t.ngay_tao DESC
");
$tuyen_thu_list = $stmt->fetchAll();

// Display
foreach ($tuyen_thu_list as $tuyen_thu) {
    echo '<td>' . escape_html($tuyen_thu['nickname']) . '</td>';
    echo '<td>' . escape_html($tuyen_thu['ten_doi'] ?? 'Free Agent') . '</td>';
}
```

---

### 3. `admin/tai_khoan.php` - Quản lý Tài khoản (chỉ Admin)

#### Security check

```php
// CHỈ admin mới được truy cập
yeu_cau_admin();

// Không được xóa chính mình
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id == $_SESSION['nguoi_dung_id']) {
        $error = 'Không thể xóa tài khoản của chính mình!';
    } else {
        $stmt = $conn->prepare("DELETE FROM nguoi_dung WHERE id = ?");
        $stmt->execute([$id]);
    }
}
```

---

## ⚙️ Configuration (config/)

### 1. `config/config.php` - Application Constants

```php
<?php
// Base URL - tự động detect
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', $protocol . '://' . $host . $base_path);

// Upload directory
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Helper function
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}
?>
```

---

### 2. `config/database.php` - Database Connection (PDO)

```php
<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gamerwiki');

try {
    // PDO connection với error mode exception
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
```

#### Tại sao sử dụng PDO?
- ✅ **Prepared Statements**: Phòng SQL Injection
- ✅ **Error Handling**: Exception mode dễ debug
- ✅ **Database Agnostic**: Dễ chuyển sang PostgreSQL, SQLite
- ✅ **Fetch Modes**: Linh hoạt khi lấy data

---

### 3. `config/google_config.php` - Google OAuth Setup

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Google OAuth credentials
define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', 'http://localhost/GamerWiki/auth/google_callback.php');

function get_google_client() {
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    
    $client->addScope('email');
    $client->addScope('profile');
    
    return $client;
}
?>
```

---

## ��️ Helper Functions (includes/functions.php)

### Session Management

```php
function khoi_tao_session() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', 1);
        }
        session_start();
    }
}

function kiem_tra_dang_nhap() {
    khoi_tao_session();
    return isset($_SESSION['nguoi_dung_id']) && isset($_SESSION['ten_dang_nhap']);
}
```

---

### Security Functions

```php
function escape_html($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = strip_tags($data);
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
```

---

### File Upload

```php
function upload_file($file, $thu_muc_dich, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Không có file được upload.'];
    }
    
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'File type không hợp lệ'];
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File quá lớn (max 5MB)'];
    }
    
    $new_file_name = uniqid() . '_' . time() . '.' . $file_ext;
    $destination = $thu_muc_dich . $new_file_name;
    
    if (!is_dir($thu_muc_dich)) {
        mkdir($thu_muc_dich, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'file_name' => $new_file_name];
    } else {
        return ['success' => false, 'message' => 'Lỗi khi upload'];
    }
}
```

---

## 🗄️ Database Schema

### Bảng `nguoi_dung` (Users)

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | User ID |
| `ten_dang_nhap` | VARCHAR(50) UNIQUE | Username |
| `mat_khau` | VARCHAR(255) | Hashed password (bcrypt) |
| `email` | VARCHAR(100) UNIQUE | Email |
| `google_id` | VARCHAR(255) UNIQUE | Google ID cho OAuth |
| `vai_tro` | ENUM('admin','customer','user') | Role |
| `trang_thai` | ENUM('active','inactive') | Account status |
| `ngay_tao` | DATETIME | Created timestamp |

---

### Entity Relationship Diagram (ERD)

```
nguoi_dung
    │
    └─ [Manages] ─> doi_tuyen
                        │
                        ├─ [Has] ─> tuyen_thu
                        │              │
                        │              └─ [Has history] ─> lich_su_chuyen_doi
                        │
                        └─ [Participates in] ─> doi_tham_gia <─ [Belongs to] ─ giai_dau
```

---

## 🔒 Security Best Practices

### 1. SQL Injection Prevention

**❌ BAD**:
```php
$query = "SELECT * FROM nguoi_dung WHERE ten_dang_nhap = '$username'";
```

**✅ GOOD**:
```php
$stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE ten_dang_nhap = ?");
$stmt->execute([$username]);
```

---

### 2. XSS Prevention

**❌ BAD**:
```php
echo "<h1>Welcome " . $_GET['name'] . "</h1>";
```

**✅ GOOD**:
```php
echo "<h1>Welcome " . escape_html($_GET['name']) . "</h1>";
```

---

### 3. Password Security

**✅ GOOD**:
```php
// Hash
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Verify
if (password_verify($input_password, $stored_hash)) {
    // Success
}
```

---

### 4. Session Security

```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
session_regenerate_id(true);
```

---

## 🔄 Request Flow

```
User Request → Entry Point → Header → Main Logic → HTML Output → Footer
```

---

## 💡 Code Examples

### Example: Xử lý đăng nhập

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_dang_nhap = sanitize_input($_POST['ten_dang_nhap'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';
    
    $stmt = $conn->prepare("SELECT id, mat_khau, vai_tro FROM nguoi_dung WHERE ten_dang_nhap = ?");
    $stmt->execute([$ten_dang_nhap]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($mat_khau, $user['mat_khau'])) {
        session_regenerate_id(true);
        $_SESSION['nguoi_dung_id'] = $user['id'];
        $_SESSION['vai_tro'] = $user['vai_tro'];
        header('Location: ../index.php');
        exit();
    }
}
```

---

## 🎓 Kết luận

### Điểm mạnh
1. ✅ Security: Prepared statements, password hashing, XSS prevention
2. ✅ Modern Auth: Google OAuth 2.0
3. ✅ Scalable: PDO architecture
4. ✅ User-friendly: Bootstrap 5 UI

### Điểm cần cải thiện
1. 🔧 Client-side validation
2. 🔧 Centralized error handling
3. 🔧 RESTful API
4. 🔧 Unit testing

---

**© 2024 NGUYỄN QUỐC TIẾN - DH52201555**  
*GamerWiki - Hệ thống quản lý đội tuyển Esport*
