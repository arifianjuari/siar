@extends('layouts.app')

@section('title', 'Kelola Item Aksi - ' . $activity->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Item Aksi: {{ $activity->title }}</h5>
                        <div>
                            <a href="{{ route('activity-management.activities.show', $activity->uuid) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            @if(auth()->user()->hasPermission('activity-management', 'can_edit'))
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($actionableItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 40px">#</th>
                                        <th>Item Aksi</th>
                                        <th style="width: 120px">Prioritas</th>
                                        <th style="width: 120px">Status</th>
                                        <th style="width: 120px">Tenggat</th>
                                        <th style="width: 200px">Dibuat Oleh</th>
                                        <th style="width: 120px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($actionableItems as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input toggle-status" 
                                                               type="checkbox" 
                                                               {{ $item->status === 'completed' ? 'checked' : '' }}
                                                               data-item-uuid="{{ $item->uuid }}"
                                                               {{ auth()->user()->hasPermission('activity-management', 'can_edit') ? '' : 'disabled' }}>
                                                    </div>
                                                    <div class="ms-2">
                                                        <strong>{{ $item->title }}</strong>
                                                        @if($item->description)
                                                            <div class="small text-muted">{{ $item->description }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $item->priority_color }}">
                                                    {{ $item->priority_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $item->status_color }}">
                                                    {{ $item->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($item->due_date)
                                                    {{ $item->due_date->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small">
                                                    {{ $item->creator->name }}
                                                    <br>
                                                    <span class="text-muted">
                                                        {{ $item->created_at->format('d/m/Y H:i') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                @if(auth()->user()->hasPermission('activity-management', 'can_edit'))
                                                    <button type="button" 
                                                            class="btn btn-sm btn-info edit-item"
                                                            data-item="{{ json_encode($item) }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editItemModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger delete-item"
                                                            data-item-uuid="{{ $item->uuid }}"
                                                            data-item-title="{{ $item->title }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted my-5">Belum ada item aksi yang ditambahkan</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Item Aksi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addItemForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tenggat Waktu</label>
                        <input type="date" class="form-control" name="due_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioritas <span class="text-danger">*</span></label>
                        <select class="form-select" name="priority" required>
                            <option value="low">Rendah</option>
                            <option value="medium">Sedang</option>
                            <option value="high">Tinggi</option>
                            <option value="critical">Kritis</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Item Aksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editItemForm">
                <input type="hidden" name="itemUuid">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tenggat Waktu</label>
                        <input type="date" class="form-control" name="due_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioritas <span class="text-danger">*</span></label>
                        <select class="form-select" name="priority" required>
                            <option value="low">Rendah</option>
                            <option value="medium">Sedang</option>
                            <option value="high">Tinggi</option>
                            <option value="critical">Kritis</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="pending">Menunggu</option>
                            <option value="in_progress">Sedang Dikerjakan</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const activityUuid = '{{ $activity->uuid }}';
    
    // Add Item Form Handler
    $('#addItemForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: `/activity-management/activities/${activityUuid}/actionable-items`,
            method: 'POST',
            data: Object.fromEntries(formData),
            success: function(response) {
                window.location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
            }
        });
    });
    
    // Edit Item Form Handler
    $('#editItemForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const itemUuid = formData.get('itemUuid');
        
        $.ajax({
            url: `/activity-management/activities/${activityUuid}/actionable-items/${itemUuid}`,
            method: 'PUT',
            data: Object.fromEntries(formData),
            success: function(response) {
                window.location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
            }
        });
    });
    
    // Delete Item Handler
    $('.delete-item').on('click', function() {
        const itemUuid = $(this).data('item-uuid');
        const itemTitle = $(this).data('item-title');
        
        if (confirm(`Apakah Anda yakin ingin menghapus item "${itemTitle}"?`)) {
            $.ajax({
                url: `/activity-management/activities/${activityUuid}/actionable-items/${itemUuid}`,
                method: 'DELETE',
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                }
            });
        }
    });
    
    // Toggle Status Handler
    $('.toggle-status').on('change', function() {
        const itemUuid = $(this).data('item-uuid');
        
        $.ajax({
            url: `/activity-management/activities/${activityUuid}/actionable-items/${itemUuid}/toggle`,
            method: 'PUT',
            success: function(response) {
                window.location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                $(this).prop('checked', !$(this).prop('checked'));
            }
        });
    });
    
    // Edit Modal Population
    $('.edit-item').on('click', function() {
        const item = $(this).data('item');
        const form = $('#editItemForm');
        
        form.find('[name="itemUuid"]').val(item.uuid);
        form.find('[name="title"]').val(item.title);
        form.find('[name="description"]').val(item.description);
        form.find('[name="due_date"]').val(item.due_date);
        form.find('[name="priority"]').val(item.priority);
        form.find('[name="status"]').val(item.status);
    });
});
</script>
@endpush