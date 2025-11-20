@extends('layouts.app', ['hideDefaultHeader' => true])

@section('title', 'Pengelolaan Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Daftar Kegiatan</h5>
                        <a href="{{ route('activity-management.activities.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Kegiatan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter dan pencarian -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form action="{{ route('activity-management.activities.index') }}" method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Direncanakan</option>
                                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Sedang Berlangsung</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="priority" class="form-label">Prioritas</label>
                                    <select name="priority" id="priority" class="form-select">
                                        <option value="all" {{ request('priority') == 'all' ? 'selected' : '' }}>Semua Prioritas</option>
                                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                                        <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Kritis</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="work_unit_id" class="form-label">Unit Kerja</label>
                                    <select name="work_unit_id" id="work_unit_id" class="form-select">
                                        <option value="0" {{ request('work_unit_id') == '0' ? 'selected' : '' }}>Semua Unit</option>
                                        @foreach($workUnits as $unit)
                                            <option value="{{ $unit->id }}" {{ request('work_unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="category" class="form-label">Kategori</label>
                                    <select name="category" id="category" class="form-select">
                                        <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="search" class="form-label">Cari</label>
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Cari kegiatan..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="assigned_to_me" name="assigned_to_me" value="1" {{ request('assigned_to_me') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="assigned_to_me">
                                            Ditugaskan kepada saya
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="created_by_me" name="created_by_me" value="1" {{ request('created_by_me') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="created_by_me">
                                            Dibuat oleh saya
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <a href="{{ route('activity-management.activities.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-sync"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <h6 class="card-title">Ringkasan</h6>
                                    <div class="row text-center">
                                        <div class="col">
                                            <div class="p-2 rounded bg-warning text-white mb-1">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                            <div class="small">Jatuh Tempo Segera</div>
                                            <div class="h5">{{ $activities->where('due_date', '<=', now()->addDays(7))->where('status', '!=', 'completed')->where('status', '!=', 'cancelled')->count() }}</div>
                                        </div>
                                        <div class="col">
                                            <div class="p-2 rounded bg-danger text-white mb-1">
                                                <i class="fas fa-calendar-times"></i>
                                            </div>
                                            <div class="small">Terlambat</div>
                                            <div class="h5">{{ $activities->where('due_date', '<', now())->where('status', '!=', 'completed')->where('status', '!=', 'cancelled')->count() }}</div>
                                        </div>
                                        <div class="col">
                                            <div class="p-2 rounded bg-info text-white mb-1">
                                                <i class="fas fa-tasks"></i>
                                            </div>
                                            <div class="small">Total</div>
                                            <div class="h5">{{ $activities->count() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Kegiatan -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Prioritas</th>
                                    <th>Kategori</th>
                                    <th>Unit Kerja</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tenggat Waktu</th>
                                    <th>Progres</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($activities->count() > 0)
                                    @foreach($activities as $activity)
                                    <tr>
                                        <td>
                                            <a href="{{ route('activity-management.activities.show', $activity->uuid) }}" class="text-decoration-none text-dark">
                                                {{ $activity->title }}
                                            </a>
                                            @if($activity->parent)
                                                <div class="small text-muted">
                                                    <i class="fas fa-link me-1"></i> Sub dari: {{ $activity->parent->title }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $activity->statusColor }}">
                                                {{ $activity->statusLabel }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $activity->priorityColor }}">
                                                {{ $activity->priorityLabel }}
                                            </span>
                                        </td>
                                        <td>{{ $activity->category }}</td>
                                        <td>{{ $activity->workUnit ? $activity->workUnit->unit_name : '-' }}</td>
                                        <td>{{ $activity->start_date->format('d M Y') }}</td>
                                        <td>
                                            @if($activity->due_date)
                                                <span class="{{ $activity->due_date->isPast() && $activity->status != 'completed' && $activity->status != 'cancelled' ? 'text-danger' : '' }}">
                                                    {{ $activity->due_date->format('d M Y') }}
                                                </span>
                                                @if($activity->due_date->isPast() && $activity->status != 'completed' && $activity->status != 'cancelled')
                                                    <span class="badge bg-danger">Terlambat</span>
                                                @elseif($activity->due_date->diffInDays(now()) <= 7 && $activity->due_date->isFuture() && $activity->status != 'completed' && $activity->status != 'cancelled')
                                                    <span class="badge bg-warning">Segera</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar {{ $activity->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $activity->progress_percentage }}%" aria-valuenow="{{ $activity->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ $activity->progress_percentage }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('activity-management.activities.show', $activity->uuid) }}" class="btn btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('activity-management.activities.edit', $activity->uuid) }}" class="btn btn-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-delete" title="Hapus" 
                                                    data-uuid="{{ $activity->uuid }}" 
                                                    data-title="{{ $activity->title }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data kegiatan yang ditemukan</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $activities->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kegiatan <strong id="delete-title"></strong>?</p>
                <p class="text-danger">Tindakan ini akan mengubah status kegiatan menjadi dibatalkan dan tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="delete-form" action="{{ route('activity-management.activities.destroy', '') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Menangani modal konfirmasi hapus
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const uuid = this.getAttribute('data-uuid');
                const title = this.getAttribute('data-title');
                
                document.getElementById('delete-title').textContent = title;
                document.getElementById('delete-form').action = "{{ url('activity-management/activities') }}/" + uuid;
                
                deleteModal.show();
            });
        });

        // Submit form filter saat perubahan dropdown
        const filterSelects = document.querySelectorAll('#status, #priority, #work_unit_id, #category');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    });
</script>
@endpush
