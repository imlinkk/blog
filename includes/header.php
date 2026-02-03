<?php
/**
 * Header - Phần đầu của website
 */

if (!isset($_SESSION)) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? escape($pageTitle) . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #3a89dd;
            --secondary-color: #6c757d;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, #275484 0%, #418ee0 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: white !important;
        }
        
        .navbar-toggler {
            border-color: rgba(255,255,255,0.3);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .btn-custom {
            border-radius: 20px;
            padding: 8px 20px;
            font-size: 0.9rem;
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <i class="fas fa-blog"></i> <?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>">
                            <i class="fas fa-home"></i> Trang Chủ
                        </a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/create-post.php">
                                <i class="fas fa-pen-fancy"></i> Viết Bài
                            </a>
                        </li>
                        
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/">
                                    <i class="fas fa-cogs"></i> Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?php echo escape($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/my-posts.php">Bài Viết Của Tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Đăng Xuất
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light btn-custom ms-2" href="<?php echo SITE_URL; ?>/login.php">
                                <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-light btn-custom ms-2" href="<?php echo SITE_URL; ?>/register.php">
                                <i class="fas fa-user-plus"></i> Đăng Ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container" style="margin-top: 30px; margin-bottom: 30px; min-height: calc(100vh - 200px);">
