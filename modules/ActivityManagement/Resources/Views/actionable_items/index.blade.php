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
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                <i class="fas fa-plus"></i> Tambah Item
                            </button>
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
                                                               data-item-uuid="{{ $item->uuid }}">
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
(function() {
    'use strict';
    
    // Ensure this script only runs once
    if (window.actionableItemsInitialized) {
        console.log('Already initialized, skipping');
        return;
    }
    window.actionableItemsInitialized = true;
    
    const activityUuid = '{{ $activity->uuid }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Add Item Form Handler
    const addItemForm = document.getElementById('addItemForm');
    
    if (addItemForm) {
        // Remove any existing listeners
        const newForm = addItemForm.cloneNode(true);
        addItemForm.parentNode.replaceChild(newForm, addItemForm);
        
        let isSubmitting = false;
        
        newForm.addEventListener('submit', function handleSubmit(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Prevent double submission
            if (isSubmitting) {
                console.log('Already submitting, ignoring duplicate request');
                return false;
            }
            
            isSubmitting = true;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            console.log('Submitting data:', data);
            
            fetch(`/activity-management/activities/${activityUuid}/actionable-items`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                console.log('Success:', data);
                // Don't reset isSubmitting, let page reload
                const modal = bootstrap.Modal.getInstance(document.getElementById('addItemModal'));
                if (modal) modal.hide();
                
                // Use setTimeout to ensure modal is closed before reload
                setTimeout(() => {
                    window.location.reload();
                }, 100);
            })
            .catch(error => {
                console.error('Error:', error);
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                
                let errorMessage = 'Terjadi kesalahan';
                if (error.message) {
                    errorMessage = error.message;
                } else if (error.errors) {
                    errorMessage = Object.values(error.errors).flat().join('\n');
                }
                alert(errorMessage);
            });
            
            return false;
        }, { once: false });
    }
    
    // Edit Item Form Handler
    const editItemForm = document.getElementById('editItemForm');
    if (editItemForm) {
        editItemForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const itemUuid = formData.get('itemUuid');
            const data = Object.fromEntries(formData);
            
            fetch(`/activity-management/activities/${activityUuid}/actionable-items/${itemUuid}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                window.location.reload();
            })
            .catch(error => {
                alert('Terjadi kesalahan: ' + (error.message || 'Unknown error'));
            });
        });
    }
    
    // Delete Item Handler
    document.querySelectorAll('.delete-item').forEach(button => {
        button.addEventListener('click', function() {
            const itemUuid = this.dataset.itemUuid;
            const itemTitle = this.dataset.itemTitle;
            
            if (confirm(`Apakah Anda yakin ingin menghapus item "${itemTitle}"?`)) {
                fetch(`/activity-management/activities/${activityUuid}/actionable-items/${itemUuid}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    window.location.reload();
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + (error.message || 'Unknown error'));
                });
            }
        });
    });
    
    // Toggle Status Handler
    document.querySelectorAll('.toggle-status').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const itemUuid = this.dataset.itemUuid;
            
            fetch(`/activity-management/activities/${activityUuid}/actionable-items/${itemUuid}/toggle`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                window.location.reload();
            })
            .catch(error => {
                alert('Terjadi kesalahan: ' + (error.message || 'Unknown error'));
                this.checked = !this.checked;
            });
        });
    });
    
    // Edit Modal Population
    document.querySelectorAll('.edit-item').forEach(button => {
        button.addEventListener('click', function() {
            const item = JSON.parse(this.dataset.item);
            const form = document.getElementById('editItemForm');
            
            form.querySelector('[name="itemUuid"]').value = item.uuid;
            form.querySelector('[name="title"]').value = item.title;
            form.querySelector('[name="description"]').value = item.description || '';
            form.querySelector('[name="due_date"]').value = item.due_date || '';
            form.querySelector('[name="priority"]').value = item.priority;
            form.querySelector('[name="status"]').value = item.status;
        });
    });
})(); // Close IIFE
</script>
@endpush