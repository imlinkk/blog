<?php
/**
 * Admin Dashboard - Trang chính của admin panel
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

// Lấy thống kê
$totalPosts = getTotalPosts();
$totalUsers = getTotalUsers();
$totalComments = getTotalComments();

// Lấy các bài viết mới nhất
$recentPosts = $db->fetchAll(
    'SELECT p.*, u.fullname FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 5'
);

// Lấy các bình luận mới nhất
$recentComments = $db->fetchAll(
    'SELECT c.*, p.title FROM comments c JOIN posts p ON c.post_id = p.id ORDER BY c.created_at DESC LIMIT 5'
);

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-6 fw-bold mb-4">
            <i class="fas fa-tachometer-alt text-primary"></i> Admin Dashboard
        </h1>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-5">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Bài Viết</h6>
                        <h2 class="mb-0"><?php echo $totalPosts; ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.5;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Người Dùng</h6>
                        <h2 class="mb-0"><?php echo $totalUsers; ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.5;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Bình Luận</h6>
                        <h2 class="mb-0"><?php echo $totalComments; ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.5;">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Posts -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt text-primary"></i> Bài Viết Mới Nhất
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentPosts)): ?>
                    <p class="p-3 text-muted mb-0">Chưa có bài viết nào.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recentPosts as $post): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">
                                        <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post['id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo escape($post['title']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        bởi <strong><?php echo escape($post['fullname']); ?></strong> 
                                        - <?php echo formatDate($post['created_at'], 'd/m/Y H:i'); ?>
                                    </small>
                                </div>
                                <div>
                                    <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light">
                <a href="posts.php" class="btn btn-primary btn-sm">
                    Quản Lý Bài Viết <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Recent Comments -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-comments text-primary"></i> Bình Luận Mới Nhất
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentComments)): ?>
                    <p class="p-3 text-muted mb-0">Chưa có bình luận nào.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recentComments as $comment): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div style="flex: 1;">
                                        <h6 class="mb-1">
                                            <?php 
                                            if ($comment['user_id']) {
                                                $user = $db->fetchOne('SELECT fullname FROM users WHERE id = ?', [$comment['user_id']]);
                                                echo escape($user['fullname'] ?? 'Ẩn danh');
                                            } else {
                                                echo escape($comment['name']);
                                            }
                                            ?>
                                        </h6>
                                        <p class="mb-2 text-muted">
                                            Trên: <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $comment['post_id']; ?>" 
                                                     class="text-decoration-none">
                                                <?php echo escape($comment['title']); ?>
                                            </a>
                                        </p>
                                        <small class="text-muted">
                                            <?php echo escape(truncate($comment['content'], 100)); ?>
                                        </small>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light">
                <a href="comments.php" class="btn btn-primary btn-sm">
                    Quản Lý Bình Luận <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-bolt text-primary"></i> Hành Động Nhanh
                </h5>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="posts.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-file-alt"></i> Quản Lý Bài Viết
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="users.php" class="btn btn-outline-success w-100">
                            <i class="fas fa-users"></i> Quản Lý Người Dùng
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="comments.php" class="btn btn-outline-info w-100">
                            <i class="fas fa-comments"></i> Quản Lý Bình Luận
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="categories.php" class="btn btn-outline-warning w-100">
                            <i class="fas fa-folder"></i> Quản Lý Danh Mục
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
