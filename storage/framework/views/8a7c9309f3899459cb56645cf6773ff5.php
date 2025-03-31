<?php $__env->startSection('title', $correspondence->document_title); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Notifikasi -->
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Surat</h1>
        <div>
            <a href="<?php echo e(route('modules.correspondence.letters.index')); ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm"></i> Kembali
            </a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('export', $correspondence)): ?>
            <div class="btn-group ml-2">
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download fa-sm"></i> Export
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo e(route('modules.correspondence.letters.export-pdf', $correspondence->id)); ?>">PDF</a>
                    <a class="dropdown-item" href="<?php echo e(route('modules.correspondence.letters.export-word', $correspondence->id)); ?>">Word</a>
                </div>
            </div>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $correspondence)): ?>
            <a href="<?php echo e(route('modules.correspondence.letters.edit', $correspondence->id)); ?>" class="btn btn-sm btn-warning ml-2">
                <i class="fas fa-edit fa-sm"></i> Edit
            </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $correspondence)): ?>
            <form action="<?php echo e(route('modules.correspondence.letters.destroy', $correspondence->id)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-sm btn-danger ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus surat ini?')">
                    <i class="fas fa-trash fa-sm"></i> Hapus
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Detail Surat -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Surat</h6>
                    <div class="dropdown no-arrow">
                        <span class="badge <?php echo e($correspondence->document_type == 'Regulasi' ? 'badge-primary' : 'badge-success'); ?>">
                            <?php echo e($correspondence->document_type); ?>

                        </span>
                        <span class="badge 
                            <?php echo e($correspondence->confidentiality_level == 'Publik' ? 'badge-info' : 
                              ($correspondence->confidentiality_level == 'Internal' ? 'badge-warning' : 'badge-danger')); ?>">
                            <?php echo e($correspondence->confidentiality_level); ?>

                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5>Nomor Dokumen</h5>
                            <p><?php echo e($correspondence->document_number); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5>Tanggal Dokumen</h5>
                            <p><?php echo e($correspondence->document_date->format('d F Y')); ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Judul Dokumen</h5>
                        <p class="font-weight-bold"><?php echo e($correspondence->document_title); ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Subjek</h5>
                        <p><?php echo e($correspondence->subject); ?></p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5>Pengirim</h5>
                            <p><?php echo e($correspondence->sender_name); ?><br>
                            <small class="text-muted"><?php echo e($correspondence->sender_position); ?></small></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5>Penerima</h5>
                            <p><?php echo e($correspondence->recipient_name); ?><br>
                            <small class="text-muted"><?php echo e($correspondence->recipient_position); ?></small></p>
                        </div>
                    </div>
                    
                    <?php if($correspondence->cc_list): ?>
                    <div class="mb-4">
                        <h5>CC</h5>
                        <p><?php echo e($correspondence->cc_list); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <h5>Isi Surat</h5>
                        <div class="border p-3 bg-light rounded">
                            <?php echo nl2br(e($correspondence->body)); ?>

                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5>Lokasi Penandatanganan</h5>
                            <p><?php echo e($correspondence->signed_at_location); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5>Tanggal Penandatanganan</h5>
                            <p><?php echo e($correspondence->signed_at_date->format('d F Y')); ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <h5>Penandatangan</h5>
                            <p>
                                <?php echo e($correspondence->signatory_name); ?><br>
                                <small class="text-muted"><?php echo e($correspondence->signatory_position); ?></small>
                                <?php if($correspondence->signatory_rank): ?>
                                <br><small class="text-muted"><?php echo e($correspondence->signatory_rank); ?></small>
                                <?php endif; ?>
                                <?php if($correspondence->signatory_nrp): ?>
                                <br><small class="text-muted">NRP: <?php echo e($correspondence->signatory_nrp); ?></small>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-4 mb-3 text-center">
                            <?php if($correspondence->signature_file): ?>
                            <img src="<?php echo e(asset('storage/' . $correspondence->signature_file)); ?>" alt="Tanda Tangan" class="img-fluid" style="max-height: 100px;">
                            <?php else: ?>
                            <p class="text-muted">Tidak ada file tanda tangan</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if($correspondence->reference_to): ?>
                    <div class="mb-4">
                        <h5>Referensi</h5>
                        <p><?php echo e($correspondence->reference_to); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Dokumen yang Terhubung -->
            <?php if($correspondence->documents->count() > 0): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dokumen Terhubung</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php $__currentLoopData = $correspondence->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><?php echo e($document->document_title); ?></h5>
                                <small><?php echo e($document->document_date->format('d/m/Y')); ?></small>
                            </div>
                            <p class="mb-1"><?php echo e(Str::limit($document->description, 100)); ?></p>
                            <small class="text-muted"><?php echo e($document->document_number); ?></small>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Metadata -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Metadata</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Versi Dokumen</h5>
                        <p><?php echo e($correspondence->document_version); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Dibuat Oleh</h5>
                        <p><?php echo e($correspondence->creator->name ?? 'Unknown'); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Tanggal Pembuatan</h5>
                        <p><?php echo e($correspondence->created_at->format('d F Y H:i')); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Tanggal Update Terakhir</h5>
                        <p><?php echo e($correspondence->updated_at->format('d F Y H:i')); ?></p>
                    </div>
                    
                    <?php if($correspondence->next_review): ?>
                    <div class="mb-3">
                        <h5>Jadwal Review Berikutnya</h5>
                        <p><?php echo e($correspondence->next_review->format('d F Y')); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- File Lampiran -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">File Lampiran</h6>
                </div>
                <div class="card-body">
                    <?php if($correspondence->file_path): ?>
                    <a href="<?php echo e(asset('storage/' . $correspondence->file_path)); ?>" class="btn btn-primary btn-block" target="_blank">
                        <i class="fas fa-file-download mr-1"></i> Download File
                    </a>
                    <?php else: ?>
                    <p class="text-center text-muted">Tidak ada file lampiran</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- QR Code -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">QR Code</h6>
                </div>
                <div class="card-body text-center">
                    <img src="<?php echo e(route('modules.correspondence.letters.qr-code', $correspondence->id)); ?>" alt="QR Code" class="img-fluid mb-2" style="max-width: 150px;">
                    <p class="small text-muted">Scan untuk melihat dokumen ini</p>
                </div>
            </div>
            
            <!-- Tag -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tag</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <?php $__currentLoopData = $correspondence->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex align-items-center badge bg-primary text-white me-1 mb-1 p-2" id="tag-item-<?php echo e($tag->id); ?>">
                            <a href="<?php echo e(route('modules.correspondence.search', ['tag' => $tag->slug])); ?>" class="text-decoration-none text-white">
                                <?php echo e($tag->name); ?>

                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $correspondence)): ?>
                            <button 
                                type="button" 
                                class="btn-close btn-close-white ms-2" 
                                style="font-size: 0.7rem;" 
                                onclick="hapusTagLangsung(<?php echo e($tag->id); ?>, <?php echo e($correspondence->id); ?>, 'App\\Models\\Correspondence')"
                                aria-label="Close">
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $correspondence)): ?>
                    <form id="formTambahTag" action="<?php echo e(route('tenant.tags.attach-document')); ?>" method="POST" class="d-flex gap-2 mt-2">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="document_id" value="<?php echo e($correspondence->id); ?>">
                        <input type="hidden" name="document_type" value="App\Models\Correspondence">
                        <select name="tag_id" id="selectTag" class="form-select form-select-sm" required>
                            <option value="">Pilih Tag</option>
                            <?php $__currentLoopData = App\Models\Tag::forTenant(session('tenant_id'))->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tag->id); ?>" data-slug="<?php echo e($tag->slug); ?>"><?php echo e($tag->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Tambah Tag</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function hapusTagLangsung(tagId, documentId, documentType) {
        if (confirm('Apakah Anda yakin ingin menghapus tag ini?')) {
            fetch('/tenant/tags/detach-document', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    tag_id: tagId,
                    document_id: documentId,
                    document_type: documentType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('tag-item-' + tagId).remove();
                } else {
                    alert('Gagal menghapus tag: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus tag');
            });
        }
    }
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/MYDEV/siar/resources/views/modules/Correspondence/letters/show.blade.php ENDPATH**/ ?>