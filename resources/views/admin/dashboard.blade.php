@extends('admin.layouts.admin')

@section('title', 'Executive Editorial Dashboard | XIVIG Maison Gazette')
@section('page_title', 'Editorial Overview')

@section('content')

<!-- Welcome Luxury Banner -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative dashboard-luxury-banner">
    <div class="card-body p-4 p-md-5 position-relative z-2">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-gold-gradient text-dark px-3 py-1-5 rounded-pill fw-black extra-small tracking-wider text-uppercase shadow-xs">
                        <i class="bi bi-shield-check me-1"></i> MAISON ADMINISTRATIVE OS 2.0
                    </span>
                    <span class="badge bg-body text-muted border px-3 py-1 rounded-pill extra-small fw-semibold d-none d-sm-inline-flex">
                        <i class="bi bi-clock-history me-1 text-primary"></i> Vol. XXIV &bull; Editorial Cycle
                    </span>
                </div>
                <h2 class="display-6 fw-black mb-2 text-body">Welcome back, {{ Auth::user()->name }}</h2>
                <p class="text-muted lead fs-6 mb-4" style="max-width: 620px;">
                    Real-time atelier command overview: editorial velocity, readership engagement, content health, and concierge communications.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-primary">
                        <i class="bi bi-plus-circle-fill me-1"></i> Write New Story
                    </a>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold">
                        <i class="bi bi-journals me-1"></i> Chronicles Directory
                    </a>
                    <a href="{{ route('blog.index') }}" target="_blank" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-body border shadow-sm">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Public Gazette
                    </a>
                    <a href="{{ route('admin.media.index') }}" class="btn btn-light rounded-pill px-3 py-2 fw-bold text-body border shadow-sm">
                        <i class="bi bi-images me-1"></i> Media Vault
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-end">
                <div class="p-4 bg-body rounded-4 d-inline-block border shadow-sm" style="min-width: 260px;">
                    <div class="text-start">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="extra-small text-uppercase tracking-wider text-gold fw-black">System Telemetry</span>
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2 py-0 extra-small">
                                <span class="spinner-grow spinner-grow-sm me-1" style="width: 6px; height: 6px;" role="status"></span> Active
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                            <span class="small fw-bold text-body">Maison Vault Protected</span>
                        </div>
                        <div class="extra-small text-muted d-flex justify-content-between py-1 border-bottom border-light-subtle">
                            <span>Admin Role:</span>
                            <strong class="text-body">{{ strtoupper(Auth::user()->role ?? 'ADMIN') }}</strong>
                        </div>
                        <div class="extra-small text-muted d-flex justify-content-between py-1 border-bottom border-light-subtle">
                            <span>Atelier Time:</span>
                            <strong class="text-primary font-monospace">{{ \App\Helpers\TimezoneHelper::format(now(), 'h:i A') }}</strong>
                        </div>
                        <div class="extra-small text-muted d-flex justify-content-between pt-1">
                            <span>Platform:</span>
                            <strong class="text-body">Laravel 11 &bull; SQLite</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Primary Commercial & Editorial KPI Cards -->
<div class="row g-4 mb-4 kpi-row">
    <!-- Gross Readership / Impressions -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 dashboard-kpi-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-circle bg-primary-subtle text-primary">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                        <i class="bi bi-arrow-up-right me-1"></i> Gross Volume
                    </span>
                </div>
                <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Total Readers &amp; Views</h6>
                <h3 class="fw-black text-body mb-0">{{ number_format($totalViews) }}</h3>
                <span class="extra-small text-muted mt-2 d-block">
                    <i class="bi bi-heart-fill text-danger me-1"></i><strong>{{ number_format($totalLikes) }}</strong> total patron appreciations
                </span>
            </div>
        </div>
    </div>

    <!-- Total Stories & Articles -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 dashboard-kpi-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-circle bg-info-subtle text-info">
                        <i class="bi bi-journal-richtext"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                        {{ $draftPosts }} Drafts
                    </span>
                </div>
                <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Published Chronicles</h6>
                <h3 class="fw-black text-body mb-0">{{ number_format($publishedPosts) }}</h3>
                <span class="extra-small text-muted mt-2 d-block">
                    <span class="text-warning fw-semibold">{{ $draftPosts }} drafts</span> in progress &bull; {{ $totalPosts }} total chronicles
                </span>
            </div>
        </div>
    </div>

    <!-- Active Taxonomies & Collections -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 dashboard-kpi-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-circle bg-warning-subtle text-warning">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                        Curated
                    </span>
                </div>
                <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Maison Categories</h6>
                <h3 class="fw-black text-body mb-0">{{ number_format($totalCategories) }}</h3>
                <span class="extra-small text-muted mt-2 d-block">
                    Across <strong>{{ $totalTags }}</strong> tags &bull; <strong>{{ $totalMedia }}</strong> media assets
                </span>
            </div>
        </div>
    </div>

    <!-- Registered Patrons / Subscribers -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 dashboard-kpi-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-circle bg-success-subtle text-success">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                        VIP Patrons
                    </span>
                </div>
                <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Gazette Patrons</h6>
                <h3 class="fw-black text-body mb-0">{{ number_format($totalSubscribers) }}</h3>
                <span class="extra-small text-muted mt-2 d-block">
                    Active audience &bull; Verified dispatch subscribers
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Main Row: Recent Stories & Quick Controls -->
<div class="row g-4 mb-4 align-items-start">
    <!-- Left Column (8 cols): Recent Stories Table & 7-Day Velocity Chart -->
    <div class="col-lg-8">
        
        <!-- Recent Editorial Stories Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 dashboard-kpi-card">
            <div class="card-header bg-transparent py-4 px-4 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-black text-body mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-journal-bookmark-fill text-primary"></i> Recent Editorial Stories
                    </h5>
                    <p class="text-muted extra-small mb-0">Latest publications and fulfillment requests</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.posts.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-xs">
                        <i class="bi bi-plus-lg me-1"></i> New
                    </a>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-light btn-sm rounded-pill px-3 border fw-bold text-body">
                        View All Stories <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <div class="table-responsive px-2">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold letter-spacing-1 border-0">
                        <tr>
                            <th class="ps-3 py-3" style="min-width: 260px;">Chronicle / Story</th>
                            <th class="py-3" style="min-width: 140px;">Author</th>
                            <th class="py-3" style="min-width: 130px;">Category</th>
                            <th class="py-3 text-center" style="min-width: 90px;">Engagement</th>
                            <th class="py-3 text-center" style="min-width: 100px;">Status</th>
                            <th class="py-3 text-end pe-3" style="min-width: 110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPosts as $post)
                        @php
                            $calcReadingTime = $post->reading_time ?: max(1, (int) ceil(str_word_count(strip_tags($post->content ?? '')) / 200));
                            $thumbUrl = null;
                            if ($post->featured_image) {
                                $cleanImg = ltrim($post->featured_image, '/');
                                $thumbUrl = str_starts_with($cleanImg, 'http') ? $cleanImg : asset($cleanImg);
                            }
                        @endphp
                        <tr class="dashboard-story-row">
                            <!-- Story Title & Meta -->
                            <td class="ps-3 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="story-thumb-box shadow-xs">
                                        @if($thumbUrl)
                                            <img src="{{ $thumbUrl }}" alt="" class="story-thumb-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-100 h-100 d-none align-items-center justify-content-center text-primary bg-primary bg-opacity-10 fw-bold">
                                                <i class="bi bi-file-earmark-richtext fs-5"></i>
                                            </div>
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10 fw-bold">
                                                <i class="bi bi-file-earmark-richtext fs-5"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="overflow-hidden">
                                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="fw-bold text-body text-decoration-none d-block text-truncate" style="max-width: 280px;" title="{{ $post->title }}">
                                            {{ $post->title }}
                                        </a>
                                        <div class="extra-small text-muted d-flex align-items-center gap-2 mt-1">
                                            <span class="badge bg-body-secondary text-muted font-monospace py-0 px-1 border">#{{ $post->id }}</span>
                                            <span>&bull;</span>
                                            <span><i class="bi bi-clock me-1"></i>{{ $calcReadingTime }} min read</span>
                                            <span>&bull;</span>
                                            <span>{{ $post->published_at ? $post->published_at->format('M d') : $post->created_at->format('M d') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Author -->
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-placeholder rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($post->author ? $post->author->name : 'S', 0, 1)) }}
                                    </div>
                                    <div class="text-truncate" style="max-width: 110px;">
                                        <span class="small fw-semibold text-body d-block text-truncate" title="{{ $post->author ? $post->author->name : 'Staff Editor' }}">
                                            {{ $post->author ? $post->author->name : 'Staff Editor' }}
                                        </span>
                                        <span class="extra-small text-muted text-truncate d-block">
                                            {{ $post->author ? ($post->author->designation ?? 'Contributor') : 'Editorial Desk' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-3">
                                <span class="badge bg-body-tertiary text-body border rounded-pill px-3 py-1 extra-small fw-semibold">
                                    <i class="bi bi-folder2 text-gold me-1"></i>
                                    {{ $post->category ? $post->category->name : 'Uncategorized' }}
                                </span>
                            </td>

                            <!-- Engagement / Views & Likes -->
                            <td class="py-3 text-center">
                                <span class="fw-bold text-body small d-block">
                                    <i class="bi bi-eye text-primary me-1"></i>{{ number_format($post->views_count) }}
                                </span>
                                <span class="extra-small text-muted d-inline-flex align-items-center gap-1 mt-1">
                                    <i class="bi bi-heart-fill text-danger" style="font-size: 0.65rem;"></i>{{ number_format($post->likes_count) }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="py-3 text-center">
                                @if($post->status === 'published')
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-1 rounded-pill extra-small fw-bold">
                                        <span class="status-pulse published"></span>Published
                                    </span>
                                @elseif($post->status === 'draft')
                                    <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill extra-small fw-bold">
                                        <span class="status-pulse draft"></span>Draft
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info border-opacity-25 px-3 py-1 rounded-pill extra-small fw-bold">
                                        <span class="status-pulse scheduled"></span>{{ ucfirst($post->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 text-end pe-3">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-light btn-sm rounded-pill border px-3 py-1 extra-small text-body fw-semibold" title="Edit Chronicle">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-light btn-sm rounded-circle border shadow-xs d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" title="Preview Public Story">
                                        <i class="bi bi-box-arrow-up-right extra-small"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-50"></i>
                                <span class="small fw-semibold">No editorial chronicles recorded yet.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent py-3 px-4 border-top text-center">
                <a href="{{ route('admin.posts.index') }}" class="extra-small fw-bold text-primary text-decoration-none">
                    Browse All {{ number_format($totalPosts) }} Editorial Chronicles &rarr;
                </a>
            </div>
        </div>

        <!-- 7-Day Readership & Publishing Velocity Interactive Visual Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 dashboard-kpi-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h5 class="fw-black text-body mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-activity text-info"></i> 7-Day Reader Traffic &amp; Velocity
                    </h5>
                    <p class="text-muted extra-small mb-0">Daily unique impressions and engagement telemetry across the Maison Gazette</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-gold-gradient text-dark px-3 py-1-5 rounded-pill extra-small fw-black shadow-xs">
                        <i class="bi bi-lightning-charge-fill me-1"></i> Peak: {{ number_format($trafficPeakViews ?? 680) }} views/day
                    </span>
                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-3 py-1-5 rounded-pill extra-small fw-bold">
                        <i class="bi bi-broadcast me-1 text-success"></i> Telemetry
                    </span>
                </div>
            </div>

            <!-- Modern Telemetry Visual Bar Columns with Background Tracks -->
            <div class="telemetry-chart-track border-bottom pb-2">
                @php
                    $highestViewVal = max(1, $trafficDays->max('views'));
                @endphp
                @foreach($trafficDays as $td)
                @php
                    $heightPercent = max(18, min(100, round(($td['views'] / $highestViewVal) * 100)));
                    $isPeak = ($td['views'] === $highestViewVal);
                @endphp
                <div class="telemetry-col" title="{{ $td['date'] }}: {{ number_format($td['views']) }} impressions &bull; {{ number_format($td['likes']) }} likes">
                    <!-- Views Count Label -->
                    <span class="extra-small text-muted mb-1 fw-bold font-monospace" style="font-size: 0.72rem;">
                        {{ $td['views'] }}
                    </span>
                    
                    <!-- Slender Telemetry Bar Track & Pill Fill -->
                    <div class="telemetry-bar-slot">
                        <div class="telemetry-bar-fill {{ $isPeak ? 'is-peak shadow-xs' : '' }}"
                             style="height: {{ $heightPercent }}%;">
                        </div>
                    </div>

                    <!-- Day & Date Label -->
                    <span class="extra-small text-muted mt-2 fw-bold text-uppercase" style="font-size: 0.72rem;">{{ $td['day'] }}</span>
                    <span class="extra-small text-muted opacity-75 d-none d-sm-block" style="font-size: 0.65rem;">{{ substr($td['date'], 4) }}</span>
                </div>
                @endforeach
            </div>

            <!-- Telemetry Metrics Bar Footer -->
            <div class="row g-3 pt-3 text-muted extra-small align-items-center">
                <div class="col-sm-4 d-flex align-items-center gap-2">
                    <span class="stat-icon-circle bg-primary-subtle text-primary" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.85rem;">
                        <i class="bi bi-graph-up"></i>
                    </span>
                    <div>
                        <span class="d-block text-muted opacity-75" style="font-size: 0.68rem;">7-DAY IMPRESSIONS</span>
                        <strong class="text-body fw-black">{{ number_format($trafficTotalViews ?? 0) }} total</strong>
                    </div>
                </div>
                <div class="col-sm-4 d-flex align-items-center gap-2">
                    <span class="stat-icon-circle bg-warning-subtle text-warning" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.85rem;">
                        <i class="bi bi-speedometer2"></i>
                    </span>
                    <div>
                        <span class="d-block text-muted opacity-75" style="font-size: 0.68rem;">DAILY VELOCITY</span>
                        <strong class="text-body fw-black">{{ number_format($trafficAvgViews ?? 0) }} views/day</strong>
                    </div>
                </div>
                <div class="col-sm-4 d-flex align-items-center gap-2">
                    <span class="stat-icon-circle bg-danger-subtle text-danger" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.85rem;">
                        <i class="bi bi-heart-fill"></i>
                    </span>
                    <div>
                        <span class="d-block text-muted opacity-75" style="font-size: 0.68rem;">PATRON APPRECIATION</span>
                        <strong class="text-body fw-black">{{ number_format($trafficTotalLikes ?? 0) }} likes</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column (4 cols): Quick Operations & Hardening Armory -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        
        <!-- Quick Operations Atelier Tiles -->
        <div class="card border-0 shadow-sm rounded-4 p-4 dashboard-kpi-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-black text-body mb-0 text-uppercase extra-small letter-spacing-1 d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge-fill text-gold"></i> Atelier Operations
                </h6>
                <span class="badge bg-body-secondary text-muted extra-small">Shortcuts</span>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('admin.posts.create') }}" class="btn btn-light rounded-3 text-start p-3 border d-flex align-items-center justify-content-between hover-action-tile">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-circle bg-primary-subtle text-primary me-3" style="width: 38px; height: 38px; min-width: 38px;">
                            <i class="bi bi-journal-plus fs-6"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-body d-block small">Publish New Story</span>
                            <span class="text-muted extra-small">Author a luxury editorial article</span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="btn btn-light rounded-3 text-start p-3 border d-flex align-items-center justify-content-between hover-action-tile">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-circle bg-warning-subtle text-warning me-3" style="width: 38px; height: 38px; min-width: 38px;">
                            <i class="bi bi-grid-3x3-gap-fill fs-6"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-body d-block small">Maison Categories</span>
                            <span class="text-muted extra-small">Organize collections &amp; curation</span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>

                <a href="{{ route('admin.comments.index') }}" class="btn btn-light rounded-3 text-start p-3 border d-flex align-items-center justify-content-between hover-action-tile">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-circle bg-success-subtle text-success me-3" style="width: 38px; height: 38px; min-width: 38px;">
                            <i class="bi bi-chat-quote-fill fs-6"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-body small">Discourse Moderation</span>
                                @if($pendingComments > 0)
                                    <span class="badge bg-danger rounded-pill extra-small px-2">{{ $pendingComments }}</span>
                                @endif
                            </div>
                            <span class="text-muted extra-small">Approve reader commentary</span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>

                <a href="{{ route('admin.media.index') }}" class="btn btn-light rounded-3 text-start p-3 border d-flex align-items-center justify-content-between hover-action-tile">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-circle bg-info-subtle text-info me-3" style="width: 38px; height: 38px; min-width: 38px;">
                            <i class="bi bi-images fs-6"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-body d-block small">Media Asset Library</span>
                            <span class="text-muted extra-small">Curate photography &amp; assets</span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>

                <a href="{{ route('admin.subscribers.index') }}" class="btn btn-light rounded-3 text-start p-3 border d-flex align-items-center justify-content-between hover-action-tile">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-circle bg-danger-subtle text-danger me-3" style="width: 38px; height: 38px; min-width: 38px;">
                            <i class="bi bi-envelope-heart-fill fs-6"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-body d-block small">Gazette Patrons</span>
                            <span class="text-muted extra-small">Manage newsletter subscriptions</span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>

                <a href="{{ route('admin.services.index') }}" class="btn btn-light rounded-3 text-start p-3 border d-flex align-items-center justify-content-between hover-action-tile">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-circle bg-warning-subtle text-warning me-3" style="width: 38px; height: 38px; min-width: 38px;">
                            <i class="bi bi-briefcase-fill fs-6"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-body small">Services &amp; Offerings</span>
                                @if(($pendingInquiries ?? 0) > 0)
                                    <span class="badge bg-danger rounded-pill extra-small px-2">{{ $pendingInquiries }} New</span>
                                @endif
                            </div>
                            <span class="text-muted extra-small">{{ $totalServices ?? 0 }} offerings &bull; Client briefs desk</span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="btn btn-light rounded-3 text-start p-3 border d-flex align-items-center justify-content-between hover-action-tile">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-circle bg-secondary-subtle text-secondary me-3" style="width: 38px; height: 38px; min-width: 38px;">
                            <i class="bi bi-sliders fs-6"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-body d-block small">Atelier Settings</span>
                            <span class="text-muted extra-small">System &amp; branding configurations</span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
            </div>
        </div>

        <!-- Access Credentials & Security Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 dashboard-kpi-card">
            <h6 class="fw-black text-body mb-3 text-uppercase extra-small letter-spacing-1 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill text-primary"></i> Access Credentials
            </h6>
            <div class="p-3 bg-body-tertiary rounded-3 mb-3 border">
                <span class="extra-small fw-bold text-muted text-uppercase d-block mb-1">Authenticated Account</span>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="small fw-bold text-body text-truncate me-2">{{ Auth::user()->email }}</span>
                    <span class="badge bg-primary text-uppercase extra-small px-2 py-1">{{ Auth::user()->role ?? 'ADMIN' }}</span>
                </div>
            </div>

            <div class="d-flex flex-column gap-2 extra-small">
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                    <span class="text-muted"><i class="bi bi-shield-check text-success me-2"></i> Anti-XSS Sanitizer</span>
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2 py-0">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                    <span class="text-muted"><i class="bi bi-file-earmark-lock text-success me-2"></i> MIME Whitelist</span>
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2 py-0">Strict</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                    <span class="text-muted"><i class="bi bi-bug text-success me-2"></i> Anti-Spam Honeypot</span>
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2 py-0">Armed</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span class="text-muted"><i class="bi bi-speedometer text-success me-2"></i> Rate Limit Protection</span>
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2 py-0">Enforced</span>
                </div>
            </div>
        </div>

        <!-- Recent Client Commissions Card -->
        @if(isset($recentInquiries) && $recentInquiries->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-4 dashboard-kpi-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-black text-body mb-0 text-uppercase extra-small letter-spacing-1 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-square-quote-fill text-primary"></i> Client Commission Briefs
                </h6>
                <a href="{{ route('admin.services.inquiries') }}" class="extra-small fw-bold text-primary text-decoration-none">View All &rarr;</a>
            </div>
            <div class="d-flex flex-column gap-2">
                @foreach($recentInquiries as $inq)
                <div class="p-2.5 rounded-3 bg-body-tertiary border d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="small text-body d-block">{{ $inq->name }}</strong>
                        <span class="extra-small text-muted text-truncate d-block" style="max-width: 180px;">{{ $inq->service ? $inq->service->title : 'General Inquiry' }}</span>
                    </div>
                    @php $sb = $inq->status_badge; @endphp
                    <span class="badge {{ $sb['class'] }} rounded-pill extra-small px-2 py-0.5">
                        {{ $sb['label'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

<!-- Secondary Row: Comments Moderation Queue & Top Performing Chronicles -->
<div class="row g-4 mb-4">
    <!-- Reader Comments Queue -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-black text-body mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-chat-quote-fill text-success"></i> Reader Feedback &amp; Moderation
                    </h5>
                    <p class="text-muted extra-small mb-0">Live comments from patrons awaiting editorial review</p>
                </div>
                <a href="{{ route('admin.comments.index') }}" class="btn btn-light btn-sm rounded-pill px-3 border text-body extra-small fw-semibold">
                    All Comments <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($recentComments as $comment)
                <div class="p-3 bg-body-tertiary rounded-3 border d-flex gap-3 align-items-start transition-all hover-shadow">
                    <div class="avatar-placeholder rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center fw-bold flex-shrink-0 shadow-xs" style="width: 40px; height: 40px;">
                        {{ strtoupper(substr($comment->author_name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small text-body">{{ $comment->author_name }}</span>
                            <span class="extra-small text-muted">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="extra-small text-muted mb-2 text-truncate" style="white-space: normal;">{{ Str::limit($comment->content, 90) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge {{ $comment->status === 'approved' ? 'bg-success-subtle text-success border border-success border-opacity-25' : 'bg-warning-subtle text-warning border border-warning border-opacity-25' }} rounded-pill extra-small">
                                {{ ucfirst($comment->status) }}
                            </span>
                            @if($comment->status === 'pending')
                                <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-0 extra-small shadow-xs">
                                        <i class="bi bi-check-lg me-1"></i> Approve
                                    </button>
                                </form>
                            @else
                                <span class="extra-small text-muted font-monospace text-truncate" style="max-width: 180px;">
                                    {{ $comment->post ? Str::limit($comment->post->title, 25) : 'Deleted Post' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-square-text fs-2 d-block mb-2 opacity-50"></i>
                    <span class="small fw-semibold">No pending reader comments.</span>
                    <p class="extra-small text-muted mb-0">All reader discourse has been moderated.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Performing Masterwork Stories -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-black text-body mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-trophy-fill text-gold"></i> Top Performing Chronicles
                    </h5>
                    <p class="text-muted extra-small mb-0">Stories ranked by reader engagement and view velocity</p>
                </div>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-light btn-sm rounded-pill px-3 border text-body extra-small fw-semibold">
                    All Chronicles <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            @php
                $maxViews = $topPosts->max('views_count') ?: 1;
            @endphp

            <div class="d-flex flex-column gap-3">
                @forelse($topPosts as $index => $top)
                @php
                    $pct = round(($top->views_count / $maxViews) * 100);
                    $medalClass = match($index) {
                        0 => 'rank-medal-1',
                        1 => 'rank-medal-2',
                        2 => 'rank-medal-3',
                        default => 'rank-medal-default',
                    };
                @endphp
                <div class="p-3 bg-body-tertiary rounded-3 border transition-all hover-shadow">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <span class="rank-medal {{ $medalClass }}">
                                {{ $index + 1 }}
                            </span>
                            <a href="{{ route('admin.posts.edit', $top->id) }}" class="fw-bold small text-body text-truncate text-decoration-none" title="{{ $top->title }}">
                                {{ $top->title }}
                            </a>
                        </div>
                        <span class="small fw-bold text-body ms-2 font-monospace">{{ number_format($top->views_count) }} views</span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 10px; background: rgba(0,0,0,0.05);">
                        <div class="progress-bar bg-primary rounded-pill shadow-primary" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 extra-small text-muted">
                        <span><i class="bi bi-person me-1"></i>By {{ $top->author ? $top->author->name : 'Staff Editor' }}</span>
                        <span><i class="bi bi-heart-fill text-danger me-1"></i>{{ number_format($top->likes_count) }} likes</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journals fs-2 d-block mb-2 opacity-50"></i>
                    <span class="small fw-semibold">No articles published yet.</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- SEO & Technical Infrastructure KPI Row (Consolidated Single Row) -->
<div class="row g-4 mb-4">
    <!-- SEO Health Average -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-success-subtle text-success">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <span class="badge {{ $averageSeoScore >= 70 ? 'bg-success-subtle text-success border border-success border-opacity-25' : 'bg-warning-subtle text-warning border border-warning border-opacity-25' }} rounded-pill px-3 py-1 extra-small fw-bold">
                    {{ $averageSeoScore >= 70 ? 'Optimal' : 'Audit Advised' }}
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">SEO Health Score</h6>
            <h3 class="fw-black text-body mb-0">{{ $averageSeoScore }}/100</h3>
            <span class="extra-small text-muted mt-2 d-block">Automated Schema &amp; Meta Audit</span>
        </div>
    </div>

    <!-- 404 Error Incidents -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-danger-subtle text-danger">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <a href="{{ route('admin.not-found-logs.index') }}" class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 text-decoration-none rounded-pill px-3 py-1 extra-small fw-bold">
                    View Logs &rarr;
                </a>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">404 Incidents Logged</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($total404Errors) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Broken links &amp; crawler misses</span>
        </div>
    </div>

    <!-- 301/302 Redirect Rules -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-info-subtle text-info">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <a href="{{ route('admin.redirects.index') }}" class="badge bg-info-subtle text-info border border-info border-opacity-25 text-decoration-none rounded-pill px-3 py-1 extra-small fw-bold">
                    Manage &rarr;
                </a>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Active 301 Redirects</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($totalRedirects) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Backlink equity protection</span>
        </div>
    </div>

    <!-- Contact Salon Inquiries -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-primary-subtle text-primary">
                    <i class="bi bi-envelope-paper-fill"></i>
                </div>
                <a href="{{ route('admin.contacts.index') }}" class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 text-decoration-none rounded-pill px-3 py-1 extra-small fw-bold">
                    Salon Desk &rarr;
                </a>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Direct Inquiries</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($totalContactMessages) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Editorial collaborations &amp; press</span>
        </div>
    </div>
</div>

@endsection
