<?php $__env->startSection('title', $spo->document_title); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Dokumen SPO</h1>
        
        <div>
            <a href="<?php echo e(route('work-units.spo.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $spo)): ?>
            <a href="<?php echo e(route('work-units.spo.edit', $spo)); ?>" class="btn btn-primary ms-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <?php endif; ?>
            
            <?php if($spo->file_path): ?>
            <a href="<?php echo e($spo->file_path); ?>" class="btn btn-info ms-2" target="_blank">
                <i class="fas fa-external-link-alt me-1"></i> Lihat Dokumen
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Informasi Dokumen -->
    <div class="row">
        <div class="col-md-8">
            <!-- Informasi Dasar -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Dokumen</h6>
                    <span class="badge <?php echo e($spo->status_validasi == 'Draft' ? 'bg-secondary' : 
                                        ($spo->status_validasi == 'Disetujui' ? 'bg-success' : 
                                        ($spo->status_validasi == 'Kadaluarsa' ? 'bg-danger' : 'bg-warning'))); ?> 
                            px-3 py-2 rounded-pill">
                        <?php echo e($spo->status_validasi); ?>

                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h3 class="h4 mb-3"><?php echo e($spo->document_title); ?></h3>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width: 150px;">Jenis Dokumen</th>
                                        <td>: <?php echo e($spo->document_type); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Dokumen</th>
                                        <td>: <?php echo e($spo->document_number); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Versi</th>
                                        <td>: <?php echo e($spo->document_version); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Berlaku</th>
                                        <td>: <?php echo e($spo->document_date->format('d/m/Y')); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width: 150px;">Unit Kerja</th>
                                        <td>: <?php echo e($spo->workUnit->unit_name ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tingkat Kerahasiaan</th>
                                        <td>: <?php echo e($spo->confidentiality_level); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Review Berikutnya</th>
                                        <td>: <?php echo e($spo->next_review ? $spo->next_review->format('d/m/Y') : 'Belum ditentukan'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Siklus Review</th>
                                        <td>: <?php echo e($spo->review_cycle_months); ?> bulan</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab untuk konten detail -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="definition-tab" data-bs-toggle="tab" data-bs-target="#definition" type="button" role="tab" aria-controls="definition" aria-selected="true">Pengertian</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="purpose-tab" data-bs-toggle="tab" data-bs-target="#purpose" type="button" role="tab" aria-controls="purpose" aria-selected="false">Tujuan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="policy-tab" data-bs-toggle="tab" data-bs-target="#policy" type="button" role="tab" aria-controls="policy" aria-selected="false">Kebijakan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="procedure-tab" data-bs-toggle="tab" data-bs-target="#procedure" type="button" role="tab" aria-controls="procedure" aria-selected="false">Prosedur</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reference-tab" data-bs-toggle="tab" data-bs-target="#reference" type="button" role="tab" aria-controls="reference" aria-selected="false">Referensi</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="spoDetailTabContent">
                        <div class="tab-pane fade show active" id="definition" role="tabpanel" aria-labelledby="definition-tab">
                            <div class="py-3">
                                <?php if($spo->definition): ?>
                                    <?php echo nl2br(e($spo->definition)); ?>

                                <?php else: ?>
                                    <p class="text-muted">Tidak ada informasi pengertian.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="purpose" role="tabpanel" aria-labelledby="purpose-tab">
                            <div class="py-3">
                                <?php if($spo->purpose): ?>
                                    <?php echo nl2br(e($spo->purpose)); ?>

                                <?php else: ?>
                                    <p class="text-muted">Tidak ada informasi tujuan.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="policy" role="tabpanel" aria-labelledby="policy-tab">
                            <div class="py-3">
                                <?php if($spo->policy): ?>
                                    <?php echo nl2br(e($spo->policy)); ?>

                                <?php else: ?>
                                    <p class="text-muted">Tidak ada informasi kebijakan.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="procedure" role="tabpanel" aria-labelledby="procedure-tab">
                            <div class="py-3">
                                <?php if($spo->procedure): ?>
                                    <?php echo nl2br(e($spo->procedure)); ?>

                                <?php else: ?>
                                    <p class="text-muted">Tidak ada informasi prosedur.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="reference" role="tabpanel" aria-labelledby="reference-tab">
                            <div class="py-3">
                                <?php if($spo->reference): ?>
                                    <?php echo nl2br(e($spo->reference)); ?>

                                <?php else: ?>
                                    <p class="text-muted">Tidak ada informasi referensi.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Informasi Tambahan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Tambahan</h6>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Unit Kerja Terkait</h6>
                    <?php if($linkedUnits->isEmpty()): ?>
                        <p class="text-muted">Tidak ada unit kerja terkait</p>
                    <?php else: ?>
                        <ul class="list-group mb-4">
                            <?php $__currentLoopData = $linkedUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo e($unit->unit_name); ?>

                                    <span class="badge bg-primary rounded-pill"><?php echo e($unit->unit_code); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <h6 class="mb-3">Tag</h6>
                    <?php if($spo->tags->isEmpty()): ?>
                        <p class="text-muted">Tidak ada tag</p>
                    <?php else: ?>
                        <div class="d-flex flex-wrap mb-4">
                            <?php $__currentLoopData = $spo->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-info me-2 mb-2 py-2 px-3"><?php echo e($tag->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <h6 class="mb-3">Persetujuan Dokumen</h6>
                    <?php if($spo->status_validasi == 'Disetujui' && $spo->approved_by && $spo->approved_at): ?>
                        <div class="list-group-item">
                            <div>Disetujui oleh:</div>
                            <div class="fw-bold"><?php echo e($spo->approver->name ?? 'N/A'); ?></div>
                            <div class="small text-muted">
                                Pada: <?php echo e($spo->approved_at->format('d/m/Y H:i')); ?>

                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Dokumen belum disetujui</p>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <h6 class="mb-3">Informasi Pembuatan</h6>
                    <div class="list-group-item mb-2">
                        <div>Dibuat oleh:</div>
                        <div class="fw-bold"><?php echo e($spo->creator->name ?? 'N/A'); ?></div>
                        <div class="small text-muted">
                            Pada: <?php echo e($spo->created_at->format('d/m/Y H:i')); ?>

                        </div>
                    </div>
                    
                    <div class="list-group-item">
                        <div>Terakhir diubah:</div>
                        <div class="small text-muted">
                            <?php echo e($spo->updated_at->format('d/m/Y H:i')); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', ['hideDefaultHeader' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/work-unit/spo/show.blade.php ENDPATH**/ ?>