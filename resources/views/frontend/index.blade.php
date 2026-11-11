@extends('frontend.layouts.app')

@section('title', 'XIVIG Maison Gazette | Haute Couture, Horology & Modern Luxury')

@section('content')
<div class="container py-4">

    <!-- 1. HERO LEAD STORY / MAGAZINE COVER -->
    @if($heroPost)
    <section class="hero-feature-section mb-5">
        <div class="hero-cover-card">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-7 position-relative hero-image-col">
                    <div class="hero-img-box">
                        @if($heroPost->featured_image)
                            <img src="{{ asset('storage/' . $heroPost->featured_image) }}" alt="{{ $heroPost->title }}" class="hero-img">
                        @else
                            <div class="w-100 h-100 bg-secondary-subtle d-flex align-items-center justify-content-center p-5 text-secondary">
                                <i class="bi bi-image display-1 opacity-25"></i>
                            </div>
                        @endif
                        <div class="hero-img-overlay"></div>
                    </div>
                    <div class="position-absolute top-0 start-0 m-3 m-md-4 z-2">
                        <span class="badge bg-gold text-dark fw-bold px-3 py-2 text-uppercase letter-spacing-1 shadow">
                            ★ Feature of the Maison
                        </span>
                    </div>
                </div>
                <div class="col-lg-5 p-4 p-xl-5 d-flex flex-column justify-content-between bg-surface">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            @if($heroPost->category)
                                <a href="{{ route('blog.category', $heroPost->category->slug) }}" class="badge bg-gold-subtle text-gold text-decoration-none px-3 py-2 rounded-pill fw-semibold">
                                    {{ $heroPost->category->name }}
                                </a>
                            @endif
                            <span class="text-secondary small">•</span>
                            <span class="text-secondary small"><i class="bi bi-clock me-1 text-gold"></i>{{ $heroPost->reading_time }} min read</span>
                        </div>

                        <h1 class="hero-title fw-bold mb-3">
                            <a href="{{ route('blog.show', $heroPost->slug) }}" class="text-body text-decoration-none hover-gold transition-all">
                                {{ $heroPost->title }}
                            </a>
                        </h1>

                        <p class="hero-excerpt mb-4">
                            {{ $heroPost->excerpt }}
                        </p>
                    </div>

                    <div class="border-top border-subtle pt-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            @if($heroPost->author && $heroPost->author->avatar_url)
                                <img src="{{ $heroPost->author->avatar_url }}" alt="{{ $heroPost->author->name }}" class="rounded-circle shadow-xs" width="44" height="44" style="object-fit:cover;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <div class="avatar-placeholder rounded-circle bg-gold text-dark fw-bold align-items-center justify-content-center shadow-xs" style="width:44px;height:44px;display:none;">
                                    {{ substr($heroPost->author->name, 0, 1) }}
                                </div>
                            @else
                                <div class="avatar-placeholder rounded-circle bg-gold text-dark fw-bold d-flex align-items-center justify-content-center shadow-xs" style="width:44px;height:44px;">
                                    {{ substr($heroPost->author ? $heroPost->author->name : 'X', 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold small text-body">{{ $heroPost->author ? $heroPost->author->name : 'Maison XIVIG Editorial' }}</div>
                                <div class="text-secondary extra-small">{{ $heroPost->published_at ? $heroPost->published_at->format('M d, Y') : 'Recent' }}</div>
                            </div>
                        </div>

                        <a href="{{ route('blog.show', $heroPost->slug) }}" class="btn btn-gold rounded-pill px-4 py-2 fw-bold small d-inline-flex align-items-center gap-2">
                            <span>Read Story</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- 2. LIVE GAZETTE MARQUEE TICKER -->
    <div class="gazette-ticker-bar rounded-pill px-3 mb-5 d-flex align-items-center gap-3">
        <div class="gazette-ticker-badge flex-shrink-0 z-2 position-relative">
            <i class="bi bi-lightning-charge-fill"></i> Dispatches
        </div>
        <div class="gazette-ticker-viewport flex-grow-1 overflow-hidden position-relative">
            <div class="gazette-ticker-track">
                @foreach($posts->take(5) as $tickerPost)
                    <a href="{{ route('blog.show', $tickerPost->slug) }}" class="ticker-item">
                        <span class="ticker-bullet"></span>
                        <span>{{ $tickerPost->title }}</span>
                        <span class="extra-small text-gold opacity-75">({{ $tickerPost->category ? $tickerPost->category->name : 'Chronicle' }})</span>
                    </a>
                @endforeach
                <!-- Duplicate for smooth seamless infinite loop -->
                @foreach($posts->take(5) as $tickerPost)
                    <a href="{{ route('blog.show', $tickerPost->slug) }}" class="ticker-item">
                        <span class="ticker-bullet"></span>
                        <span>{{ $tickerPost->title }}</span>
                        <span class="extra-small text-gold opacity-75">({{ $tickerPost->category ? $tickerPost->category->name : 'Chronicle' }})</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top Editorial Sponsor Showcase --}}
    @if(isset($headerAd) && $headerAd)
        @include('frontend.partials.ad-slot', ['ad' => $headerAd, 'layout' => 'banner'])
    @endif

    <!-- 3. THE EDITOR'S CURATION (3-COLUMN HIGHLIGHTS) -->
    @if($editorPicks->count() > 0)
    <section class="editor-curation-section mb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="text-gold text-uppercase fw-bold letter-spacing-2 extra-small d-block mb-1">Handpicked Dispatches</span>
                <h3 class="fw-bold mb-0">The Editor's Curation</h3>
            </div>
            <div class="d-none d-md-block flex-grow-1 ms-4 border-bottom border-subtle"></div>
        </div>

        <div class="row g-4">
            @foreach($editorPicks as $pick)
            <div class="col-md-4">
                <article class="bento-card h-100">
                    <div class="bento-img-wrap position-relative">
                        <div class="ratio ratio-16x9">
                            @if($pick->featured_image)
                                <img src="{{ asset('storage/' . $pick->featured_image) }}" alt="{{ $pick->title }}">
                            @else
                                <div class="w-100 h-100 bg-secondary-subtle d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-image fs-2 opacity-50"></i>
                                </div>
                            @endif
                        </div>
                        @if($pick->category)
                            <a href="{{ route('blog.category', $pick->category->slug) }}" class="floating-category-pill">
                                {{ $pick->category->name }}
                            </a>
                        @endif
                    </div>
                    <div class="p-4 d-flex flex-column justify-content-between flex-grow-1">
                        <div>
                            <div class="d-flex align-items-center gap-2 extra-small text-secondary mb-2">
                                <span>{{ $pick->published_at ? $pick->published_at->format('M d, Y') : '' }}</span>
                                <span>•</span>
                                <span><i class="bi bi-clock me-1 text-gold"></i>{{ $pick->reading_time }} min</span>
                            </div>
                            <h5 class="fw-bold mb-2">
                                <a href="{{ route('blog.show', $pick->slug) }}" class="text-body text-decoration-none hover-gold">
                                    {{ $pick->title }}
                                </a>
                            </h5>
                            <p class="text-secondary small mb-3">
                                {{ Str::limit($pick->excerpt, 110) }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top border-subtle extra-small text-secondary">
                            <span>By {{ $pick->author ? $pick->author->name : 'Staff' }}</span>
                            <span class="d-flex align-items-center gap-1">
                                <i class="bi bi-eye text-gold"></i> {{ number_format($pick->views_count) }}
                            </span>
                        </div>
                    </div>
                </article>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Haute Maison Partner Showcase Breakout Banner --}}
    @if(isset($showcaseAd) && $showcaseAd)
        <section class="partner-showcase-section mb-5">
            @include('frontend.partials.ad-slot', ['ad' => $showcaseAd, 'layout' => 'banner'])
        </section>
    @endif

    <!-- ATELIER BESPOKE COMMISSIONS & SERVICES SHOWCASE -->
    @if(isset($homeServices) && $homeServices->isNotEmpty())
    <section class="atelier-services-section mb-5 p-4 p-md-5 rounded-4 position-relative overflow-hidden"
             style="background: linear-gradient(135deg, rgba(201, 169, 110, 0.08) 0%, rgba(31, 41, 55, 0.04) 100%); border: 1px solid rgba(201, 169, 110, 0.25);">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <span class="badge bg-gold text-dark fw-bold text-uppercase letter-spacing-1 extra-small mb-2">The Atelier</span>
                <h3 class="fw-bold mb-1 font-cinzel">Bespoke Services &amp; Private Commissions</h3>
                <p class="text-secondary small mb-0">Authoritative journalism, haute horlogerie advisory, and creative brand direction for distinguished maisons.</p>
            </div>
            <a href="{{ route('services.index') }}" class="btn btn-outline-gold rounded-pill px-4 btn-sm fw-bold">
                <span>Explore All Offerings</span> <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($homeServices as $service)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-body hover-up" style="border: 1px solid rgba(201, 169, 110, 0.2) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-gold shadow-xs"
                             style="width: 46px; height: 46px; background: rgba(201, 169, 110, 0.15); border: 1px solid rgba(201, 169, 110, 0.3);">
                            <i class="bi {{ $service->icon ?: 'bi-gem' }} fs-4"></i>
                        </div>
                        @if($service->badge)
                            <span class="badge bg-warning-subtle text-warning extra-small rounded-pill">{{ $service->badge }}</span>
                        @endif
                    </div>
                    <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1 d-block mb-1">{{ $service->category }}</span>
                    <h5 class="fw-bold text-body mb-2">
                        <a href="{{ route('services.show', $service->slug) }}" class="text-body text-decoration-none hover-gold">
                            {{ $service->title }}
                        </a>
                    </h5>
                    <p class="text-secondary small mb-4 flex-grow-1">
                        {{ $service->tagline ?: Str::limit(strip_tags($service->description), 90) }}
                    </p>
                    <div class="d-flex align-items-center justify-content-between pt-3 border-top border-subtle">
                        <div>
                            <span class="extra-small text-muted d-block text-uppercase">Investment</span>
                            <strong class="text-gold small">{{ $service->formatted_price }}</strong>
                        </div>
                        <a href="{{ route('services.show', $service->slug) }}" class="btn btn-gold btn-sm rounded-pill px-3 py-1 extra-small fw-bold">
                            Commission &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 4. CATEGORY FILTER TABS & MAIN CHRONICLES FEED -->
    <section class="main-stories-feed mb-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom border-subtle">
            <div class="category-filter-scroller">
                <a href="{{ route('blog.index') }}" class="category-tab-pill {{ !$categorySlug ? 'active' : '' }}">
                    All Chronicles
                </a>
                @foreach($globalCategories as $c)
                    <a href="{{ route('blog.index', ['category' => $c->slug]) }}" class="category-tab-pill {{ $categorySlug === $c->slug ? 'active' : '' }}">
                        {{ $c->name }} <span class="opacity-75 ms-1">({{ $c->published_posts_count }})</span>
                    </a>
                @endforeach
            </div>

            <div class="text-secondary small fw-medium">
                Showing {{ $posts->total() }} published chronicles
            </div>
        </div>

        <div class="row g-4 mb-5">
            @forelse($posts as $post)
            @if($loop->iteration === 4 && isset($feedAd) && $feedAd)
                @include('frontend.partials.ad-slot', ['ad' => $feedAd, 'layout' => 'card'])
            @endif
            <div class="col-md-6 col-lg-4">
                <article class="bento-card h-100">
                    <div class="bento-img-wrap position-relative">
                        <div class="ratio ratio-16x9">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                            @else
                                <div class="w-100 h-100 bg-secondary-subtle d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-journal-richtext fs-1 opacity-25"></i>
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
                            <div class="d-flex align-items-center gap-2 extra-small text-secondary mb-2 flex-wrap">
                                @if($post->is_sticky)
                                    <span class="badge bg-gold text-dark extra-small rounded-pill px-2">
                                        <i class="bi bi-pin-angle-fill me-1"></i> Sticky
                                    </span>
                                @endif
                                @if($post->isPasswordProtected())
                                    <span class="badge bg-secondary text-light extra-small rounded-pill px-2" title="Passphrase Protected">
                                        <i class="bi bi-lock-fill me-1"></i> Protected
                                    </span>
                                @endif
                                <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : 'Recent' }}</span>
                                <span>•</span>
                                <span><i class="bi bi-clock me-1 text-gold"></i>{{ $post->reading_time }} min read</span>
                            </div>

                            <h5 class="fw-bold mb-2">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-body text-decoration-none hover-gold">
                                    {{ $post->title }}
                                </a>
                            </h5>

                            <p class="text-secondary small mb-3">
                                {{ Str::limit($post->excerpt, 120) }}
                            </p>
                        </div>

                        <div class="pt-3 border-top border-subtle d-flex align-items-center justify-content-between extra-small text-secondary">
                            <div class="d-flex align-items-center gap-2">
                                @if($post->author && $post->author->avatar_url)
                                    <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="rounded-circle shadow-xs" width="26" height="26" style="object-fit:cover;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                    <div class="avatar-placeholder rounded-circle bg-gold text-dark fw-bold align-items-center justify-content-center shadow-xs" style="width:26px;height:26px;font-size:0.65rem;display:none;">
                                        {{ substr($post->author->name, 0, 1) }}
                                    </div>
                                @else
                                    <div class="avatar-placeholder rounded-circle bg-gold text-dark fw-bold d-inline-flex align-items-center justify-content-center shadow-xs" style="width:26px;height:26px;font-size:0.65rem;">
                                        {{ substr($post->author ? $post->author->name : 'M', 0, 1) }}
                                    </div>
                                @endif
                                <span class="fw-semibold text-body">{{ $post->author ? $post->author->name : 'Staff' }}</span>
                            </div>
                            <div class="d-flex gap-3">
                                <span class="d-flex align-items-center gap-1">
                                    <i class="bi bi-heart-fill text-danger opacity-75"></i> {{ $post->likes_count }}
                                </span>
                                <span class="d-flex align-items-center gap-1">
                                    <i class="bi bi-eye text-gold opacity-75"></i> {{ $post->views_count }}
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            @empty
            <div class="col-12 py-5 text-center text-secondary">
                <i class="bi bi-journal-x display-3 text-gold opacity-50 mb-3 d-block"></i>
                <h4 class="fw-bold mb-2">No chronicles found in this category.</h4>
                <p class="small text-muted mb-4">Discover our full library of haute couture and horology insights.</p>
                <a href="{{ route('blog.index') }}" class="btn btn-gold rounded-pill px-4">Browse All Chronicles</a>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links() }}
        </div>
    </section>

    <!-- 5. POPULAR TAGS & THEMATIC DISCOVERY -->
    @if($popularTags->count() > 0)
    <section class="thematic-tags-section py-4 border-top border-subtle">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-gold text-dark fw-bold text-uppercase" style="font-size: 0.65rem;">Lexicon</span>
                <span class="text-gold fw-bold small text-uppercase letter-spacing-1">Popular Themes:</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($popularTags as $t)
                    <a href="{{ route('blog.tag', $t->slug) }}" class="popular-tag-pill">
                        #{{ $t->name }} <span class="opacity-75">({{ $t->published_posts_count }})</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Bottom Maison Partner Showcase Banner --}}
    @if(isset($footerAd) && $footerAd)
        @include('frontend.partials.ad-slot', ['ad' => $footerAd, 'layout' => 'banner'])
    @endif

</div>
@endsection
