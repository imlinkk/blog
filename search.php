<?php
/**
 * Trang tìm kiếm bài viết
 */

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Lấy từ khóa tìm kiếm
$searchQuery = trim($_GET['q'] ?? '');
$searchTag = trim($_GET['tag'] ?? '');

$posts = [];
$pageTitle = 'Kết Quả Tìm Kiếm';

if ($searchQuery) {
    // Tìm kiếm theo tiêu đề
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    
    $totalPosts = $db->fetchColumn(
        'SELECT COUNT(*) FROM posts WHERE title LIKE ? OR content LIKE ?',
        ['%' . $searchQuery . '%', '%' . $searchQuery . '%']
    );
    
    $pageInfo = getPaginationInfo($currentPage, $totalPosts, POSTS_PER_PAGE);
    
    $posts = $db->fetchAll(
        'SELECT p.*, u.fullname, (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
         FROM posts p 
         JOIN users u ON p.user_id = u.id 
         WHERE p.title LIKE ? OR p.content LIKE ?
         ORDER BY p.created_at DESC 
         LIMIT ? OFFSET ?',
        ['%' . $searchQuery . '%', '%' . $searchQuery . '%', $pageInfo['limit'], $pageInfo['offset']]
    );
    
    $pageTitle = 'Kết Quả Tìm Kiếm: ' . escape($searchQuery);
} elseif ($searchTag) {
    // Tìm kiếm theo tag
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    
    $totalPosts = $db->fetchColumn(
        'SELECT COUNT(*) FROM posts WHERE tags LIKE ?',
        ['%' . $searchTag . '%']
    );
    
    $pageInfo = getPaginationInfo($currentPage, $totalPosts, POSTS_PER_PAGE);
    
    $posts = $db->fetchAll(
        'SELECT p.*, u.fullname, (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
         FROM posts p 
         JOIN users u ON p.user_id = u.id 
         WHERE p.tags LIKE ?
         ORDER BY p.created_at DESC 
         LIMIT ? OFFSET ?',
        ['%' . $searchTag . '%', $pageInfo['limit'], $pageInfo['offset']]
    );
    
    $pageTitle = 'Bài Viết Với Tag: #' . escape($searchTag);
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-5">
            <h2 class="display-5 fw-bold mb-3">
                <i class="fas fa-search text-primary"></i> <?php echo $pageTitle; ?>
            </h2>
            
            <!-- Form tìm kiếm -->
            <form action="search.php" method="GET" class="mb-4">
                <div class="input-group input-group-lg">
                    <input type="text" class="form-control" name="q" placeholder="Tìm kiếm bài viết..." 
                           value="<?php echo escape($_GET['q'] ?? ''); ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Tìm Kiếm
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Kết quả tìm kiếm -->
        <?php if (empty($searchQuery) && empty($searchTag)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Vui lòng nhập từ khóa tìm kiếm.
            </div>
        <?php elseif (empty($posts)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Không tìm thấy bài viết nào phù hợp.
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
            <?php if (isset($pageInfo) && $pageInfo['totalPages'] > 1): ?>
                <div class="mt-5">
                    <?php 
                    $paginationUrl = $searchQuery ? 'search.php?q=' . urlencode($searchQuery) : 'search.php?tag=' . urlencode($searchTag);
                    echo renderPagination($currentPage, $pageInfo['totalPages'], $paginationUrl); 
                    ?>
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
