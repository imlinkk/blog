<?php
/**
 * Trang bài viết của tôi
 * Hiển thị các bài viết do user hiện tại viết
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

// Lấy trang hiện tại
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Lấy tổng số bài viết của user
$totalPosts = $db->fetchColumn('SELECT COUNT(*) FROM posts WHERE user_id = ?', [$_SESSION['user_id']]);

// Lấy thông tin phân trang
$pageInfo = getPaginationInfo($currentPage, $totalPosts, POSTS_PER_PAGE);

// Lấy bài viết
$posts = $db->fetchAll(
    'SELECT p.*, (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
     FROM posts p 
     WHERE p.user_id = ?
     ORDER BY p.created_at DESC 
     LIMIT ? OFFSET ?',
    [$_SESSION['user_id'], $pageInfo['limit'], $pageInfo['offset']]
);

$pageTitle = 'Bài Viết Của Tôi';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold mb-0">
                <i class="fas fa-list text-primary"></i> Bài Viết Của Tôi
            </h2>
            <a href="create-post.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Viết Bài Mới
            </a>
        </div>
        
        <?php if (empty($posts)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Bạn chưa viết bài viết nào. 
                <a href="create-post.php" class="alert-link">Viết bài đầu tiên ngay</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tiêu Đề</th>
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
                                    <a href="post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none fw-500">
                                        <?php echo escape($post['title']); ?>
                                    </a>
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
                                    <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-info" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Bạn chắc chắn muốn xóa bài viết này?');" title="Xóa">
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
                    <?php echo renderPagination($currentPage, $pageInfo['totalPages'], 'my-posts.php'); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
