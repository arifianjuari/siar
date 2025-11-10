<?php $__env->startSection('title', ' | Daftar Laporan Risiko'); ?>

<?php $__env->startSection('header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Daftar Laporan Risiko</h1>
        <div>
            <?php
                $userRole = auth()->user()->role->slug ?? '';
                $isTenantAdmin = $userRole === 'tenant-admin' || 
                                  strtolower($userRole) === 'tenant-admin';
            ?>
            
            <?php if(auth()->user()->role && $isTenantAdmin): ?>
                <a href="<?php echo e(route('modules.risk-management.analysis-config')); ?>" class="btn btn-secondary me-2">
                    <i class="fas fa-cog me-1"></i> Konfigurasi Akses Analisis
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('modules.risk-management.risk-reports.create')); ?>" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-1"></i> Buat Laporan Baru
            </a>
            <a href="<?php echo e(route('modules.risk-management.dashboard')); ?>" class="btn btn-primary">
                <i class="fas fa-chart-bar me-1"></i> Dashboard Statistik
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .table-risk-reports th,
    .table-risk-reports td {
        font-size: 0.85rem; 
        padding: 0.3rem 0.4rem; /* Perkecil padding lagi */
        line-height: 1.1; /* Perkecil jarak baris lagi */
        vertical-align: middle; 
    }
    .table-risk-reports thead th {
        padding-top: 0.4rem;
        padding-bottom: 0.4rem;
    }
    .table-risk-reports .badge {
        font-size: 0.75rem; 
        padding: 0.25rem 0.4rem; /* Sesuaikan padding badge */
    }
    .tag-input.form-control-sm {
        font-size: 0.75rem; /* Perkecil font input */
        padding: 0.15rem 0.4rem; /* Perkecil padding vertikal input */
        height: calc(1.1 * 0.75rem + 0.15rem * 2 + 2px); /* Sesuaikan tinggi input (line-height * font-size + padding-y*2 + border*2) */
    }
    /* Perkecil tombol tambah tag */
    .tag-form .btn-sm {
        padding: 0.15rem 0.5rem; /* Sesuaikan padding tombol */
        font-size: 0.75rem; /* Samakan font tombol */
        height: calc(1.1 * 0.75rem + 0.15rem * 2 + 2px); /* Samakan tinggi tombol */
        line-height: 1.1; /* Samakan line-height */
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Form Filter -->
    <div class="card mb-4">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-filter me-1"></i> Filter</h6>
            <button type="button" class="btn btn-link btn-sm p-0" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body py-2">
                <form action="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>" method="GET" id="filterForm">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label for="status" class="form-label small">Status</label>
                            <select name="status" id="status" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="Draft" <?php echo e(request('status') == 'Draft' ? 'selected' : ''); ?>>Draft</option>
                                <option value="Ditinjau" <?php echo e(request('status') == 'Ditinjau' ? 'selected' : ''); ?>>Ditinjau</option>
                                <option value="Selesai" <?php echo e(request('status') == 'Selesai' ? 'selected' : ''); ?>>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="risk_level" class="form-label small">Tingkat Risiko</label>
                            <select name="risk_level" id="risk_level" class="form-select form-select-sm">
                                <option value="">Semua Tingkat</option>
                                <option value="Rendah" <?php echo e(request('risk_level') == 'Rendah' ? 'selected' : ''); ?>>Rendah</option>
                                <option value="Sedang" <?php echo e(request('risk_level') == 'Sedang' ? 'selected' : ''); ?>>Sedang</option>
                                <option value="Tinggi" <?php echo e(request('risk_level') == 'Tinggi' ? 'selected' : ''); ?>>Tinggi</option>
                                <option value="Ekstrem" <?php echo e(request('risk_level') == 'Ekstrem' ? 'selected' : ''); ?>>Ekstrem</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="work_unit_id" class="form-label small">Unit Kerja</label>
                            <select name="work_unit_id" id="work_unit_id" class="form-select form-select-sm">
                                <option value="">Semua Unit</option>
                                <?php $__currentLoopData = App\Models\WorkUnit::where('tenant_id', session('tenant_id'))->where('is_active', true)->orderBy('unit_name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($unit->id); ?>" <?php echo e(request('work_unit_id') == $unit->id ? 'selected' : ''); ?>><?php echo e($unit->unit_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tag" class="form-label small">Tag</label>
                            <select name="tag" id="tag" class="form-select form-select-sm">
                                <option value="">Semua Tag</option>
                                <?php $__currentLoopData = App\Models\Tag::where('tenant_id', session('tenant_id'))->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tag->slug); ?>" <?php echo e(request('tag') == $tag->slug ? 'selected' : ''); ?>><?php echo e($tag->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label small">Kata Kunci</label>
                            <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="Cari..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('modules.risk-management.risk-reports.index')); ?>" class="btn btn-sm btn-secondary">Reset</a>
                                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Tabel Laporan Risiko -->
    <div class="card shadow">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-clipboard-list me-2"></i>Daftar Laporan</h5>
            <span class="badge bg-primary">Total: <?php echo e($riskReports->count()); ?></span>
        </div>
        <div class="card-body">
            <?php if($riskReports->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Tidak ada laporan risiko yang ditemukan.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered table-risk-reports">
                        <thead class="table-primary">
                            <tr>
                                <th width="40">No</th>
                                <th>Nomor Laporan</th>
                                <th>Judul Insiden</th>
                                <th>Unit Pelapor</th>
                                <th>Tipe</th>
                                <th>Kategori</th>
                                <th>Tanggal Kejadian</th>
                                <th>Tingkat Risiko</th>
                                <th width="18%">Tag</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $riskReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($report->document_number); ?></td>
                                    <td><?php echo e($report->document_title); ?></td>
                                    <td><?php echo e($report->workUnit->unit_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($report->risk_type ?? 'N/A'); ?></td>
                                    <td><?php echo e($report->risk_category); ?></td>
                                    <td><?php echo e($report->occurred_at->format('d/m/Y')); ?></td>
                                    <td>
                                        <?php if(strtolower($report->risk_level) == 'rendah' || strtolower($report->risk_level) == 'low'): ?>
                                            <span class="badge bg-success"><?php echo e($report->risk_level); ?></span>
                                        <?php elseif(strtolower($report->risk_level) == 'sedang' || strtolower($report->risk_level) == 'medium'): ?>
                                            <span class="badge bg-warning text-dark" style="background-color: #FFFF00 !important;"><?php echo e($report->risk_level); ?></span>
                                        <?php elseif(strtolower($report->risk_level) == 'tinggi' || strtolower($report->risk_level) == 'high'): ?>
                                            <span class="badge text-white" style="background-color: #FFA500 !important;"><?php echo e($report->risk_level); ?></span>
                                        <?php elseif(strtolower($report->risk_level) == 'ekstrem' || strtolower($report->risk_level) == 'extreme'): ?>
                                            <span class="badge bg-danger" style="background-color: #FF0000 !important;"><?php echo e($report->risk_level); ?></span>
                                        <?php else: ?>
                                            <?php echo e($report->risk_level); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                            <?php $__currentLoopData = $report->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="d-flex align-items-center badge bg-secondary text-white me-1 mb-1 p-1" 
                                                     id="tag-item-<?php echo e($tag->id); ?>-<?php echo e($report->id); ?>" 
                                                     style="font-size: 0.75rem;"> 
                                                    <a href="<?php echo e(route('tenant.tags.documents', $tag->slug)); ?>" class="text-decoration-none text-white">
                                                        <?php echo e($tag->name); ?>

                                                    </a>
                                                    <button 
                                                        type="button" 
                                                        class="btn-close btn-close-white ms-2"
                                                        style="font-size: 0.6rem;" 
                                                        onclick="hapusTagLangsung(<?php echo e($tag->id); ?>, <?php echo e($report->id); ?>, 'App\\Models\\RiskReport')"
                                                        aria-label="Close">
                                                    </button>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        
                                        
                                        <form class="d-flex gap-2 tag-form" data-report-id="<?php echo e($report->id); ?>">
                                            
                                            <input type="text" class="form-control form-control-sm tag-input" placeholder="Tambah tag baru..." required style="width: 150px;">
                                            
                                            <button type="submit" class="btn btn-sm btn-primary">+</button>
                                        </form>
                                    </td>
                                    <td>
                                        <?php if($report->analysis): ?>
                                            <?php if($report->analysis->analysis_status == 'draft'): ?>
                                                <span class="badge bg-danger">Draft</span>
                                            <?php elseif($report->analysis->analysis_status == 'in_progress' || $report->analysis->analysis_status == 'reviewed'): ?>
                                                <span class="badge bg-warning text-dark">Ditinjau</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Selesai</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if($report->status == 'Draft'): ?>
                                                <span class="badge bg-danger">Draft</span>
                                            <?php elseif($report->status == 'Ditinjau'): ?>
                                                <span class="badge bg-warning text-dark">Ditinjau</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Selesai</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo e(route('modules.risk-management.risk-reports.show', $report->id)); ?>" class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('modules.risk-management.risk-reports.edit', $report->id)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Form Hapus -->
                                            <form method="POST" action="<?php echo e(route('modules.risk-management.risk-reports.destroy', $report->id)); ?>" style="display: inline;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Tombol Analisis -->
                                            <?php if($report->status === 'Draft'): ?>
                                                <a href="<?php echo e(route('modules.risk-management.risk-analysis.create', $report->id)); ?>" class="btn btn-sm btn-primary" title="Analisis">
                                                    <i class="fas fa-chart-line"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Tombol Setujui -->
                                            <?php if($report->status === 'Ditinjau'): ?>
                                                <form method="POST" action="<?php echo e(route('modules.risk-management.risk-reports.approve', $report->id)); ?>" style="display: inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PUT'); ?>
                                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <!-- Export Awal -->
                                            <a href="<?php echo e(route('modules.risk-management.risk-reports.export-awal', $report->id)); ?>" class="btn btn-sm btn-secondary" title="Export Awal">
                                                <i class="fas fa-file-word"></i>
                                            </a>
                                            
                                            <!-- Export Akhir -->
                                            <?php if($report->status === 'Selesai'): ?>
                                                <a href="<?php echo e(route('modules.risk-management.risk-reports.export-akhir', $report->id)); ?>" class="btn btn-sm btn-primary" title="Export PDF Akhir">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi toggle collapse filter
        const filterToggleBtn = document.querySelector('[data-bs-target="#filterCollapse"]');
        const filterCollapse = document.getElementById('filterCollapse');
        const filterIcon = filterToggleBtn.querySelector('i');
        
        // Toggle icon saat collapse/expand
        filterCollapse.addEventListener('show.bs.collapse', function () {
            filterIcon.classList.remove('fa-chevron-down');
            filterIcon.classList.add('fa-chevron-up');
        });
        
        filterCollapse.addEventListener('hide.bs.collapse', function () {
            filterIcon.classList.remove('fa-chevron-up');
            filterIcon.classList.add('fa-chevron-down');
        });
        
        // Auto-submit form saat select berubah
        const autoSubmitFields = document.querySelectorAll('#status, #risk_level, #work_unit_id, #tag');
        
        autoSubmitFields.forEach(function(field) {
            field.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });

        // Tangani submit form tambah tag
        const tagForms = document.querySelectorAll('.tag-form');
        tagForms.forEach(function(form) {
            let isSubmitting = false; // Tambahkan flag untuk form spesifik ini
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (isSubmitting) {
                    console.log('Submission already in progress for form:', form.dataset.reportId);
                    return; // Cegah submit ganda
                }
                isSubmitting = true; // Set flag
                
                // Dapatkan CSRF token dari meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Dapatkan data dari form
                const reportId = form.dataset.reportId;
                const inputElement = form.querySelector('.tag-input'); // Ambil dari input text
                const tagName = inputElement.value.trim(); // Ambil nama tag dari input
                
                if (!tagName) {
                    isSubmitting = false; // Reset flag jika input kosong
                    return; 
                }
                
                // Buat form data
                const formData = new FormData();
                formData.append('tag_name', tagName); // Kirim tag_name bukan tag_id
                formData.append('document_id', reportId);
                formData.append('document_type', 'App\\Models\\RiskReport');
                formData.append('_token', csrfToken);

                const tagsContainer = form.previousElementSibling; // Target kontainer tag
                
                // Kirim request dengan fetch API ke route baru
                fetch('<?php echo e(route('tenant.tags.create-and-attach')); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json' // Kita mengharapkan JSON
                    },
                    body: formData
                })
                .then(response => {
                    // Coba periksa apakah respons OK sebelum parse JSON
                    if (!response.ok) {
                        // Jika tidak OK, coba baca teks error dan lempar
                        return response.text().then(text => {
                            throw new Error(`Server error: ${response.status} ${response.statusText}. ${text}`);
                        });
                    }
                    // Jika OK, coba parse JSON
                    return response.json(); 
                })
                .then(data => {
                    // Hanya proses jika sukses DAN ada data tag
                    if (data.success && data.tag) {
                        const newTag = data.tag; 
                        if (!tagsContainer.querySelector(`#tag-item-${newTag.id}-${reportId}`)) {
                            const newTagHtml = `
                                <div class="d-flex align-items-center badge bg-secondary text-white me-1 mb-1 p-1" 
                                     id="tag-item-${newTag.id}-${reportId}" 
                                     style="font-size: 0.75rem;"> 
                                    <a href="/tenant/tags/${newTag.slug}/documents" class="text-decoration-none text-white">
                                        ${newTag.name}
                                    </a>
                                    <button 
                                        type="button" 
                                        class="btn-close btn-close-white ms-2"
                                        style="font-size: 0.6rem;" 
                                        onclick="hapusTagLangsung(${newTag.id}, ${reportId}, 'App\\Models\\RiskReport')"
                                        aria-label="Close">
                                    </button>
                                </div>
                            `;
                            tagsContainer.insertAdjacentHTML('beforeend', newTagHtml);
                        } else {
                           console.warn('Tag already exists visually, skipping DOM insertion.'); 
                        }
                        inputElement.value = ''; // Reset input hanya jika sukses
                    } else {
                        // Jika tidak sukses atau tidak ada data tag, log error tapi jangan tampilkan alert
                        console.error('Gagal menambahkan tag atau respons tidak valid:', data.error || data);
                        // alert('Gagal menambahkan tag: ' + (data.error || 'Silakan coba lagi.')); // Hapus alert
                    }
                })
                .catch(error => {
                    // Log error tapi jangan tampilkan alert
                    console.error('Error saat fetch atau parsing JSON:', error);
                    // alert('Terjadi kesalahan saat menambahkan tag.'); // Hapus alert
                })
                .finally(() => {
                    isSubmitting = false; 
                });
            });
        });
    });

    // Fungsi untuk menghapus tag langsung
    function hapusTagLangsung(tagId, documentId, documentType) {
        // Dapatkan CSRF token dari meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Hapus tag dari DOM sebelum request selesai (untuk UX yang lebih cepat)
        const tagElement = document.getElementById(`tag-item-${tagId}-${documentId}`);
        if (tagElement) {
            tagElement.style.opacity = '0.5'; // Visual feedback saat proses penghapusan
        }
        
        // Buat form data untuk request
        const formData = new FormData();
        formData.append('tag_id', tagId);
        formData.append('document_id', documentId);
        formData.append('document_type', documentType);
        formData.append('_token', csrfToken);
        
        // Kirim request dengan fetch API ke route delete-tag
        fetch('<?php echo e(route('tenant.tags.delete-tag')); ?>', { // Menggunakan route delete-tag
            method: 'POST', // Route delete-tag menggunakan POST
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                // 'Accept': 'application/json' // Tidak perlu memaksakan JSON jika server mungkin mengembalikan 204
            },
            body: formData
        })
        .then(response => {
            // Jika respons OK (status 2xx), anggap sukses dan hapus elemen
            if (response.ok) {
                if (tagElement) {
                    tagElement.remove();
                }
            } else {
                // Jika respons tidak OK, lempar error untuk ditangani .catch
                // Coba dapatkan teks error dari respons jika ada
                return response.text().then(text => {
                    throw new Error(`Server error: ${response.status} ${response.statusText}. ${text}`);
                });
            }
        })
        .catch(error => {
            console.error('Error saat menghapus tag:', error);
            // Kembalikan tampilan tag jika terjadi error network atau respons non-OK
            if (tagElement) {
                tagElement.style.opacity = '1';
            }
            // Tampilkan pesan error umum (opsional)
            // alert('Terjadi kesalahan saat mencoba menghapus tag.');
        });
    }
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arifianjuari/Library/CloudStorage/GoogleDrive-arifianjuari@gmail.com/My Drive/01 PAPA/05 DEVELOPMENT/siar/resources/views/modules/RiskManagement/risk-reports/index.blade.php ENDPATH**/ ?>