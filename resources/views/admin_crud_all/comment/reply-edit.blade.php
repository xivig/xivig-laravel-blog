@extends('admin.layouts.admin')

@section('title', 'Edit Staff Reply')
@section('page_title', 'Edit Thread Reply')
@section('parentTitle', 'Discourse & Comments')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 bg-card">
            <div class="card-header bg-transparent py-4 px-4 px-md-5 border-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="fw-black text-body mb-1">Edit Thread Reply</h4>
                    <p class="text-muted small mb-0">Refine the discourse response to the reader's commentary</p>
                </div>
                <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back to Remarks
                </a>
            </div>

            <div class="card-body p-4 p-md-5 pt-0">
                <form action="{{ route('admin.comments.reply.update', $reply->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <!-- Original Comment Preview -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Parent Commentary</label>
                            @php
                                $parentComment = $reply->comment ?? $reply->parent;
                                $parentAuthor = $parentComment->user->name ?? ($parentComment->author_name ?? 'Comment Author');
                                $parentText = $parentComment->comment ?? ($parentComment->content ?? 'Parent comment text');
                            @endphp
                            <div class="p-3 bg-body-tertiary rounded-3 mb-2 small border border-opacity-10">
                                <div class="fw-bold text-gold mb-1">
                                    <i class="bi bi-chat-left-quote-fill me-1"></i> {{ $parentAuthor }}:
                                </div>
                                <div class="text-body-secondary fst-italic">
                                    "{{ Str::limit($parentText, 250) }}"
                                </div>
                            </div>
                        </div>

                        <!-- Reply Content -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Reply Content <span class="text-danger">*</span></label>
                            <textarea name="reply" class="form-control @error('reply') is-invalid @enderror" rows="5" required>{{ old('reply', $reply->reply ?? $reply->content) }}</textarea>
                            @error('reply')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 border-top pt-4 mt-4 d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-gold rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> Update Reply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
