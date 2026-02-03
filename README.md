# Blog PHP Thuần - Hướng Dẫn Cài Đặt & Sử Dụng

Một ứng dụng blog đơn giản được xây dựng hoàn toàn bằng **PHP thuần** (không sử dụng framework) với đầy đủ tính năng quản lý bài viết, bình luận phân cấp, xác thực người dùng và quản lý admin.

## 📋 Yêu Cầu

- **PHP** 7.4 hoặc cao hơn
- **MySQL** 5.7 hoặc cao hơn
- **Web Server** (Apache, Nginx, v.v.)
- **Laragon** hoặc bất kỳ local server nào

## 🚀 Cài Đặt Nhanh

### 1. Clone/Tải Về Dự Án

```bash
# Thư mục gốc của dự án
c:\laragon\www\Blog
```

### 2. Tạo Database

**Cách 1: Sử dụng phpMyAdmin (Laragon)**

- Mở Laragon, nhấp vào **Database** → **phpMyAdmin**
- Truy cập giao diện phpMyAdmin
- Chọn tab **SQL** và dán nội dung file `blog_db.sql`
- Nhấp **Go** để chạy script

**Cách 2: Sử dụng Command Line**

```bash
mysql -u root -p < blog_db.sql
```

Nếu không có password:
```bash
mysql -u root < blog_db.sql
```

### 3. Cấu Hình Database (Tùy Chọn)

Nếu cấu hình database khác, chỉnh sửa file `includes/config.php`:

```php
define('DB_HOST', 'localhost');    // Thay đổi host
define('DB_USER', 'root');         // Thay đổi user
define('DB_PASS', '');             // Thêm password nếu có
define('DB_NAME', 'blog');      // Thay đổi database name
```

### 4. Chạy Ứng Dụng

**Nếu sử dụng Laragon:**

- Mở Laragon
- Nhấp **Start All** 
- Truy cập: `http://localhost/Blog`

**Nếu sử dụng PHP Built-in Server:**

```bash
cd c:\laragon\www\Blog
php -S localhost:8000
```

Sau đó truy cập: `http://localhost:8000`

## 👤 Tài Khoản Demo

Sau khi cài đặt, bạn có thể đăng nhập bằng tài khoản admin:

| Thông Tin | Chi Tiết |
|-----------|----------|
| **Username** | `admin` |
| **Password** | `admin123` |
| **Email** | `admin@blog.local` |

**Các tài khoản user khác:**
- username: `john`, password: `admin123`
- username: `jane`, password: `admin123`
- username: `bob`, password: `admin123`
- username: `alice`, password: `admin123`

## 📁 Cấu Trúc Dự Án

```
Blog/
├── admin/                          # Admin Panel
│   ├── index.php                  # Dashboard
│   ├── posts.php                  # Quản lý bài viết
│   ├── users.php                  # Quản lý người dùng
│   ├── comments.php               # Quản lý bình luận
│   └── categories.php             # Quản lý danh mục
├── assets/                         # Tài nguyên tĩnh
│   ├── css/
│   │   └── style.css              # CSS custom
│   └── js/
│       └── script.js              # JavaScript custom
├── includes/                       # Thư mục include
│   ├── config.php                 # Cấu hình chung
│   ├── database.php               # Class PDO Database
│   ├── functions.php              # Hàm hỗ trợ chung
│   ├── header.php                 # Header template
│   └── footer.php                 # Footer template
├── uploads/                        # Upload files (tạo sau)
├── index.php                       # Trang chủ
├── register.php                    # Đăng ký
├── login.php                       # Đăng nhập
├── logout.php                      # Đăng xuất
├── forgot-password.php             # Quên mật khẩu
├── create-post.php                 # Viết bài mới
├── edit-post.php                   # Chỉnh sửa bài
├── delete-post.php                 # Xóa bài
├── post.php                        # Chi tiết bài viết
├── search.php                      # Tìm kiếm
├── category.php                    # Xem danh mục
├── my-posts.php                    # Bài viết của tôi
├── comment-handler.php             # Xử lý bình luận
├── blog_db.sql                     # SQL database
└── README.md                       # Tài liệu này
```

## ✨ Tính Năng Chính

### 🔐 Xác Thực & Bảo Mật
- ✅ Đăng ký tài khoản mới
- ✅ Đăng nhập/Đăng xuất
- ✅ Quên mật khẩu (cơ bản)
- ✅ Mã hóa mật khẩu bằng `password_hash()`
- ✅ Kiểm tra quyền trên mỗi page
- ✅ Chống XSS bằng `htmlspecialchars()`

### 📝 Quản Lý Bài Viết
- ✅ Tạo bài viết mới (chỉ user đã đăng nhập)
- ✅ Chỉnh sửa bài viết (chủ bài + admin)
- ✅ Xóa bài viết (chủ bài + admin)
- ✅ Tự động tạo slug từ tiêu đề
- ✅ Hỗ trợ danh mục & tags
- ✅ Tăng lượt xem tự động
- ✅ Hiển thị tóm tắt trên trang chủ
- ✅ Phân trang danh sách bài

### 💬 Bình Luận Phân Cấp
- ✅ Bình luận gốc & reply (tối đa 3 cấp)
- ✅ Form bình luận cho guest & member
- ✅ Xóa bình luận (tác giả + admin)
- ✅ Hiển thị danh sách bình luận theo thời gian
- ✅ Phân trang bình luận

### 🔍 Tìm Kiếm & Duyệt
- ✅ Tìm kiếm theo tiêu đề/nội dung
- ✅ Tìm kiếm theo tags
- ✅ Xem bài viết theo danh mục
- ✅ Liên kết bài viết liên quan

### 👨‍💼 Admin Panel
- ✅ Dashboard với thống kê
- ✅ Quản lý tất cả bài viết
- ✅ Quản lý người dùng (thay đổi role, xóa)
- ✅ Quản lý bình luận (xóa spam)
- ✅ Quản lý danh mục

### 🎨 Giao Diện & UX
- ✅ Bootstrap 5 responsive
- ✅ Dark navigation bar
- ✅ Clean card-based layout
- ✅ Icons Font Awesome
- ✅ Mobile-friendly
- ✅ Form validation

## 🔧 Các Hàm Hỗ Trợ Chính

### Xác Thực
```php
isLoggedIn()              // Kiểm tra đã đăng nhập?
isAdmin()                 // Kiểm tra quyền admin?
getCurrentUser()          // Lấy thông tin user hiện tại
```

### Bảo Mật
```php
escape($text)             // Chống XSS
filterHTML($html)         // Lọc HTML chỉ cho phép tag cơ bản
isValidEmail($email)      // Validate email
isValidPassword($pass)    // Kiểm tra mật khẩu mạnh
```

### Bài Viết
```php
canEditPost($id, $uid)    // Kiểm tra quyền chỉnh sửa
canDeletePost($id, $uid)  // Kiểm tra quyền xóa
createSlug($title)        // Tạo slug từ tiêu đề
getTotalPosts()           // Tổng số bài viết
```

### UI Helpers
```php
renderPagination($page, $total, $url)  // Phân trang HTML
formatDate($date, $format)             // Định dạng ngày
escape($text)                          // Escape output
showSuccess($msg)                      // Alert thành công
showError($msg)                        // Alert lỗi
```

## 🗄️ Database Schema

### Users Table
```sql
id              INT PRIMARY KEY
username        VARCHAR(50) UNIQUE
email           VARCHAR(100) UNIQUE
password        VARCHAR(255) - Mã hóa bcrypt
fullname        VARCHAR(100)
role            ENUM('user', 'admin')
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Posts Table
```sql
id              INT PRIMARY KEY
title           VARCHAR(255)
slug            VARCHAR(255) UNIQUE
excerpt         TEXT
content         LONGTEXT - Hỗ trợ HTML
user_id         INT (FK → users)
category_id     INT (FK → categories)
tags            VARCHAR(255)
views           INT (default: 0)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Comments Table
```sql
id              INT PRIMARY KEY
post_id         INT (FK → posts)
parent_id       INT (FK → comments) - 0 = root comment
name            VARCHAR(100) - Guest name
email           VARCHAR(100) - Guest email
content         TEXT
user_id         INT (FK → users, NULL = guest)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Categories Table
```sql
id              INT PRIMARY KEY
name            VARCHAR(100)
slug            VARCHAR(100) UNIQUE
description     TEXT
created_at      TIMESTAMP
```

## 🔒 Bảo Mật

### Được Cài Đặt
✅ Password hashing với `password_hash()` & `password_verify()`  
✅ Prepared statements (PDO) chống SQL Injection  
✅ XSS protection với `htmlspecialchars()`  
✅ HTML filtering cho nội dung bài viết  
✅ Session-based authentication  
✅ Role-based access control  

### Khuyến Cáo
⚠️ Luôn validate dữ liệu đầu vào  
⚠️ Sử dụng HTTPS trong production  
⚠️ Thay đổi mật khẩu admin mặc định  
⚠️ Cập nhật PHP thường xuyên  

## 📊 Ví Dụ Sử Dụng

### Tạo bài viết mới
1. Đăng nhập vào hệ thống
2. Nhấp **"Viết Bài"** trên menu top
3. Điền thông tin & nhấp **"Đăng Bài"**

### Bình luận với reply
1. Mở chi tiết bài viết
2. Điền form bình luận & nhấp **"Gửi"**
3. Nhấp **"Trả lời"** trên bình luận để reply (tối đa 3 cấp)

### Quản lý admin
1. Đăng nhập với tài khoản admin
2. Nhấp **"Admin"** trên menu top
3. Chọn chức năng: Bài viết, Người dùng, Bình luận, Danh mục

## 🐛 Troubleshooting

### "Kết nối database thất bại"
→ Kiểm tra config.php, đảm bảo host, user, password, database name đúng

### "404 Not Found"
→ Đảm bảo routing đúng, hoặc tạo .htaccess cho Apache

### "Bài viết không tìm thấy"
→ Kiểm tra database có dữ liệu không, hoặc ID bài viết có tồn tại

### "Không thể đăng nhập"
→ Kiểm tra username/password đúng, hoặc thử lại

## 📝 Ghi Chú

- Dữ liệu mẫu đã được thêm trong file SQL (10 bài, 20 bình luận, 5 người dùng)
- Slug tự động tạo từ tiêu đề, không được sửa tay
- Comments phân cấp hỗ trợ tối đa 3 cấp
- Phân trang mặc định 5 bài viết/trang, 10 bình luận/trang
- HTML filter chỉ cho phép tag cơ bản (p, strong, em, ul, ol, img, v.v.)

## 📞 Hỗ Trợ & Liên Hệ

Nếu có vấn đề hoặc câu hỏi, vui lòng liên hệ:

📧 Email: `admin@blog.local`  
🌐 Website: `http://localhost/Blog`

## 📄 Giấy Phép

Dự án này tự do sử dụng cho mục đích học tập & phát triển.

---

**Tạo bởi:** Sinh viên PHP  
**Ngày:** Tháng 2, 2026  
**Phiên bản:** 1.0
#   b l o g 
 
 
