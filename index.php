<?php
/**
 * Trang chủ - Hiển thị danh sách bài viết
 */

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Lấy trang hiện tại
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Lấy tổng số bài viết
$totalPosts = $db->fetchColumn('SELECT COUNT(*) FROM posts');

// Lấy thông tin phân trang
$pageInfo = getPaginationInfo($currentPage, $totalPosts, POSTS_PER_PAGE);

// Lấy bài viết theo trang
$posts = $db->fetchAll(
    'SELECT p.*, u.fullname, (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
     FROM posts p 
     JOIN users u ON p.user_id = u.id 
     ORDER BY p.created_at DESC 
     LIMIT ? OFFSET ?',
    [$pageInfo['limit'], $pageInfo['offset']]
);

// Lấy danh sách categories
$categories = $db->fetchAll('SELECT * FROM categories ORDER BY name');

$pageTitle = 'Trang Chủ';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row mb-4">
    <div class="col-lg-8">
        <!-- Tiêu đề -->
        <div class="mb-5 text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-book-open text-primary"></i> Khám Phá Bài Viết
            </h1>
            <p class="lead text-muted">Chia sẻ kiến thức, truyền cảm hứng, tạo nên sự thay đổi</p>
        </div>
        
        <!-- Danh sách bài viết -->
        <?php if (empty($posts)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Chưa có bài viết nào. 
                <?php if (isLoggedIn()): ?>
                    <a href="create-post.php">Hãy viết bài đầu tiên!</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="card mb-4 shadow-sm hover-effect" style="transition: transform 0.3s;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h3 class="card-title mb-0">
                                <a href="post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo escape($post['title']); ?>
                                </a>
                            </h3>
                            <?php if ($post['category_id']): ?>
                                <span class="badge bg-primary">
                                    <?php 
                                    $category = $db->fetchOne('SELECT name FROM categories WHERE id = ?', [$post['category_id']]);
                                    echo escape($category['name'] ?? '');
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
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
                        
                        <!-- Tags -->
                        <?php if (!empty($post['tags'])): ?>
                            <div class="mb-3">
                                <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                    <a href="search.php?tag=<?php echo urlencode(trim($tag)); ?>" class="badge bg-light text-dark text-decoration-none">
                                        #<?php echo escape(trim($tag)); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-right"></i> Đọc tiếp
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Phân trang -->
        <?php if ($pageInfo['totalPages'] > 1): ?>
            <div class="mt-5">
                <?php echo renderPagination($currentPage, $pageInfo['totalPages'], 'index.php'); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Search -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-search text-primary"></i> Tìm Kiếm
                </h5>
                <form action="search.php" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" placeholder="Tìm kiếm bài viết..." 
                               value="<?php echo escape($_GET['q'] ?? ''); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Categories -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-list text-primary"></i> Danh Mục
                </h5>
                <ul class="list-unstyled">
                    <?php foreach ($categories as $cat): ?>
                        <li class="mb-2">
                            <a href="category.php?id=<?php echo $cat['id']; ?>" class="text-decoration-none">
                                <i class="fas fa-folder"></i> <?php echo escape($cat['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-bar-chart text-primary"></i> Thống Kê
                </h5>
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="h4 text-primary mb-0"><?php echo getTotalPosts(); ?></div>
                        <small class="text-muted">Bài Viết</small>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="h4 text-success mb-0"><?php echo getTotalUsers(); ?></div>
                        <small class="text-muted">Thành Viên</small>
                    </div>
                    <div class="col-6">
                        <div class="h4 text-info mb-0"><?php echo getTotalComments(); ?></div>
                        <small class="text-muted">Bình Luận</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-effect:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,123,255,0.15) !important;
    }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
