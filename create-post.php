<?php
/**
 * Tạo bài viết mới
 * Chỉ cho phép user đã đăng nhập
 */

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

// Kiểm tra đã đăng nhập?
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Bạn phải đăng nhập để viết bài!';
    header('Location: login.php?redirect=create-post.php');
    exit;
}

$error = '';
$success = '';
$categories = $db->fetchAll('SELECT * FROM categories ORDER BY name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $categoryId = (int)$_POST['category_id'] ?? null;
    $tags = trim($_POST['tags'] ?? '');
    
    // Validate
    if (empty($title)) {
        $error = 'Tiêu đề không được để trống!';
    } elseif (strlen($title) > 255) {
        $error = 'Tiêu đề quá dài (tối đa 255 ký tự)!';
    } elseif (empty($excerpt)) {
        $error = 'Tóm tắt không được để trống!';
    } elseif (strlen($excerpt) > 500) {
        $error = 'Tóm tắt quá dài (tối đa 500 ký tự)!';
    } elseif (empty($content)) {
        $error = 'Nội dung bài viết không được để trống!';
    } elseif (strlen($content) > 100000) {
        $error = 'Nội dung quá dài (tối đa 100000 ký tự)!';
    } else {
        // Kiểm tra category nếu được chọn
        if ($categoryId > 0) {
            $category = $db->fetchOne('SELECT id FROM categories WHERE id = ?', [$categoryId]);
            if (!$category) {
                $categoryId = null;
            }
        } else {
            $categoryId = null;
        }
        
        // Tạo slug
        $slug = createSlug($title);
        
        // Kiểm tra slug đã tồn tại?
        $existingSlug = $db->fetchOne('SELECT id FROM posts WHERE slug = ?', [$slug]);
        if ($existingSlug) {
            $slug = $slug . '-' . time();
        }
        
        // Lọc HTML
        $content = filterHTML($content);
        
        // Lưu bài viết
        try {
            $db->insert(
                'INSERT INTO posts (title, slug, excerpt, content, user_id, category_id, tags) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$title, $slug, $excerpt, $content, $_SESSION['user_id'], $categoryId, $tags]
            );
            
            $success = 'Bài viết đã được tạo thành công!';
        } catch (Exception $e) {
            $error = 'Đã xảy ra lỗi: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Viết Bài Viết Mới';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-5">
                <h2 class="card-title mb-4">
                    <i class="fas fa-pen-fancy text-primary"></i> Viết Bài Viết Mới
                </h2>
                
                <?php if ($error): ?>
                    <?php echo showError($error); ?>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo showSuccess($success); ?>
                        <p class="mt-3">
                            <a href="my-posts.php" class="btn btn-primary btn-sm">Xem bài viết của tôi</a>
                            <a href="index.php" class="btn btn-secondary btn-sm">Quay lại trang chủ</a>
                        </p>
                    </div>
                <?php else: ?>
                    <form method="POST" action="create-post.php" novalidate>
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu Đề *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   maxlength="255" placeholder="Nhập tiêu đề bài viết"
                                   value="<?php echo escape($_POST['title'] ?? ''); ?>" required>
                            <small class="form-text text-muted">Tối đa 255 ký tự.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Danh Mục</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" 
                                                <?php echo isset($_POST['category_id']) && $_POST['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo escape($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <input type="text" class="form-control" id="tags" name="tags" 
                                       placeholder="Phân tách bằng dấu phẩy (vd: php,blog,hướng dẫn)"
                                       value="<?php echo escape($_POST['tags'] ?? ''); ?>">
                                <small class="form-text text-muted">Cách nhau bằng dấu phẩy</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Tóm Tắt *</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3" 
                                      maxlength="500" placeholder="Viết tóm tắt ngắn về bài viết"
                                      required><?php echo escape($_POST['excerpt'] ?? ''); ?></textarea>
                            <small class="form-text text-muted">Tối đa 500 ký tự.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội Dung *</label>
                            <textarea class="form-control" id="content" name="content" rows="10" 
                                      placeholder="Nội dung bài viết"
                                      required><?php echo escape($_POST['content'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Đăng Bài
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
