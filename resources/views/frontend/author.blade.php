@extends('frontend.layouts.app')

@section('title', $author->name . ' - Editorial Portfolio | ' . $globalSettings['title'])
@section('meta_description', Str::limit($author->bio ?: 'Chronicles of fine horology, haute couture, and artisan crafts by ' . $author->name . ' for Maison XIVIG.', 160))
@section('canonical_url', route('blog.author', $author->slug))

@section('structured_data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => $author->name,
    'jobTitle' => $author->designation ?: 'Editorial Chronicler',
    'description' => $author->bio,
    'image' => $author->avatar_url ?: asset('assets/images/branding/logo-CDpcaf-E.png'),
    'url' => route('blog.author', $author->slug),
    'sameAs' => array_values(array_filter([
        $author->twitter,
        $author->linkedin,
        $author->instagram,
    ])),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
</script>
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
            'name' => $author->name,
            'item' => route('blog.author', $author->slug),
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
            <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">{{ $author->name }}</li>
        </ol>
    </nav>

    <!-- Author Header Hero Profile Card -->
    <div class="hero-cover-card p-4 p-md-5 mb-5">
        <div class="row align-items-center">
            <div class="col-md-3 text-center mb-4 mb-md-0">
                @if($author->avatar_url)
                    <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}" class="rounded-circle shadow" width="140" height="140" style="object-fit:cover;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                    <div class="rounded-circle bg-gold text-dark fw-bold display-4 align-items-center justify-content-center shadow" style="width:140px;height:140px;display:none;">
                        {{ substr($author->name, 0, 1) }}
                    </div>
                @else
                    <div class="rounded-circle bg-gold text-dark fw-bold display-4 d-inline-flex align-items-center justify-content-center shadow" style="width:140px;height:140px;">
                        {{ substr($author->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="col-md-9 text-center text-md-start">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                    <span class="badge bg-gold text-dark text-uppercase letter-spacing-1 px-3 py-1 rounded-pill fw-bold">
                        Editorial Atelier
                    </span>
                    <span class="badge bg-gold-subtle text-gold rounded-pill px-3 py-1 extra-small fw-semibold">
                        {{ $posts->total() }} Published Works
                    </span>
                </div>
                <h1 class="display-5 fw-bold mb-1">{{ $author->name }}</h1>
                <h6 class="text-gold fw-semibold mb-3">{{ $author->designation ?: 'Editorial Critic & Chronicler' }}</h6>
                <p class="text-secondary max-w-750 mb-4">{{ $author->bio ?: 'Chronicler of fine horology, haute couture, and artisan crafts for the prestigious archives of Maison XIVIG.' }}</p>
                <div class="d-flex gap-3 justify-content-center justify-content-md-start fs-5 text-secondary">
                    @if($author->twitter)<a href="{{ $author->twitter }}" target="_blank" class="text-secondary hover-gold"><i class="bi bi-twitter-x"></i></a>@endif
                    @if($author->linkedin)<a href="{{ $author->linkedin }}" target="_blank" class="text-secondary hover-gold"><i class="bi bi-linkedin"></i></a>@endif
                    @if($author->instagram)<a href="{{ $author->instagram }}" target="_blank" class="text-secondary hover-gold"><i class="bi bi-instagram"></i></a>@endif
                </div>
            </div>
        </div>
    </div>

    <!-- Author Articles -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <span class="text-gold text-uppercase fw-bold letter-spacing-2 extra-small d-block mb-1">Author Portfolio</span>
            <h3 class="fw-bold mb-0">Published Chronicles by {{ $author->name }}</h3>
        </div>
        <div class="d-none d-md-block flex-grow-1 ms-4 border-bottom border-subtle"></div>
    </div>

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
                    <div class="pt-3 border-top border-subtle d-flex justify-content-between align-items-center extra-small text-secondary">
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
            <h4 class="fw-bold mb-2">No published chronicles from this author yet.</h4>
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
