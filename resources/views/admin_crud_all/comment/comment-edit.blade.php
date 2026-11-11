@extends('admin.layouts.admin')

@section('title', 'Edit Commentary #' . $comment->id)
@section('page_title', 'Edit Discourse Remark')
@section('parentTitle', 'Discourse & Comments')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 bg-card">
            <div class="card-header bg-transparent py-4 px-4 px-md-5 border-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="fw-black text-body mb-1">Edit Commentary</h4>
                    <p class="text-muted small mb-0">Moderate or refine the content of this reader remark</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.comments.show', $comment->id) }}" class="btn btn-outline-info btn-sm rounded-pill px-3">
                        <i class="bi bi-eye me-1"></i> View Thread
                    </a>
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body p-4 p-md-5 pt-0">
                <form action="{{ route('admin.comments.update', $comment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <!-- Post Reference -->
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Associated Publication <span class="text-danger">*</span></label>
                            <select name="post_id" class="form-select @error('post_id') is-invalid @enderror" required>
                                @foreach($posts as $post)
                                    <option value="{{ $post->id }}" {{ (old('post_id', $comment->post_id) == $post->id) ? 'selected' : '' }}>
                                        {{ Str::limit($post->title, 55) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('post_id')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Moderation Status -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Moderation Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="approved" {{ old('status', $comment->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending" {{ old('status', $comment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="spam" {{ old('status', $comment->status) == 'spam' ? 'selected' : '' }}>Spam</option>
                            </select>
                            @error('status')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Author Telemetry Preview -->
                        <div class="col-12">
                            <div class="p-3 bg-body-tertiary rounded-3 d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    {{ strtoupper(substr($comment->author_name ?? 'G', 0, 1)) }}
                                </div>
                                <div class="small">
                                    <div class="fw-bold text-body">{{ $comment->author_name ?? 'Guest Reader' }}</div>
                                    <div class="text-muted extra-small">{{ $comment->author_email ?? 'No email' }} &bull; Submitted {{ $comment->created_at ? $comment->created_at->format('M d, Y h:i A') : '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Comment Content -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Commentary Body <span class="text-danger">*</span></label>
                            <textarea name="comment" class="form-control @error('comment') is-invalid @enderror" rows="5" required>{{ old('comment', $comment->comment ?? $comment->content) }}</textarea>
                            @error('comment')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="col-12 border-top pt-4 mt-4 d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-gold rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="bi bi-save2 me-1"></i> Update Commentary
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
