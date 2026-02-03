<?php
/**
 * Admin - Quản lý bình luận
 */

session_start();

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra quyền admin
if (!isAdmin()) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
    header('Location: ' . SITE_URL);
    exit;
}

// Xử lý xóa bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $commentId = (int)$_POST['comment_id'];
    
    $comment = $db->fetchOne('SELECT post_id FROM comments WHERE id = ?', [$commentId]);
    
    if ($comment) {
        $db->execute('DELETE FROM comments WHERE id = ? OR parent_id = ?', [$commentId, $commentId]);
        $_SESSION['success'] = 'Bình luận đã được xóa!';
    }
    
    header('Location: comments.php');
    exit;
}

// Lấy trang hiện tại
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Lấy tổng số bình luận
$totalComments = $db->fetchColumn('SELECT COUNT(*) FROM comments WHERE parent_id = 0');

// Lấy thông tin phân trang
$pageInfo = getPaginationInfo($currentPage, $totalComments, 10);

// Lấy danh sách bình luận (chỉ parent comments)
$comments = $db->fetchAll(
    'SELECT c.*, p.title, u.fullname 
     FROM comments c 
     JOIN posts p ON c.post_id = p.id 
     LEFT JOIN users u ON c.user_id = u.id 
     WHERE c.parent_id = 0
     ORDER BY c.created_at DESC 
     LIMIT ? OFFSET ?',
    [$pageInfo['limit'], $pageInfo['offset']]
);

$pageTitle = 'Quản Lý Bình Luận';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-6 fw-bold mb-0">
            <i class="fas fa-comments text-primary"></i> Quản Lý Bình Luận
        </h1>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <?php echo showSuccess($_SESSION['success']); unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <?php echo showError($_SESSION['error']); unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (empty($comments)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Chưa có bình luận nào.
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($comments as $comment): ?>
            <!-- Lấy các reply -->
            <?php $replies = $db->fetchAll('SELECT * FROM comments WHERE parent_id = ? ORDER BY created_at ASC', [$comment['id']]); ?>
            
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1">
                                    <strong>
                                        <?php 
                                        echo $comment['user_id'] ? escape($comment['fullname']) : escape($comment['name']);
                                        ?>
                                    </strong>
                                    <?php if ($comment['user_id']): ?>
                                        <span class="badge bg-info">Thành viên</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Khách</span>
                                    <?php endif; ?>
                                </h6>
                                <p class="text-muted mb-0">
                                    Trên: <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $comment['post_id']; ?>" 
                                             class="text-decoration-none">
                                        <?php echo escape($comment['title']); ?>
                                    </a>
                                </p>
                            </div>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Xóa bình luận này?');">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                        
                        <p class="mb-2">
                            <?php echo nl2br(escape($comment['content'])); ?>
                        </p>
                        
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> <?php echo formatDate($comment['created_at'], 'd/m/Y H:i'); ?>
                        </small>
                        
                        <!-- Replies -->
                        <?php if (!empty($replies)): ?>
                            <div class="mt-3 ps-4 border-start">
                                <h6 class="mb-3">
                                    <i class="fas fa-reply"></i> <?php echo count($replies); ?> trả lời
                                </h6>
                                
                                <?php foreach ($replies as $reply): ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <strong>
                                                        <?php 
                                                        echo $reply['user_id'] ? escape($db->fetchColumn('SELECT fullname FROM users WHERE id = ?', [$reply['user_id']])) : escape($reply['name']);
                                                        ?>
                                                    </strong>
                                                </h6>
                                                <p class="mb-2">
                                                    <?php echo nl2br(escape($reply['content'])); ?>
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> <?php echo formatDate($reply['created_at'], 'd/m/Y H:i'); ?>
                                                </small>
                                            </div>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="comment_id" value="<?php echo $reply['id']; ?>">
                                                <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Xóa bình luận này?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Phân trang -->
    <?php if ($pageInfo['totalPages'] > 1): ?>
        <div class="mt-5">
            <?php echo renderPagination($currentPage, $pageInfo['totalPages'], 'comments.php'); ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="mt-4">
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay Lại Dashboard
    </a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
