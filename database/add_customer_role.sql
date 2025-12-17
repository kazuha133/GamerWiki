-- Migration: Thêm vai trò customer
-- Chạy file này trong phpMyAdmin

USE gamerwiki;

-- Sửa ENUM của cột vai_tro để thêm 'customer'
ALTER TABLE nguoi_dung 
MODIFY COLUMN vai_tro ENUM('admin', 'customer', 'user') DEFAULT 'user';

-- Tạo 1 tài khoản customer mẫu (password: customer123)
INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro, trang_thai) 
VALUES ('customer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer@gamerwiki.com', 'customer', 'active');

-- Kiểm tra kết quả
SELECT ten_dang_nhap, email, vai_tro, trang_thai FROM nguoi_dung;
