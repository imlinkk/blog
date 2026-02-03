<?php
/**
 * Admin - Quản lý bài viết
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

// Lấy trang hiện tại
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Lấy tổng số bài viết
$totalPosts = $db->fetchColumn('SELECT COUNT(*) FROM posts');

// Lấy thông tin phân trang
$pageInfo = getPaginationInfo($currentPage, $totalPosts, POSTS_PER_PAGE);

// Lấy bài viết
$posts = $db->fetchAll(
    'SELECT p.*, u.fullname, (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
     FROM posts p 
     JOIN users u ON p.user_id = u.id 
     ORDER BY p.created_at DESC 
     LIMIT ? OFFSET ?',
    [$pageInfo['limit'], $pageInfo['offset']]
);

$pageTitle = 'Quản Lý Bài Viết';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="display-6 fw-bold mb-0">
                <i class="fas fa-file-alt text-primary"></i> Quản Lý Bài Viết
            </h1>
            <a href="<?php echo SITE_URL; ?>/create-post.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Bài Viết Mới
            </a>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <?php echo showSuccess($_SESSION['success']); unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <?php echo showError($_SESSION['error']); unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (empty($posts)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Chưa có bài viết nào.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tiêu Đề</th>
                    <th>Tác Giả</th>
                    <th>Lượt Xem</th>
                    <th>Bình Luận</th>
                    <th>Ngày Tạo</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <span class="badge bg-secondary"><?php echo $post['id']; ?></span>
                        </td>
                        <td>
                            <strong><?php echo escape($post['title']); ?></strong>
                        </td>
                        <td>
                            <?php echo escape($post['fullname']); ?>
                        </td>
                        <td>
                            <i class="fas fa-eye text-muted"></i> <?php echo $post['views']; ?>
                        </td>
                        <td>
                            <i class="fas fa-comments text-muted"></i> <?php echo $post['comment_count']; ?>
                        </td>
                        <td>
                            <small class="text-muted"><?php echo formatDate($post['created_at'], 'd/m/Y H:i'); ?></small>
                        </td>
                        <td>
                            <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post['id']; ?>" 
                               class="btn btn-sm btn-info" title="Xem">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo SITE_URL; ?>/edit-post.php?id=<?php echo $post['id']; ?>" 
                               class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?php echo SITE_URL; ?>/delete-post.php?id=<?php echo $post['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Bạn chắc chắn muốn xóa?');" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Phân trang -->
    <?php if ($pageInfo['totalPages'] > 1): ?>
        <div class="mt-5">
            <?php echo renderPagination($currentPage, $pageInfo['totalPages'], 'posts.php'); ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="mt-4">
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay Lại Dashboard
    </a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
