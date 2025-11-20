<div class="mb-3">
    <label class="form-label">Dokumen Terkait</label>
    <div class="card">
        <div class="card-body bg-light">
            @if($documents->count() > 0)
                <div class="alert alert-info mb-3">
                    <small><i class="fas fa-info-circle me-1"></i> Dokumen yang dipilih akan tertampil di dashboard manajemen dokumen.</small>
                </div>
                <div class="row">
                    @foreach($documents as $doc)
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" 
                                    name="document_ids[]" 
                                    value="{{ $doc->id }}"
                                    id="doc-{{ $doc->id }}"
                                    @if(isset($riskReport) && $riskReport->documents->contains($doc->id)) checked @endif>
                                <label class="form-check-label" for="doc-{{ $doc->id }}">
                                    {{ $doc->document_title }}
                                    <small class="d-block text-muted">{{ $doc->document_number }}</small>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    <small class="text-muted">Total dokumen tersedia: {{ $documents->count() }}</small>
                    @if(isset($riskReport))
                        <small class="d-block text-muted">Dokumen terkait dengan laporan ini: {{ $riskReport->documents->count() }}</small>
                    @endif
                </div>
            @else
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i> Belum ada dokumen yang tersedia. 
                    <a href="{{ route('modules.document-management.documents.create') }}" class="alert-link" target="_blank">Tambahkan dokumen baru</a> terlebih dahulu.
                </div>
            @endif
        </div>
    </div>
</div> 