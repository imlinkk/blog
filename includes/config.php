<?php
/**
 * Cấu hình ứng dụng Blog
 * Kết nối database, hằng số, thiết lập toàn cục
 */

// Chế độ debug (true = hiển thị lỗi, false = ẩn lỗi)
define('DEBUG', true);

// Thông tin database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Laragon thường không có password
define('DB_NAME', 'blog');

// URL gốc của ứng dụng
define('SITE_URL', 'http://blog.test');
define('SITE_NAME', 'Blog');

// Cấu hình phân trang
define('POSTS_PER_PAGE', 5);
define('COMMENTS_PER_PAGE', 10);

// Cấu hình upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024);  // 5MB
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Cấu hình email (tùy chọn)
define('CONTACT_EMAIL', 'admin@blog.local');

// Thiết lập error reporting
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}

// Thiết lập encoding mặc định
ini_set('default_charset', 'utf-8');
