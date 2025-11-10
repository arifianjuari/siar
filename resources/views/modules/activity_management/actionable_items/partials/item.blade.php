@php
$detail = $item->getDetailAttribute();
@endphp

<div class="list-group-item actionable-item" id="item-{{ $item->id }}">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="badge bg-{{ $item->statusColor }} me-2">{{ $item->statusLabel }}</span>
            <strong>{{ $detail['title'] }}</strong>
        </div>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-sm btn-outline-primary update-item-btn" 
                    data-id="{{ $item->id }}" 
                    data-bs-toggle="modal" 
                    data-bs-target="#updateItemModal" 
                    data-title="{{ $detail['title'] }}"
                    data-status="{{ $item->status }}">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger delete-item-btn" 
                    data-id="{{ $item->id }}" 
                    data-title="{{ $detail['title'] }}">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    
    <div class="mt-2 small">
        <p class="mb-1">{{ $detail['description'] }}</p>
        
        @if(isset($detail['reference']) && !empty($detail['reference']))
            <p class="mb-1"><strong>Referensi:</strong> {{ $detail['reference'] }}</p>
        @endif
        
        @if(isset($detail['source_module']) && !empty($detail['source_module']))
            <p class="mb-1"><strong>Sumber:</strong> {{ $detail['source_module'] }}</p>
        @endif
        
        @if($item->note)
            <div class="mt-2 p-2 bg-light rounded">
                <p class="mb-0"><strong>Catatan:</strong> {{ $item->note }}</p>
            </div>
        @endif
    </div>
    
    <div class="mt-2 text-muted small">
        <div class="d-flex justify-content-between">
            <span>
                <i class="fas fa-user me-1"></i> Dibuat oleh: {{ $item->creator->name ?? 'Tidak diketahui' }}
            </span>
            <span>
                <i class="fas fa-calendar me-1"></i> {{ $item->created_at->format('d M Y') }}
            </span>
        </div>
    </div>
</div> 