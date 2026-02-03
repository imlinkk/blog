/**
 * Custom JavaScript cho Blog
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts sau 5 giây
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (!alert.classList.contains('alert-permanent')) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });
    
    // Confirm delete
    const deleteButtons = document.querySelectorAll('a[onclick*="confirm"]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Bạn chắc chắn muốn thực hiện hành động này?')) {
                e.preventDefault();
            }
        });
    });
});
