<footer class="footer bg-white py-1 mt-auto border-top" style="z-index: 5;">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted" style="font-size: 11px;">
                &copy; {{ date('Y') }} {{ config('app.name', 'SIAR') }}. Hak Cipta Dilindungi.
            </div>
            <div class="d-none d-sm-flex">
                <a href="{{ route('pages.privacy') }}" class="text-decoration-none text-muted me-3" style="font-size: 11px;">Kebijakan Privasi</a>
                <a href="{{ route('pages.terms') }}" class="text-decoration-none text-muted me-3" style="font-size: 11px;">Syarat &amp; Ketentuan</a>
                <a href="{{ route('pages.help') }}" class="text-decoration-none text-muted" style="font-size: 11px;">Bantuan</a>
            </div>
        </div>
    </div>
</footer>

<style>
    /* Footer responsif */
    @media (max-width: 767.98px) {
        .footer {
            padding: 5px 0 !important;
        }
    }
</style>

<!-- Bootstrap core JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<!-- PWA Service Worker -->
<script>
    // Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                })
                .catch(function(error) {
                    console.log('ServiceWorker registration failed: ', error);
                });
        });
    }
</script>

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

@stack('scripts') 