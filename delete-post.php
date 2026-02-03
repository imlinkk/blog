<?php
/**
 * Xóa bài viết
 * Chỉ cho phép tác giả hoặc admin
 */

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Kiểm tra đã đăng nhập?
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Lấy ID bài viết
$postId = (int)$_GET['id'] ?? 0;

if ($postId <= 0) {
    $_SESSION['error'] = 'Bài viết không tồn tại!';
    header('Location: index.php');
    exit;
}

// Lấy bài viết
$post = $db->fetchOne('SELECT * FROM posts WHERE id = ?', [$postId]);

if (!$post) {
    $_SESSION['error'] = 'Bài viết không tồn tại!';
    header('Location: index.php');
    exit;
}

// Kiểm tra quyền xóa
if ($_SESSION['user_id'] != $post['user_id'] && !isAdmin()) {
    $_SESSION['error'] = 'Bạn không có quyền xóa bài viết này!';
    header('Location: post.php?id=' . $postId);
    exit;
}

// Xóa bài viết (sẽ tự động xóa bình luận do ON DELETE CASCADE)
try {
    $db->execute('DELETE FROM posts WHERE id = ?', [$postId]);
    $_SESSION['success'] = 'Bài viết đã được xóa thành công!';
} catch (Exception $e) {
    $_SESSION['error'] = 'Đã xảy ra lỗi khi xóa bài viết: ' . $e->getMessage();
}

header('Location: index.php');
exit;
