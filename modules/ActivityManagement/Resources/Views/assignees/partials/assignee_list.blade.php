@if($assignees->count() > 0)
    <div class="list-group">
        @foreach($assignees as $assignee)
            @include('modules.activity_management.assignees.partials.assignee_item', ['assignee' => $assignee])
        @endforeach
    </div>
@else
    <div class="text-center text-muted p-4">
        <i class="fas fa-users fa-2x mb-3"></i>
        <p>Belum ada penugasan untuk kegiatan ini</p>
    </div>
@endif 