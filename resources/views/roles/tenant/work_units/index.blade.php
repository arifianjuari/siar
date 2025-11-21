@extends('layouts.app')

@php $hideDefaultHeader = true; @endphp

@section('content')
    <!-- Card Filter -->
    <div class="card mb-4 shadow-sm border-top-0">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <h6 class="mb-0 me-3 text-dark"><i class="fas fa-filter me-1 small"></i> Filter</h6>
                <form action="{{ route('tenant.work-units.index') }}" method="GET" class="flex-grow-1">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode unit kerja..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="unit_type" class="form-select">
                                <option value="">Semua Tipe Unit</option>
                                <option value="medical" {{ request('unit_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                                <option value="non-medical" {{ request('unit_type') == 'non-medical' ? 'selected' : '' }}>Non-Medical</option>
                                <option value="supporting" {{ request('unit_type') == 'supporting' ? 'selected' : '' }}>Supporting</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                            <a href="{{ route('tenant.work-units.index') }}" class="btn btn-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Card Data Unit Kerja -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-building me-1"></i> Daftar Unit Kerja</h4>
            <div>
                <button id="saveOrder" class="btn btn-primary btn-sm me-2" style="display: none;">
                    <i class="fas fa-save me-1"></i> Simpan Urutan
                </button>
                <a href="{{ route('tenant.work-units.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Unit
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($workUnits->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Belum ada data unit kerja. Silakan tambahkan unit kerja baru.
                </div>
            @else
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="enableDragDrop">
                        <label class="form-check-label" for="enableDragDrop">Aktifkan mode susun urutan (Drag & Drop)</label>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm">
                        <thead class="table-light">
                            <tr class="small">
                                <th width="3%">No</th>
                                <th width="10%">Kode</th>
                                <th width="20%">Nama Unit</th>
                                <th width="5%">Tipe</th>
                                <th width="20%">Kepala Unit</th>
                                <th width="20%">Parent Unit</th>
                                <th width="7%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-units" class="small">
                            @foreach($workUnits as $index => $unit)
                                <tr data-id="{{ $unit->id }}" class="sortable-row">
                                    <td class="text-center">
                                        <span class="index-number">{{ $index + 1 }}</span>
                                        <i class="fas fa-arrows-alt handle" style="display: none; cursor: move; color: #aaa;"></i>
                                    </td>
                                    <td>{{ $unit->unit_code ?? '-' }}</td>
                                    <td>{{ $unit->unit_name }}</td>
                                    <td>
                                        @if($unit->unit_type == 'medical')
                                            <span class="badge bg-success">Medical</span>
                                        @elseif($unit->unit_type == 'non-medical')
                                            <span class="badge bg-info">Non-Medical</span>
                                        @elseif($unit->unit_type == 'non-medical')
                                            <span class="badge bg-info">Non-Medical</span>
                                        @elseif($unit->unit_type == 'supporting')
                                            <span class="badge bg-secondary">Supporting</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $unit->headOfUnit->name ?? '-' }}</td>
                                    <td>{{ $unit->parent ? $unit->parent->unit_name : '-' }}</td>
                                    <td class="text-center">
                                        @if($unit->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Unit Actions">
                                            <a href="{{ route('tenant.work-units.show', $unit) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tenant.work-units.edit', $unit) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm {{ $unit->is_active ? 'btn-secondary' : 'btn-success' }} toggle-status" 
                                                data-action="{{ route('tenant.work-units.toggle-status', $unit) }}"
                                                data-status="{{ $unit->is_active ? 'active' : 'inactive' }}"
                                                title="{{ $unit->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas {{ $unit->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-unit" 
                                                data-action="{{ route('tenant.work-units.destroy', $unit) }}"
                                                data-name="{{ $unit->unit_name }}"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Form untuk delete & toggle -->
    <form id="actionForm" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="_method" value="DELETE" id="methodField">
    </form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const actionForm = document.getElementById('actionForm');
        const methodField = document.getElementById('methodField');
        
        // Handle tombol hapus
        document.querySelectorAll('.delete-unit').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.dataset.action;
                const unitName = this.dataset.name;
                
                // Tampilkan dialog konfirmasi untuk hapus
                const confirmed = confirm(`Apakah Anda yakin ingin menghapus unit kerja ${unitName}?`);
                
                if (confirmed) {
                    // Hapus setelah konfirmasi
                    methodField.value = 'DELETE';
                    actionForm.setAttribute('action', action);
                    
                    // Tambahkan efek visual pada tombol
                    const originalInnerHTML = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    this.disabled = true;
                    
                    // Submit form
                    actionForm.submit();
                }
            });
        });
        
        // Handle tombol toggle status
        document.querySelectorAll('.toggle-status').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.dataset.action;
                
                // Toggle langsung tanpa konfirmasi
                methodField.value = 'POST';
                actionForm.setAttribute('action', action);
                
                // Tambahkan efek visual pada tombol
                const originalInnerHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                // Submit form
                actionForm.submit();
            });
        });

        // Inisialisasi Sortable
        let sortable = null;
        const sortableContainer = document.getElementById('sortable-units');
        const enableDragDropSwitch = document.getElementById('enableDragDrop');
        const saveOrderButton = document.getElementById('saveOrder');
        const handleIcons = document.querySelectorAll('.handle');
        const indexNumbers = document.querySelectorAll('.index-number');

        // Toggle drag and drop mode
        enableDragDropSwitch.addEventListener('change', function() {
            if (this.checked) {
                // Enable drag and drop mode
                handleIcons.forEach(icon => icon.style.display = 'inline-block');
                indexNumbers.forEach(num => num.style.display = 'none');
                saveOrderButton.style.display = 'inline-block';
                
                // Initialize sortable
                sortable = new Sortable(sortableContainer, {
                    handle: '.handle',
                    animation: 150,
                    onEnd: function(evt) {
                        // Re-number rows after sorting
                        updateRowNumbers();
                    }
                });

                // Add visual indication for sortable rows
                document.querySelectorAll('.sortable-row').forEach(row => {
                    row.classList.add('bg-light');
                    row.style.cursor = 'grab';
                });
            } else {
                // Disable drag and drop mode
                handleIcons.forEach(icon => icon.style.display = 'none');
                indexNumbers.forEach(num => num.style.display = 'inline-block');
                saveOrderButton.style.display = 'none';
                
                // Destroy sortable instance
                if (sortable) {
                    sortable.destroy();
                    sortable = null;
                }
                
                // Remove visual indication
                document.querySelectorAll('.sortable-row').forEach(row => {
                    row.classList.remove('bg-light');
                    row.style.cursor = 'default';
                });
            }
        });

        // Update row numbers after sorting
        function updateRowNumbers() {
            document.querySelectorAll('#sortable-units tr').forEach((row, index) => {
                row.querySelector('.index-number').textContent = index + 1;
            });
        }

        // Save the new order
        saveOrderButton.addEventListener('click', function() {
            const newOrder = [];
            document.querySelectorAll('#sortable-units tr').forEach((row, index) => {
                newOrder.push({
                    id: row.dataset.id,
                    position: index + 1
                });
            });

            // Tampilkan indikator loading
            saveOrderButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
            saveOrderButton.disabled = true;

            // Send the new order to the server
            fetch('{{ route("tenant.work-units.update-order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({order: newOrder})
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Kembalikan tombol ke keadaan semula setelah berhasil
                    setTimeout(() => {
                        saveOrderButton.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Urutan';
                        saveOrderButton.disabled = false;
                        
                        // Nonaktifkan mode drag & drop
                        document.getElementById('enableDragDrop').click();
                    }, 500);
                } else {
                    saveOrderButton.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Urutan';
                    saveOrderButton.disabled = false;
                    alert('Terjadi kesalahan saat menyimpan urutan.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                saveOrderButton.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Urutan';
                saveOrderButton.disabled = false;
                alert('Terjadi kesalahan saat mengirim data ke server.');
            });
        });
    });
</script>
@endpush 