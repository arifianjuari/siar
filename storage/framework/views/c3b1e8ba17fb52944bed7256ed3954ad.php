<footer class="footer bg-white py-1 mt-auto border-top" style="z-index: 5;">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted" style="font-size: 11px;">
                &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name', 'SIAR')); ?>. Hak Cipta Dilindungi.
            </div>
            <div class="d-flex align-items-center">
                <!-- Tombol Install App di footer -->
                <button id="footerInstallPWA" onclick="installPWA()" class="btn btn-sm btn-outline-primary me-3 d-none">
                    <i class="fas fa-download me-1"></i> Install App
                </button>
                
                <div class="d-none d-sm-flex">
                    <a href="<?php echo e(route('pages.privacy')); ?>" class="text-decoration-none text-muted me-3" style="font-size: 11px;">Kebijakan Privasi</a>
                    <a href="<?php echo e(route('pages.terms')); ?>" class="text-decoration-none text-muted me-3" style="font-size: 11px;">Syarat &amp; Ketentuan</a>
                    <a href="<?php echo e(route('pages.help')); ?>" class="text-decoration-none text-muted" style="font-size: 11px;">Bantuan</a>
                </div>
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
    
    /* Animasi pulse untuk tombol install */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
        }
    }
</style>

<!-- Bootstrap core JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<!-- PWA Service Worker -->
<script>
    let deferredPrompt;
    let pwaInstallable = false;
    
    // Fungsi untuk mendapatkan semua tombol install
    function getInstallButtons() {
        const buttons = [];
        const navbarButton = document.getElementById('installPWA');
        const footerButton = document.getElementById('footerInstallPWA');
        
        if (navbarButton) buttons.push(navbarButton);
        if (footerButton) buttons.push(footerButton);
        
        return buttons;
    }
    
    // Fungsi untuk menampilkan tombol install
    function showInstallButtons() {
        const buttons = getInstallButtons();
        if (buttons.length > 0) {
            buttons.forEach(button => {
                button.classList.remove('d-none');
            });
            console.log('Tombol install ditampilkan');
        } else {
            console.warn('Tombol install tidak ditemukan');
        }
    }
    
    // Fungsi untuk menyembunyikan tombol install
    function hideInstallButtons() {
        const buttons = getInstallButtons();
        if (buttons.length > 0) {
            buttons.forEach(button => {
                button.classList.add('d-none');
            });
            console.log('Tombol install disembunyikan');
        }
    }
    
    // Menangkap event beforeinstallprompt
    window.addEventListener('beforeinstallprompt', (e) => {
        console.log('🚀 beforeinstallprompt event triggered');
        // Mencegah Chrome menampilkan prompt instalasi otomatis
        e.preventDefault();
        // Simpan event untuk digunakan nanti
        deferredPrompt = e;
        pwaInstallable = true;
        
        // Tampilkan tombol instalasi
        showInstallButtons();
        
        // Log status untuk debugging
        console.log('PWA installable status:', pwaInstallable);
    });

    // Sembunyikan tombol setelah instalasi
    window.addEventListener('appinstalled', () => {
        console.log('🎉 PWA berhasil diinstal!');
        hideInstallButtons();
        deferredPrompt = null;
        pwaInstallable = false;
    });

    // Fungsi untuk menampilkan prompt instalasi
    async function installPWA() {
        console.log('📱 Mencoba menampilkan prompt instalasi...');
        console.log('deferredPrompt status:', deferredPrompt ? 'ada' : 'tidak ada');
        
        if (!deferredPrompt) {
            console.log('❌ Tidak ada prompt installasi tersedia');
            alert('Aplikasi ini sudah diinstal atau browser Anda tidak mendukung instalasi PWA.');
            return;
        }
        
        try {
            console.log('📲 Menampilkan prompt instalasi...');
            // Tampilkan prompt
            deferredPrompt.prompt();
            
            // Tunggu user merespons prompt
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`✅ Respons user terhadap prompt: ${outcome}`);
            
            // Clear the deferredPrompt variable
            deferredPrompt = null;
            pwaInstallable = false;
            
            // Sembunyikan tombol
            hideInstallButtons();
        } catch (error) {
            console.error('⚠️ Error menampilkan prompt instalasi:', error);
        }
    }

    // Check instalasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        console.log('🔍 Memeriksa status installable PWA...');
        if (pwaInstallable && deferredPrompt) {
            showInstallButtons();
        } else {
            hideInstallButtons();
        }
    });

    // Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('✅ ServiceWorker registration successful with scope: ', registration.scope);
                })
                .catch(function(error) {
                    console.error('❌ ServiceWorker registration failed: ', error);
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

<?php echo $__env->yieldPushContent('scripts'); ?> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/layouts/partials/footer.blade.php ENDPATH**/ ?>