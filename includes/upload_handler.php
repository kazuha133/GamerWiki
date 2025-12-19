<?php
/**
 * Image Upload Handler
 * Xử lý upload và quản lý ảnh cho đội tuyển và tuyển thủ
 */

/**
 * Upload image with validation
 * Validates: type, upload errors, file size, extension, and MIME type
 * 
 * @param array $file - $_FILES array element
 * @param string $type - 'logo' hoặc 'avatar'
 * @param string|null $old_file - Tên file cũ để xóa (nếu có)
 * @return array - ['success' => bool, 'filename' => string|null, 'error' => string|null]
 */
function upload_image($file, $type, $old_file = null) {
    // Validate type
    $allowed_types = ['logo', 'avatar'];
    if (!in_array($type, $allowed_types)) {
        return ['success' => false, 'error' => 'Loại file không hợp lệ.'];
    }
    
    // Check if file was uploaded
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        if (isset($file['error']) && $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'error' => 'Không có file được chọn.'];
        }
        return ['success' => false, 'error' => 'Lỗi khi upload file. Mã lỗi: ' . ($file['error'] ?? 'unknown')];
    }
    
    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File không được vượt quá 5MB.'];
    }
    
    // Validate file extension
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_name = $file['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_extensions)) {
        return ['success' => false, 'error' => 'Chỉ chấp nhận file ảnh: jpg, jpeg, png, gif.'];
    }
    
    // Validate MIME type (real file type check, not just extension)
    $allowed_mime_types = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo === false) {
        return ['success' => false, 'error' => 'Không thể xác thực loại file.'];
    }
    
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!$mime_type || !in_array($mime_type, $allowed_mime_types)) {
        return ['success' => false, 'error' => 'File không phải là ảnh hợp lệ.'];
    }
    
    // Generate secure unique filename
    $new_filename = $type . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
    
    // Determine upload directory
    $upload_dir = __DIR__ . '/../uploads/' . ($type === 'logo' ? 'logos' : 'avatars') . '/';
    
    // Create directory if not exists
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            return ['success' => false, 'error' => 'Không thể tạo thư mục upload.'];
        }
    }
    
    $upload_path = $upload_dir . $new_filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => false, 'error' => 'Không thể lưu file.'];
    }
    
    // Set proper permissions
    chmod($upload_path, 0644);
    
    // Delete old file if exists
    if ($old_file) {
        delete_image($old_file, $type);
    }
    
    return [
        'success' => true,
        'filename' => $new_filename,
        'error' => null
    ];
}

/**
 * Delete image file
 * 
 * @param string $filename - Tên file cần xóa
 * @param string $type - 'logo' hoặc 'avatar'
 * @return bool - True nếu xóa thành công hoặc file không tồn tại
 */
function delete_image($filename, $type) {
    if (empty($filename)) {
        return true;
    }
    
    // Validate type
    $allowed_types = ['logo', 'avatar'];
    if (!in_array($type, $allowed_types)) {
        return false;
    }
    
    // Determine file path
    $file_path = __DIR__ . '/../uploads/' . ($type === 'logo' ? 'logos' : 'avatars') . '/' . $filename;
    
    // Check if file exists and delete
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    
    return true; // Return true if file doesn't exist (already deleted or never existed)
}

/**
 * Get image URL for display
 * 
 * @param string|null $filename - Tên file ảnh
 * @param string $type - 'logo' hoặc 'avatar'
 * @return string|null - URL của ảnh hoặc null nếu không có
 */
function get_image_url($filename, $type) {
    if (empty($filename)) {
        return null;
    }
    
    $allowed_types = ['logo', 'avatar'];
    if (!in_array($type, $allowed_types)) {
        return null;
    }
    
    $subdir = ($type === 'logo') ? 'logos' : 'avatars';
    $file_path = __DIR__ . '/../uploads/' . $subdir . '/' . $filename;
    
    // Check if file exists
    if (!file_exists($file_path)) {
        return null;
    }
    
    return url('uploads/' . $subdir . '/' . $filename);
}

/**
 * Check if image file exists
 * 
 * @param string|null $filename - Tên file ảnh
 * @param string $type - 'logo' hoặc 'avatar'
 * @return bool - True nếu file tồn tại
 */
function image_exists($filename, $type) {
    if (empty($filename)) {
        return false;
    }
    
    $allowed_types = ['logo', 'avatar'];
    if (!in_array($type, $allowed_types)) {
        return false;
    }
    
    $subdir = ($type === 'logo') ? 'logos' : 'avatars';
    $file_path = __DIR__ . '/../uploads/' . $subdir . '/' . $filename;
    
    return file_exists($file_path);
}
?>
