<?php
/**
 * Trang đăng nhập
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate
    if (empty($username)) {
        $error = 'Tên đăng nhập không được để trống!';
    } elseif (empty($password)) {
        $error = 'Mật khẩu không được để trống!';
    } else {
        // Tìm user
        $user = $db->fetchOne(
            'SELECT * FROM users WHERE username = ?',
            [$username]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Chuyển hướng
            $redirect = $_GET['redirect'] ?? 'index.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        }
    }
}

$pageTitle = 'Đăng Nhập';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-5">
                <h2 class="card-title text-center mb-4">
                    <i class="fas fa-sign-in-alt text-primary"></i> Đăng Nhập
                </h2>
                
                <?php if ($error): ?>
                    <?php echo showError($error); ?>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên Đăng Nhập *</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo escape($_POST['username'] ?? ''); ?>" autofocus required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Mật Khẩu *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-lock"></i> Đăng Nhập
                    </button>
                </form>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-6">
                        <a href="forgot-password.php" class="text-decoration-none text-muted d-block text-center">
                            <i class="fas fa-key"></i> Quên Mật Khẩu?
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="register.php" class="text-decoration-none text-primary d-block text-center">
                            <i class="fas fa-user-plus"></i> Đăng Ký
                        </a>
                    </div>
                </div>
                
                <!-- Demo account info -->
                <div class="alert alert-info mt-4 mb-0">
                    <small>
                        <strong>Tài khoản demo:</strong><br>
                        Tên: <code>admin</code><br>
                        Pass: <code>admin123</code>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
