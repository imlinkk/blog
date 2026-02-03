<?php
/**
 * Các hàm hỗ trợ chung cho ứng dụng
 */

require_once __DIR__ . '/database.php';

/**
 * Kiểm tra người dùng đã đăng nhập?
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Kiểm tra quyền admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Lấy thông tin user hiện tại
 */
function getCurrentUser() {
    global $db;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    return $db->fetchOne('SELECT * FROM users WHERE id = ?', [$_SESSION['user_id']]);
}

/**
 * Chuyển hướng
 */
function redirect($url) {
    header('Location: ' . SITE_URL . $url);
    exit;
}

/**
 * Hiển thị thông báo lỗi
 */
function showError($message) {
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        ' . htmlspecialchars($message) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

/**
 * Hiển thị thông báo thành công
 */
function showSuccess($message) {
    return '<div class="alert alert-success alert-dismissible fade show" role="alert">
        ' . htmlspecialchars($message) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

/**
 * Hiển thị thông báo info
 */
function showInfo($message) {
    return '<div class="alert alert-info alert-dismissible fade show" role="alert">
        ' . htmlspecialchars($message) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

/**
 * Chống XSS - Escape HTML
 */
function escape($text) {
    if ($text === null) {
        return '';
    }
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

/**
 * Tạo slug từ tiêu đề
 * "Hello World" -> "hello-world"
 */
function createSlug($title) {
    $slug = strtolower(trim($title));
    // Xóa ký tự đặc biệt, giữ lại chữ, số, dấu gạch
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * Cắt ngắn text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Lọc HTML - cho phép một số tag cơ bản
 */
function filterHTML($html) {
    // Cho phép các tag cơ bản: p, strong, em, u, br, ul, ol, li, img, a, blockquote, h1-h6
    $allowed = '<p><br><strong><em><u><ul><ol><li><img><a><blockquote><h1><h2><h3><h4><h5><h6>';
    
    // Strip tất cả tag ngoài danh sách được phép
    $html = strip_tags($html, $allowed);
    
    // Sử dụng HTML Purifier nếu cần bảo mật cao hơn
    // Nhưng ở đây chúng ta dùng cách đơn giản
    
    return $html;
}

/**
 * Kiểm tra email hợp lệ
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Kiểm tra username hợp lệ (3-20 ký tự, chữ số, gạch dưới)
 */
function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

/**
 * Kiểm tra mật khẩu mạnh (tối thiểu 6 ký tự)
 */
function isValidPassword($password) {
    return strlen($password) >= 6;
}

/**
 * Lấy tính năng phân trang
 */
function getPaginationInfo($currentPage, $totalItems, $itemsPerPage) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'offset' => $offset,
        'limit' => $itemsPerPage,
        'totalItems' => $totalItems,
    ];
}

/**
 * Tạo HTML phân trang
 */
function renderPagination($currentPage, $totalPages, $url) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Nút Previous
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($currentPage - 1) . '">← Trước</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">← Trước</span></li>';
    }
    
    // Các trang
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $currentPage) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    // Nút Next
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($currentPage + 1) . '">Tiếp →</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Tiếp →</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Định dạng ngày giờ
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    if (empty($date)) {
        return '';
    }
    return date($format, strtotime($date));
}

/**
 * Tính thời gian từ bây giờ (ví dụ: "2 giờ trước")
 */
function timeAgo($date) {
    $timestamp = strtotime($date);
    $time_ago = time() - $timestamp;
    
    $period = [
        'giây' => 60,
        'phút' => 60 * 60,
        'giờ' => 60 * 60 * 24,
        'ngày' => 60 * 60 * 24 * 7,
        'tuần' => 60 * 60 * 24 * 7 * 4,
        'tháng' => 60 * 60 * 24 * 7 * 4 * 12,
        'năm' => PHP_INT_MAX,
    ];
    
    foreach ($period as $key => $value) {
        if ($time_ago < $value) {
            $time_period = floor($time_ago / ($value / $period[key(array_slice(array_keys($period), array_search($key, array_keys($period)) - 1, 1))]));
            return $time_period . ' ' . $key . ' trước';
        }
    }
}

/**
 * Kiểm tra nếu người dùng có quyền chỉnh sửa bài viết
 */
function canEditPost($postId, $userId) {
    global $db;
    
    $post = $db->fetchOne('SELECT user_id FROM posts WHERE id = ?', [$postId]);
    
    if (!$post) {
        return false;
    }
    
    // Admin hoặc chủ bài viết
    return $userId == $post['user_id'] || isAdmin();
}

/**
 * Kiểm tra nếu người dùng có quyền xóa bài viết
 */
function canDeletePost($postId, $userId) {
    return canEditPost($postId, $userId);
}

/**
 * Kiểm tra nếu người dùng có quyền xóa bình luận
 */
function canDeleteComment($commentId, $userId) {
    global $db;
    
    $comment = $db->fetchOne('SELECT post_id FROM comments WHERE id = ?', [$commentId]);
    
    if (!$comment) {
        return false;
    }
    
    // Kiểm tra là tác giả bài hoặc admin
    $post = $db->fetchOne('SELECT user_id FROM posts WHERE id = ?', [$comment['post_id']]);
    
    return $userId == $post['user_id'] || isAdmin();
}

/**
 * Lấy tổng số bài viết
 */
function getTotalPosts() {
    global $db;
    return $db->fetchColumn('SELECT COUNT(*) FROM posts');
}

/**
 * Lấy tổng số người dùng
 */
function getTotalUsers() {
    global $db;
    return $db->fetchColumn('SELECT COUNT(*) FROM users');
}

/**
 * Lấy tổng số bình luận
 */
function getTotalComments() {
    global $db;
    return $db->fetchColumn('SELECT COUNT(*) FROM comments');
}

/**
 * Xóa session
 */
function logout() {
    session_destroy();
    redirect('/');
}
