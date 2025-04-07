<?php $__env->startSection('title', 'Standar Prosedur Operasional (SPO)'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Standar Prosedur Operasional (SPO)</h1>
    </div>
    
    <!-- Filter dan Pencarian -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter dan Pencarian</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('work-units.spo.index')); ?>" method="GET" class="mb-0">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="work_unit_id" class="form-label">Unit Kerja</label>
                        <select class="form-select" id="work_unit_id" name="work_unit_id">
                            <option value="">Semua Unit Kerja</option>
                            <?php $__currentLoopData = $workUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($unit->id); ?>" <?php echo e(request('work_unit_id') == $unit->id ? 'selected' : ''); ?>>
                                    <?php echo e($unit->unit_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="document_type" class="form-label">Jenis Dokumen</label>
                        <select class="form-select" id="document_type" name="document_type">
                            <option value="">Semua Jenis</option>
                            <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(request('document_type') == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <?php $__currentLoopData = $statusValidasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(request('status') == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Cari judul atau nomor dokumen" value="<?php echo e(request('search')); ?>">
                    </div>
                    
                    <div class="col-md-2 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                        <a href="<?php echo e(route('work-units.spo.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Daftar SPO -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Standar Prosedur Operasional</h6>
            
            <a href="<?php echo e(route('work-units.spo.create')); ?>" class="btn btn-sm btn-success">
                <i class="fas fa-plus me-1"></i> Tambah SPO Baru
            </a>
        </div>
        <div class="card-body">
            <?php if($spos->isEmpty()): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-gray-300 mb-3"></i>
                    <p class="text-gray-500 mb-0">Belum ada dokumen SPO yang tersedia.</p>
                    
                    <a href="<?php echo e(route('work-units.spo.create')); ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Tambah SPO Baru
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No.</th>
                                <th width="15%">Nomor SPO</th>
                                <th width="20%">Judul Dokumen</th>
                                <th width="7%">Versi</th>
                                <th width="12%">Tgl. Berlaku</th>
                                <th width="10%">Status</th>
                                <th width="11%">Tgl. Perubahan</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $spos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $spo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($spos->firstItem() + $index); ?></td>
                                    <td><?php echo e($spo->document_number); ?></td>
                                    <td><?php echo e($spo->document_title); ?></td>
                                    <td><?php echo e($spo->document_version); ?></td>
                                    <td><?php echo e($spo->document_date->format('d/m/Y')); ?></td>
                                    <td>
                                        <span class="badge <?php echo e($spo->status_validasi == 'Draft' ? 'bg-secondary' : 
                                                          ($spo->status_validasi == 'Disetujui' ? 'bg-success' : 
                                                          ($spo->status_validasi == 'Kadaluarsa' ? 'bg-danger' : 'bg-warning'))); ?>">
                                            <?php echo e($spo->status_validasi); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($spo->updated_at->format('d/m/Y')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('work-units.spo.show', $spo)); ?>" class="btn btn-sm btn-info mb-1" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if($spo->file_path): ?>
                                        <a href="<?php echo e($spo->file_path); ?>" class="btn btn-sm btn-secondary mb-1" target="_blank" title="Lihat Dokumen">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        
                                        <?php
                                            $canUpdate = auth()->user()->can('update', $spo);
                                            $canDelete = auth()->user()->can('delete', $spo);
                                            $hasUpdatePermission = auth()->user()->hasPermission('work-units', 'can_edit');
                                            $hasDeletePermission = auth()->user()->hasPermission('work-units', 'can_delete');
                                        ?>
                                        <div class="small d-none">
                                            canUpdate: <?php echo e($canUpdate ? 'true' : 'false'); ?><br>
                                            canDelete: <?php echo e($canDelete ? 'true' : 'false'); ?><br>
                                            hasUpdatePermission: <?php echo e($hasUpdatePermission ? 'true' : 'false'); ?><br>
                                            hasDeletePermission: <?php echo e($hasDeletePermission ? 'true' : 'false'); ?>

                                        </div>
                                        
                                        
                                        <a href="<?php echo e(route('work-units.spo.edit', $spo)); ?>" class="btn btn-sm btn-warning mb-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        
                                        <a href="<?php echo e(route('work-units.spo.generate-pdf', $spo)); ?>" class="btn btn-sm btn-primary mb-1" title="Generate PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        
                                        
                                        <form action="<?php echo e(route('work-units.spo.destroy', $spo)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    <?php echo e($spos->withQueryString()->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', ['hideDefaultHeader' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/work-unit/spo/index.blade.php ENDPATH**/ ?>