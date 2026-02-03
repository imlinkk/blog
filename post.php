<?php
/**
 * Trang chi tiết bài viết
 * Hiển thị nội dung đầy đủ, tăng lượt xem, bình luận
 */

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Lấy ID bài viết
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($postId <= 0) {
    header('HTTP/1.0 404 Not Found');
    die('Bài viết không tồn tại.');
}

// Lấy thông tin bài viết
$post = $db->fetchOne(
    'SELECT p.*, u.id as author_id, u.fullname 
     FROM posts p 
     JOIN users u ON p.user_id = u.id 
     WHERE p.id = ?',
    [$postId]
);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    die('Bài viết không tồn tại.');
}

// Tăng lượt xem
$db->execute('UPDATE posts SET views = views + 1 WHERE id = ?', [$postId]);

// Lấy bình luận (có phân trang)
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$totalComments = $db->fetchColumn('SELECT COUNT(*) FROM comments WHERE post_id = ? AND (parent_id IS NULL OR parent_id = 0)', [$postId]);
$pageInfo = getPaginationInfo($currentPage, $totalComments, COMMENTS_PER_PAGE);

// Lấy bình luận gốc (parent_id = NULL hoặc 0)
$comments = $db->fetchAll(
    'SELECT * FROM comments WHERE post_id = ? AND (parent_id IS NULL OR parent_id = 0) ORDER BY created_at DESC LIMIT ? OFFSET ?',
    [$postId, $pageInfo['limit'], $pageInfo['offset']]
);

// Lấy danh sách categories
$categories = $db->fetchAll('SELECT * FROM categories ORDER BY name');

$pageTitle = escape($post['title']);
require_once __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <!-- Thông báo -->
        <?php if (isset($_SESSION['success'])): ?>
            <?php echo showSuccess($_SESSION['success']); ?>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <?php echo showError($_SESSION['error']); ?>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Bài viết -->
        <article class="card shadow-sm mb-4">
            <div class="card-body p-5">
                <!-- Tiêu đề -->
                <h1 class="card-title mb-3"><?php echo escape($post['title']); ?></h1>
                
                <!-- Meta information -->
                <div class="d-flex flex-wrap gap-3 text-muted small mb-4 pb-3 border-bottom">
                    <div>
                        <i class="fas fa-user-circle"></i>
                        <strong><?php echo escape($post['fullname']); ?></strong>
                    </div>
                    <div>
                        <i class="fas fa-calendar-alt"></i>
                        <?php echo formatDate($post['created_at'], 'd/m/Y H:i'); ?>
                    </div>
                    <div>
                        <i class="fas fa-eye"></i>
                        <?php echo $post['views']; ?> lượt xem
                    </div>
                    <?php if ($post['category_id']): ?>
                        <div>
                            <i class="fas fa-folder"></i>
                            <?php 
                            $category = $db->fetchOne('SELECT name FROM categories WHERE id = ?', [$post['category_id']]);
                            echo escape($category['name'] ?? '');
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Tags -->
                <?php if (!empty($post['tags'])): ?>
                    <div class="mb-4">
                        <?php foreach (explode(',', $post['tags']) as $tag): ?>
                            <a href="search.php?tag=<?php echo urlencode(trim($tag)); ?>" class="badge bg-light text-dark text-decoration-none">
                                #<?php echo escape(trim($tag)); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Nội dung -->
                <div class="card-text post-content mb-4">
                    <?php echo $post['content']; ?>
                </div>
                
                <!-- Action buttons -->
                <div class="border-top pt-4">
                    <?php 
                    // Kiểm tra quyền chỉnh sửa/xóa
                    $canEdit = isLoggedIn() && (
                        $_SESSION['user_id'] == $post['author_id'] || 
                        isAdmin()
                    );
                    ?>
                    
                    <?php if ($canEdit): ?>
                        <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Chỉnh Sửa
                        </a>
                        <a href="delete-post.php?id=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm" 
                           onclick="return confirm('Bạn chắc chắn muốn xóa bài viết này?');">
                            <i class="fas fa-trash"></i> Xóa
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        
        <!-- Bình luận -->
        <section class="card shadow-sm">
            <div class="card-body p-5">
                <h3 class="card-title mb-4">
                    <i class="fas fa-comments"></i> Bình Luận (<?php echo $totalComments; ?>)
                </h3>
                
                <!-- Form thêm bình luận -->
                <div class="mb-5 p-4 bg-light rounded">
                    <h5 class="mb-3">Viết Bình Luận</h5>
                    
                    <form id="commentForm" method="POST" action="comment-handler.php">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <input type="hidden" name="parent_id" value="">
                        
                        <?php if (!isLoggedIn()): ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" name="name" placeholder="Tên của bạn *" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" class="form-control" name="email" placeholder="Email *" required>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-user"></i> Đang đăng nhập với tài khoản: <strong><?php echo escape($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'User'); ?></strong>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="4" placeholder="Nội dung bình luận *" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Gửi Bình Luận
                        </button>
                        
                        <?php if (!isLoggedIn()): ?>
                            <p class="text-muted mt-3 mb-0">
                                <a href="login.php" class="text-decoration-none">Đăng nhập</a> để viết bình luận với tài khoản của bạn.
                            </p>
                        <?php endif; ?>
                    </form>
                </div>
                
                <!-- Danh sách bình luận -->
                <?php if (empty($comments)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Chưa có bình luận nào.
                    </div>
                <?php else: ?>
                    <div class="comments-list">
                        <?php 
                        foreach ($comments as $comment) {
                            renderComment($comment, $post['id'], $post['user_id']);
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <!-- Phân trang bình luận -->
                <?php if ($pageInfo['totalPages'] > 1): ?>
                    <div class="mt-5">
                        <?php echo renderPagination($currentPage, $pageInfo['totalPages'], 'post.php?id=' . $post['id']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Related posts -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-link text-primary"></i> Bài Viết Liên Quan
                </h5>
                <?php 
                $relatedPosts = $db->fetchAll(
                    'SELECT id, title FROM posts 
                     WHERE id != ? 
                     ORDER BY created_at DESC 
                     LIMIT 5',
                    [$postId]
                );
                if (empty($relatedPosts)): 
                ?>
                    <p class="text-muted">Không có bài viết nào.</p>
                <?php else: ?>
                    <ul class="list-unstyled">
                        <?php foreach ($relatedPosts as $relPost): ?>
                            <li class="mb-2">
                                <a href="post.php?id=<?php echo $relPost['id']; ?>" class="text-decoration-none">
                                    <i class="fas fa-chevron-right small"></i> <?php echo escape($relPost['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Categories -->
        <div class="card shadow-sm">
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
    </div>
</div>

<style>
    .post-content {
        font-size: 1.05rem;
        line-height: 1.8;
    }
    
    .post-content p {
        margin-bottom: 1rem;
    }
    
    .post-content strong {
        color: #333;
    }
    
    .post-content ul, .post-content ol {
        margin-bottom: 1rem;
        margin-left: 2rem;
    }
    
    .post-content li {
        margin-bottom: 0.5rem;
    }
    
    .comment-item {
        border-left: 3px solid #007bff;
        padding-left: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .comment-reply {
        margin-left: 2rem;
        border-left-color: #6c757d;
    }
</style>

<?php 
/**
 * Hàm hiển thị bình luận (bao gồm reply)
 * Sử dụng recursive để hiển thị các reply lồng nhau
 */
function renderComment($comment, $postId, $authorId) {
    global $db;
    
    // Lấy các reply cho bình luận này
    $replies = $db->fetchAll(
        'SELECT * FROM comments WHERE parent_id = ? ORDER BY created_at ASC',
        [$comment['id']]
    );
    
    // Xác định độ sâu (depth) của bình luận
    $depth = 0;
    $parentId = $comment['parent_id'];
    while ($parentId > 0) {
        $parent = $db->fetchOne('SELECT parent_id FROM comments WHERE id = ?', [$parentId]);
        $parentId = $parent ? $parent['parent_id'] : 0;
        $depth++;
    }
    
    // Kiểm tra quyền xóa
    $canDelete = isLoggedIn() && (
        (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']) ||
        (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $authorId) ||
        isAdmin()
    );
    
    // Xác định người bình luận
    $commenterName = $comment['name'] ?? escape($_SESSION['fullname'] ?? 'Ẩn danh');
    if ($comment['user_id']) {
        $user = $db->fetchOne('SELECT fullname FROM users WHERE id = ?', [$comment['user_id']]);
        $commenterName = escape($user['fullname'] ?? 'Ẩn danh');
    }
    
    $marginClass = $depth > 0 ? 'ms-' . min($depth * 3, 5) : '';
    ?>
    
    <div class="comment-item <?php echo $marginClass; ?>">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <strong><?php echo $commenterName; ?></strong>
                <div class="text-muted small">
                    <i class="fas fa-clock"></i> <?php echo formatDate($comment['created_at'], 'd/m/Y H:i'); ?>
                </div>
            </div>
            
            <?php if ($canDelete): ?>
                <a href="comment-handler.php?action=delete&id=<?php echo $comment['id']; ?>&post_id=<?php echo $postId; ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Bạn chắc chắn muốn xóa bình luận này?');">
                    <i class="fas fa-trash"></i>
                </a>
            <?php endif; ?>
        </div>
        
        <p class="mb-3"><?php echo nl2br(escape($comment['content'])); ?></p>
        
        <!-- Nút reply -->
        <?php if ($depth < 2): ?>  <!-- Tối đa 3 cấp -->
            <button class="btn btn-sm btn-outline-primary reply-btn" data-comment-id="<?php echo $comment['id']; ?>">
                <i class="fas fa-reply"></i> Trả lời
            </button>
            
            <!-- Form reply (ẩn ban đầu) -->
            <div id="reply-form-<?php echo $comment['id']; ?>" class="reply-form mt-3" style="display: none;">
                <form method="POST" action="comment-handler.php">
                    <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                    <input type="hidden" name="parent_id" value="<?php echo $comment['id']; ?>">
                    
                    <?php if (!isLoggedIn()): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="text" class="form-control form-control-sm" name="name" placeholder="Tên của bạn *" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="email" class="form-control form-control-sm" name="email" placeholder="Email *" required>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <textarea class="form-control form-control-sm" name="content" rows="3" placeholder="Nội dung trả lời *" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-paper-plane"></i> Gửi
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-reply" data-comment-id="<?php echo $comment['id']; ?>">
                        Hủy
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <?php 
    // Hiển thị các reply
    if (!empty($replies)) {
        foreach ($replies as $reply) {
            renderComment($reply, $postId, $authorId);
        }
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show reply form
    document.querySelectorAll('.reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const form = document.getElementById('reply-form-' + commentId);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });
    });
    
    // Cancel reply
    document.querySelectorAll('.cancel-reply').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            document.getElementById('reply-form-' + commentId).style.display = 'none';
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
