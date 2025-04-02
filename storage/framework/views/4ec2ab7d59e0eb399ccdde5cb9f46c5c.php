<!-- Meta Tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<meta name="description" content="Sistem Informasi Administrasi Risiko">
<meta name="author" content="SIAR">
<meta name="theme-color" content="#4285f4">

<!-- Favicon -->
<link rel="icon" href="<?php echo e(asset('/images/pwa/icon-192.png')); ?>" type="image/png">

<!-- PWA  -->
<link rel="manifest" href="<?php echo e(asset('/manifest.json')); ?>">
<link rel="apple-touch-icon" href="<?php echo e(asset('/images/pwa/icon-192.png')); ?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="<?php echo e(config('app.name', 'SIAR')); ?>">

<title><?php echo e(config('app.name', 'SIAR')); ?><?php echo $__env->yieldContent('title'); ?></title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Tailwind CSS - Optional, uncomment if using -->
<!-- <script src="https://cdn.tailwindcss.com"></script> -->

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

<!-- Chart.js -->


<!-- Custom Styles -->
<style>
    :root {
        --primary-color: #4F46E5;
        --primary-hover: #4338CA;
        --secondary-color: #10B981;
        --dark-color: #1F2937;
        --light-color: #F9FAFB;
        --danger-color: #EF4444;
        --warning-color: #F59E0B;
        --info-color: #3B82F6;
        --navbar-height: 62px;
    }
    
    body {
        font-family: 'Figtree', sans-serif;
        background-color: #f8f9fa;
        overflow-x: hidden;
        padding-top: var(--navbar-height); /* Ruang untuk navbar fixed */
    }
    
    /* Navbar Styles - Fixed Top */
    .navbar {
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1030;
        height: var(--navbar-height);
    }
    
    .navbar-brand {
        font-weight: 700;
        color: var(--primary-color);
    }
    
    /* Wrapper Style */
    #app.wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    .container-fluid {
        flex: 1;
    }
    
    /* Sidebar Styles - Fixed Left */
    .sidebar-wrapper {
        position: fixed;
        top: var(--navbar-height);
        left: 0;
        bottom: 0;
        z-index: 1020;
        width: 16.666667%; /* col-md-2 */
        overflow-y: auto;
    }
    
    .sidebar {
        height: 100%;
        background-color: var(--dark-color);
        color: #fff;
        transition: all 0.3s;
        overflow-y: auto;
    }
    
    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.75);
        padding: 0.75rem 1.25rem;
        border-radius: 6px;
        margin: 2px 0;
        transition: all 0.2s ease;
    }
    
    .sidebar .nav-link:hover {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .sidebar .nav-link.active {
        color: #fff;
        background-color: var(--primary-color);
    }
    
    /* Content Wrapper with proper margin */
    .content-wrapper {
        margin-left: 16.666667%; /* Match sidebar width */
        padding-bottom: 70px; /* Space for footer */
    }
    
    /* Footer - Fixed Bottom */
    .footer {
        position: fixed;
        bottom: 0;
        right: 0;
        left: 0;
        z-index: 1020;
        background: white;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
    }
    
    /* Card Styles */
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    .card-stat {
        border-left: 4px solid var(--primary-color);
    }
    
    .card-stat.success {
        border-left-color: var(--secondary-color);
    }
    
    .card-stat.warning {
        border-left-color: var(--warning-color);
    }
    
    .card-stat.danger {
        border-left-color: var(--danger-color);
    }
    
    .card-stat.info {
        border-left-color: var(--info-color);
    }
    
    /* Icon Container */
    .icon-container {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Responsive Utilities */
    @media (max-width: 767.98px) {
        .sidebar-wrapper {
            position: fixed;
            top: var(--navbar-height);
            left: -250px;
            width: 250px;
            z-index: 1020;
            transition: left 0.3s ease;
        }
        
        .sidebar-wrapper.show {
            left: 0;
        }
        
        .content-wrapper {
            margin-left: 0 !important;
        }
        
        .footer {
            left: 0 !important;
        }
        
        /* Gunakan class body .mobile-view untuk target yang lebih kuat */
        body.mobile-view .navbar .navbar-brand .tenant-branding .tenant-name {
            display: none !important;
            /* Hapus properti lain yang mungkin tidak perlu jika display: none sudah cukup */
        }

        body.mobile-view .navbar .navbar-brand .tenant-branding .tenant-logo,
        body.mobile-view .navbar .navbar-brand .tenant-branding .tenant-icon {
            height: 32px !important;
            width: auto !important;
            vertical-align: middle !important;
            margin-right: 0 !important; /* Reset margin jika nama hilang */
        }
        
        /* Pastikan navbar brand tidak terlalu lebar */
        body.mobile-view .navbar-brand {
            max-width: calc(100% - 150px); /* Sesuaikan 150px dengan lebar tombol kanan */
            overflow: hidden;
            flex-shrink: 1 !important; /* Izinkan menyusut jika perlu */
        }
    }
    
    @media (min-width: 768px) and (max-width: 991.98px) {
        .sidebar-wrapper {
            width: 25%; /* col-md-3 */
        }
        
        .content-wrapper {
            margin-left: 25%; /* Match sidebar width */
        }
    }
</style>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<?php echo $__env->yieldPushContent('styles'); ?> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/layouts/partials/head.blade.php ENDPATH**/ ?>