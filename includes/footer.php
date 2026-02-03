    </div>
    <!-- End Main Content -->
    
    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5><i class="fas fa-blog"></i> <?php echo SITE_NAME; ?></h5>
                    <p class="text-secondary">Chia sẻ những bài viết hay và bữa tiệc tri thức với cộng đồng.</p>
                </div>
                
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5>Danh Mục</h5>
                    <ul class="list-unstyled text-secondary">
                        <li><a href="#" class="text-decoration-none text-secondary">Công Nghệ</a></li>
                        <li><a href="#" class="text-decoration-none text-secondary">Du Lịch</a></li>
                        <li><a href="#" class="text-decoration-none text-secondary">Sức Khỏe</a></li>
                        <li><a href="#" class="text-decoration-none text-secondary">Ẩm Thực</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h5>Liên Hệ</h5>
                    <ul class="list-unstyled text-secondary">
                        <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo CONTACT_EMAIL; ?>" class="text-decoration-none text-secondary"><?php echo CONTACT_EMAIL; ?></a></li>
                        <li class="mt-2">
                            <a href="#" class="text-white me-2"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-3">
            
            <div class="row">
                <div class="col-md-6">
                    <p class="text-secondary text-sm mb-0">&copy; 2024 <?php echo SITE_NAME; ?>. Tất cả quyền được bảo lưu.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-secondary text-sm mb-0">Thiết kế bởi Sinh Viên PHP | <a href="#" class="text-decoration-none text-secondary">Chính sách Bảo mật</a></p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
    
    <?php if (isset($customJS)): ?>
        <?php echo $customJS; ?>
    <?php endif; ?>
</body>
</html>
