<div class="reply" id="comment-{{ $comment->id }}">
    <div class="d-flex justify-content-between align-items-start">
        <div class="d-flex align-items-start">
            <div class="avatar me-2" style="width: 30px; height: 30px;">
                <div class="avatar-initial rounded-circle bg-secondary" style="width: 30px; height: 30px; font-size: 0.8rem;">
                    {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                </div>
            </div>
            <div>
                <h6 class="mb-0 fs-6">{{ $comment->user->name ?? 'Pengguna tidak ditemukan' }}</h6>
                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
            </div>
        </div>
        
        @if(auth()->id() == $comment->user_id || (auth()->user()->role && in_array(auth()->user()->role->slug, ['admin', 'tenant-admin', 'supervisor'])))
        <div class="dropdown">
            <button class="btn btn-sm btn-link text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item edit-reply" href="javascript:void(0);" data-id="{{ $comment->id }}">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </li>
                <li>
                    <a class="dropdown-item delete-reply text-danger" href="javascript:void(0);" data-id="{{ $comment->id }}">
                        <i class="fas fa-trash me-1"></i> Hapus
                    </a>
                </li>
            </ul>
        </div>
        @endif
    </div>
    
    <div class="ps-4 ms-2 mt-2 reply-content">
        <p class="mb-2">{!! nl2br(e($comment->comment)) !!}</p>
        
        @if(!empty($comment->attachments))
            <div class="attachments mb-2">
                <p class="mb-1"><strong><i class="fas fa-paperclip me-1"></i> Lampiran:</strong></p>
                <div class="list-group">
                    @foreach($comment->attachments as $attachment)
                        <a href="{{ asset('storage/' . $attachment['path']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-1 px-2" target="_blank">
                            <div>
                                <i class="
                                    @if(Str::contains($attachment['type'], 'pdf'))
                                        fas fa-file-pdf text-danger
                                    @elseif(Str::contains($attachment['type'], 'word') || Str::contains($attachment['type'], 'document'))
                                        fas fa-file-word text-primary
                                    @elseif(Str::contains($attachment['type'], 'excel') || Str::contains($attachment['type'], 'spreadsheet'))
                                        fas fa-file-excel text-success
                                    @elseif(Str::contains($attachment['type'], 'powerpoint') || Str::contains($attachment['type'], 'presentation'))
                                        fas fa-file-powerpoint text-warning
                                    @elseif(Str::contains($attachment['type'], 'image'))
                                        fas fa-file-image text-info
                                    @else
                                        fas fa-file text-secondary
                                    @endif
                                    me-2
                                "></i>
                                <small>{{ $attachment['filename'] }}</small>
                            </div>
                            <span class="badge bg-light text-dark">
                                <small>{{ round($attachment['size'] / 1024) }} KB</small>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div> 