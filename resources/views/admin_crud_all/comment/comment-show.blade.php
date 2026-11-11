@extends('admin.layouts.admin')

@section('title', 'Commentary Thread #' . $comment->id)
@section('page_title', 'Discourse Thread Details')
@section('parentTitle', 'Discourse & Comments')

@section('content')

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Main Comment Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-card">
            <div class="card-header bg-transparent border-bottom p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-primary-subtle text-primary fw-bold me-3 rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 48px; height: 48px; font-size: 1.4rem;">
                        <span>{{ strtoupper(substr($comment->author_name ?? 'G', 0, 1)) }}</span>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-black text-body">{{ $comment->author_name ?? 'Guest Reader' }}</h5>
                        <p class="text-muted small mb-0">
                            {{ $comment->author_email ?? '' }} &bull; {{ $comment->created_at ? $comment->created_at->format('M d, Y \a\t h:i A') : '—' }}
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    @switch($comment->status)
                        @case('approved')
                            <span class="badge bg-success-subtle text-success extra-small px-3 py-1.5 rounded-pill fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i> Approved
                            </span>
                            @break
                        @case('spam')
                            <span class="badge bg-danger-subtle text-danger extra-small px-3 py-1.5 rounded-pill fw-bold">
                                <i class="bi bi-bug-fill me-1"></i> Spam
                            </span>
                            @break
                        @default
                            <span class="badge bg-warning-subtle text-warning extra-small px-3 py-1.5 rounded-pill fw-bold">
                                <i class="bi bi-hourglass-split me-1"></i> Pending
                            </span>
                    @endswitch

                    <a href="{{ route('admin.comments.edit', $comment->id) }}" class="btn btn-outline-primary btn-sm rounded-circle shadow-xs" title="Edit Commentary" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; padding: 0;">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle shadow-xs delete-confirm" data-title="Move to Trash?" data-message="Move this commentary to the trash vault?" style="width: 32px; height: 32px; padding: 0;">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="mb-4">
                    <h6 class="extra-small fw-black text-uppercase text-muted tracking-widest mb-2" style="font-size: 0.72rem; letter-spacing: 0.08em;">Primary Commentary Content</h6>
                    <div class="p-4 bg-body-tertiary rounded-4 border border-opacity-10 shadow-xs" style="line-height: 1.8; font-size: 1.05rem;">
                        {{ $comment->content ?? $comment->comment }}
                    </div>
                </div>

                @php
                    $allReplies = $comment->replies;
                    if ($allReplies->isEmpty() && method_exists($comment, 'commentReplies')) {
                        $allReplies = $comment->commentReplies;
                    }
                @endphp

                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="extra-small fw-black text-uppercase text-muted tracking-widest mb-0" style="font-size: 0.72rem; letter-spacing: 0.08em;">
                            Thread Replies ({{ $allReplies->count() }})
                        </h6>
                    </div>

                    <div class="list-group list-group-flush rounded-4 overflow-hidden border">
                        @forelse($allReplies as $reply)
                        @php
                            $replyAuthor = $reply->user->name ?? ($reply->author_name ?? 'Editorial Staff');
                            $replyText = $reply->reply ?? $reply->content;
                            $replyInitial = strtoupper(substr($replyAuthor, 0, 1));
                        @endphp
                        <div class="list-group-item p-4 border-bottom bg-body-tertiary bg-opacity-50">
                            <div class="d-flex align-items-start gap-3">
                                <div class="avatar bg-info-subtle text-info fw-bold rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-xs" style="width: 36px; height: 36px; font-size: 0.95rem;">
                                    <span>{{ $replyInitial }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold small text-body">{{ $replyAuthor }}</h6>
                                        <small class="text-muted extra-small">{{ $reply->created_at ? $reply->created_at->diffForHumans() : '—' }}</small>
                                    </div>
                                    <p class="small mb-0 text-body-secondary" style="line-height: 1.6;">{{ $replyText }}</p>
                                </div>
                                <div class="d-flex gap-1 ms-2">
                                    <a href="{{ route('admin.comments.reply.edit', $reply->id) }}" class="btn btn-link btn-sm text-primary p-0" title="Edit Reply">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.comments.reply.destroy', $reply->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-link btn-sm text-danger p-0 delete-confirm" data-title="Move Reply to Trash?" title="Delete Reply">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item p-4 text-center text-muted small">
                            No replies submitted for this commentary yet.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card-footer bg-body-tertiary p-3 text-center border-top">
                <a href="{{ route('admin.comments.index') }}" class="btn btn-link text-muted text-decoration-none fw-bold small">
                    <i class="bi bi-arrow-left me-1"></i> Return to All Remarks
                </a>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Associated Article -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-card">
            <div class="card-header bg-transparent border-bottom p-4">
                <h6 class="mb-0 fw-black text-body">Associated Publication</h6>
            </div>
            <div class="card-body p-4">
                @if($comment->post)
                    @php
                        $postThumb = $comment->post->image->link ?? ($comment->post->featured_image ? (str_starts_with($comment->post->featured_image, 'http') ? $comment->post->featured_image : 'storage/' . ltrim($comment->post->featured_image, '/')) : null);
                    @endphp
                    @if($postThumb)
                        <img src="{{ asset($postThumb) }}" class="w-100 rounded-3 shadow-sm mb-3" style="height: 180px; object-fit: cover;" alt="{{ $comment->post->title }}">
                    @endif
                    <h6 class="fw-bold text-body mb-2">{{ $comment->post->title }}</h6>
                    <p class="text-muted extra-small mb-3" style="line-height: 1.5;">{{ Str::limit(strip_tags($comment->post->excerpt ?: $comment->post->content), 120) }}</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.posts.edit', $comment->post->id) }}" class="btn btn-outline-secondary rounded-pill fw-bold small">
                            <i class="bi bi-pencil-square me-1"></i> Edit Article
                        </a>
                        <a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank" class="btn btn-gold rounded-pill fw-bold small">
                            <i class="bi bi-box-arrow-up-right me-1"></i> View on Public Gazette
                        </a>
                    </div>
                @else
                    <p class="text-muted small mb-0">No publication is currently linked to this commentary.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
