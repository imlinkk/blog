<?php
/**
 * Admin - Quản lý danh mục
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

$error = '';
$success = '';

// Xử lý thêm danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if (empty($name)) {
        $error = 'Tên danh mục không được để trống!';
    } else {
        $slug = createSlug($name);
        
        // Kiểm tra slug đã tồn tại?
        $existing = $db->fetchOne('SELECT id FROM categories WHERE slug = ?', [$slug]);
        if ($existing) {
            $error = 'Danh mục này đã tồn tại!';
        } else {
            $db->insert(
                'INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)',
                [$name, $slug, $description]
            );
            $_SESSION['success'] = 'Thêm danh mục thành công!';
            header('Location: categories.php');
            exit;
        }
    }
}

// Xử lý xóa danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $categoryId = (int)$_POST['category_id'];
    
    // Xóa danh mục và cập nhật bài viết (set category_id = NULL)
    $db->execute('UPDATE posts SET category_id = NULL WHERE category_id = ?', [$categoryId]);
    $db->execute('DELETE FROM categories WHERE id = ?', [$categoryId]);
    
    $_SESSION['success'] = 'Xóa danh mục thành công!';
    header('Location: categories.php');
    exit;
}

// Lấy danh sách danh mục
$categories = $db->fetchAll(
    'SELECT c.*, (SELECT COUNT(*) FROM posts WHERE category_id = c.id) as post_count 
     FROM categories c 
     ORDER BY c.name'
);

$pageTitle = 'Quản Lý Danh Mục';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-6 fw-bold mb-0">
            <i class="fas fa-folder text-primary"></i> Quản Lý Danh Mục
        </h1>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <?php echo showSuccess($_SESSION['success']); unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <?php echo showError($_SESSION['error']); unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if ($error): ?>
    <?php echo showError($error); ?>
<?php endif; ?>

<div class="row mb-5">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-plus text-primary"></i> Thêm Danh Mục Mới
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="categories.php">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên Danh Mục *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Ví dụ: Công Nghệ"
                               value="<?php echo escape($_POST['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô Tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Mô tả danh mục (tùy chọn)"><?php echo escape($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus"></i> Thêm Danh Mục
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-list text-primary"></i> Danh Sách Danh Mục
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($categories)): ?>
                    <p class="p-3 text-muted mb-0">Chưa có danh mục nào.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($categories as $cat): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?php echo escape($cat['name']); ?></h6>
                                    <small class="text-muted"><?php echo escape($cat['post_count']); ?> bài viết</small>
                                </div>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="category_id" value="<?php echo $cat['id']; ?>">
                                    <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Xóa danh mục này? Các bài viết sẽ không còn danh mục.');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay Lại Dashboard
    </a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
