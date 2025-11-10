<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <!-- Metadata dasar -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo e(config('app.name', 'SIAR')); ?></title>
    
    <?php echo $__env->make('layouts.partials.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <!-- Vite Assets -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <!-- Unregister Service Worker -->
    <script>
    // Unregister all service workers to prevent caching issues
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for (let registration of registrations) {
                registration.unregister();
                console.log('Service worker unregistered');
            }
        }).catch(function(err) {
            console.log('Service worker unregister failed: ', err);
        });
        
        // Clear all caches
        if ('caches' in window) {
            caches.keys().then(function(names) {
                for (let name of names) {
                    caches.delete(name);
                    console.log('Cache deleted: ', name);
                }
            });
        }
    }
    </script>
</head>
<body>
    <?php echo $__env->make('layouts.partials.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div id="app" class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <?php if(auth()->guard()->check()): ?>
                    <!-- Sidebar -->
                    <aside class="col-12 col-md-3 col-lg-2 sidebar-wrapper p-0">
                        <?php echo $__env->make('layouts.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </aside>

                    <!-- Main Content -->
                    <main class="col-12 col-md-9 col-lg-10 content-wrapper py-3">
                        <?php if(session('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-check-circle me-2"></i></strong> <?php echo e(session('success')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(session('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-circle me-2"></i></strong> <?php echo e(session('error')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle me-2"></i></strong> Terdapat kesalahan dalam input:
                                <ul class="mb-0 mt-2">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Page Header -->
                        <?php if (! empty(trim($__env->yieldContent('header')))): ?>
                            <div class="mb-4 pb-3 border-bottom">
                                <?php echo $__env->yieldContent('header'); ?>
                            </div>
                        <?php elseif(!isset($hideDefaultHeader) && !Route::is('*.risk-analysis.*') && !Route::is('modules.risk-management.dashboard')): ?>
                            <div class="mb-4 pb-3 border-bottom">
                                <h1 class="h3 mb-0"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h1>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Page Content -->
                        <?php echo $__env->yieldContent('content'); ?>
                    </main>
                <?php else: ?>
                    <!-- Full Width Content for guests -->
                    <main class="col-12 py-4">
                        <div class="container">
                            <?php if(session('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-check-circle me-2"></i></strong> <?php echo e(session('success')); ?>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(session('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-circle me-2"></i></strong> <?php echo e(session('error')); ?>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php echo $__env->yieldContent('content'); ?>
                        </div>
                    </main>
                <?php endif; ?>
            </div>
        </div>
        
        <?php echo $__env->make('layouts.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html> <?php /**PATH G:\My Drive\MYDEV\siar\resources\views/layouts/app.blade.php ENDPATH**/ ?>