@extends('admin.layouts.admin')

@section('title', 'Revisions: ' . $post->title . ' | XIVIG Maison Gazette')
@section('page_title', 'Editorial Revisions & Rollback')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Bar -->
    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-light">
        <div>
            <h4 class="fw-bold mb-1 text-white">Chronicle Snapshots: <span class="text-gold">{{ $post->title }}</span></h4>
            <p class="text-muted small mb-0">Browse editorial history, inspect snapshots, and restore content to previous milestones.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-outline-light rounded-pill px-3 py-2 small">
                &larr; Back to Editor
            </a>
            <a href="{{ route('admin.posts.preview', $post->id) }}" target="_blank" class="btn btn-dark rounded-pill px-3 py-2 border small">
                Live Preview &nearr;
            </a>
        </div>
    </div>

    <!-- Active Article State Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: rgba(201, 169, 110, 0.05); border: 1px solid rgba(201, 169, 110, 0.2) !important;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <span class="badge bg-gold text-dark text-uppercase px-3 py-1 mb-2 fw-bold">Current Live State</span>
                <h5 class="fw-bold text-white mb-1">{{ $post->title }}</h5>
                <div class="text-muted small">
                    <span>Status: <strong class="text-white text-capitalize">{{ $post->status }}</strong></span>
                    <span class="mx-2">•</span>
                    <span>Last Updated: <strong class="text-white">{{ $post->updated_at ? $post->updated_at->format('M d, Y H:i:s') : 'N/A' }}</strong></span>
                    <span class="mx-2">•</span>
                    <span>Approx. {{ str_word_count(strip_tags($post->content)) }} words</span>
                </div>
            </div>
            <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-gold rounded-pill px-4 py-2 fw-semibold">
                Edit Current Version
            </a>
        </div>
    </div>

    <!-- Revisions Archive List -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #141a1f; border: 1px solid rgba(255,255,255,0.08) !important;">
        <div class="card-header bg-transparent py-3 px-4 border-bottom border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
            <span class="fw-bold text-white"><i class="fas fa-history me-2 text-gold"></i>Revision Archives ({{ $revisions->count() }})</span>
            <span class="text-muted small">Snapshots are captured automatically upon content revisions.</span>
        </div>

        <div class="card-body p-0">
            @if($revisions->isEmpty())
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-archive fa-3x mb-3 text-secondary opacity-50"></i>
                    <h6 class="text-white fw-semibold">No historical revisions recorded yet.</h6>
                    <p class="small mb-0">Whenever an editor updates this chronicle's content, a prior rollback snapshot is automatically archived here.</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($revisions as $index => $rev)
                        <div class="list-group-item bg-transparent border-bottom border-secondary border-opacity-25 p-4 text-white">
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-secondary bg-opacity-25 text-gold border border-gold border-opacity-25 px-2 py-1 small">
                                            Snapshot #{{ $rev->id }}
                                        </span>
                                        <span class="text-white fw-bold">{{ $rev->title ?: $post->title }}</span>
                                        @if($index === 0)
                                            <span class="badge bg-info bg-opacity-25 text-info px-2 py-1 small">Most Recent Archive</span>
                                        @endif
                                    </div>

                                    <div class="text-muted small d-flex flex-wrap gap-3 align-items-center mb-3">
                                        <span><i class="far fa-clock me-1 text-gold"></i>{{ $rev->created_at ? $rev->created_at->format('F d, Y \a\t H:i:s') : 'Historical' }}</span>
                                        <span><i class="far fa-user me-1 text-gold"></i>{{ $rev->user ? $rev->user->name : 'System / Editorial' }}</span>
                                        <span><i class="fas fa-pen-nib me-1 text-gold"></i>Approx. {{ str_word_count(strip_tags($rev->content ?? '')) }} words</span>
                                        @if($rev->summary)
                                            <span class="badge bg-dark border text-muted">{{ $rev->summary }}</span>
                                        @endif
                                    </div>

                                    <!-- Collapsible Preview -->
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 text-light" type="button" data-bs-toggle="collapse" data-bs-target="#revCollapse{{ $rev->id }}">
                                        <i class="fas fa-eye me-1"></i>Toggle Content Snapshot
                                    </button>

                                    <div class="collapse mt-3" id="revCollapse{{ $rev->id }}">
                                        <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.08); max-height: 280px; overflow-y: auto;">
                                            <h6 class="text-gold fw-bold mb-2">{{ $rev->title }}</h6>
                                            <div class="small text-light opacity-75">
                                                {!! $rev->content !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <form action="{{ route('admin.posts.revisions.restore', ['post' => $post->id, 'revision' => $rev->id]) }}" method="POST" onsubmit="return confirm('Rollback chronicle to this snapshot from {{ $rev->created_at ? $rev->created_at->format('M d, Y H:i') : 'historical' }}? Current version will be preserved as a snapshot.');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-gold rounded-pill px-3 py-2 small fw-semibold">
                                            <i class="fas fa-undo-alt me-1"></i>Rollback to this Snapshot
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
