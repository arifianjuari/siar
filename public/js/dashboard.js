/**
 * SIAR - Dashboard JavaScript
 * Menyediakan fungsionalitas interaktif untuk dashboard
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    if (typeof bootstrap !== 'undefined') {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Sidebar toggle mechanism for mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            document.body.classList.toggle('sidebar-open');

            // Add overlay when sidebar is shown on mobile
            if (sidebar.classList.contains('show')) {
                if (!document.querySelector('.sidebar-overlay')) {
                    const overlay = document.createElement('div');
                    overlay.className = 'sidebar-overlay';
                    document.body.appendChild(overlay);

                    overlay.addEventListener('click', function () {
                        sidebar.classList.remove('show');
                        document.body.classList.remove('sidebar-open');
                        this.remove();
                    });
                }
            } else {
                const overlay = document.querySelector('.sidebar-overlay');
                if (overlay) overlay.remove();
            }
        });
    }

    // Collapsible card functionality
    const collapsibleCards = document.querySelectorAll('.card-collapsible');
    collapsibleCards.forEach(card => {
        const collapseToggle = card.querySelector('.card-collapse-toggle');
        if (collapseToggle) {
            collapseToggle.addEventListener('click', function () {
                const cardBody = card.querySelector('.card-body');
                if (cardBody) {
                    cardBody.classList.toggle('d-none');
                    this.querySelector('i').classList.toggle('fa-chevron-down');
                    this.querySelector('i').classList.toggle('fa-chevron-up');
                }
            });
        }
    });

    // Notification mark all as read functionality
    const markAllRead = document.getElementById('markAllNotificationsRead');
    if (markAllRead) {
        markAllRead.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.notification-badge').forEach(badge => {
                badge.classList.add('d-none');
            });

            // Here you would also make an AJAX call to mark notifications as read in the backend
            console.log('All notifications marked as read');
        });
    }
});

// Alpine.js Data Components
window.dashboardData = function () {
    return {
        // Sidebar state management
        sidebarOpen: window.innerWidth >= 768,

        // User profile dropdown state
        profileDropdownOpen: false,

        // Toast notification system
        toasts: [],

        showToast(message, type = 'success', duration = 5000) {
            const id = Date.now();
            this.toasts.push({ id, message, type });

            // Auto remove toast after duration
            setTimeout(() => {
                this.removeToast(id);
            }, duration);
        },

        removeToast(id) {
            this.toasts = this.toasts.filter(toast => toast.id !== id);
        },

        // Dashboard filters
        filters: {
            dateRange: 'thisWeek',
            module: 'all'
        },

        // Apply dashboard filters
        applyFilters() {
            // Here you would fetch filtered data from the backend
            console.log('Applying filters:', this.filters);
            // For demo, just show a toast notification
            this.showToast('Filter berhasil diterapkan', 'success');
        },

        // Modal functionality
        activeModal: null,

        openModal(modalId) {
            this.activeModal = modalId;
        },

        closeModal() {
            this.activeModal = null;
        },

        // Theme switcher
        isDarkMode: false,

        toggleDarkMode() {
            this.isDarkMode = !this.isDarkMode;
            document.body.classList.toggle('dark-mode', this.isDarkMode);
            localStorage.setItem('darkMode', this.isDarkMode);
        },

        init() {
            // Check for saved dark mode preference
            const savedDarkMode = localStorage.getItem('darkMode') === 'true';
            if (savedDarkMode) {
                this.isDarkMode = true;
                document.body.classList.add('dark-mode');
            }

            // Listen for window resize for sidebar responsiveness
            window.addEventListener('resize', () => {
                if (window.innerWidth < 768) {
                    this.sidebarOpen = false;
                } else {
                    this.sidebarOpen = true;
                }
            });
        }
    };
};

// Chart data factory function for reuse across components
window.createChartData = function (type, labels, dataset) {
    return {
        type: type,
        data: {
            labels: labels,
            datasets: dataset
        }
    };
};

// Export functions for possible module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        dashboardData,
        createChartData
    };
} 