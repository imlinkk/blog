<?php
/**
 * Admin - Quản lý người dùng
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

// Xử lý update role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'change-role') {
        $userId = (int)$_POST['user_id'];
        $newRole = $_POST['role'] === 'admin' ? 'admin' : 'user';
        
        // Không cho phép xóa role của chính mình
        if ($userId == $_SESSION['user_id'] && $newRole === 'user') {
            $_SESSION['error'] = 'Bạn không thể hạ quyền chính mình!';
        } else {
            $db->execute('UPDATE users SET role = ? WHERE id = ?', [$newRole, $userId]);
            $_SESSION['success'] = 'Cập nhật quyền thành công!';
        }
    } elseif ($_POST['action'] === 'delete-user') {
        $userId = (int)$_POST['user_id'];
        
        // Không cho phép xóa chính mình
        if ($userId == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Bạn không thể xóa chính mình!';
        } else {
            $db->execute('DELETE FROM users WHERE id = ?', [$userId]);
            $_SESSION['success'] = 'Xóa người dùng thành công!';
        }
    }
    
    header('Location: users.php');
    exit;
}

// Lấy trang hiện tại
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Lấy tổng số người dùng
$totalUsers = $db->fetchColumn('SELECT COUNT(*) FROM users');

// Lấy thông tin phân trang
$pageInfo = getPaginationInfo($currentPage, $totalUsers, 10);

// Lấy danh sách người dùng
$users = $db->fetchAll(
    'SELECT u.*, (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as post_count 
     FROM users u 
     ORDER BY u.created_at DESC 
     LIMIT ? OFFSET ?',
    [$pageInfo['limit'], $pageInfo['offset']]
);

$pageTitle = 'Quản Lý Người Dùng';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-6 fw-bold mb-0">
            <i class="fas fa-users text-primary"></i> Quản Lý Người Dùng
        </h1>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <?php echo showSuccess($_SESSION['success']); unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <?php echo showError($_SESSION['error']); unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (empty($users)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Không có người dùng nào.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên Đăng Nhập</th>
                    <th>Email</th>
                    <th>Tên Đầy Đủ</th>
                    <th>Quyền</th>
                    <th>Bài Viết</th>
                    <th>Ngày Tạo</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <span class="badge bg-secondary"><?php echo $user['id']; ?></span>
                        </td>
                        <td>
                            <strong><?php echo escape($user['username']); ?></strong>
                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                <span class="badge bg-warning ms-2">Bạn</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="mailto:<?php echo escape($user['email']); ?>" class="text-decoration-none">
                                <?php echo escape($user['email']); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo escape($user['fullname']); ?>
                        </td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <i class="fas fa-file-alt text-muted"></i> <?php echo $user['post_count']; ?>
                        </td>
                        <td>
                            <small class="text-muted"><?php echo formatDate($user['created_at'], 'd/m/Y H:i'); ?></small>
                        </td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    
                                    <!-- Change role -->
                                    <button type="submit" name="action" value="change-role" class="btn btn-sm btn-warning"
                                            onclick="if (!confirm('Thay đổi quyền?')) return false;">
                                        <i class="fas fa-crown"></i> 
                                        <?php echo $user['role'] === 'admin' ? 'Hạ xuống User' : 'Nâng lên Admin'; ?>
                                    </button>
                                    
                                    <!-- Delete -->
                                    <input type="hidden" name="role" value="<?php echo $user['role'] === 'admin' ? 'user' : 'admin'; ?>">
                                    <button type="submit" name="action" value="delete-user" class="btn btn-sm btn-danger"
                                            onclick="if (!confirm('Bạn chắc chắn muốn xóa?')) return false;">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">Không thể quản lý chính mình</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Phân trang -->
    <?php if ($pageInfo['totalPages'] > 1): ?>
        <div class="mt-5">
            <?php echo renderPagination($currentPage, $pageInfo['totalPages'], 'users.php'); ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="mt-4">
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay Lại Dashboard
    </a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
