<div class="comment card mb-3" id="comment-{{ $comment->id }}">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-start">
                <div class="avatar me-2">
                    <div class="avatar-initial rounded-circle bg-secondary">
                        {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
                <div>
                    <h6 class="mb-0">{{ $comment->user->name ?? 'Pengguna tidak ditemukan' }}</h6>
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
                        <a class="dropdown-item edit-comment" href="javascript:void(0);" data-id="{{ $comment->id }}">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item delete-comment text-danger" href="javascript:void(0);" data-id="{{ $comment->id }}">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </a>
                    </li>
                </ul>
            </div>
            @endif
        </div>
        
        <div class="mt-3 comment-content">
            <p>{!! nl2br(e($comment->comment)) !!}</p>
            
            @if(!empty($comment->attachments))
                <div class="attachments mt-2">
                    <p class="mb-1"><strong><i class="fas fa-paperclip me-1"></i> Lampiran:</strong></p>
                    <div class="list-group">
                        @foreach($comment->attachments as $attachment)
                            <a href="{{ asset('storage/' . $attachment['path']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank">
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
                                    {{ $attachment['filename'] }}
                                </div>
                                <span class="badge bg-light text-dark">
                                    {{ round($attachment['size'] / 1024) }} KB
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-sm btn-outline-primary reply-button" data-id="{{ $comment->id }}">
                <i class="fas fa-reply me-1"></i> Balas
            </button>
            
            <span class="text-muted small">
                {{ $comment->replies->count() }} balasan
            </span>
        </div>
        
        <!-- Form balas (hidden by default) -->
        <div class="reply-form mt-3" id="reply-form-{{ $comment->id }}" style="display: none;">
            <form class="reply-comment-form" data-parent-id="{{ $comment->id }}">
                <input type="hidden" name="activity_id" value="{{ $comment->activity_id }}">
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <div class="mb-3">
                    <textarea class="form-control" name="comment" rows="2" placeholder="Tulis balasan Anda..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Lampiran (opsional)</label>
                    <input class="form-control form-control-sm" type="file" name="attachments[]" multiple>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-secondary me-2 cancel-reply" data-id="{{ $comment->id }}">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-paper-plane"></i> Kirim
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Daftar balasan -->
        @if($comment->replies->count() > 0)
            <div class="replies-container mt-3" id="replies-{{ $comment->id }}">
                <div class="card bg-light">
                    <div class="card-body py-2">
                        @foreach($comment->replies as $reply)
                            @include('modules.activity_management.comments.partials.reply_item', ['comment' => $reply])
                            @if(!$loop->last)
                                <hr class="my-2">
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="replies-container mt-3" id="replies-{{ $comment->id }}" style="display: none;"></div>
        @endif
    </div>
</div> 