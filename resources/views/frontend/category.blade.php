@extends('frontend.layouts.app')

@section('title', $category->name . ' Chronicles | ' . $globalSettings['title'])
@section('meta_description', $category->description ?: 'Explore curated chronicles on ' . $category->name)
@section('canonical_url', route('blog.category', $category->slug))

@section('structured_data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Chronicles',
            'item' => route('blog.index'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $category->name,
            'item' => route('blog.category', $category->slug),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
</script>
@endsection

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
            <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Category Header Hero -->
    <div class="category-hero-header text-center max-w-750 mx-auto mb-5">
        <span class="badge bg-gold text-dark text-uppercase letter-spacing-1 px-3 py-2 rounded-pill mb-3 fw-bold">
            Category Department
        </span>
        <h1 class="display-4 fw-bold mb-3">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-secondary fs-5 mb-0">{{ $category->description }}</p>
        @else
            <p class="text-secondary fs-5 mb-0">Curated dispatches and editorial analysis on {{ $category->name }}.</p>
        @endif
        <div class="d-flex align-items-center justify-content-center gap-2 mt-3 text-gold extra-small text-uppercase letter-spacing-1 fw-bold">
            <span>{{ $posts->total() }} Published Chronicles</span>
        </div>
    </div>

    <!-- Chronicles Grid -->
    <div class="row g-4 mb-5">
        @forelse($posts as $post)
        <div class="col-md-6 col-lg-4">
            <article class="bento-card h-100">
                <div class="bento-img-wrap ratio ratio-16x9">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                    @else
                        <div class="w-100 h-100 bg-secondary-subtle d-flex align-items-center justify-content-center text-muted">
                            <i class="bi bi-image fs-2 opacity-50"></i>
                        </div>
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
                    <div class="pt-3 border-top border-subtle d-flex justify-content-between align-items-center extra-small text-secondary">
                        <span class="fw-semibold text-body">By {{ $post->author ? $post->author->name : 'Staff' }}</span>
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
            <h4 class="fw-bold mb-2">No published chronicles found under this category.</h4>
            <a href="{{ route('blog.index') }}" class="btn btn-gold rounded-pill mt-3 px-4">Browse All Chronicles</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
</div>
@endsection
