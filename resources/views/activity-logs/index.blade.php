@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Log Aktivitas') }}</h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="mb-4">
                        <form action="{{ route('activity-logs.index') }}" method="GET" class="row g-3">
                            <div class="col-md-2">
                                <label for="search" class="form-label">{{ __('Pencarian') }}</label>
                                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Cari...">
                            </div>
                            <div class="col-md-2">
                                <label for="log_name" class="form-label">{{ __('Log Name') }}</label>
                                <select class="form-select" id="log_name" name="log_name">
                                    <option value="">Semua</option>
                                    @foreach($logNames as $name)
                                    <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="event" class="form-label">{{ __('Event') }}</label>
                                <select class="form-select" id="event" name="event">
                                    <option value="">Semua</option>
                                    @foreach($events as $event)
                                    <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>{{ $event }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="causer_id" class="form-label">{{ __('User') }}</label>
                                <select class="form-select" id="causer_id" name="causer_id">
                                    <option value="">Semua User</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">{{ __('Dari Tanggal') }}</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">{{ __('Sampai Tanggal') }}</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                                <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Waktu') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Deskripsi') }}</th>
                                    <th>{{ __('Event') }}</th>
                                    <th>{{ __('IP Address') }}</th>
                                    <th>{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        @if($activity->causer)
                                        {{ $activity->causer->name }}
                                        @else
                                        {{ __('System') }}
                                        @endif
                                    </td>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->event }}</td>
                                    <td>{{ $activity->properties['ip_address'] ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('activity-logs.show', $activity) }}" class="btn btn-sm btn-info">
                                            {{ __('Detail') }}
                                        </a>
                                        @if(auth()->user()->role && auth()->user()->role->slug === 'superadmin')
                                        <form action="{{ route('activity-logs.destroy', $activity) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus log ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">{{ __('Hapus') }}</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('Tidak ada data log aktivitas') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $activities->links() }}
                    </div>

                    @if(auth()->user()->role && auth()->user()->role->slug === 'superadmin')
                    <div class="mt-4">
                        <h5>{{ __('Pembersihan Log') }}</h5>
                        <form action="{{ route('activity-logs.purge') }}" method="POST" class="row g-3" onsubmit="return confirm('Apakah Anda yakin ingin menghapus log yang lebih lama dari periode ini?')">
                            @csrf
                            <div class="col-md-4">
                                <label for="days" class="form-label">{{ __('Hapus log lebih lama dari (hari)') }}</label>
                                <input type="number" min="1" class="form-control" id="days" name="days" value="30" required>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-danger">{{ __('Hapus Log Lama') }}</button>
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