@extends('frontend.layouts.app')

@section('title', $post->title . ' | ' . $globalSettings['title'])
@section('meta_description', $post->meta_description ?: $post->excerpt)
@section('meta_keywords', $post->meta_keywords)
@section('canonical_url', $post->canonical_url ?: route('blog.show', $post->slug))
@section('og_type', 'article')
@section('og_image', $post->featured_image ? asset('storage/' . $post->featured_image) : asset('assets/images/branding/logo-CDpcaf-E.png'))
@section('twitter_creator', $post->author && $post->author->twitter ? '@' . ltrim(basename($post->author->twitter), '@') : '@XIVIGMaison')
@if((isset($isPreview) && $isPreview) || $post->is_private || $post->status !== 'published' || ($post->published_at && $post->published_at->isFuture()))
@section('meta_robots', 'noindex, nofollow')
@endif

@section('structured_data')
<script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->title,
    'description' => $post->excerpt,
    'datePublished' => $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String(),
    'dateModified' => $post->updated_at->toIso8601String(),
    'author' => [
        '@type' => 'Person',
        'name' => $post->author ? $post->author->name : 'Maison XIVIG Editorial',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'Maison XIVIG',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset('assets/images/branding/logo-CDpcaf-E.png'),
        ],
    ],
    'mainEntityOfPage' => route('blog.show', $post->slug),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array_values(array_filter([
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Chronicles',
            'item' => route('blog.index'),
        ],
        $post->category ? [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $post->category->name,
            'item' => route('blog.category', $post->category->slug),
        ] : null,
        [
            '@type' => 'ListItem',
            'position' => $post->category ? 3 : 2,
            'name' => $post->title,
            'item' => route('blog.show', $post->slug),
        ],
    ])),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
</script>
@endsection

@section('content')
@if(isset($isPreview) && $isPreview)
<div class="preview-mode-banner bg-warning text-dark py-2 px-3 text-center fw-bold small sticky-top border-bottom border-dark shadow-sm" style="z-index: 1040;">
    <i class="bi bi-eye-fill me-2"></i> ATELIER PREVIEW MODE &mdash; Viewing {{ strtoupper($post->status) }} chronicle. Visible only to editorial staff.
    <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-dark btn-sm rounded-pill ms-3 py-0 px-3 extra-small">Return to Editor</a>
</div>
@endif
<!-- Ultramodern Floating Reading Utility Dock (Pinned Bottom Center) -->
<div class="reading-utility-dock d-none d-md-flex">
    <!-- Like Action -->
    <button type="button" class="dock-btn dock-btn-danger" id="dockLikeBtn" data-post-id="{{ $post->id }}" title="Appreciate Chronicle">
        <i class="bi bi-heart-fill text-danger" id="dockLikeHeartIcon"></i>
    </button>
    <span class="small fw-bold pe-2" id="dockLikeCounter">{{ $post->likes_count }}</span>

    <div class="dock-divider"></div>

    <!-- Reading Progress Indicator -->
    <div class="dock-progress-pill" id="dockProgressPill" title="Reading Progress">
        <span id="dockProgressText">0%</span> read
    </div>

    <div class="dock-divider"></div>

    <!-- Font Size Switcher -->
    <button type="button" class="dock-btn" onclick="toggleArticleFontSize()" title="Adjust Typography Scale (A-/A+)">
        <i class="bi bi-type"></i>
    </button>

    <!-- Quick Copy Link -->
    <button type="button" class="dock-btn" onclick="copyCurrentLink()" title="Copy Chronicle Link">
        <i class="bi bi-link-45deg"></i>
    </button>

    <!-- Jump to Discussion -->
    <a href="#commentsArea" class="dock-btn" title="Jump to Discussion">
        <i class="bi bi-chat-quote-fill"></i>
    </a>
</div>

<article class="article-detail-container py-4 py-md-5">
    <div class="container">
        <!-- Breadcrumbs Navigation -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb extra-small text-uppercase letter-spacing-1 align-items-center mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('blog.index') }}" class="text-decoration-none text-secondary hover-gold d-inline-flex align-items-center gap-1">
                        <i class="bi bi-house-door"></i> Chronicles
                    </a>
                </li>
                @if($post->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('blog.category', $post->category->slug) }}" class="text-decoration-none text-secondary hover-gold">
                            {{ $post->category->name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">
                    {{ Str::limit($post->title, 38) }}
                </li>
            </ol>
        </nav>

        <!-- Main 2-Column Grid Layout matching the user reference mockup -->
        <div class="row g-4 align-items-start">
            <!-- Left Main Column: Hero Card + Content Card -->
            <div class="col-lg-8">
                <!-- 1. The Article Hero Card (Top Card) -->
                <div class="article-hero-card mb-4">
                    <!-- Category Pill Badge & Status Badges -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        @if($post->category)
                            <a href="{{ route('blog.category', $post->category->slug) }}" class="article-category-pill" title="Explore {{ $post->category->name }}">
                                {{ $post->category->name }}
                            </a>
                        @endif
                        @if($post->is_featured)
                            <span class="badge bg-gold-subtle text-gold text-uppercase px-3 py-2 rounded-pill fw-bold" style="font-size: 0.72rem; letter-spacing: 0.06em;">
                                <i class="bi bi-star-fill me-1"></i> Featured Edition
                            </span>
                        @endif
                        @if($post->is_sticky)
                            <span class="badge bg-warning text-dark text-uppercase px-3 py-2 rounded-pill fw-bold" style="font-size: 0.72rem; letter-spacing: 0.06em;">
                                <i class="bi bi-pin-angle-fill me-1"></i> Pinned
                            </span>
                        @endif
                        @if($post->isPasswordProtected())
                            <span class="badge bg-secondary text-light text-uppercase px-3 py-2 rounded-pill fw-bold" style="font-size: 0.72rem; letter-spacing: 0.06em;">
                                <i class="bi bi-lock-fill me-1"></i> Codex Protected
                            </span>
                        @endif
                        @if($post->is_private)
                            <span class="badge bg-danger text-light text-uppercase px-3 py-2 rounded-pill fw-bold" style="font-size: 0.72rem; letter-spacing: 0.06em;">
                                <i class="bi bi-eye-slash-fill me-1"></i> Private Staff Only
                            </span>
                        @endif
                    </div>

                    <!-- Article Headline: Left-aligned bold display headline -->
                    <h1 class="article-card-title article-main-title">
                        {{ $post->title }}
                    </h1>

                    <!-- Author & Social Share Meta Row -->
                    <div class="article-meta-row">
                        <!-- Left: Author Info -->
                        <div class="article-author-cluster">
                            <a href="{{ $post->author ? route('blog.author', $post->author->slug) : '#' }}" class="text-decoration-none">
                                @if($post->author && $post->author->avatar_url)
                                    <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="article-author-avatar" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                    <div class="article-author-avatar-fallback" style="display:none;">
                                        {{ mb_substr($post->author->name, 0, 1, 'UTF-8') }}
                                    </div>
                                @else
                                    <div class="article-author-avatar-fallback">
                                        {{ mb_substr($post->author ? $post->author->name : 'X', 0, 1, 'UTF-8') }}
                                    </div>
                                @endif
                            </a>
                            <div>
                                <div class="article-author-name">
                                    <a href="{{ $post->author ? route('blog.author', $post->author->slug) : '#' }}" class="text-body text-decoration-none hover-primary">
                                        {{ $post->author ? $post->author->name : 'Maison Editorial' }}
                                    </a>
                                </div>
                                <div class="article-author-sub">
                                    {{ $post->author && $post->author->designation ? $post->author->designation : 'SENIOR HAUTE COUTURE CRITIC' }} &bull; {{ $post->published_at ? $post->published_at->format('M d, Y') : now()->format('M d, Y') }}
                                </div>
                            </div>
                        </div>

                        <!-- Right: Social Share Buttons & Like Heart Pill -->
                        <div class="d-flex align-items-center gap-2">
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" class="article-share-btn" title="Share on X">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" class="article-share-btn" title="Share on LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank" class="article-share-btn" title="Share on WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="article-share-btn d-none" title="Share on Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <button type="button" class="article-share-btn d-none" onclick="copyCurrentLink()" title="Copy Link">
                                <i class="bi bi-link-45deg"></i>
                            </button>

                            <div class="vr my-1 opacity-25"></div>

                            <button type="button" class="article-like-pill" id="likeArticleBtn" data-post-id="{{ $post->id }}" title="Appreciate Chronicle">
                                <i class="bi bi-heart text-danger" id="likeHeartIcon"></i>
                                <span id="likeCounterText">{{ $post->likes_count ?? 0 }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Featured Media Photography with docked caption bar -->
                    @if($post->featured_image)
                        <div class="article-hero-media-wrap shadow-sm">
                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                 alt="{{ $post->featured_image_alt }}" 
                                 class="article-hero-media-img"
                                 loading="eager">
                            @if($post->featured_image_caption)
                                <div class="article-hero-caption-bar">
                                    <i class="bi bi-camera-fill"></i>
                                    <span>{{ $post->featured_image_caption }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- 2. The Article Content Card -->
                <div class="article-content-card mb-4">
                    @if($post->excerpt)
                        <p class="article-standfirst article-lead-text text-muted mb-4 fs-5 lh-base">
                            {{ $post->excerpt }}
                        </p>
                    @endif

                    <!-- Rich Content Body -->
                    <div class="article-body-content mb-5" id="articleBodyContent">
                        {!! $post->content !!}
                    </div>

                    {{-- Contextual In-Article Maison Partner Dispatch --}}
                    @if(isset($inArticleAd) && $inArticleAd)
                        @include('frontend.partials.ad-slot', ['ad' => $inArticleAd, 'layout' => 'inline'])
                    @endif

                <!-- Tags / Lexicon -->
                @if($post->tags->count() > 0)
                <div class="article-tags-wrapper pt-4 pb-4 border-top border-subtle d-flex flex-wrap align-items-center gap-2">
                    <span class="text-gold fw-bold small text-uppercase me-2"><i class="bi bi-tags-fill me-1"></i> Lexicon:</span>
                    @foreach($post->tags as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}" class="popular-tag-pill">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
                @endif

                <!-- Unlimited Gallery Images Portfolio Showcase -->
                @if($post->galleryImages->count() > 0)
                <section class="post-gallery-section my-5 pt-4 border-top border-subtle">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-gold text-dark fw-bold text-uppercase" style="font-size: 0.65rem;">Visual Portfolio</span>
                            <h5 class="fw-bold mb-0">Atelier Visual Gallery</h5>
                        </div>
                        <span class="badge bg-gold-subtle text-gold rounded-pill extra-small px-3 py-1">{{ $post->galleryImages->count() }} Plates</span>
                    </div>
                    <div class="row g-3">
                        @foreach($post->galleryImages as $img)
                            <div class="col-sm-6 col-md-4">
                                <div class="gallery-card bento-card p-2 h-100">
                                    <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank" class="gallery-img-link d-block ratio ratio-4x3 overflow-hidden rounded-3 shadow-xs">
                                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $img->alt_text ?: $post->title }}" class="img-fluid object-fit-cover w-100 h-100">
                                    </a>
                                    @if($img->caption)
                                        <div class="gallery-caption extra-small text-muted fst-italic pt-2 text-center">
                                            {{ $img->caption }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- Interactive Appreciation & Social Sharing Bar -->
                <div class="interaction-bar bg-surface rounded-4 p-4 border border-subtle mb-5 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <!-- AJAX Like Button -->
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 py-2 d-flex align-items-center gap-2" id="likeArticleBtn" data-post-id="{{ $post->id }}">
                            <i class="bi bi-heart-fill fs-5" id="likeHeartIcon"></i>
                            <span class="fw-bold" id="likeCounterText">{{ $post->likes_count }}</span>
                            <span class="small d-none d-md-inline">Appreciations</span>
                        </button>
                        <span id="likeSuccessMsg" class="extra-small text-gold fw-semibold d-none">
                            <i class="bi bi-sparkles me-1"></i> Appreciated!
                        </span>
                    </div>

                    <!-- Social Share Suite -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-secondary small me-1">Share:</span>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" class="btn btn-sm btn-outline-editorial rounded-circle p-2" title="Share on X">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-editorial rounded-circle p-2" title="Share on Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-editorial rounded-circle p-2" title="Share on LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-editorial rounded-circle p-2" title="Share on WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-editorial rounded-pill px-3" onclick="copyCurrentLink()" title="Copy Link">
                            <i class="bi bi-link-45deg me-1"></i> <span id="copyLinkBtnText">Copy Link</span>
                        </button>
                    </div>
                </div>

                <!-- Author Bio Atelier Card -->
                @if($post->author)
                <div class="editorial-sidebar-card p-4 mb-5">
                    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4">
                        @if($post->author->avatar_url)
                            <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="rounded-circle shadow-sm" width="80" height="80" style="object-fit:cover;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                            <div class="rounded-circle bg-gold text-dark fw-bold display-6 align-items-center justify-content-center shadow-sm" style="width:80px;height:80px;display:none;">
                                {{ substr($post->author->name, 0, 1) }}
                            </div>
                        @else
                            <div class="rounded-circle bg-gold text-dark fw-bold display-6 d-flex align-items-center justify-content-center shadow-sm" style="width:80px;height:80px;">
                                {{ substr($post->author->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-grow-1 text-center text-sm-start">
                            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-between gap-2 mb-2">
                                <div>
                                    <h5 class="fw-bold mb-0">
                                        <a href="{{ route('blog.author', $post->author->slug) }}" class="text-body text-decoration-none hover-gold">{{ $post->author->name }}</a>
                                    </h5>
                                    <span class="text-gold extra-small text-uppercase letter-spacing-1 fw-bold">{{ $post->author->designation ?: 'Editorial Contributor' }}</span>
                                </div>
                                <a href="{{ route('blog.author', $post->author->slug) }}" class="btn btn-sm btn-outline-editorial rounded-pill px-3">
                                    All Articles &rarr;
                                </a>
                            </div>
                            <p class="text-secondary small mb-3">
                                {{ $post->author->bio ?: 'Chronicler of haute couture, luxury savoir-faire, and contemporary aesthetics for Maison XIVIG Gazette.' }}
                            </p>
                            <div class="d-flex gap-3 justify-content-center justify-content-sm-start text-secondary fs-6">
                                @if($post->author->twitter)<a href="{{ $post->author->twitter }}" target="_blank" class="text-secondary hover-gold"><i class="bi bi-twitter-x"></i></a>@endif
                                @if($post->author->linkedin)<a href="{{ $post->author->linkedin }}" target="_blank" class="text-secondary hover-gold"><i class="bi bi-linkedin"></i></a>@endif
                                @if($post->author->instagram)<a href="{{ $post->author->instagram }}" target="_blank" class="text-secondary hover-gold"><i class="bi bi-instagram"></i></a>@endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Next & Previous Story Navigation -->
                <div class="row g-3 mb-5 border-top border-bottom border-subtle py-4">
                    <div class="col-sm-6 text-start">
                        @if($prevPost)
                            <a href="{{ route('blog.show', $prevPost->slug) }}" class="text-decoration-none group d-block">
                                <span class="extra-small text-gold text-uppercase fw-bold letter-spacing-1 d-block mb-1">&larr; Previous Chronicle</span>
                                <h6 class="fw-bold text-body hover-gold mb-0">{{ Str::limit($prevPost->title, 45) }}</h6>
                            </a>
                        @endif
                    </div>
                    <div class="col-sm-6 text-end">
                        @if($nextPost)
                            <a href="{{ route('blog.show', $nextPost->slug) }}" class="text-decoration-none group d-block">
                                <span class="extra-small text-gold text-uppercase fw-bold letter-spacing-1 d-block mb-1">Next Chronicle &rarr;</span>
                                <h6 class="fw-bold text-body hover-gold mb-0">{{ Str::limit($nextPost->title, 45) }}</h6>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Comments & Discourse Salon -->
                <section class="comments-section mb-5" id="commentsArea">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="fw-bold mb-0">Discourse & Remarks ({{ $post->comments->where('status', 'approved')->count() }})</h4>
                        <span class="text-gold small fw-bold"><i class="bi bi-chat-quote-fill me-1"></i> Maison Salon</span>
                    </div>

                    @if(session('comment_success'))
                        <div class="alert alert-success rounded-4 p-3 mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <span>{{ session('comment_success') }}</span>
                        </div>
                    @endif

                    <!-- Commentary Submission Form -->
                    <div class="editorial-sidebar-card p-4 mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-gold text-dark fw-bold text-uppercase" style="font-size: 0.65rem;">Forum</span>
                            <h6 class="fw-bold mb-0 text-gold text-uppercase letter-spacing-1 small">Leave an Editorial Commentary</h6>
                        </div>

                        <form action="{{ route('blog.comments.store', $post->id) }}" method="POST">
                            @csrf
                            <!-- Anti-spam Honeypot -->
                            <div style="display:none !important;" aria-hidden="true">
                                <input type="text" name="website_url_check" tabindex="-1" autocomplete="off">
                            </div>

                            <input type="hidden" name="parent_id" id="commentParentId" value="">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Your Name *</label>
                                    <input type="text" name="author_name" required class="form-control form-control-sm bg-body border-subtle rounded-3 shadow-none py-2" placeholder="e.g. Laurent V.">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Email Address (Kept Private) *</label>
                                    <input type="email" name="author_email" required class="form-control form-control-sm bg-body border-subtle rounded-3 shadow-none py-2" placeholder="e.g. laurent@maison.com">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Commentary *</label>
                                <textarea name="content" rows="4" required class="form-control bg-body border-subtle rounded-3 shadow-none small" placeholder="Share your perspectives on this chronicle..."></textarea>
                            </div>
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                                <span class="extra-small text-muted">All commentary is reviewed under Maison ethical guidelines.</span>
                                <button type="submit" class="btn btn-gold rounded-pill px-4 fw-bold small">Submit Commentary</button>
                            </div>
                        </form>
                    </div>

                    <!-- Existing Comments List -->
                    <div class="comment-list d-flex flex-column gap-3">
                        @forelse($post->approvedComments as $comment)
                            <div class="comment-item p-3 p-md-4 rounded-4 bg-surface border border-subtle shadow-xs">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-gold text-dark fw-bold d-flex align-items-center justify-content-center shadow-xs" style="width:34px;height:34px;">
                                            {{ substr($comment->author_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold small text-body">{{ $comment->author_name }}</div>
                                            <div class="extra-small text-muted">{{ $comment->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-link text-gold text-decoration-none p-0 extra-small fw-semibold" onclick="replyToComment({{ $comment->id }}, '{{ addslashes($comment->author_name) }}')">
                                        <i class="bi bi-reply-fill"></i> Reply
                                    </button>
                                </div>
                                <p class="text-body small mb-2 ms-4 ps-2 border-start border-gold">
                                    {!! nl2br(e($comment->content)) !!}
                                </p>

                                <!-- Nested Replies (Reader Replies & Staff Responses) -->
                                @php
                                    $allReplies = $comment->replies->concat($comment->commentReplies ?? collect());
                                @endphp
                                @if($allReplies->count() > 0)
                                    <div class="ms-4 ps-3 mt-3 pt-2 border-top border-subtle d-flex flex-column gap-2">
                                        @foreach($allReplies as $reply)
                                            <div class="reply-item p-3 rounded-3 bg-body-tertiary">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold extra-small text-gold">
                                                        <i class="bi bi-patch-check-fill me-1"></i>{{ $reply->author_name ?: ($reply->user->name ?? 'Editorial Desk') }}
                                                    </span>
                                                    <span class="extra-small text-muted">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="small text-body mb-0">{!! nl2br(e($reply->content ?: $reply->reply)) !!}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4 text-secondary">
                                <i class="bi bi-chat-dots fs-1 opacity-25 d-block mb-2 text-primary"></i>
                                <p class="small fst-italic mb-0">Be the first to share an editorial perspective on this chronicle.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
                </div>
            </div>

            <!-- Editorial Sidebar (Matching user mockup reference) -->
            <div class="col-lg-4">
                <aside class="editorial-sidebar">
                    <div class="d-flex flex-column gap-4">

                        <!-- Widget 1: Search Insights -->
                        <div class="editorial-sidebar-card bento-card p-4 rounded-4">
                            <h6 class="fw-bold mb-3 text-body" style="font-size: 0.95rem;">Search Insights</h6>
                            <form action="{{ route('blog.search') }}" method="GET">
                                <div class="sidebar-search-pill">
                                    <i class="bi bi-search text-muted me-2 small"></i>
                                    <input type="text" name="q" class="form-control form-control-sm bg-transparent border-0 shadow-none p-0 text-body" placeholder="Search archive..." autocomplete="off">
                                </div>
                            </form>
                        </div>

                        {{-- Editorial Sidebar Ad Placement --}}
                        @if(isset($sidebarAd) && $sidebarAd)
                            @include('frontend.partials.ad-slot', ['ad' => $sidebarAd, 'layout' => 'sidebar'])
                        @endif

                        <!-- Widget 2: Top Categories -->
                        @if($categories->count() > 0)
                        <div class="editorial-sidebar-card bento-card p-4 rounded-4">
                            <h6 class="fw-bold mb-3 text-body" style="font-size: 0.95rem;">Top Categories</h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach($categories as $cat)
                                    <a href="{{ route('blog.category', $cat->slug) }}" class="sidebar-category-row {{ ($post->category && $post->category->id == $cat->id) ? 'active' : '' }}">
                                        <span class="d-flex align-items-center gap-2 small fw-semibold text-body">
                                            <i class="bi bi-folder2 text-primary"></i>
                                            <span>{{ $cat->name }}</span>
                                        </span>
                                        <span class="sidebar-count-badge">
                                            {{ $cat->published_posts_count }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Widget 3: Latest Insights -->
                        @if($recentPosts->count() > 0 || $trendingPosts->count() > 0)
                        @php
                            $sidebarInsights = $recentPosts->count() > 0 ? $recentPosts->take(4) : $trendingPosts->take(4);
                        @endphp
                        <div class="editorial-sidebar-card bento-card p-4 rounded-4">
                            <h6 class="fw-bold mb-3 text-body" style="font-size: 0.95rem;">Latest Insights</h6>
                            <div class="d-flex flex-column gap-3">
                                @foreach($sidebarInsights as $insight)
                                    <a href="{{ route('blog.show', $insight->slug) }}" class="sidebar-insight-item">
                                        @if($insight->featured_image)
                                            <img src="{{ asset('storage/' . $insight->featured_image) }}" alt="{{ $insight->title }}" class="sidebar-insight-img">
                                        @else
                                            <div class="sidebar-insight-img bg-body-tertiary d-flex align-items-center justify-content-center text-muted">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="sidebar-insight-title">
                                                {{ $insight->title }}
                                            </h6>
                                            <div class="sidebar-insight-date">
                                                {{ $insight->published_at ? $insight->published_at->format('M d, Y') : '' }}
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Widget 4: Popular Tags -->
                        @if($popularTags->count() > 0)
                        <div class="editorial-sidebar-card bento-card p-4 rounded-4">
                            <h6 class="fw-bold mb-3 text-body" style="font-size: 0.95rem;">Popular Tags</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($popularTags as $tag)
                                    <a href="{{ route('blog.tag', $tag->slug) }}" class="sidebar-tag-chip {{ $post->tags->contains($tag->id) ? 'active' : '' }}">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Widget 5: Article Index (Table of Contents) -->
                        <div class="editorial-sidebar-card bento-card p-4 rounded-4 d-none" id="tableOfContentsWidget">
                            <h6 class="fw-bold mb-3 text-body" style="font-size: 0.95rem;">
                                <i class="bi bi-list-nested text-primary me-1"></i> Article Index
                            </h6>
                            <nav id="tableOfContentsContainer">
                                <ul class="toc-list" id="tocList"></ul>
                            </nav>
                        </div>

                        <!-- Widget 6: The Maison VIP Gazette Dispatch -->
                        <div class="vip-dispatch-box p-4 rounded-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-envelope-paper-heart-fill text-gold fs-5"></i>
                                <span class="extra-small text-gold fw-bold text-uppercase letter-spacing-1">VIP Atelier Dispatch</span>
                            </div>
                            <h6 class="fw-bold mb-2">Curated Dispatches in Your Inbox</h6>
                            <p class="text-secondary extra-small mb-3">
                                Receive private notifications for runway critiques, collector horology, and bespoke artisan insights.
                            </p>
                            <form action="{{ route('newsletter.subscribe') }}" method="POST">
                                @csrf
                                <div style="display:none !important;" aria-hidden="true">
                                    <input type="text" name="company_verify_field" tabindex="-1" autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <input type="email" name="email" required placeholder="Your email address..." class="vip-dispatch-input" autocomplete="email">
                                </div>
                                <button type="submit" class="btn-vip-dispatch">
                                    Join The Maison Circle
                                </button>
                            </form>
                        </div>

                    </div>
                </aside>
            </div>
        </div>

        <!-- Related Curations Section -->
        @if($relatedPosts->count() > 0)
        <section class="related-curations pt-5 border-top border-subtle mt-5">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <span class="text-gold text-uppercase fw-bold letter-spacing-2 extra-small d-block mb-1">Further Exploration</span>
                    <h4 class="fw-bold mb-0">Chronicles of Similar Essence</h4>
                </div>
            </div>
            <div class="row g-4">
                @foreach($relatedPosts as $rel)
                <div class="col-md-4">
                    <article class="bento-card h-100">
                        <div class="bento-img-wrap position-relative">
                            <div class="ratio ratio-16x9">
                                @if($rel->featured_image)
                                    <img src="{{ asset('storage/' . $rel->featured_image) }}" alt="{{ $rel->title }}">
                                @else
                                    <div class="w-100 h-100 bg-secondary-subtle d-flex align-items-center justify-content-center text-muted">
                                        <i class="bi bi-image fs-3 opacity-50"></i>
                                    </div>
                                @endif
                            </div>
                            @if($rel->category)
                                <a href="{{ route('blog.category', $rel->category->slug) }}" class="floating-category-pill">
                                    {{ $rel->category->name }}
                                </a>
                            @endif
                        </div>
                        <div class="p-4 d-flex flex-column justify-content-between flex-grow-1">
                            <div>
                                <h6 class="fw-bold mb-2">
                                    <a href="{{ route('blog.show', $rel->slug) }}" class="text-body text-decoration-none hover-gold">
                                        {{ $rel->title }}
                                    </a>
                                </h6>
                            </div>
                            <div class="d-flex justify-content-between extra-small text-secondary pt-3 border-top border-subtle">
                                <span><i class="bi bi-clock me-1 text-gold"></i>{{ $rel->reading_time }} min read</span>
                                <span>{{ $rel->published_at ? $rel->published_at->format('M d, Y') : '' }}</span>
                            </div>
                        </div>
                    </article>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</article>
@endsection
