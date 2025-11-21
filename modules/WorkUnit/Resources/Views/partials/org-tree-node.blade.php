<li>
    @php
        // Menentukan level class berdasarkan kedalaman
        $levelClass = 'level-manager';
        if (isset($depth)) {
            $currentDepth = $depth + 1;
            if ($currentDepth > 3) {
                $levelClass = 'level-staff';
            }
        } else {
            $currentDepth = 1;
        }
        
        // Cek apakah node memiliki anak
        $hasChildren = $node->children && $node->children->count() > 0;
    @endphp

    <div class="node {{ $levelClass }}">
        <div class="node-unit">
            {{ $node->unit_name }}
            @if($hasChildren)
                <button class="toggle-btn" onclick="toggleChildren(this)">
                    <i class="fas fa-minus"></i>
                </button>
            @endif
        </div>
        <div class="node-divider"></div>
        @if($node->headOfUnit)
            <div class="node-name">{{ $node->headOfUnit->name }}</div>
        @endif
    </div>

    @if($hasChildren)
        <ul class="children-container">
            @foreach($node->children as $child)
                @include('modules.WorkUnit.partials.org-tree-node', ['node' => $child, 'depth' => $currentDepth])
            @endforeach
        </ul>
    @endif
</li> 