<li>
    <?php
        // Menentukan level class berdasarkan kedalaman
        $levelClass = 'level-manager';
        if (isset($depth)) {
            $currentDepth = $depth + 1;
            if ($currentDepth > 3) {
                $levelClass = 'level-staff';
            }
        } else {
            $currentDepth = 1;
        }
        
        // Cek apakah node memiliki anak
        $hasChildren = $node->children && $node->children->count() > 0;
    ?>

    <div class="node <?php echo e($levelClass); ?>">
        <div class="node-unit">
            <?php echo e($node->unit_name); ?>

            <?php if($hasChildren): ?>
                <button class="toggle-btn" onclick="toggleChildren(this)">
                    <i class="fas fa-minus"></i>
                </button>
            <?php endif; ?>
        </div>
        <div class="node-divider"></div>
        <?php if($node->headOfUnit): ?>
            <div class="node-name"><?php echo e($node->headOfUnit->name); ?></div>
        <?php endif; ?>
    </div>

    <?php if($hasChildren): ?>
        <ul class="children-container">
            <?php $__currentLoopData = $node->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('modules.WorkUnit.partials.org-tree-node', ['node' => $child, 'depth' => $currentDepth], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
</li> <?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/WorkUnit/partials/org-tree-node.blade.php ENDPATH**/ ?>