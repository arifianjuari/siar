<!-- File ini adalah template baru -->
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Rencana Tindak Lanjut</h5>
    </div>
    <div class="card-body">
        @if($riskReport->activity)
            <div class="alert alert-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Kegiatan terkait:</strong>
                        <a href="{{ route('activity-management.activities.show', $riskReport->activity->uuid) }}" class="ms-2">
                            {{ $riskReport->activity->title }}
                        </a>
                        <span class="badge bg-{{ $riskReport->activity->statusColor }}">
                            {{ $riskReport->activity->statusLabel }}
                        </span>
                    </div>
                    <button class="btn btn-sm btn-danger" id="btn-unlink-activity" data-bs-toggle="modal" data-bs-target="#unlinkActivityModal">
                        <i class="fas fa-unlink"></i> Lepaskan
                    </button>
                </div>
                <div class="mt-2 small">
                    <div class="progress mb-1" style="height: 8px;">
                        <div class="progress-bar {{ $riskReport->activity->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" 
                             role="progressbar" 
                             style="width: {{ $riskReport->activity->progress_percentage }}%" 
                             aria-valuenow="{{ $riskReport->activity->progress_percentage }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Progres: {{ $riskReport->activity->progress_percentage }}%</span>
                        <span>
                            <i class="fas fa-calendar-alt me-1"></i> Tenggat: 
                            {{ $riskReport->activity->due_date ? $riskReport->activity->due_date->format('d M Y') : 'Tidak ditentukan' }}
                        </span>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-3">
                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                <p>Belum ada kegiatan tindak lanjut untuk risiko ini.</p>
                <div class="mt-3">
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#linkActivityModal">
                        <i class="fas fa-link"></i> Hubungkan dengan Kegiatan
                    </button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createActivityModal">
                        <i class="fas fa-plus-circle"></i> Buat Kegiatan Baru
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal untuk menghubungkan dengan kegiatan yang sudah ada -->
<div class="modal fade" id="linkActivityModal" tabindex="-1" aria-labelledby="linkActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="linkActivityModalLabel">Hubungkan dengan Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="link-activity-form">
                    @csrf
                    <div class="mb-3">
                        <label for="activity_id" class="form-label">Pilih Kegiatan</label>
                        <select class="form-select" id="activity_id" name="activity_id" required>
                            <option value="">-- Pilih Kegiatan --</option>
                            @foreach($activities ?? [] as $activity)
                                <option value="{{ $activity->id }}">{{ $activity->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Hubungkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk membuat kegiatan baru -->
<div class="modal fade" id="createActivityModal" tabindex="-1" aria-labelledby="createActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createActivityModalLabel">Buat Kegiatan Tindak Lanjut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('risk-reports.create-activity', $riskReport->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', 'Tindak Lanjut Risiko: ' . $riskReport->risk_title) }}" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', "Tindak lanjut untuk risiko:\n" . $riskReport->risk_title . "\n\nKronologi risiko:\n" . Str::limit($riskReport->chronology, 200)) }}</textarea>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+1 week'))) }}" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="priority" class="form-label">Prioritas <span class="text-danger">*</span></label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                                <option value="high" {{ old('priority', 'high') == 'high' ? 'selected' : '' }}>Tinggi</option>
                                <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Kritis</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Buat Kegiatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal konfirmasi pelepasan kegiatan -->
<div class="modal fade" id="unlinkActivityModal" tabindex="-1" aria-labelledby="unlinkActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unlinkActivityModalLabel">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin melepaskan hubungan dengan kegiatan ini?</p>
                <p class="text-danger small">Catatan: Tindakan ini tidak akan menghapus kegiatan, hanya melepaskan hubungan dengan risiko ini.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-unlink-btn">Ya, Lepaskan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi select2 jika tersedia
        if ($.fn.select2) {
            $('#activity_id').select2({
                dropdownParent: $('#linkActivityModal'),
                width: '100%',
                placeholder: 'Pilih kegiatan...',
                allowClear: true
            });
        }
        
        // Handle link activity form submission
        $('#link-activity-form').submit(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: "{{ route('risk-reports.link-activity', $riskReport->id) }}",
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.reload();
                    } else {
                        alert(response.message || 'Terjadi kesalahan');
                    }
                },
                error: function(xhr) {
                    let message = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        });
        
        // Handle unlink activity
        $('#confirm-unlink-btn').click(function() {
            $.ajax({
                url: "{{ route('risk-reports.unlink-activity', $riskReport->id) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.reload();
                    } else {
                        alert(response.message || 'Terjadi kesalahan');
                    }
                },
                error: function(xhr) {
                    let message = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        });
    });
</script>
@endpush 