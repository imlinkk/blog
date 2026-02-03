-- ============================================================
-- Blog Database - SQL Script
-- ============================================================

-- Xóa database cũ nếu tồn tại
DROP DATABASE IF EXISTS blog_db;
CREATE DATABASE blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_db;

-- ============================================================
-- Bảng Users (Người dùng)
-- ============================================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng Categories (Danh mục)
-- ============================================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng Posts (Bài viết)
-- ============================================================
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    user_id INT NOT NULL,
    category_id INT,
    tags VARCHAR(255),
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_category (category_id),
    INDEX idx_slug (slug),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng Comments (Bình luận - có thể lồng nhau)
-- ============================================================
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    parent_id INT DEFAULT 0,  -- 0 = bình luận gốc, >0 = trả lời bình luận khác
    name VARCHAR(100),  -- Tên guest (nếu không đăng nhập)
    email VARCHAR(100),  -- Email guest
    content TEXT NOT NULL,
    user_id INT,  -- NULL nếu guest, không NULL nếu đã đăng nhập
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_post (post_id),
    INDEX idx_user (user_id),
    INDEX idx_parent (parent_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DỮ LIỆU MẪU
-- ============================================================

-- Users: 5 người dùng (1 admin, 4 user)
INSERT INTO users (username, email, password, fullname, role) VALUES
('admin', 'admin@blog.local', '$2y$10$YIjlrWxWaUEKK4C6.0qVruJhT0T2F7V0FpJ5dj.qKI8rJZFI3z9z2', 'Quản Trị Viên', 'admin'),
('john', 'john@example.com', '$2y$10$LpVjLPf2gRVLlnHqJ5X9FesHzvVxC0.4TQK6WfJQHPxgJvlDLmY3m', 'John Doe', 'user'),
('jane', 'jane@example.com', '$2y$10$LpVjLPf2gRVLlnHqJ5X9FesHzvVxC0.4TQK6WfJQHPxgJvlDLmY3m', 'Jane Smith', 'user'),
('bob', 'bob@example.com', '$2y$10$LpVjLPf2gRVLlnHqJ5X9FesHzvVxC0.4TQK6WfJQHPxgJvlDLmY3m', 'Bob Wilson', 'user'),
('alice', 'alice@example.com', '$2y$10$LpVjLPf2gRVLlnHqJ5X9FesHzvVxC0.4TQK6WfJQHPxgJvlDLmY3m', 'Alice Johnson', 'user');

-- Categories: 5 danh mục
INSERT INTO categories (name, slug, description) VALUES
('Công Nghệ', 'cong-nghe', 'Bài viết về công nghệ, lập trình'),
('Du Lịch', 'du-lich', 'Những chuyến du lịch thú vị'),
('Sức Khỏe', 'suc-khoe', 'Mẹo sức khỏe và cuộc sống lành mạnh'),
('Ẩm Thực', 'am-thuc', 'Công thức nấu ăn và chia sẻ món ăn ngon'),
('Thời Trang', 'thoi-trang', 'Xu hướng thời trang mới nhất');

-- Posts: 10 bài viết (Mật khẩu mẫu: admin123 cho tất cả)
INSERT INTO posts (title, slug, excerpt, content, user_id, category_id, tags, views) VALUES
('Hướng Dẫn Tạo Blog Với PHP', 'huong-dan-tao-blog-voi-php', 'Bài viết hướng dẫn chi tiết cách xây dựng một ứng dụng blog đơn giản bằng PHP thuần...', '<p>Trong bài viết này, tôi sẽ hướng dẫn bạn cách xây dựng một ứng dụng blog đơn giản bằng PHP thuần.</p><p><strong>Những điều bạn sẽ học:</strong></p><ul><li>Kiến trúc cơ bản của một ứng dụng web</li><li>Cách sử dụng PDO để kết nối database</li><li>Xử lý session và authentication</li><li>Phân quyền người dùng</li></ul><p>Blog là một dự án tuyệt vời để học các khái niệm web development cơ bản.</p>', 1, 1, 'php,blog,hướng dẫn', 150),
('Những Điều Cần Biết Về CSS Flexbox', 'nhung-dieu-can-biet-ve-css-flexbox', 'CSS Flexbox là một công cụ mạnh mẽ để tạo layout responsive...', '<p>CSS Flexbox làm cho việc tạo layout trở nên dễ dàng hơn.</p><p>Với Flexbox, bạn có thể:</p><ul><li>Căn chỉnh các phần tử một cách dễ dàng</li><li>Tạo layout responsive mà không cần viết quá nhiều code</li><li>Quản lý không gian và khoảng cách giữa các phần tử</li></ul><p>Đây là một kỹ năng cần thiết cho mọi web developer.</p>', 2, 1, 'css,flexbox,layout', 200),
('Khám Phá Đảo Bali: Thiên Đường Của Indonesia', 'kham-pha-dao-bali', 'Bali là một trong những điểm du lịch nổi tiếng nhất ở Đông Nam Á...', '<p>Bali không chỉ là một điểm du lịch, mà nó là một trải nghiệm cuộc sống.</p><p><strong>Những điều bạn không nên bỏ qua:</strong></p><ul><li>Các ngôi đền cổ xưa</li><li>Những bãi biển đẹp nhất</li><li>Nền văn hóa độc đáo</li><li>Ẩm thực địa phương tuyệt vời</li></ul><p>Nếu bạn yêu thích thiên nhiên và văn hóa, Bali là lựa chọn hoàn hảo cho bạn.</p>', 2, 2, 'du-lịch,bali,indonesia', 300),
('Công Thức Làm Bánh Choco Chip Ngon Tuyệt', 'cong-thuc-lam-banh-choco-chip', 'Hôm nay mình muốn chia sẻ công thức làm bánh choco chip giòn bên ngoài, mềm bên trong...', '<p>Công thức này rất dễ làm và sử dụng những nguyên liệu thông dụng.</p><p><strong>Nguyên liệu:</strong></p><ul><li>250g bơ mềm</li><li>200g đường</li><li>2 quả trứng</li><li>300g bột mì</li><li>200g socola chip</li></ul><p><strong>Cách làm:</strong></p><p>Trộn bơ và đường cho đến khi nhẹ và xốp. Thêm trứng lần lượt. Trộn bột mì vào nhẹ nhàng. Cuối cùng thêm socola chip và nướng ở 180°C trong 12-15 phút.</p>', 3, 4, 'bánh,choco,công thức', 250),
('5 Bài Tập Yoga Cho Người Bận Rộn', '5-bai-tap-yoga-cho-nguoi-ban-rong', 'Ngay cả khi bạn bận rộn, bạn vẫn có thể duy trì sức khỏe với những bài tập yoga đơn giản...', '<p>Yoga không chỉ giúp tăng tính linh hoạt mà còn giảm stress và lo âu.</p><p>Bạn chỉ cần 10-15 phút mỗi ngày để cảm nhận những lợi ích:</p><ul><li>Tăng năng lượng</li><li>Cải thiện tuần hoàn máu</li><li>Giảm căng thẳng cơ bắp</li></ul><p>Bắt đầu ngay hôm nay và thấy sự thay đổi trong vòng 1 tuần.</p>', 3, 3, 'yoga,sức khỏe,tập luyện', 180),
('Xu Hướng Thời Trang Mùa Hè 2024', 'xu-huong-thoi-trang-mua-he-2024', 'Mùa hè năm nay có những xu hướng thời trang mới mẻ và độc đáo...', '<p>Mùa hè 2024 mang lại những xu hướng thời trang tươi mới.</p><p><strong>Những màu sắc nổi bật:</strong></p><ul><li>Màu hồng pastel</li><li>Màu xanh biển</li><li>Màu vàng chanh</li></ul><p>Những chất liệu nhẹ và thoáng khí là lựa chọn hàng đầu cho mùa hè này.</p>', 4, 5, 'thời trang,mùa hè,xu hướng', 220),
('JavaScript ES6: Những Tính Năng Mới Mà Bạn Phải Biết', 'javascript-es6-tinh-nang-moi', 'ES6 đã thay đổi cách chúng ta viết JavaScript mãi mãi...', '<p>ES6 (ECMAScript 2015) giới thiệu nhiều tính năng tuyệt vời.</p><p><strong>Các tính năng chính:</strong></p><ul><li>Arrow functions</li><li>let và const</li><li>Classes</li><li>Template literals</li><li>Destructuring</li></ul><p>Những tính năng này làm cho code JavaScript sạch sẽ và dễ đọc hơn.</p>', 1, 1, 'javascript,es6,lập trình', 280),
('Cách Tạo Một Bộ Sưu Tập Thời Trang Capsule', 'cach-tao-mot-bo-suu-tap-thoi-trang-capsule', 'Capsule wardrobe là cách tuyệt vời để đơn giản hóa việc chọn quần áo hàng ngày...', '<p>Một capsule wardrobe bao gồm những món đồ cơ bản có thể kết hợp với nhau dễ dàng.</p><p><strong>Những items cần thiết:</strong></p><ul><li>Quần jean xanh</li><li>Áo trắng cơ bản</li><li>Áo len trung tính</li><li>Blazer đen</li><li>Giày da cơ bản</li></ul><p>Bằng cách này, bạn có thể tạo ra vô số tổ hợp từ các item giới hạn.</p>', 4, 5, 'thời trang,capsule,phong cách', 190),
('Máy Tính Lượng Tử: Tương Lai Của Công Nghệ', 'may-tinh-luong-tu-tuong-lai-cong-nghe', 'Máy tính lượng tử có thể sẽ thay đổi mọi thứ...', '<p>Máy tính lượng tử là một bước tiến lớn trong công nghệ.</p><p>Khác với máy tính cổ điển, chúng sử dụng các qubit có thể tồn tại ở nhiều trạng thái cùng lúc.</p><p><strong>Ứng dụng tiềm năng:</strong></p><ul><li>Phân tích dữ liệu khổng lồ</li><li>Mã hóa</li><li>Mô phỏng phân tử</li></ul><p>Mặc dù còn trong giai đoạn ban đầu, máy tính lượng tử hứa hẹn một tương lai rất sáng sủa.</p>', 1, 1, 'công nghệ,máy tính,lượng tử', 320),
('Công Thức Nước Ép Xanh Detox Hiệu Quả', 'cong-thuc-nuoc-ep-xanh-detox', 'Nước ép xanh là cách tuyệt vời để thanh lọc cơ thể và cấp năng lượng...', '<p>Công thức nước ép xanh này có đầy đủ chất dinh dưỡng và rất dễ làm.</p><p><strong>Nguyên liệu:</strong></p><ul><li>Bó rau cải</li><li>2 quả táo xanh</li><li>1 cục gừng tươi</li><li>Nước chanh</li><li>Nước lọc</li></ul><p><strong>Lợi ích:</strong></p><ul><li>Tăng năng lượng</li><li>Cải thiện tiêu hóa</li><li>Detox cơ thể</li><li>Cung cấp enzym sống</li></ul><p>Hãy uống ngay sau khi ép để có được lợi ích tối đa.</p>', 2, 3, 'detox,sức khỏe,nước ép', 210);

-- Comments: 20 bình luận
INSERT INTO comments (post_id, parent_id, name, email, content, user_id) VALUES
(1, 0, 'Nguyễn Văn A', 'vana@example.com', 'Bài viết rất hữu ích! Tôi mới bắt đầu học PHP và cảm thấy bài này giải thích rất rõ ràng.', NULL),
(1, 1, NULL, NULL, 'Cảm ơn bạn! Nếu có thắc mắc gì cứ hỏi nhé.', 2),
(1, 0, NULL, NULL, 'Mình cũng đang làm project tương tự. Có thể hỏi về phần validation data được không?', 3),
(2, 0, NULL, NULL, 'Flexbox thực sự là game-changer đối với tôi. Bài viết này giải thích rất tốt.', 4),
(2, 4, 'Trần Thị B', 'thib@example.com', 'Bạn ơi, bài viết về CSS Grid có phải dễ hơn Flexbox không?', NULL),
(3, 0, NULL, NULL, 'Tôi vừa về từ Bali. Bài viết của bạn giúp tôi không bỏ lỡ những điểm đẹp nào!', 1),
(3, 6, NULL, NULL, 'Được rồi, lần tới tôi chắc chắn sẽ khám phá thêm.', 2),
(4, 0, 'Lê Văn C', 'levanc@example.com', 'Công thức này quá ngon! Tôi vừa làm và gia đình rất thích.', NULL),
(4, 8, NULL, NULL, 'Vậy là bạn làm thành công rồi! Vui lắm 😄', 3),
(5, 0, NULL, NULL, 'Yoga thực sự giúp tôi giảm stress. Cảm ơn vì những bài tập này!', 4),
(6, 0, 'Phạm Thanh D', 'phamd@example.com', 'Mùa hè năm nay tôi sẽ chắc chắn áp dụng những xu hướng này.', NULL),
(6, 11, NULL, NULL, 'Tuyệt vời! Hy vọng bạn thích những gì bạn mua.', 1),
(7, 0, NULL, NULL, 'ES6 thực sự làm cho JavaScript trở nên tuyệt vời hơn. Arrow functions là yêu thích của tôi.', 2),
(7, 13, NULL, NULL, 'Đúng! Arrow functions tiết kiệm rất nhiều dòng code.', 1),
(8, 0, 'Hoàng Anh E', 'hoanganhe@example.com', 'Ý tưởng capsule wardrobe thực sự giúp tôi tiết kiệm thời gian vào buổi sáng.', NULL),
(8, 15, NULL, NULL, 'Đó là mục đích! Cuộc sống sẽ đơn giản hơn khi wardrobe được tổ chức tốt.', 4),
(9, 0, NULL, NULL, 'Máy tính lượng tử thực sự là tương lai. Bài viết rất chuyên sâu!', 1),
(10, 0, 'Tạ Thị F', 'tathif@example.com', 'Mình vừa thử công thức này và nó thực sự tuyệt vời. Nước ép xanh quá ngon!', NULL),
(10, 18, NULL, NULL, 'Thật vui được nghe! Hãy uống thường xuyên để có kết quả tốt nhất.', 3),
(10, 0, NULL, NULL, 'Có thể sử dụng máy xay sinh tố thường thay vì máy ép được không?', 2);

-- Cập nhật lượt xem bài viết dựa trên bình luận
UPDATE posts SET views = views + 50 WHERE id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
