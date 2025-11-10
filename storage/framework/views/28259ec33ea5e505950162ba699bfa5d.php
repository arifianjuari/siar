<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Sidebar dan Navbar sudah diatur di layouts.app -->
    <main class="py-4 mt-5">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/siar/resources/views/roles/superadmin/layout.blade.php ENDPATH**/ ?>