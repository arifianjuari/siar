@if($actionableItems->count() > 0)
    <div class="list-group">
        @foreach($actionableItems as $item)
            @include('modules.activity_management.actionable_items.partials.item', ['item' => $item])
        @endforeach
    </div>
@else
    <div class="text-center text-muted p-4">
        <i class="fas fa-list-check fa-2x mb-3"></i>
        <p>Belum ada item tindakan untuk kegiatan ini</p>
    </div>
@endif 