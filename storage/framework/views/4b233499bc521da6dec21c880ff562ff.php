<?php $__env->startSection('title', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <!-- Tombol Tambah Referensi dipindahkan ke bawah, sejajar dengan form pencarian -->
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">Daftar Dokumen Referensi</h4>
            <div class="d-flex">
                <form action="<?php echo e(route('tenant.document-references.index')); ?>" method="GET" class="d-flex me-2">
                    <input type="text" name="search" class="form-control form-control-sm me-2" 
                          placeholder="Cari..." value="<?php echo e(request('search')); ?>">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <a href="<?php echo e(route('tenant.document-references.create')); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Referensi
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Jenis</th>
                            <th>Nomor Referensi</th>
                            <th>Judul</th>
                            <th>Diterbitkan Oleh</th>
                            <th>Tanggal</th>
                            <th>Unit Terkait</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $references; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reference): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($reference->reference_type); ?></td>
                                <td><?php echo e($reference->reference_number); ?></td>
                                <td><?php echo e(Str::limit($reference->title, 50)); ?></td>
                                <td><?php echo e($reference->issued_by); ?></td>
                                <td><?php echo e($reference->issued_date->format('d-m-Y')); ?></td>
                                <td><?php echo e($reference->related_unit ?: '-'); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php if($reference->file_url): ?>
                                            <a href="<?php echo e($reference->file_url); ?>" 
                                               class="btn btn-sm btn-success" title="Buka Link File" target="_blank">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('tenant.document-references.show', $reference->id)); ?>" 
                                           class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('tenant.document-references.edit', $reference->id)); ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete('<?php echo e($reference->id); ?>')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-<?php echo e($reference->id); ?>" 
                                          action="<?php echo e(route('tenant.document-references.destroy', $reference->id)); ?>" 
                                          method="POST" style="display: none;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada dokumen referensi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($references->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus dokumen referensi ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/tenant/document-references/index.blade.php ENDPATH**/ ?>