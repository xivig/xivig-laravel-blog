@extends('admin.layouts.admin')

@section('title', 'Discourse & Community Commentary')
@section('page_title', 'Discourse & Comments Moderation')
@section('parentTitle', 'Desk & Communications')

@section('content')

<!-- Metric Overview Cards -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 h-100 bg-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">Total Remarks</p>
                    <h3 class="fw-black mb-0 text-body">{{ $totalCount ?? $allActivity->total() }}</h3>
                </div>
                <div class="icon-shape rounded-3 p-3 bg-primary-subtle text-primary">
                    <i class="bi bi-chat-left-text-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 h-100 bg-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">Approved Public</p>
                    <h3 class="fw-black mb-0 text-success">{{ $approvedCount ?? 0 }}</h3>
                </div>
                <div class="icon-shape rounded-3 p-3 bg-success-subtle text-success">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 h-100 bg-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">Pending Review</p>
                    <h3 class="fw-black mb-0 text-warning">{{ $pendingCount ?? 0 }}</h3>
                </div>
                <div class="icon-shape rounded-3 p-3 bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 h-100 bg-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">In Trash</p>
                    <h3 class="fw-black mb-0 text-danger">{{ $trashedCount ?? 0 }}</h3>
                </div>
                <div class="icon-shape rounded-3 p-3 bg-danger-subtle text-danger">
                    <i class="bi bi-trash3-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Elite Navigation Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <!-- Active / Trash Filter Tabs -->
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="nav-pills-elite p-1 bg-body-tertiary shadow-sm rounded-pill d-inline-flex border">
            <a href="{{ route('admin.comments.index') }}" class="nav-link px-4 py-2 rounded-pill small fw-bold {{ request('status') !== 'trash' ? 'active bg-dark text-white' : 'text-body-secondary' }}">
                <i class="bi bi-chat-quote me-1"></i> Active Discourse
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'trash']) }}" class="nav-link px-4 py-2 rounded-pill small fw-bold {{ request('status') === 'trash' ? 'active bg-danger text-white' : 'text-body-secondary' }}">
                <i class="bi bi-trash3 me-1"></i> Trash Vault
                @if(($trashedCount ?? 0) > 0)
                    <span class="badge rounded-pill bg-danger-subtle text-danger ms-1 px-2" style="font-size: 0.7rem;">{{ $trashedCount }}</span>
                @endif
            </a>
        </div>

        @if(request('status') !== 'trash')
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle px-3 py-2 fw-semibold" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-funnel me-1"></i> {{ request('status') ? ucfirst(request('status')) : 'All Statuses' }}
            </button>
            <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                <li><a class="dropdown-item {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">All Statuses</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item {{ request('status') === 'approved' ? 'active' : '' }}" href="{{ route('admin.comments.index', ['status' => 'approved']) }}"><i class="bi bi-check-circle-fill text-success me-2"></i> Approved</a></li>
                <li><a class="dropdown-item {{ request('status') === 'pending' ? 'active' : '' }}" href="{{ route('admin.comments.index', ['status' => 'pending']) }}"><i class="bi bi-hourglass-split text-warning me-2"></i> Pending</a></li>
                <li><a class="dropdown-item {{ request('status') === 'spam' ? 'active' : '' }}" href="{{ route('admin.comments.index', ['status' => 'spam']) }}"><i class="bi bi-bug-fill text-danger me-2"></i> Spam</a></li>
            </ul>
        </div>
        @endif
    </div>

    <!-- Search & Add Actions -->
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <form method="GET" action="{{ route('admin.comments.index') }}" class="d-flex align-items-center">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control rounded-start-pill px-3 py-2 bg-body" placeholder="Search author, content..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary rounded-end-pill px-3" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            @if(request('search'))
                <a href="{{ route('admin.comments.index', request()->except('search', 'page')) }}" class="btn btn-sm btn-link text-muted ms-1" title="Clear Search">
                    <i class="bi bi-x-circle-fill"></i>
                </a>
            @endif
        </form>

        <a href="{{ route('admin.comments.create') }}" class="btn btn-gold btn-sm rounded-pill px-4 py-2 fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Manual Comment
        </a>
    </div>
</div>

<!-- Bulk Action Sticky Toolbar -->
<div id="bulk-toolbar" class="card border-0 shadow-lg rounded-pill px-4 py-2 bg-dark text-white mb-4 d-none" style="position: sticky; top: 80px; z-index: 1020; border: 1px solid rgba(201, 169, 110, 0.35) !important;">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-warning text-dark rounded-pill fw-bold" id="selected-count">0</span>
            <span class="small fw-bold">Items Selected</span>
            <div class="form-check ms-3">
                <input class="form-check-input" type="checkbox" id="select-all">
                <label class="form-check-label small fw-bold" for="select-all">Select All</label>
            </div>
        </div>
        <div class="d-flex gap-3 align-items-center">
            @if(request('status') === 'trash')
                <button type="button" class="btn btn-link text-success p-0 fw-bold small text-decoration-none" onclick="runBulk('restore')">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                </button>
                <div class="vr bg-white opacity-25"></div>
                <button type="button" class="btn btn-link text-danger p-0 fw-bold small text-decoration-none" onclick="runBulk('force_delete')">
                    <i class="bi bi-x-circle me-1"></i> Permanent Delete
                </button>
            @else
                <button type="button" class="btn btn-link text-success p-0 fw-bold small text-decoration-none" onclick="runBulk('approve')">
                    <i class="bi bi-check-lg me-1"></i> Approve
                </button>
                <div class="vr bg-white opacity-25"></div>
                <button type="button" class="btn btn-link text-warning p-0 fw-bold small text-decoration-none" onclick="runBulk('spam')">
                    <i class="bi bi-bug me-1"></i> Mark Spam
                </button>
                <div class="vr bg-white opacity-25"></div>
                <button type="button" class="btn btn-link text-danger p-0 fw-bold small text-decoration-none" onclick="runBulk('delete')">
                    <i class="bi bi-trash3 me-1"></i> Move to Trash
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Hidden Bulk Action Form -->
<form id="bulk-action-form" action="{{ route('admin.comments.bulk') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input">
    <div id="bulk-ids-container"></div>
</form>

<!-- Main Activity Stream Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-card">
    <div class="card-body p-0">
        <div class="notification-list">
            @forelse($allActivity as $activity)
            @php 
                $isReply = isset($activity->comment_id) || (!empty($activity->parent_id));
                $user = $activity->user;
                $post = $isReply ? ($activity->comment->post ?? ($activity->post ?? null)) : $activity->post;
                $authorName = !empty($activity->author_name) ? $activity->author_name : ($user->name ?? 'Guest Reader');
                $authorEmail = !empty($activity->author_email) ? $activity->author_email : ($user->email ?? 'unregistered');
                $contentText = $isReply ? ($activity->reply ?? $activity->content) : ($activity->comment ?? $activity->content);
                $initial = strtoupper(substr($authorName, 0, 1));
            @endphp
            <div class="notification-item {{ $isReply ? 'bg-body-secondary bg-opacity-25' : '' }}">
                <div class="form-check me-3 align-self-start mt-1">
                    <input class="form-check-input row-checkbox" type="checkbox" value="{{ $activity->id }}">
                </div>

                <div class="avatar {{ $isReply ? 'bg-info-subtle text-info' : 'bg-primary-subtle text-primary' }} fw-black rounded-3 me-3 flex-shrink-0 shadow-xs" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <span>{{ $initial }}</span>
                </div>

                <div class="notification-content flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                        <div>
                            <strong class="text-body">{{ $authorName }}</strong>
                            <span class="text-muted extra-small ms-1">({{ $authorEmail }})</span>
                            <span class="text-muted extra-small ms-1">{{ $isReply ? 'replied on' : 'commented on' }}</span>
                            @if($post)
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-gold fw-bold text-decoration-none small hover-underline">
                                    "{{ Str::limit($post->title, 55) }}"
                                </a>
                            @else
                                <span class="text-muted small fst-italic">Archived Post</span>
                            @endif
                        </div>
                    </div>

                    <p class="mb-2 text-body-secondary small" style="line-height: 1.6; white-space: normal;">
                        {{ $contentText }}
                    </p>

                    <div class="notification-time d-flex flex-wrap align-items-center gap-2">
                        <span class="extra-small text-muted">
                            <i class="bi bi-clock me-1"></i> {{ $activity->created_at ? $activity->created_at->diffForHumans() : '—' }}
                        </span>
                        <span class="text-muted opacity-25">&bull;</span>
                        <span class="badge bg-body-tertiary text-body border extra-small px-2 py-0.5 rounded-pill font-monospace" style="font-size: 0.65rem;">
                            {{ $isReply ? 'REPLY' : 'COMMENT' }}
                        </span>

                        @if(!$isReply)
                            @switch($activity->status)
                                @case('approved')
                                    <span class="badge bg-success-subtle text-success extra-small px-2.5 py-0.5 rounded-pill fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Approved
                                    </span>
                                    @break
                                @case('spam')
                                    <span class="badge bg-danger-subtle text-danger extra-small px-2.5 py-0.5 rounded-pill fw-bold">
                                        <i class="bi bi-bug-fill me-1"></i> Spam
                                    </span>
                                    @break
                                @default
                                    <span class="badge bg-warning-subtle text-warning extra-small px-2.5 py-0.5 rounded-pill fw-bold">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending
                                    </span>
                            @endswitch
                        @endif

                        @if($activity->trashed())
                            <span class="badge bg-danger text-white extra-small px-2 py-0.5 rounded-pill">
                                <i class="bi bi-trash3-fill me-1"></i> In Trash
                            </span>
                        @endif
                    </div>
                </div>

                @if($post && ($post->image || $post->featured_image))
                <div class="ms-3 flex-shrink-0 d-none d-lg-block">
                    @php
                        $thumb = $post->image->link ?? ($post->featured_image ? (str_starts_with($post->featured_image, 'http') ? $post->featured_image : 'storage/' . ltrim($post->featured_image, '/')) : null);
                    @endphp
                    @if($thumb)
                        <img src="{{ asset($thumb) }}" class="rounded-3 shadow-xs border" width="60" height="42" style="object-fit: cover;" alt="Post Thumbnail">
                    @endif
                </div>
                @endif

                <div class="action-buttons d-flex gap-1 ms-3 align-self-center">
                    @if($activity->trashed())
                        <form action="{{ $isReply ? route('admin.comments.reply.restore', $activity->id) : route('admin.comments.restore', $activity->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm rounded-circle shadow-xs" title="Restore Entry" style="width: 32px; height: 32px; padding: 0;">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </form>
                        <form action="{{ $isReply ? route('admin.comments.reply.forceDelete', $activity->id) : route('admin.comments.forceDelete', $activity->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle shadow-xs delete-confirm" data-title="Delete Permanently?" data-message="Permanently remove this discourse entry?" style="width: 32px; height: 32px; padding: 0;">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                    @else
                        @if(!$isReply)
                            @if($activity->status !== 'approved')
                                <form action="{{ route('admin.comments.approve', $activity->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm rounded-circle shadow-xs" title="Approve Comment" style="width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            @endif
                            @if($activity->status !== 'spam')
                                <form action="{{ route('admin.comments.spam', $activity->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning btn-sm rounded-circle shadow-xs" title="Mark as Spam" style="width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-bug"></i>
                                    </button>
                                </form>
                            @endif
                        @endif

                        <a href="{{ $isReply ? route('admin.comments.show', $activity->comment_id ?? $activity->parent_id) : route('admin.comments.show', $activity->id) }}" 
                           class="btn btn-outline-info btn-sm rounded-circle shadow-xs" title="View Full Thread" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; padding: 0;">
                            <i class="bi bi-eye"></i>
                        </a>

                        <a href="{{ $isReply ? route('admin.comments.reply.edit', $activity->id) : route('admin.comments.edit', $activity->id) }}" 
                           class="btn btn-outline-primary btn-sm rounded-circle shadow-xs" title="Edit Content" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; padding: 0;">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ $isReply ? route('admin.comments.reply.destroy', $activity->id) : route('admin.comments.destroy', $activity->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle shadow-xs delete-confirm" data-title="Move to Trash?" data-message="Move this discourse item to the trash vault?" style="width: 32px; height: 32px; padding: 0;">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-chat-left-dots display-1 text-muted opacity-25"></i>
                <h5 class="fw-bold mt-3 mb-1 text-body">No Community Remarks Found</h5>
                <p class="small text-muted mb-3">No comments or replies matched the specified criteria.</p>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                    </a>
                @else
                    <a href="{{ route('admin.comments.create') }}" class="btn btn-gold btn-sm rounded-pill px-4">
                        <i class="bi bi-plus-lg me-1"></i> Add First Comment
                    </a>
                @endif
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination Footer -->
    <div class="px-4 py-3 border-top bg-body-tertiary d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="small text-muted">
            Showing {{ $allActivity->firstItem() ?? 0 }} to {{ $allActivity->lastItem() ?? 0 }} of {{ $allActivity->total() }} entries
        </div>
        <div>
            {{ $allActivity->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    window.NotificationService = window.NotificationService || {
        confirm: function(title, message, btnText) {
            if (typeof Swal !== 'undefined') {
                return Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#c9a96e',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: btnText || 'Yes, proceed',
                    customClass: { popup: 'elite-swal-popup' }
                });
            }
            return Promise.resolve({ isConfirmed: window.confirm(message || title) });
        }
    };

    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const toolbar = document.getElementById('bulk-toolbar');
    const countSpan = document.getElementById('selected-count');

    const updateToolbar = () => {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        if (countSpan) countSpan.innerText = checkedCount;
        if (toolbar) {
            if (checkedCount > 0) {
                toolbar.classList.remove('d-none');
            } else {
                toolbar.classList.add('d-none');
                if (selectAll) selectAll.checked = false;
            }
        }
    };

    selectAll?.addEventListener('change', () => {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateToolbar();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateToolbar);
    });

    window.runBulk = (action) => {
        const form = document.getElementById('bulk-action-form');
        const actionInput = document.getElementById('bulk-action-input');
        const idsContainer = document.getElementById('bulk-ids-container');

        idsContainer.innerHTML = '';
        document.querySelectorAll('.row-checkbox:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            idsContainer.appendChild(input);
        });

        let message = "Perform this action on selected discourse entries?";
        if (action === 'force_delete') message = "PERMANENTLY eradicate selected entries? This is irreversible.";

        window.NotificationService.confirm("Bulk Action", message, "Proceed")
            .then(res => {
                if (res.isConfirmed) {
                    actionInput.value = action;
                    form.submit();
                }
            });
    };
});
</script>

<style>
.notification-item {
    display: flex;
    align-items: flex-start;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.notification-item:hover {
    background: rgba(201, 169, 110, 0.05);
}
[data-bs-theme="light"] .notification-item {
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}
[data-bs-theme="light"] .notification-item:hover {
    background: rgba(201, 169, 110, 0.08);
}
.hover-underline:hover {
    text-decoration: underline !important;
}
.row-checkbox { cursor: pointer; transform: scale(1.1); }
</style>

@endsection
