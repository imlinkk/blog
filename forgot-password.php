<?php
/**
 * Trang quên mật khẩu (đơn giản)
 */

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $message = showError('Email không được để trống!');
    } elseif (!isValidEmail($email)) {
        $message = showError('Email không hợp lệ!');
    } else {
        // Trong ứng dụng thực, chúng ta sẽ gửi email reset password
        // Ở đây chúng ta chỉ hiển thị thông báo
        $message = showInfo('Liên hệ với admin để đặt lại mật khẩu. Email: ' . CONTACT_EMAIL);
    }
}

$pageTitle = 'Quên Mật Khẩu';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-5">
                <h2 class="card-title text-center mb-4">
                    <i class="fas fa-key text-primary"></i> Quên Mật Khẩu
                </h2>
                
                <p class="text-center text-muted mb-4">
                    Nhập email của bạn, chúng tôi sẽ hướng dẫn bạn đặt lại mật khẩu.
                </p>
                
                <?php if ($message): ?>
                    <?php echo $message; ?>
                <?php endif; ?>
                
                <form method="POST" action="forgot-password.php">
                    <div class="mb-4">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo escape($_POST['email'] ?? ''); ?>" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-envelope"></i> Gửi Yêu Cầu
                    </button>
                </form>
                
                <hr class="my-4">
                
                <p class="text-center">
                    <a href="login.php" class="text-decoration-none text-primary">← Quay lại đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
