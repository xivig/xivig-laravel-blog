@extends('frontend.layouts.app')

@section('title', 'Master Editorial Sitemap & Directory | ' . ($globalSettings['title'] ?? 'XIVIG Maison Gazette'))
@section('meta_description', 'The comprehensive editorial directory of XIVIG Maison Gazette. Explore our complete catalog of haute couture chronicles, horology reviews, atelier services, and institutional governance.')
@section('canonical_url', url('/sitemap'))

@section('structured_data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'XIVIG Maison Gazette Master Sitemap & Editorial Directory',
    'description' => 'The comprehensive editorial directory of XIVIG Maison Gazette.',
    'url' => url('/sitemap'),
    'publisher' => [
        '@type' => 'Organization',
        'name' => $globalSettings['title'] ?? 'XIVIG Maison Gazette',
        'url' => url('/'),
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
</script>
@endsection

@section('content')
<div class="container py-4 py-md-5">

    <!-- 1. Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb extra-small text-uppercase letter-spacing-1 align-items-center mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('blog.index') }}" class="text-decoration-none text-secondary hover-gold d-inline-flex align-items-center gap-1">
                    <i class="bi bi-house-door"></i> Home
                </a>
            </li>
            <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">Editorial Directory</li>
        </ol>
    </nav>

    <!-- 2. Master Hero Header -->
    <div class="text-center max-w-850 mx-auto mb-5">
        <span class="badge bg-gold text-dark text-uppercase letter-spacing-1 px-3 py-1.5 rounded-pill mb-3 fw-bold shadow-sm">
            <i class="bi bi-compass-fill me-1"></i> Haute Cartography &amp; Index
        </span>
        <h1 class="display-4 fw-bold font-serif mb-3 tracking-tight">The Master Editorial Sitemap</h1>
        <p class="text-secondary fs-5 mb-4 lead">
            A comprehensive, structured architectural codex of every chronicle, atelier commission, curatorial desk, and institutional governance standard published across the Maison.
        </p>

        <!-- Syndication & Protocol Pills -->
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
            <a href="{{ route('blog.sitemap') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 hover-gold border shadow-xs">
                <i class="bi bi-filetype-xml text-primary fs-6"></i>
                <span class="small fw-semibold">Interactive XML Sitemap</span>
            </a>
            <a href="{{ route('blog.feed') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 hover-gold border shadow-xs">
                <i class="bi bi-rss-fill text-warning fs-6"></i>
                <span class="small fw-semibold">RSS 2.0 Syndication Stream</span>
            </a>
            <a href="{{ url('/robots.txt') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 hover-gold border shadow-xs">
                <i class="bi bi-robot text-info fs-6"></i>
                <span class="small fw-semibold">Robots.txt Codex</span>
            </a>
        </div>

        <!-- Live Real-Time Filter -->
        <div class="max-w-500 mx-auto position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
            <input type="text" id="sitemapFilter" class="form-control rounded-pill ps-5 py-2.5 bg-card border-subtle text-body shadow-sm" placeholder="Filter channels, services, stories, or codex..." autocomplete="off">
        </div>
    </div>

    <!-- 3. Bento Section Architecture -->
    <div class="row g-4 mb-5">
        
        <!-- Column 1: Editorial Desks & Institutional Codex -->
        <div class="col-lg-6">
            
            <!-- Signature Editorial Desks -->
            <div class="bento-card p-4 p-md-5 mb-4 border-0 shadow-sm rounded-4 bg-card h-auto">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-subtle">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-gold-subtle text-gold shadow-sm" style="width: 44px; height: 44px;">
                            <i class="bi bi-journal-bookmark fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 font-serif">Editorial Channels</h4>
                            <span class="text-muted extra-small text-uppercase letter-spacing-1">Curated Gazette Desks</span>
                        </div>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1.5 extra-small fw-bold">
                        {{ $categories->count() }} Desks
                    </span>
                </div>

                <div class="row g-3">
                    @forelse($categories as $cat)
                    <div class="col-sm-6 sitemap-item" data-keywords="{{ strtolower($cat->name . ' ' . $cat->slug) }}">
                        <a href="{{ route('blog.category', $cat->slug) }}" class="p-3 rounded-3 border border-subtle d-flex justify-content-between align-items-center text-decoration-none text-body hover-gold bg-body-tertiary h-100 transition-all">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-folder2-open text-gold"></i>
                                <span class="fw-semibold small">{{ $cat->name }}</span>
                            </div>
                            <span class="badge rounded-pill bg-body extra-small text-muted border">
                                {{ $cat->published_posts_count }}
                            </span>
                        </a>
                    </div>
                    @empty
                    <div class="col-12 text-muted small fst-italic">No active editorial channels found.</div>
                    @endforelse
                </div>
            </div>

            <!-- Institutional Governance & Codex -->
            <div class="bento-card p-4 p-md-5 border-0 shadow-sm rounded-4 bg-card">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-subtle">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info-subtle text-info shadow-sm" style="width: 44px; height: 44px;">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 font-serif">Institutional Codex</h4>
                            <span class="text-muted extra-small text-uppercase letter-spacing-1">Governance &amp; Transparency</span>
                        </div>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1.5 extra-small fw-bold">
                        {{ $pages->count() + 1 }} Documents
                    </span>
                </div>

                <div class="d-flex flex-column gap-2">
                    @foreach($pages as $page)
                    <div class="sitemap-item" data-keywords="{{ strtolower($page->title . ' ' . $page->slug) }}">
                        <a href="{{ route('page.show', $page->slug) }}" class="p-3 rounded-3 border border-subtle d-flex justify-content-between align-items-center text-decoration-none text-body hover-gold bg-body-tertiary transition-all">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-text text-secondary"></i>
                                <span class="fw-semibold small">{{ $page->title }}</span>
                            </div>
                            <i class="bi bi-arrow-right-short text-gold fs-5"></i>
                        </a>
                    </div>
                    @endforeach

                    <div class="sitemap-item" data-keywords="contact salon desk inquiry feedback">
                        <a href="{{ route('contact') }}" class="p-3 rounded-3 border border-subtle d-flex justify-content-between align-items-center text-decoration-none text-body hover-gold bg-body-tertiary transition-all">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-envelope-paper text-warning"></i>
                                <span class="fw-semibold small">Editorial &amp; Client Salon (Contact Desk)</span>
                            </div>
                            <span class="badge bg-warning-subtle text-warning extra-small rounded-pill">Open</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Column 2: Atelier Services & Editorial Masthead -->
        <div class="col-lg-6">

            <!-- Atelier Services & Bespoke Offerings -->
            <div class="bento-card p-4 p-md-5 mb-4 border-0 shadow-sm rounded-4 bg-card">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-subtle">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary shadow-sm" style="width: 44px; height: 44px;">
                            <i class="bi bi-gem fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 font-serif">Atelier Services</h4>
                            <span class="text-muted extra-small text-uppercase letter-spacing-1">Bespoke Client Commissions</span>
                        </div>
                    </div>
                    <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 extra-small fw-bold hover-gold">
                        View All
                    </a>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse($services as $srv)
                    <div class="sitemap-item" data-keywords="{{ strtolower($srv->title . ' ' . $srv->category . ' ' . $srv->tagline) }}">
                        <a href="{{ route('services.show', $srv->slug) }}" class="p-3 rounded-3 border border-subtle d-flex justify-content-between align-items-center text-decoration-none text-body hover-gold bg-body-tertiary transition-all">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-gold shadow-xs" style="width: 36px; height: 36px; background: rgba(201, 169, 110, 0.1);">
                                    <i class="bi {{ $srv->icon ?: 'bi-gem' }}"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small text-body mb-0">{{ $srv->title }}</div>
                                    <div class="text-muted extra-small">{{ $srv->category }} &#183; {{ $srv->formatted_price }}</div>
                                </div>
                            </div>
                            <span class="badge bg-body-secondary text-body extra-small rounded-pill px-2.5 py-1">
                                {{ $srv->delivery_time ?: 'Bespoke' }}
                            </span>
                        </a>
                    </div>
                    @empty
                    <div class="text-muted small fst-italic">No bespoke commissions listed currently.</div>
                    @endforelse
                </div>
            </div>

            <!-- Editorial Masthead & Authors -->
            <div class="bento-card p-4 p-md-5 border-0 shadow-sm rounded-4 bg-card">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-subtle">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success shadow-sm" style="width: 44px; height: 44px;">
                            <i class="bi bi-feather fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 font-serif">Curators &amp; Masthead</h4>
                            <span class="text-muted extra-small text-uppercase letter-spacing-1">Verified Editorial Bylines</span>
                        </div>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1.5 extra-small fw-bold">
                        {{ $authors->count() }} Authors
                    </span>
                </div>

                <div class="row g-2">
                    @forelse($authors as $author)
                    <div class="col-sm-6 sitemap-item" data-keywords="{{ strtolower($author->name . ' ' . $author->slug) }}">
                        <a href="{{ route('blog.author', $author->slug) }}" class="p-2.5 rounded-3 border border-subtle d-flex align-items-center gap-2.5 text-decoration-none text-body hover-gold bg-body-tertiary transition-all">
                            <div class="rounded-circle bg-gold text-dark fw-bold d-flex align-items-center justify-content-center shadow-xs" style="width: 32px; height: 32px; font-size: 0.78rem;">
                                {{ strtoupper(substr($author->name, 0, 1)) }}
                            </div>
                            <div class="text-truncate">
                                <div class="fw-semibold small text-truncate">{{ $author->name }}</div>
                                <div class="text-muted extra-small">{{ $author->published_posts_count }} stories</div>
                            </div>
                        </a>
                    </div>
                    @empty
                    <div class="col-12 text-muted small fst-italic">No authors currently indexed.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- 4. Topical Lexicon & Tags Cloud -->
    <div class="bento-card p-4 p-md-5 mb-5 border-0 shadow-sm rounded-4 bg-card">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-subtle">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning shadow-sm" style="width: 44px; height: 44px;">
                    <i class="bi bi-tags fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 font-serif">Topical Lexicon &amp; Tags</h4>
                    <span class="text-muted extra-small text-uppercase letter-spacing-1">Taxonomy &amp; Key Themes</span>
                </div>
            </div>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1.5 extra-small fw-bold">
                {{ $tags->count() }} Topics
            </span>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @forelse($tags as $tag)
            <div class="sitemap-item" data-keywords="{{ strtolower($tag->name . ' ' . $tag->slug) }}">
                <a href="{{ route('blog.tag', $tag->slug) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 extra-small text-decoration-none hover-gold border">
                    #{{ $tag->name }}
                    <span class="opacity-50 ms-1">({{ $tag->published_posts_count }})</span>
                </a>
            </div>
            @empty
            <span class="text-muted small fst-italic">No topical tags indexed.</span>
            @endforelse
        </div>
    </div>

    <!-- 5. Recent Gazette Dispatches (Chronological Catalog) -->
    <div class="bento-card p-4 p-md-5 border-0 shadow-sm rounded-4 bg-card">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-subtle">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #c9a96e;">
                    <i class="bi bi-newspaper text-gold fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 font-serif">Recent Gazette Chronicles</h4>
                    <span class="text-muted extra-small text-uppercase letter-spacing-1">Catalog of Latest Published Stories</span>
                </div>
            </div>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1.5 extra-small fw-bold">
                {{ $recentPosts->count() }} Dispatches
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold letter-spacing-1 border-0">
                    <tr>
                        <th class="ps-3 py-3" style="width: 40px;">#</th>
                        <th class="py-3">Chronicle Title</th>
                        <th class="py-3">Desk</th>
                        <th class="py-3">Curator</th>
                        <th class="py-3 text-end pe-3">Published Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPosts as $idx => $post)
                    <tr class="sitemap-item" data-keywords="{{ strtolower($post->title . ' ' . ($post->category?->name ?? '') . ' ' . ($post->author?->name ?? '')) }}">
                        <td class="ps-3 text-muted extra-small">{{ $idx + 1 }}</td>
                        <td>
                            <a href="{{ route('blog.show', $post->slug) }}" class="fw-bold text-body text-decoration-none hover-gold d-inline-flex align-items-center gap-1.5">
                                {{ $post->title }}
                                @if($post->is_featured)
                                    <span class="badge bg-warning-subtle text-warning extra-small rounded-pill">Featured</span>
                                @endif
                            </a>
                        </td>
                        <td>
                            @if($post->category)
                                <a href="{{ route('blog.category', $post->category->slug) }}" class="badge bg-body-secondary text-body text-decoration-none extra-small rounded-pill px-2.5 py-1">
                                    {{ $post->category->name }}
                                </a>
                            @else
                                <span class="text-muted extra-small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="small text-muted">{{ $post->author?->name ?? 'Maison Desk' }}</span>
                        </td>
                        <td class="text-end pe-3 text-muted extra-small">
                            {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted small">No chronicles currently published.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Client-side Fast Filtering Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterInput = document.getElementById('sitemapFilter');
    const items = document.querySelectorAll('.sitemap-item');

    if (filterInput) {
        filterInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();

            items.forEach(el => {
                const keywords = (el.getAttribute('data-keywords') || '') + ' ' + (el.textContent || '').toLowerCase();
                if (!query || keywords.includes(query)) {
                    el.style.display = '';
                } else {
                    el.style.display = 'none';
                }
            });
        });
    }
});
</script>

<style>
.bg-gold-subtle { background: rgba(201, 169, 110, 0.12); }
.text-gold { color: #c9a96e !important; }
.hover-gold:hover { color: #c9a96e !important; }
.bento-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.bento-card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}
.font-serif {
    font-family: 'Cinzel', serif, system-ui;
}
</style>
@endsection
