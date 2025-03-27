<footer class="footer bg-white py-4 mt-auto border-top">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">
                &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name', 'SIAR')); ?>. Hak Cipta Dilindungi.
            </div>
            <div>
                <a href="#" class="text-decoration-none text-muted me-3">Kebijakan Privasi</a>
                <a href="#" class="text-decoration-none text-muted me-3">Syarat &amp; Ketentuan</a>
                <a href="#" class="text-decoration-none text-muted">Bantuan</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap core JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<!-- Core theme JS-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Sidebar (mobile)
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const contentWrapper = document.querySelector('.content-wrapper');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                
                // Add overlay when sidebar is shown
                if (sidebar.classList.contains('show')) {
                    const overlay = document.createElement('div');
                    overlay.className = 'sidebar-overlay position-fixed top-0 left-0 w-100 h-100 bg-dark bg-opacity-50';
                    overlay.style.zIndex = '999';
                    document.body.appendChild(overlay);
                    
                    overlay.addEventListener('click', function() {
                        sidebar.classList.remove('show');
                        this.remove();
                    });
                } else {
                    const overlay = document.querySelector('.sidebar-overlay');
                    if (overlay) overlay.remove();
                }
            });
        }
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>

<?php echo $__env->yieldPushContent('scripts'); ?> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/layouts/partials/footer.blade.php ENDPATH**/ ?>