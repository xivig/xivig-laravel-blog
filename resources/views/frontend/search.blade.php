@extends('frontend.layouts.app')

@section('title', 'Search Dispatches: ' . $q . ' | ' . $globalSettings['title'])

@section('content')
<div class="container py-4 py-md-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb extra-small text-uppercase letter-spacing-1 align-items-center mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('blog.index') }}" class="text-decoration-none text-secondary hover-gold d-inline-flex align-items-center gap-1">
                    <i class="bi bi-house-door"></i> Chronicles
                </a>
            </li>
            <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">Search Archive</li>
        </ol>
    </nav>

    <!-- Search Hero Header -->
    <div class="text-center max-w-750 mx-auto mb-5">
        <span class="badge bg-gold text-dark text-uppercase letter-spacing-1 px-3 py-1 rounded-pill mb-3 fw-bold">
            Gazette Archive
        </span>
        <h1 class="display-5 fw-bold mb-3">&ldquo;{{ $q }}&rdquo;</h1>
        <p class="text-secondary small mb-4">
            @if(strlen($q) >= 2)
                Found {{ $posts->total() }} matching chronicle(s) across the Maison Gazette archives.
            @else
                Please enter at least 2 characters to discover curated chronicles.
            @endif
        </p>

        <!-- Inline Capsule Search Form -->
        <form action="{{ route('blog.search') }}" method="GET" class="d-flex gap-2 max-w-500 mx-auto">
            <input type="text" name="q" value="{{ $q }}" required class="form-control rounded-pill px-4 shadow-none bg-surface border-subtle text-body py-2" placeholder="Search chronicles, designers, watches...">
            <button type="submit" class="btn btn-gold rounded-pill px-4 fw-bold">Search</button>
        </form>
    </div>

    @if(strlen($q) >= 2)
    <div class="row g-4 mb-5">
        @forelse($posts as $post)
        <div class="col-md-6 col-lg-4">
            <article class="bento-card h-100">
                <div class="bento-img-wrap position-relative">
                    <div class="ratio ratio-16x9">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                        @else
                            <div class="w-100 h-100 bg-secondary-subtle d-flex align-items-center justify-content-center text-muted">
                                <i class="bi bi-image fs-2 opacity-50"></i>
                            </div>
                        @endif
                    </div>
                    @if($post->category)
                        <a href="{{ route('blog.category', $post->category->slug) }}" class="floating-category-pill">
                            {{ $post->category->name }}
                        </a>
                    @endif
                </div>
                <div class="p-4 d-flex flex-column justify-content-between flex-grow-1">
                    <div>
                        <div class="d-flex align-items-center gap-2 extra-small text-secondary mb-2">
                            <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : '' }}</span>
                            <span>•</span>
                            <span><i class="bi bi-clock me-1 text-gold"></i>{{ $post->reading_time }} min read</span>
                        </div>
                        <h5 class="fw-bold mb-2">
                            <a href="{{ route('blog.show', $post->slug) }}" class="text-body text-decoration-none hover-gold">{{ $post->title }}</a>
                        </h5>
                        <p class="text-secondary small mb-3">{{ Str::limit($post->excerpt, 110) }}</p>
                    </div>
                    <div class="pt-3 border-top border-subtle d-flex justify-content-between extra-small text-secondary">
                        <span class="fw-semibold text-body">{{ $post->category ? $post->category->name : 'Chronicle' }}</span>
                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-eye text-gold"></i> {{ number_format($post->views_count) }}
                        </span>
                    </div>
                </div>
            </article>
        </div>
        @empty
        <div class="col-12 py-5 text-center text-secondary">
            <i class="bi bi-journal-x display-3 text-gold opacity-50 mb-3 d-block"></i>
            <h4 class="fw-bold mb-2">No chronicles match your query &ldquo;{{ $q }}&rdquo;.</h4>
            <p class="small text-muted mb-4">Try searching for alternative terms like "couture", "horology", or "florentine".</p>
            <a href="{{ route('blog.index') }}" class="btn btn-gold rounded-pill px-4">Browse All Chronicles</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
