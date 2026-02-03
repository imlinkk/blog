<?php
/**
 * Trang đăng ký tài khoản
 */

session_start();

// Nếu đã đăng nhập thì chuyển hướng về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $fullname = trim($_POST['fullname'] ?? '');
    
    // Validate
    if (empty($username)) {
        $error = 'Tên đăng nhập không được để trống!';
    } elseif (!isValidUsername($username)) {
        $error = 'Tên đăng nhập phải 3-20 ký tự (chữ, số, gạch dưới)!';
    } elseif (empty($email)) {
        $error = 'Email không được để trống!';
    } elseif (!isValidEmail($email)) {
        $error = 'Email không hợp lệ!';
    } elseif (empty($password)) {
        $error = 'Mật khẩu không được để trống!';
    } elseif (!isValidPassword($password)) {
        $error = 'Mật khẩu tối thiểu 6 ký tự!';
    } elseif ($password !== $password_confirm) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } elseif (empty($fullname)) {
        $error = 'Tên đầy đủ không được để trống!';
    } else {
        // Kiểm tra username/email đã tồn tại?
        $existingUser = $db->fetchOne(
            'SELECT id FROM users WHERE username = ? OR email = ?',
            [$username, $email]
        );
        
        if ($existingUser) {
            $error = 'Tên đăng nhập hoặc email đã được sử dụng!';
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Lưu vào database
            try {
                $db->insert(
                    'INSERT INTO users (username, email, password, fullname, role) VALUES (?, ?, ?, ?, ?)',
                    [$username, $email, $hashedPassword, $fullname, 'user']
                );
                $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
            } catch (Exception $e) {
                $error = 'Đã xảy ra lỗi: ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Đăng Ký';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-5">
                <h2 class="card-title text-center mb-4">
                    <i class="fas fa-user-plus text-primary"></i> Đăng Ký Tài Khoản
                </h2>
                
                <?php if ($error): ?>
                    <?php echo showError($error); ?>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <?php echo showSuccess($success); ?>
                    <div class="text-center mt-3">
                        <p>Hãy <a href="login.php" class="btn btn-primary btn-sm">đăng nhập tại đây</a></p>
                    </div>
                <?php else: ?>
                    <form method="POST" action="register.php" novalidate>
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Tên Đầy Đủ *</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" 
                                   value="<?php echo escape($_POST['fullname'] ?? ''); ?>" required>
                            <small class="form-text text-muted">Tên của bạn sẽ hiển thị dưới bài viết.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên Đăng Nhập *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="3-20 ký tự (a-z, 0-9, _)"
                                   value="<?php echo escape($_POST['username'] ?? ''); ?>" required>
                            <small class="form-text text-muted">Dùng để đăng nhập vào tài khoản.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo escape($_POST['email'] ?? ''); ?>" required>
                            <small class="form-text text-muted">Email sẽ được dùng để đặt lại mật khẩu.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật Khẩu *</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Tối thiểu 6 ký tự" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Xác Nhận Mật Khẩu *</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Đăng Ký
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <p class="text-center text-muted">
                        Đã có tài khoản? <a href="login.php" class="text-primary text-decoration-none">Đăng nhập ngay</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
