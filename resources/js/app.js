// Import dependencies
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/js/all.min';

// Inisialisasi komponen Bootstrap dasar
document.addEventListener('DOMContentLoaded', function () {
    // Initialize all tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize all popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Handle mobile sidebar toggle
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            const sidebarWrapper = document.querySelector('.sidebar-wrapper');
            if (sidebarWrapper) {
                sidebarWrapper.classList.toggle('show');

                // Tambahkan/hapus overlay saat sidebar tampil
                if (sidebarWrapper.classList.contains('show')) {
                    // Create overlay if not exists
                    if (!document.querySelector('.sidebar-overlay')) {
                        const overlay = document.createElement('div');
                        overlay.className = 'sidebar-overlay';
                        document.body.appendChild(overlay);

                        // Listener untuk menutup sidebar saat overlay diklik
                        overlay.addEventListener('click', function () {
                            sidebarWrapper.classList.remove('show');
                            this.remove();
                        });
                    }
                } else {
                    // Hapus overlay jika sidebar ditutup
                    const overlay = document.querySelector('.sidebar-overlay');
                    if (overlay) overlay.remove();
                }
            }
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function (event) {
        const sidebar = document.querySelector('.sidebar-wrapper');
        const sidebarToggle = document.querySelector('.sidebar-toggle');

        if (sidebar && sidebarToggle && window.innerWidth <= 767.98) {
            if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('show');

                // Hapus overlay jika ada
                const overlay = document.querySelector('.sidebar-overlay');
                if (overlay) overlay.remove();
            }
        }
    });

    // Handle alert dismissal after timeout
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        if (!alert.classList.contains('alert-permanent')) {
            setTimeout(function () {
                alert.classList.add('fade');
                setTimeout(function () {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 150);
            }, 3000);
        }
    });

    // Handle delete confirmation modals
    const deleteButtons = document.querySelectorAll('[data-delete-form]');
    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const formId = this.getAttribute('data-delete-form');
            const form = document.getElementById(formId);

            if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                form.submit();
            }
        });
    });

    // Handle form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Tambahkan CSRF token untuk semua form yang belum memilikinya
    document.querySelectorAll('form').forEach(form => {
        if (!form.querySelector('input[name="_token"]')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);
        }
    });

    // Inisialisasi feather icons jika tersedia
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Mobile view detection
    function checkMobileView() {
        if (window.innerWidth <= 767.98) {
            document.body.classList.add('mobile-view');
        } else {
            document.body.classList.remove('mobile-view');

            // Sembunyikan sidebar pada view mobile jika sedang tampil
            const sidebar = document.querySelector('.sidebar-wrapper');
            if (sidebar && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');

                // Hapus overlay
                const overlay = document.querySelector('.sidebar-overlay');
                if (overlay) overlay.remove();
            }
        }
    }

    // Panggil saat load
    checkMobileView();

    // Panggil saat resize
    window.addEventListener('resize', checkMobileView);

    // User dropdown menu toggle functions for sidebar (minimal implementation)
    window.toggleUserManagementDropdown = function () {
        const menu = document.getElementById('userManagementSubmenu');
        if (menu) {
            menu.classList.toggle('collapse');
        }
    };

    window.toggleWorkUnitDropdown = function () {
        const menu = document.getElementById('workUnitSubmenu');
        if (menu) {
            menu.classList.toggle('collapse');
        }
    };

    window.toggleTenantDropdown = function () {
        const menu = document.getElementById('tenantSubmenu');
        if (menu) {
            menu.classList.toggle('collapse');
        }
    };

    // Sidebar navigation: force full page reload only on sidebar links
    document.querySelectorAll('.sidebar-wrapper a').forEach(link => {
        // Skip external or special links
        if (link.hostname !== window.location.hostname ||
            link.target === '_blank' ||
            link.hasAttribute('download')) {
            return;
        }
        link.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = this.href;
        });
    });
});