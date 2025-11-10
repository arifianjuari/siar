@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Detail Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Detail Kegiatan</h5>
                        <div>
                            <a href="{{ route('activity-management.activities.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <a href="{{ route('activity-management.activities.edit', $activity->uuid) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('activity-management.activities.destroy', $activity->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aktivitas ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Kolom Kiri - Informasi Utama -->
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>{{ $activity->title }}</h4>
                                <span class="badge bg-{{ $activity->statusColor }} fs-6">{{ $activity->statusLabel }}</span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="progress mb-3" style="height: 25px;">
                                <div class="progress-bar {{ $activity->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" 
                                     role="progressbar" 
                                     style="width: {{ $activity->progress_percentage }}%" 
                                     aria-valuenow="{{ $activity->progress_percentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $activity->progress_percentage }}% Selesai
                                </div>
                            </div>
                            
                            <!-- Info Dasar -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Kategori:</strong> {{ $activity->category }}</p>
                                    <p class="mb-1"><strong>Unit Kerja:</strong> {{ $activity->workUnit ? $activity->workUnit->unit_name : '-' }}</p>
                                    <p class="mb-1">
                                        <strong>Prioritas:</strong> 
                                        <span class="badge bg-{{ $activity->priorityColor }}">{{ $activity->priorityLabel }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Tanggal Mulai:</strong> {{ $activity->start_date->format('d M Y') }}</p>
                                    <p class="mb-1"><strong>Tanggal Selesai:</strong> {{ $activity->end_date->format('d M Y') }}</p>
                                    <p class="mb-1">
                                        <strong>Tenggat Waktu:</strong> 
                                        @if($activity->due_date)
                                            <span class="{{ $activity->due_date->isPast() && $activity->status != 'completed' && $activity->status != 'cancelled' ? 'text-danger' : '' }}">
                                                {{ $activity->due_date->format('d M Y') }}
                                                @if($activity->due_date->isPast() && $activity->status != 'completed' && $activity->status != 'cancelled')
                                                    <span class="badge bg-danger">Terlambat</span>
                                                @elseif($activity->due_date->diffInDays(now()) <= 7 && $activity->due_date->isFuture() && $activity->status != 'completed' && $activity->status != 'cancelled')
                                                    <span class="badge bg-warning">Segera</span>
                                                @endif
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Deskripsi -->
                            <div class="mb-4">
                                <h6>Deskripsi</h6>
                                <div class="p-3 bg-light rounded">
                                    {!! nl2br(e($activity->description)) !!}
                                </div>
                            </div>
                            
                            <!-- Relasi Kegiatan -->
                            @if($activity->parent || $activity->children->count() > 0)
                            <div class="mb-4">
                                <h6>Relasi Kegiatan</h6>
                                <div class="p-3 bg-light rounded">
                                    @if($activity->parent)
                                        <p class="mb-2">
                                            <strong>Kegiatan Induk:</strong> 
                                            <a href="{{ route('activity-management.activities.show', $activity->parent->uuid) }}">
                                                {{ $activity->parent->title }}
                                            </a>
                                        </p>
                                    @endif
                                    
                                    @if($activity->children->count() > 0)
                                        <p class="mb-1"><strong>Sub Kegiatan:</strong></p>
                                        <ul>
                                            @foreach($activity->children as $child)
                                                <li>
                                                    <a href="{{ route('activity-management.activities.show', $child->uuid) }}">
                                                        {{ $child->title }}
                                                    </a>
                                                    <span class="badge bg-{{ $child->statusColor }}">{{ $child->statusLabel }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <!-- Log Aktivitas -->
                            <div class="mb-4">
                                <h6>Riwayat Aktivitas Terakhir</h6>
                                <div class="p-3 bg-light rounded">
                                    @if($activity->statusLogs->count() > 0)
                                        <ul class="list-group list-group-flush">
                                            @foreach($activity->statusLogs->take(5) as $log)
                                                <li class="list-group-item bg-transparent px-0">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <i class="fas fa-history text-muted me-2"></i>
                                                            <strong>{{ $log->logTypeLabel }}</strong>: {{ $log->note }}
                                                        </div>
                                                        <small class="text-muted">{{ $log->created_at->format('d M Y H:i') }}</small>
                                                    </div>
                                                    <div class="ps-4 mt-1">
                                                        <small>Oleh: {{ $log->changer ? $log->changer->name : 'Sistem' }}</small>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                        @if($activity->statusLogs->count() > 5)
                                            <div class="mt-2 text-center">
                                                <button type="button" class="btn btn-link btn-sm" id="show-more-logs">
                                                    Lihat semua riwayat ({{ $activity->statusLogs->count() }})
                                                </button>
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-muted">Belum ada riwayat aktivitas</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kolom Kanan - Sidebar Informasi -->
                        <div class="col-md-4">
                            <!-- Aksi -->
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="card-title mb-0">Aksi</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('activity-management.activities.update-status', $activity->uuid) }}" method="POST" id="form-status">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Ubah Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="draft" {{ $activity->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="planned" {{ $activity->status == 'planned' ? 'selected' : '' }}>Direncanakan</option>
                                                <option value="pending" {{ $activity->status == 'pending' ? 'selected' : '' }}>Tertunda</option>
                                                <option value="ongoing" {{ $activity->status == 'ongoing' ? 'selected' : '' }}>Sedang Berlangsung</option>
                                                <option value="completed" {{ $activity->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                                <option value="cancelled" {{ $activity->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="note" class="form-label">Catatan (opsional)</label>
                                            <textarea class="form-control" id="note" name="note" rows="2"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save"></i> Perbarui Status
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Info Penugasan -->
                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0">Penugasan</h6>
                                        <a href="{{ route('activity-management.assignees.index', $activity->uuid) }}" class="btn btn-sm btn-light">
                                            <i class="fas fa-users"></i> Kelola
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @if($activity->assignees->count() > 0)
                                            @foreach($activity->assignees as $assignee)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        @if($assignee->assignee_type == 'user')
                                                            <i class="fas fa-user text-primary me-2"></i>
                                                            {{ optional($assignee->assignee)->name ?? 'Pengguna tidak ditemukan' }}
                                                        @else
                                                            <i class="fas fa-building text-success me-2"></i>
                                                            {{ optional($assignee->assignee)->unit_name ?? 'Unit tidak ditemukan' }}
                                                        @endif
                                                        <div class="small text-muted">
                                                            {{ $assignee->getRoleLabelAttribute() }}
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="list-group-item text-center text-muted">Belum ada penugasan</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Risiko Terkait -->
                            @if(isset($relatedRisks) && $relatedRisks->count() > 0)
                            <div class="card mb-3">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="card-title mb-0">Risiko Terkait</h6>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @foreach($relatedRisks as $risk)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                                    <strong>{{ $risk->risk_title }}</strong>
                                                    <div class="small text-muted">
                                                        Dilaporkan: {{ $risk->created_at->format('d M Y') }}
                                                    </div>
                                                </div>
                                                <a href="{{ route('modules.risk-management.risk-reports.show', $risk->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Item Tindakan -->
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0">Item Tindakan</h6>
                                        <a href="{{ route('activity-management.actionable-items.index', $activity->uuid) }}" class="btn btn-sm btn-light">
                                            <i class="fas fa-list-check"></i> Kelola
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    @if($activity->actionableItems->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($activity->actionableItems as $item)
                                                <div class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="badge bg-{{ $item->statusColor }} me-2">{{ $item->statusLabel }}</span>
                                                            {{ $item->getDetailAttribute()['title'] }}
                                                        </div>
                                                    </div>
                                                    <div class="small text-muted mt-1">
                                                        {{ $item->getDetailAttribute()['description'] }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="card-body text-center text-muted">
                                            Tidak ada item tindakan
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Info Lainnya -->
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="card-title mb-0">Informasi</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1 small">
                                        <strong>Dibuat oleh:</strong> {{ $activity->creator ? $activity->creator->name : '-' }}
                                    </p>
                                    <p class="mb-1 small">
                                        <strong>Tanggal dibuat:</strong> {{ $activity->created_at->format('d M Y H:i') }}
                                    </p>
                                    @if($activity->updated_by)
                                        <p class="mb-1 small">
                                            <strong>Diperbarui oleh:</strong> {{ $activity->updater ? $activity->updater->name : '-' }}
                                        </p>
                                        <p class="mb-1 small">
                                            <strong>Terakhir diperbarui:</strong> {{ $activity->updated_at->format('d M Y H:i') }}
                                        </p>
                                    @endif
                                    @if($activity->status == 'completed')
                                        <p class="mb-1 small">
                                            <strong>Diselesaikan oleh:</strong> {{ $activity->completer ? $activity->completer->name : '-' }}
                                        </p>
                                        <p class="mb-1 small">
                                            <strong>Tanggal selesai:</strong> {{ $activity->completed_at ? $activity->completed_at->format('d M Y H:i') : '-' }}
                                        </p>
                                    @endif
                                    @if($activity->status == 'cancelled')
                                        <p class="mb-1 small">
                                            <strong>Dibatalkan oleh:</strong> {{ $activity->canceller ? $activity->canceller->name : '-' }}
                                        </p>
                                        <p class="mb-1 small">
                                            <strong>Tanggal pembatalan:</strong> {{ $activity->cancelled_at ? $activity->cancelled_at->format('d M Y H:i') : '-' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab Konten Tambahan -->
        <div class="col-12">
            <ul class="nav nav-tabs" id="activityTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments" type="button" role="tab">
                        <i class="fas fa-comments me-1"></i> Komentar
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                        <i class="fas fa-history me-1"></i> Riwayat Lengkap
                    </button>
                </li>
            </ul>
            <div class="tab-content pt-4" id="activityTabsContent">
                <!-- Tab Komentar -->
                <div class="tab-pane fade show active" id="comments" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Diskusi</h5>
                            <div id="comment-form-container" class="mb-4">
                                <form id="comment-form">
                                    <div class="mb-3">
                                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Tulis komentar Anda..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="attachments" class="form-label">Lampiran (opsional)</label>
                                        <input class="form-control" type="file" id="attachments" name="attachments[]" multiple>
                                        <div class="form-text">Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG, XLS, XLSX, PPT, PPTX (Maks 10MB)</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Kirim Komentar
                                    </button>
                                </form>
                            </div>
                            
                            <div id="comments-container">
                                @if($activity->comments->count() > 0)
                                    <div class="comments-list">
                                        @foreach($activity->comments as $comment)
                                            <!-- Template komentar akan di-load disini -->
                                            <div class="comment-item" id="comment-{{ $comment->id }}">
                                                <!-- Content akan di-load lewat Ajax -->
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted p-4">
                                        <i class="fas fa-comments fa-2x mb-3"></i>
                                        <p>Belum ada komentar untuk kegiatan ini</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Riwayat -->
                <div class="tab-pane fade" id="history" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Riwayat Aktivitas</h5>
                            @if($activity->statusLogs->count() > 0)
                                <div class="timeline">
                                    @foreach($activity->statusLogs as $log)
                                        <div class="timeline-item">
                                            <div class="timeline-item-marker">
                                                <div class="timeline-item-marker-text">{{ $log->created_at->format('d M Y') }}</div>
                                                <div class="timeline-item-marker-indicator bg-primary"></div>
                                            </div>
                                            <div class="timeline-item-content">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="fw-bold">{{ $log->logTypeLabel }}</span>
                                                        <span class="text-muted ms-2">{{ $log->note }}</span>
                                                    </div>
                                                    <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                                                </div>
                                                <div class="mt-1 small text-muted">
                                                    Oleh: {{ $log->changer ? $log->changer->name : 'Sistem' }}
                                                </div>
                                                @if($log->from_value || $log->to_value)
                                                    <div class="mt-1 small">
                                                        @if($log->from_value)
                                                            <span class="text-danger">{{ $log->from_value }}</span>
                                                        @endif
                                                        @if($log->from_value && $log->to_value)
                                                            <i class="fas fa-arrow-right mx-1"></i>
                                                        @endif
                                                        @if($log->to_value)
                                                            <span class="text-success">{{ $log->to_value }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted p-4">
                                    <i class="fas fa-history fa-2x mb-3"></i>
                                    <p>Belum ada riwayat aktivitas</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Pembatalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin membatalkan kegiatan <strong id="delete-title"></strong>?</p>
                <p class="text-danger">Tindakan ini akan mengubah status kegiatan menjadi dibatalkan dan tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="cancel-form" action="{{ route('activity-management.activities.update-status', $activity->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <input type="hidden" name="note" value="Dibatalkan oleh pengguna">
                    <button type="submit" class="btn btn-danger">Konfirmasi Pembatalan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Lengkap -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Riwayat Aktivitas Lengkap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="full-history-container">
                    <!-- Akan diisi melalui Javascript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 1.625rem;
    }
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0.4375rem;
        height: 100%;
        border-left: 2px solid #e3e6ec;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 2rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-item-marker {
        position: absolute;
        left: -1.625rem;
        width: 1.625rem;
    }
    .timeline-item-marker-text {
        margin-left: -0.8125rem;
        margin-bottom: 0.25rem;
        font-size: 0.8rem;
        color: #a7aeb8;
    }
    .timeline-item-marker-indicator {
        width: 11px;
        height: 11px;
        border-radius: 100%;
        background-color: #fff;
        border: 2px solid #0061f2;
        margin-left: 0.0625rem;
    }
    .timeline-item-content {
        padding: 0 0 0 0.625rem;
        border-left: 0.125rem solid transparent;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Konfirmasi modal hapus
        $('.btn-delete').on('click', function() {
            const uuid = $(this).data('uuid');
            const title = $(this).data('title');
            
            $('#delete-title').text(title);
            $('#delete-form').attr('action', `{{ route('activity-management.activities.destroy', '') }}/${uuid}`);
            
            $('#deleteModal').modal('show');
        });
        
        // Menangani submit form pembatalan
        $('#cancel-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();
            
            $.ajax({
                url: url,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    // Reload page on success
                    window.location.reload();
                },
                error: function(xhr) {
                    console.error('Error cancelling activity:', xhr);
                    alert('Terjadi kesalahan saat membatalkan kegiatan. Silakan coba lagi.');
                }
            });
        });
        
        // Tampilkan semua riwayat aktivitas
        $('#show-more-logs').on('click', function() {
            $('#history-tab').tab('show');
        });
        
        // Load komentar via AJAX
        function loadComments() {
            $.ajax({
                url: "{{ route('activity-management.comments.index', $activity->uuid) }}",
                type: "GET",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#comments-container').html(response.html);
                },
                error: function(xhr) {
                    console.error('Error loading comments:', xhr);
                }
            });
        }
        
        // Load komentar saat halaman dibuka
        loadComments();
        
        // Kirim komentar via AJAX
        $('#comment-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            
            $.ajax({
                url: "{{ route('activity-management.comments.store', $activity->uuid) }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Reset form
                    $('#comment-form')[0].reset();
                    
                    // Tambahkan komentar baru ke container
                    if ($('.comments-list').length === 0) {
                        $('#comments-container').html('<div class="comments-list"></div>');
                    }
                    
                    $('.comments-list').prepend(response.html);
                },
                error: function(xhr) {
                    console.error('Error posting comment:', xhr);
                    alert('Terjadi kesalahan saat mengirim komentar. Silakan coba lagi.');
                }
            });
        });
        
        // Submit form perubahan status
        $('#form-status').on('submit', function(e) {
            e.preventDefault();
            
            if (!confirm('Apakah Anda yakin ingin mengubah status kegiatan?')) {
                return false;
            }
            
            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();
            
            $.ajax({
                url: url,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    // Reload the page on success
                    window.location.reload();
                },
                error: function(xhr) {
                    console.error('Error updating status:', xhr);
                    alert('Terjadi kesalahan saat memperbarui status. Silakan coba lagi.');
                }
            });
        });
    });
</script>
@endpush
