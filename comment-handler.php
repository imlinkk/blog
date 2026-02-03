<?php
/**
 * Xử lý bình luận - Thêm, xóa
 * Hỗ trợ nested comments (reply)
 */

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Action: Add comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $postId = (int)$_POST['post_id'];
    $parentId = (!empty($_POST['parent_id']) && $_POST['parent_id'] > 0) ? (int)$_POST['parent_id'] : null;
    $content = trim($_POST['content'] ?? '');
    
    // Validate
    if (empty($content)) {
        $_SESSION['error'] = 'Nội dung bình luận không được để trống!';
        header('Location: post.php?id=' . $postId);
        exit;
    }
    
    if (strlen($content) > 5000) {
        $_SESSION['error'] = 'Nội dung bình luận quá dài (tối đa 5000 ký tự)!';
        header('Location: post.php?id=' . $postId);
        exit;
    }
    
    // Kiểm tra bài viết có tồn tại?
    $post = $db->fetchOne('SELECT id FROM posts WHERE id = ?', [$postId]);
    if (!$post) {
        $_SESSION['error'] = 'Bài viết không tồn tại!';
        header('Location: index.php');
        exit;
    }
    
    // Kiểm tra parent_id nếu là reply
    if ($parentId > 0) {
        $parentComment = $db->fetchOne('SELECT id FROM comments WHERE id = ? AND post_id = ?', [$parentId, $postId]);
        if (!$parentComment) {
            $_SESSION['error'] = 'Bình luận gốc không tồn tại!';
            header('Location: post.php?id=' . $postId);
            exit;
        }
    }
    
    // Lấy thông tin người bình luận
    if (isLoggedIn()) {
        $userId = $_SESSION['user_id'];
        $name = null;
        $email = null;
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $userId = null;
        
        // Validate guest comment
        if (empty($name)) {
            $_SESSION['error'] = 'Tên không được để trống!';
            header('Location: post.php?id=' . $postId);
            exit;
        }
        
        if (empty($email) || !isValidEmail($email)) {
            $_SESSION['error'] = 'Email không hợp lệ!';
            header('Location: post.php?id=' . $postId);
            exit;
        }
    }
    
    // Lưu bình luận
    try {
        $db->insert(
            'INSERT INTO comments (post_id, parent_id, name, email, content, user_id) VALUES (?, ?, ?, ?, ?, ?)',
            [$postId, $parentId, $name, $email, $content, $userId]
        );
        
        $_SESSION['success'] = 'Bình luận của bạn đã được gửi thành công!';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Đã xảy ra lỗi khi gửi bình luận: ' . $e->getMessage();
    }
    
    header('Location: post.php?id=' . $postId);
    exit;
}

// Action: Delete comment
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Bạn phải đăng nhập để xóa bình luận!';
        header('Location: login.php');
        exit;
    }
    
    $commentId = (int)$_GET['id'] ?? 0;
    $postId = (int)$_GET['post_id'] ?? 0;
    
    if ($commentId <= 0 || $postId <= 0) {
        $_SESSION['error'] = 'Thông tin không hợp lệ!';
        header('Location: index.php');
        exit;
    }
    
    // Lấy thông tin bình luận
    $comment = $db->fetchOne('SELECT * FROM comments WHERE id = ? AND post_id = ?', [$commentId, $postId]);
    
    if (!$comment) {
        $_SESSION['error'] = 'Bình luận không tồn tại!';
        header('Location: post.php?id=' . $postId);
        exit;
    }
    
    // Kiểm tra quyền xóa (chủ bình luận, tác giả bài, hoặc admin)
    $post = $db->fetchOne('SELECT user_id FROM posts WHERE id = ?', [$postId]);
    
    $canDelete = false;
    if (isAdmin()) {
        $canDelete = true;
    } elseif ($comment['user_id'] && $_SESSION['user_id'] == $comment['user_id']) {
        $canDelete = true;
    } elseif ($post && $_SESSION['user_id'] == $post['user_id']) {
        $canDelete = true;
    }
    
    if (!$canDelete) {
        $_SESSION['error'] = 'Bạn không có quyền xóa bình luận này!';
        header('Location: post.php?id=' . $postId);
        exit;
    }
    
    // Xóa bình luận (cascade delete cho reply)
    try {
        $db->execute('DELETE FROM comments WHERE id = ? OR parent_id = ?', [$commentId, $commentId]);
        $_SESSION['success'] = 'Bình luận đã được xóa!';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Đã xảy ra lỗi khi xóa bình luận: ' . $e->getMessage();
    }
    
    header('Location: post.php?id=' . $postId);
    exit;
}

// Nếu không có action nào, chuyển hướng về trang chủ
header('Location: index.php');
exit;
