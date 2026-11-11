@extends('admin.layouts.admin')

@section('title', 'Add Manual Commentary')
@section('page_title', 'New Discourse Commentary')
@section('parentTitle', 'Discourse & Comments')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 bg-card">
            <div class="card-header bg-transparent py-4 px-4 px-md-5 border-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="fw-black text-body mb-1">Add Editorial Commentary</h4>
                    <p class="text-muted small mb-0">Manually post an accredited insight or remark on a publication</p>
                </div>
                <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back to Remarks
                </a>
            </div>

            <div class="card-body p-4 p-md-5 pt-0">
                <form action="{{ route('admin.comments.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <!-- Select Article -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Target Chronicle / Article <span class="text-danger">*</span></label>
                            <select name="post_id" class="form-select @error('post_id') is-invalid @enderror" required>
                                <option value="">Select Article</option>
                                @foreach($posts as $post)
                                    <option value="{{ $post->id }}" {{ old('post_id') == $post->id ? 'selected' : '' }}>
                                        {{ Str::limit($post->title, 50) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('post_id')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Post As User -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Author Identity <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (old('user_id', auth()->id()) == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ ucfirst($user->role) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Comment Content -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase letter-spacing-1">Commentary Content <span class="text-danger">*</span></label>
                            <textarea name="comment" class="form-control @error('comment') is-invalid @enderror" rows="5" placeholder="Write the editorial remark or reader discourse here..." required>{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-12 border-top pt-4 mt-4 d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.comments.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-gold rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="bi bi-send-check me-1"></i> Publish Commentary
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
