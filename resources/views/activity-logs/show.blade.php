@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Detail Log Aktivitas') }}</h5>
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-secondary">{{ __('Kembali') }}</a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <h6 class="text-muted">{{ __('Informasi Dasar') }}</h6>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 30%">{{ __('ID') }}</th>
                                    <td>{{ $activity->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Log Name') }}</th>
                                    <td>{{ $activity->log_name ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Event') }}</th>
                                    <td>{{ $activity->event }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Deskripsi') }}</th>
                                    <td>{{ $activity->description }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Waktu') }}</th>
                                    <td>{{ $activity->created_at->format('d M Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 30%">{{ __('User') }}</th>
                                    <td>
                                        @if($activity->causer)
                                        {{ $activity->causer->name }} ({{ $activity->causer->email }})
                                        @else
                                        {{ __('System') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('IP Address') }}</th>
                                    <td>{{ $activity->properties['ip_address'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('User Agent') }}</th>
                                    <td style="font-size: 0.85em; word-break: break-word;">{{ $activity->properties['user_agent'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Method') }}</th>
                                    <td>{{ $activity->properties['request_method'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('URL') }}</th>
                                    <td style="font-size: 0.85em; word-break: break-word;">{{ $activity->properties['request_url'] ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <h6 class="text-muted">{{ __('Properties') }}</h6>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <pre style="margin-bottom: 0;"><code>{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($activity->subject)
                    <div class="row mt-4">
                        <div class="col-md-12 mb-3">
                            <h6 class="text-muted">{{ __('Subject') }}</h6>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 20%">{{ __('Type') }}</th>
                                    <td>{{ get_class($activity->subject) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <td>{{ $activity->subject->getKey() }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Attributes') }}</th>
                                    <td>
                                        <div class="card">
                                            <div class="card-body bg-light">
                                                <pre style="margin-bottom: 0;"><code>{{ json_encode($activity->subject->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->role && auth()->user()->role->slug === 'superadmin')
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <form action="{{ route('activity-logs.destroy', $activity) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus log ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">{{ __('Hapus Log Ini') }}</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 