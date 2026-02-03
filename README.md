# 📘 Blog PHP Thuần

Ứng dụng blog đơn giản xây dựng bằng **PHP thuần (vanilla PHP)** với đầy đủ tính năng quản lý bài viết, bình luận phân cấp, xác thực người dùng và admin panel.

---

## ⚙️ Yêu Cầu Hệ Thống

| Thành phần   | Phiên bản                     |
| ------------ | ----------------------------- |
| PHP          | >= 7.4                        |
| MySQL        | >= 5.7                        |
| Web Server   | Apache / Nginx                |
| Local Server | Laragon / XAMPP / tương đương |

---

## 🚀 Cài Đặt Nhanh

### 1. Clone dự án

```bash
git clone <link-repo>
cd Blog
```

### 2. Tạo Database

**Cách 1: phpMyAdmin (Laragon)**

* Mở Laragon → Database → phpMyAdmin
* Vào tab **SQL**
* Import file `blog_db.sql`

**Cách 2: Command Line**

```bash
mysql -u root -p < blog_db.sql
```

---

### 3. Cấu hình Database

Sửa file: `includes/config.php`

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'blog');
```

---

### 4. Chạy ứng dụng

**Laragon:**

```
Start All → http://localhost/Blog
```

**PHP Built-in Server:**

```bash
php -S localhost:8000
```

Truy cập: [http://localhost:8000](http://localhost:8000)

---

## 👤 Tài Khoản Demo

| Role   | Username | Password |
| ------ | -------- | -------- |
| Admin  | admin    | admin123 |
| User 1 | john     | admin123 |
| User 2 | jane     | admin123 |
| User 3 | bob      | admin123 |
| User 4 | alice    | admin123 |

---

## 📁 Cấu Trúc Dự Án

```
Blog/
├── admin/
│   ├── index.php        # Dashboard
│   ├── posts.php        # Quản lý bài viết
│   ├── users.php        # Quản lý người dùng
│   ├── comments.php     # Quản lý bình luận
│   └── categories.php   # Quản lý danh mục
│
├── assets/
│   ├── css/style.css
│   └── js/script.js
│
├── includes/
│   ├── config.php
│   ├── database.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── uploads/
├── index.php            # Trang chủ
├── login.php
├── register.php
├── logout.php
├── post.php             # Chi tiết bài viết
├── create-post.php
├── edit-post.php
├── delete-post.php
├── search.php
├── category.php
├── my-posts.php
├── comment-handler.php
├── forgot-password.php
├── blog_db.sql
└── README.md
```

---

## ✨ Tính Năng Chính

### 🔐 Xác thực & Bảo mật

* Đăng ký / Đăng nhập / Đăng xuất
* Hash password (`password_hash()`)
* Phân quyền user / admin
* Chống XSS, SQL Injection

### 📝 Quản lý bài viết

* Tạo / Sửa / Xóa bài viết
* Upload ảnh
* Tự động tạo slug
* Phân trang
* Danh mục & tags

### 💬 Bình luận phân cấp

* Bình luận gốc + reply (tối đa 3 cấp)
* Guest & member đều có thể bình luận
* Xóa bình luận (admin + tác giả)

### 🔍 Tìm kiếm & duyệt

* Tìm theo tiêu đề / nội dung / tag
* Xem theo danh mục
* Bài viết liên quan

### 🛠 Admin Panel

* Dashboard thống kê
* Quản lý bài viết / user / bình luận / danh mục
* Phân quyền role

---

## 🎨 Giao diện

* Bootstrap responsive
* Mobile-friendly
* Font Awesome icons

---

## 📌 Ghi chú

> Dự án phục vụ mục đích học tập PHP thuần + MVC + CRUD + Auth + Admin Panel

---


