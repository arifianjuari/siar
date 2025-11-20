<div class="list-group-item d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <div class="avatar-sm me-3">
            @if($assignee->user->avatar)
                <img src="{{ asset('storage/' . $assignee->user->avatar) }}" alt="{{ $assignee->user->name }}" class="rounded-circle img-fluid">
            @else
                <div class="avatar-circle bg-primary text-white">
                    {{ substr($assignee->user->name, 0, 1) }}
                </div>
            @endif
        </div>
        <div>
            <h6 class="mb-0">{{ $assignee->user->name }}</h6>
            <small class="text-muted">{{ $assignee->user->email }}</small>
            @if($assignee->role)
                <span class="badge bg-info ms-2">{{ $assignee->role }}</span>
            @endif
        </div>
    </div>
    <div>
        <span class="badge bg-light text-dark">Ditugaskan: {{ $assignee->created_at->format('d M Y') }}</span>
        @if(auth()->user()->hasRole(['admin', 'superadmin']) || auth()->id() == $activity->created_by)
            <button class="btn btn-sm btn-outline-danger ms-2 remove-assignee" 
                data-id="{{ $assignee->id }}"
                data-user="{{ $assignee->user->name }}">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>
</div> 