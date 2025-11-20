@if($comments->count() > 0)
    <div class="comments-list">
        @foreach($comments as $comment)
            @include('modules.activity_management.comments.partials.comment_item', ['comment' => $comment])
        @endforeach
    </div>
    
    <!-- Pagination jika diperlukan -->
    @if($comments->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $comments->links() }}
        </div>
    @endif
@else
    <div class="text-center text-muted p-4">
        <i class="fas fa-comments fa-2x mb-3"></i>
        <p>Belum ada komentar untuk kegiatan ini</p>
    </div>
@endif 