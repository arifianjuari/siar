<?php $__env->startSection('title', 'Daftar Dokumen'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Daftar Dokumen</h2>
            <p class="text-muted mb-0">Kelola dokumen dari berbagai sumber</p>
        </div>
        <div>
            <a href="<?php echo e(route('modules.document-management.documents.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus-circle me-1"></i> Unggah Dokumen
            </a>
            <a href="<?php echo e(route('modules.document-management.dashboard')); ?>" class="btn btn-primary">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="<?php echo e(route('modules.document-management.documents.index')); ?>" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Cari Dokumen</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Judul, nomor, deskripsi..." value="<?php echo e(request('search')); ?>">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Semua Kategori</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category); ?>" <?php echo e(request('category') == $category ? 'selected' : ''); ?>>
                                    <?php echo e(ucfirst($category)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan</label>
                        <select class="form-select" id="confidentiality_level" name="confidentiality_level">
                            <option value="">Semua Level</option>
                            <option value="public" <?php echo e(request('confidentiality_level') == 'public' ? 'selected' : ''); ?>>Publik</option>
                            <option value="internal" <?php echo e(request('confidentiality_level') == 'internal' ? 'selected' : ''); ?>>Internal</option>
                            <option value="confidential" <?php echo e(request('confidentiality_level') == 'confidential' ? 'selected' : ''); ?>>Rahasia</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="tag" class="form-label">Tag</label>
                        <select class="form-select" id="tag" name="tag">
                            <option value="">Semua Tag</option>
                            <?php $__currentLoopData = $availableTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tag->slug); ?>" <?php echo e(request('tag') == $tag->slug ? 'selected' : ''); ?>>
                                    <?php echo e($tag->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="<?php echo e(route('modules.document-management.documents.index')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Results Section -->
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nomor Dokumen</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Kerahasiaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($document->document_number); ?></td>
                                <td>
                                    <div class="fw-medium"><?php echo e($document->document_title); ?></div>
                                    <?php if($document->tags->count() > 0): ?>
                                        <div class="mt-1">
                                            <?php $__currentLoopData = $document->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(route('modules.document-management.documents.index', ['tag' => $tag->slug])); ?>" 
                                                   class="badge bg-light text-dark text-decoration-none">
                                                    <?php echo e($tag->name); ?>

                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo e(ucfirst($document->category)); ?></span>
                                </td>
                                <td><?php echo e($document->document_date ? $document->document_date->format('d M Y') : 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e($document->confidentiality_level == 'public' ? 'success' : ($document->confidentiality_level == 'internal' ? 'warning' : 'danger')); ?>">
                                        <?php echo e(ucfirst($document->confidentiality_level)); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('modules.document-management.documents.show', $document->id)); ?>" 
                                           class="btn btn-sm btn-info" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('modules.document-management.documents.edit', $document->id)); ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('modules.document-management.documents.destroy', $document->id)); ?>" 
                                              style="display: inline;" onsubmit="return confirm('Anda yakin ingin menghapus dokumen ini?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-file-alt fa-2x mb-3"></i>
                                        <p>Belum ada dokumen yang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($documents->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/DocumentManagement/documents/index.blade.php ENDPATH**/ ?>