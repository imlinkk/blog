<?php
/**
 * Trang danh mục - Hiển thị bài viết theo danh mục
 */

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Lấy ID danh mục
$categoryId = (int)$_GET['id'] ?? 0;

if ($categoryId <= 0) {
    header('Location: index.php');
    exit;
}

// Lấy thông tin danh mục
$category = $db->fetchOne('SELECT * FROM categories WHERE id = ?', [$categoryId]);

if (!$category) {
    header('Location: index.php');
    exit;
}

// Lấy bài viết theo danh mục (có phân trang)
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$totalPosts = $db->fetchColumn('SELECT COUNT(*) FROM posts WHERE category_id = ?', [$categoryId]);
$pageInfo = getPaginationInfo($currentPage, $totalPosts, POSTS_PER_PAGE);

$posts = $db->fetchAll(
    'SELECT p.*, u.fullname, (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
     FROM posts p 
     JOIN users u ON p.user_id = u.id 
     WHERE p.category_id = ?
     ORDER BY p.created_at DESC 
     LIMIT ? OFFSET ?',
    [$categoryId, $pageInfo['limit'], $pageInfo['offset']]
);

$pageTitle = 'Danh Mục: ' . escape($category['name']);
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-5">
            <h2 class="display-5 fw-bold mb-2">
                <i class="fas fa-folder text-primary"></i> Danh Mục: <?php echo escape($category['name']); ?>
            </h2>
            
            <?php if ($category['description']): ?>
                <p class="lead text-muted"><?php echo escape($category['description']); ?></p>
            <?php endif; ?>
            
            <p class="text-muted">
                Tổng cộng: <strong><?php echo $pageInfo['totalItems']; ?></strong> bài viết
            </p>
        </div>
        
        <!-- Danh sách bài viết -->
        <?php if (empty($posts)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Danh mục này chưa có bài viết.
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="card mb-4 shadow-sm hover-effect">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-2">
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none text-dark">
                                <?php echo escape($post['title']); ?>
                            </a>
                        </h3>
                        
                        <p class="card-text text-muted mb-3">
                            <?php echo escape($post['excerpt']); ?>
                        </p>
                        
                        <div class="row text-muted small mb-3">
                            <div class="col-6 col-md-3">
                                <i class="fas fa-user-circle"></i>
                                <strong><?php echo escape($post['fullname']); ?></strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo formatDate($post['created_at'], 'd/m/Y'); ?>
                            </div>
                            <div class="col-6 col-md-3 mt-2 mt-md-0">
                                <i class="fas fa-eye"></i>
                                <?php echo $post['views']; ?> lượt xem
                            </div>
                            <div class="col-6 col-md-3 mt-2 mt-md-0">
                                <i class="fas fa-comments"></i>
                                <?php echo $post['comment_count']; ?> bình luận
                            </div>
                        </div>
                        
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-right"></i> Đọc tiếp
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Phân trang -->
            <?php if ($pageInfo['totalPages'] > 1): ?>
                <div class="mt-5">
                    <?php echo renderPagination($currentPage, $pageInfo['totalPages'], 'category.php?id=' . $categoryId); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .hover-effect:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,123,255,0.15) !important;
    }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
