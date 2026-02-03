# 📚 Blog PHP Thuần

> Ứng dụng blog đơn giản xây dựng bằng **PHP thuần** (vanilla PHP) với đầy đủ tính năng quản lý bài viết, bình luận phân cấp, xác thực người dùng và admin panel.

## ⚙️ Yêu Cầu Hệ Thống

| Yêu Cầu | Phiên Bản |
|---------|----------|
| PHP | 7.4+ |
| MySQL | 5.7+ |
| Web Server | Apache / Nginx |
| Local Server | Laragon hoặc tương tự |

## 🚀 Cài Đặt Nhanh

### 1. Clone Dự Án

```bash
git clone <repo-url>
cd Blog
```

### 2. Tạo Database

**Với phpMyAdmin (Laragon):**
- Mở Laragon → **Database** → **phpMyAdmin**
- Vào tab **SQL**, dán nội dung `blog_db.sql`
- Nhấp **Go**

**Với Command Line:**
```bash
mysql -u root -p < blog_db.sql
# Hoặc không có password:
mysql -u root < blog_db.sql
```

### 3. Cấu Hình Database (Nếu Khác)

Sửa file `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // Thêm password nếu có
define('DB_NAME', 'blog');
```

### 4. Chạy Ứng Dụng

**Laragon:**
- Start All → Truy cập `http://localhost/Blog`

**PHP Built-in Server:**
```bash
php -S localhost:8000
# Truy cập: http://localhost:8000
```

## 👤 Tài Khoản Demo

| Tài Khoản | Username | Password |
|-----------|----------|----------|
| **Admin** | `admin` | `admin123` |
| **User 1** | `john` | `admin123` |
| **User 2** | `jane` | `admin123` |
| **User 3** | `bob` | `admin123` |
| **User 4** | `alice` | `admin123` |

## 📁 Cấu Trúc Dự Án

```
Blog/
├── admin/                      # Admin Panel
│   ├── index.php              # Dashboard
│   ├── posts.php              # Quản lý bài viết
│   ├── users.php              # Quản lý người dùng
│   ├── comments.php           # Quản lý bình luận
│   └── categories.php         # Quản lý danh mục
├── assets/                     # Tài nguyên tĩnh
│   ├── css/style.css          # CSS custom
│   └── js/script.js           # JavaScript custom
├── includes/                   # Thư mục include
│   ├── config.php             # Cấu hình chung
│   ├── database.php           # Class PDO Database
│   ├── functions.php          # Hàm hỗ trợ
│   ├── header.php             # Header template
│   └── footer.php             # Footer template
├── uploads/                    # Upload files
├── index.php                   # Trang chủ
├── register.php                # Đăng ký
├── login.php                   # Đăng nhập
├── logout.php                  # Đăng xuất
├── forgot-password.php         # Quên mật khẩu
├── create-post.php             # Viết bài mới
├── edit-post.php               # Chỉnh sửa bài
├── delete-post.php             # Xóa bài
├── post.php                    # Chi tiết bài viết
├── search.php                  # Tìm kiếm
├── category.php                # Xem danh mục
├── my-posts.php                # Bài viết của tôi
├── comment-handler.php         # Xử lý bình luận
├── blog_db.sql                 # Database SQL
└── README.md                   # Tài liệu này
```

## ✨ Tính Năng Chính

### 🔐 Xác Thực & Bảo Mật
- Đăng ký / Đăng nhập / Đăng xuất
- Quên mật khẩu (cơ bản)
- Mã hóa mật khẩu `password_hash()`
- Kiểm tra quyền trên mỗi trang
- Chống XSS, SQL Injection

### 📝 Quản Lý Bài Viết
- Tạo / Chỉnh sửa / Xóa bài viết
- Tự động tạo slug từ tiêu đề
- Hỗ trợ danh mục & tags
- Tăng lượt xem tự động
- Phân trang danh sách bài

### 💬 Bình Luận Phân Cấp
- Bình luận gốc & reply (tối đa 3 cấp)
- Cho phép guest & member bình luận
- Xóa bình luận (tác giả + admin)
- Hiển thị theo thời gian

### 🔍 Tìm Kiếm & Duyệt
- Tìm kiếm theo tiêu đề / nội dung / tags
- Xem bài viết theo danh mục
- Bài viết liên quan

### 👨‍💼 Admin Panel
- Dashboard với thống kê
- Quản lý tất cả bài viết / người dùng / bình luận / danh mục
- Thay đổi role người dùng

### 🎨 Giao Diện
- Bootstrap 5 responsive
- Mobile-friendly
- Icons Font Awesome
- Form validation

## 🔧 Các Hàm Hỗ Trợ Chính

```php
// Xác thực
isLoggedIn()              // Kiểm tra đã đăng nhập?
isAdmin()                 // Kiểm tra quyền admin?
getCurrentUser()          // Lấy thông tin user hiện tại

// Bảo mật
escape($text)             // Chống XSS
filterHTML($html)         // Lọc HTML cho phép tag cơ bản
isValidEmail($email)      // Validate email
isValidPassword($pass)    // Kiểm tra mật khẩu mạnh

// Bài viết
canEditPost($id, $uid)    // Kiểm tra quyền chỉnh sửa
canDeletePost($id, $uid)  // Kiểm tra quyền xóa
createSlug($title)        // Tạo slug từ tiêu đề
getTotalPosts()           // Tổng số bài viết

// UI
renderPagination()        // Phân trang HTML
formatDate($date)         // Định dạng ngày
showSuccess($msg)         // Alert thành công
showError($msg)           // Alert lỗi
```

## 🗄️ Database Schema

### Users
```sql
id          INT PRIMARY KEY AUTO_INCREMENT
username    VARCHAR(50) UNIQUE NOT NULL
email       VARCHAR(100) UNIQUE NOT NULL
password    VARCHAR(255) NOT NULL (bcrypt)
fullname    VARCHAR(100)
role        ENUM('user', 'admin') DEFAULT 'user'
created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

### Posts
```sql
id          INT PRIMARY KEY AUTO_INCREMENT
title       VARCHAR(255) NOT NULL
slug        VARCHAR(255) UNIQUE NOT NULL
excerpt     TEXT
content     LONGTEXT
user_id     INT NOT NULL (FK → users)
category_id INT (FK → categories)
tags        VARCHAR(255)
views       INT DEFAULT 0
created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

### Comments
```sql
id          INT PRIMARY KEY AUTO_INCREMENT
post_id     INT NOT NULL (FK → posts)
parent_id   INT DEFAULT 0 (0 = root)
name        VARCHAR(100) (guest name)
email       VARCHAR(100)
content     TEXT
user_id     INT (FK → users, NULL = guest)
created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

### Categories
```sql
id          INT PRIMARY KEY AUTO_INCREMENT
name        VARCHAR(100) NOT NULL
slug        VARCHAR(100) UNIQUE NOT NULL
description TEXT
created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

## 🔒 Bảo Mật

✅ **Đã cài đặt:**
- Password hashing với `password_hash()` & `password_verify()`
- Prepared statements (PDO) chống SQL Injection
- XSS protection với `htmlspecialchars()`
- HTML filtering cho nội dung bài viết
- Session-based authentication
- Role-based access control

⚠️ **Khuyến cáo:**
- Luôn validate dữ liệu đầu vào
- Sử dụng HTTPS trong production
- Thay đổi mật khẩu admin mặc định
- Cập nhật PHP thường xuyên

## 📊 Hướng Dẫn Sử Dụng

### Viết bài mới
1. Đăng nhập → Nhấp **"Viết Bài"**
2. Điền thông tin & nhấp **"Đăng Bài"**

### Bình luận & reply
1. Mở chi tiết bài viết
2. Điền form bình luận → Nhấp **"Gửi"**
3. Nhấp **"Trả lời"** trên bình luận để reply (tối đa 3 cấp)

### Quản lý admin
1. Đăng nhập với tài khoản admin
2. Nhấp **"Admin"** trên menu top
3. Chọn chức năng: Bài viết / Người dùng / Bình luận / Danh mục

## 🐛 Troubleshooting

| Lỗi | Giải Pháp |
|-----|----------|
| "Kết nối database thất bại" | Kiểm tra config.php, đảm bảo host/user/password/database đúng |
| "404 Not Found" | Kiểm tra routing, tạo .htaccess nếu dùng Apache |
| "Bài viết không tìm thấy" | Kiểm tra database có dữ liệu, hoặc ID bài viết có tồn tại |
| "Không thể đăng nhập" | Kiểm tra username/password đúng |

## 📝 Ghi Chú

- Dữ liệu mẫu đã có sẵn trong file SQL (10 bài, 20 bình luận, 5 user)
- Slug tự động tạo từ tiêu đề
- Bình luận phân cấp tối đa 3 cấp
- Phân trang mặc định: 5 bài viết/trang, 10 bình luận/trang
- HTML filter cho phép tags cơ bản (p, strong, em, ul, ol, img, ...)

## 📄 Giấy Phép

Dự án tự do sử dụng cho mục đích học tập & phát triển.


 
=======
**Tạo bởi:** Sinh viên PHP  
**Ngày:** Tháng 2, 2026  
**Phiên bản:** 1.0

 


