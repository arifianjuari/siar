<?php $__env->startSection('title', 'Daftar Surat dan Nota Dinas'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-4">Daftar Surat dan Nota Dinas</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('modules.correspondence.index')); ?>">Korespondensi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Daftar Surat</li>
                    </ol>
                </nav>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Correspondence::class)): ?>
            <a href="<?php echo e(route('modules.correspondence.letters.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Buat Surat Baru
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i> <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i> <?php echo e(session('error')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <!-- Filter dan Pencarian -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter dan Pencarian</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('modules.correspondence.letters.index')); ?>" method="GET">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Kata Kunci</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Judul, nomor, perihal..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="document_type" class="form-label">Tipe Dokumen</label>
                        <select class="form-control" id="document_type" name="document_type">
                            <option value="">Semua Tipe</option>
                            <option value="Regulasi" <?php echo e(request('document_type') == 'Regulasi' ? 'selected' : ''); ?>>Regulasi</option>
                            <option value="Bukti" <?php echo e(request('document_type') == 'Bukti' ? 'selected' : ''); ?>>Bukti</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="confidentiality_level" class="form-label">Tingkat Kerahasiaan</label>
                        <select class="form-control" id="confidentiality_level" name="confidentiality_level">
                            <option value="">Semua Level</option>
                            <option value="Publik" <?php echo e(request('confidentiality_level') == 'Publik' ? 'selected' : ''); ?>>Publik</option>
                            <option value="Internal" <?php echo e(request('confidentiality_level') == 'Internal' ? 'selected' : ''); ?>>Internal</option>
                            <option value="Rahasia" <?php echo e(request('confidentiality_level') == 'Rahasia' ? 'selected' : ''); ?>>Rahasia</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                            <a href="<?php echo e(route('modules.correspondence.letters.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Surat -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Surat</h6>
        </div>
        <div class="card-body">
            <?php if($correspondences->isEmpty()): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="mb-3">Tidak ada surat yang ditemukan.</p>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Correspondence::class)): ?>
                    <a href="<?php echo e(route('modules.correspondence.letters.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Buat Surat Baru
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>No. Dokumen</th>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Pengirim</th>
                                <th>Penerima</th>
                                <th>Tanggal</th>
                                <th>Tag</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $correspondences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $letter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($letter->document_number ?? '-'); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('modules.correspondence.letters.show', $letter->id)); ?>" class="font-weight-bold text-primary">
                                            <?php echo e($letter->document_title); ?>

                                        </a>
                                        <div>
                                            <span class="badge badge-<?php echo e($letter->confidentiality_level == 'Publik' ? 'info' : ($letter->confidentiality_level == 'Internal' ? 'warning' : 'danger')); ?>">
                                                <?php echo e($letter->confidentiality_level); ?>

                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo e($letter->document_type == 'Regulasi' ? 'primary' : 'success'); ?>">
                                            <?php echo e($letter->document_type); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($letter->sender_name); ?></td>
                                    <td><?php echo e($letter->recipient_name); ?></td>
                                    <td><?php echo e($letter->document_date->format('d/m/Y')); ?></td>
                                    <td>
                                        <?php $__currentLoopData = $letter->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge badge-secondary"><?php echo e($tag->name); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('modules.correspondence.letters.show', $letter->id)); ?>" class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $letter)): ?>
                                            <a href="<?php echo e(route('modules.correspondence.letters.edit', $letter->id)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $letter)): ?>
                                            <form action="<?php echo e(route('modules.correspondence.letters.destroy', $letter->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($correspondences->withQueryString()->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/Correspondence/letters/index.blade.php ENDPATH**/ ?>