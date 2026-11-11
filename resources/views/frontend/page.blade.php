@extends('frontend.layouts.app')

@section('title', ($page->meta_title ?: $page->title) . ' | ' . $globalSettings['title'])
@section('meta_description', $page->meta_description ?: Str::limit(strip_tags($page->content), 160))
@section('meta_keywords', $page->meta_keywords)
@section('canonical_url', route('page.show', $page->slug))

@section('structured_data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $page->title,
    'description' => $page->meta_description ?: Str::limit(strip_tags($page->content), 160),
    'url' => route('page.show', $page->slug),
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
            <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">{{ $page->title }}</li>
        </ol>
    </nav>

    <!-- Page Header Hero -->
    <div class="page-hero-header text-center max-w-750 mx-auto mb-5">
        <span class="badge bg-gold text-dark text-uppercase letter-spacing-1 px-3 py-1 rounded-pill mb-3 fw-bold">
            Maison Gazette
        </span>
        <h1 class="display-4 fw-bold mb-3">{{ $page->title }}</h1>
        <div class="d-none d-md-block mx-auto border-bottom border-subtle max-w-500 pt-2"></div>
    </div>

    <!-- Page Body Card -->
    <div class="bento-card max-w-850 mx-auto p-4 p-md-5">
        <div class="article-body-content">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection
