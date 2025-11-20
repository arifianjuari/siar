@extends('layouts.app')

@section('title', 'Kelola Penugasan Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Kelola Penugasan: {{ $activity->title }}</h5>
                        <a href="{{ route('activity-management.activities.show', $activity->uuid) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Current Assignees -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Penugasan Saat Ini</h6>
                        @if($currentAssignees->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tipe</th>
                                            <th>Nama</th>
                                            <th>Peran</th>
                                            <th>Ditugaskan Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($currentAssignees as $assignee)
                                            <tr>
                                                <td>
                                                    @if($assignee->assignee_type == 'user')
                                                        <i class="fas fa-user text-primary"></i> Pengguna
                                                    @else
                                                        <i class="fas fa-building text-success"></i> Unit Kerja
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($assignee->assignee_type == 'user')
                                                        {{ optional($assignee->assignee)->name ?? 'Pengguna tidak ditemukan' }}
                                                    @else
                                                        {{ optional($assignee->assignee)->unit_name ?? 'Unit tidak ditemukan' }}
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-info">{{ $assignee->getRoleLabelAttribute() }}</span></td>
                                                <td>{{ optional($assignee->assigner)->name ?? 'Sistem' }}</td>
                                                <td>
                                                    @if(auth()->user()->hasPermission('activity-management', 'can_edit'))
                                                        <form action="{{ route('activity-management.assignees.destroy', [$activity->uuid, $assignee->id]) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus penugasan ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center">Belum ada penugasan</p>
                        @endif
                    </div>

                    @if(auth()->user()->hasPermission('activity-management', 'can_edit'))
                        <!-- Add New Assignee Form -->
                        <div class="mt-4">
                            <h6 class="border-bottom pb-2">Tambah Penugasan Baru</h6>
                            <form action="{{ route('activity-management.assignees.store', $activity->uuid) }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <!-- Assignee Type Selection -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Tipe Penugasan</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="assignee_type" 
                                                       id="type_user" value="user" checked>
                                                <label class="form-check-label" for="type_user">
                                                    <i class="fas fa-user text-primary"></i> Pengguna
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="assignee_type" 
                                                       id="type_work_unit" value="work_unit">
                                                <label class="form-check-label" for="type_work_unit">
                                                    <i class="fas fa-building text-success"></i> Unit Kerja
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assignee Selection -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Pilih Penugasan</label>
                                        <select class="form-select" name="assignee_id" required>
                                            <option value="">-- Pilih --</option>
                                            <!-- Users -->
                                            <optgroup label="Pengguna" class="user-options">
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" data-type="user">
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                            <!-- Work Units -->
                                            <optgroup label="Unit Kerja" class="work-unit-options" style="display: none;">
                                                @foreach($workUnits as $unit)
                                                    <option value="{{ $unit->id }}" data-type="work_unit">
                                                        {{ $unit->unit_name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>

                                    <!-- Role Selection -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Peran</label>
                                        <select class="form-select" name="role" required>
                                            <option value="">-- Pilih Peran --</option>
                                            <option value="responsible">Penanggung Jawab (R)</option>
                                            <option value="accountable">Pemberi Kewenangan (A)</option>
                                            <option value="consulted">Konsultan (C)</option>
                                            <option value="informed">Penerima Informasi (I)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize select2 if available
    if ($.fn.select2) {
        $('select').select2({
            theme: 'bootstrap-5'
        });
    }

    // Handle assignee type selection
    $('input[name="assignee_type"]').change(function() {
        var type = $(this).val();
        var select = $('select[name="assignee_id"]');
        
        // Reset selection
        select.val('').trigger('change');
        
        if (type === 'user') {
            $('.user-options').show();
            $('.work-unit-options').hide();
            select.find('option[data-type="work_unit"]').prop('disabled', true);
            select.find('option[data-type="user"]').prop('disabled', false);
        } else {
            $('.user-options').hide();
            $('.work-unit-options').show();
            select.find('option[data-type="user"]').prop('disabled', true);
            select.find('option[data-type="work_unit"]').prop('disabled', false);
        }
    });
});
</script>
@endpush