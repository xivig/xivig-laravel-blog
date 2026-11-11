@extends('admin.layouts.admin')

@section('title', 'Master SEO Studio & Search Engine Hub | XIVIG Maison Gazette')
@section('page_title', 'Master SEO Studio')

@section('content')
<!-- SEO Health Overview -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-success-subtle text-success">
                    <i class="bi bi-graph-up-arrow fs-4"></i>
                </div>
                <span class="badge {{ $avgScore >= 70 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-3 py-1 small fw-bold">
                    {{ $avgScore >= 70 ? 'Optimal' : 'Needs Optimization' }}
                </span>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Average Chronicle SEO Score</h6>
            <h3 class="fw-black text-body mb-0">{{ $avgScore }}/100</h3>
            <span class="extra-small text-muted mt-2 d-block">Automatic Schema & Meta Audit</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-primary-subtle text-primary">
                    <i class="bi bi-diagram-3-fill fs-4"></i>
                </div>
                <a href="{{ url('/sitemap.xml') }}" target="_blank" class="badge bg-primary-subtle text-primary text-decoration-none rounded-pill px-3 py-1 small fw-bold">
                    View Live &rarr;
                </a>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">XML Sitemap Status</h6>
            <h3 class="fw-black text-body mb-0">Active &amp; Indexed</h3>
            <form action="{{ route('admin.seo.regenerate-sitemap') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-link p-0 extra-small text-primary text-decoration-none fw-bold">
                    <i class="bi bi-arrow-repeat me-1"></i> Force Regenerate Sitemap Now
                </button>
            </form>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-info-subtle text-info">
                    <i class="bi bi-robot fs-4"></i>
                </div>
                <a href="{{ url('/robots.txt') }}" target="_blank" class="badge bg-info-subtle text-info text-decoration-none rounded-pill px-3 py-1 small fw-bold">
                    View Raw &rarr;
                </a>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Robots.txt Directive</h6>
            <h3 class="fw-black text-body mb-0">Crawler Guard Active</h3>
            <span class="extra-small text-muted mt-2 d-block">Protects /admin while opening content to Google</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Robots.txt GUI Editor -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-black text-body mb-1">Robots.txt GUI Configuration</h5>
                    <p class="text-muted extra-small mb-0">Direct search engine crawlers (Google, Bing, Yandex)</p>
                </div>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 small">Dynamic Engine</span>
            </div>

            <form action="{{ route('admin.seo.update-robots') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="robots_txt" rows="8" class="form-control font-monospace small rounded-3 p-3">{{ $robotsContent }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-primary">
                    <i class="bi bi-save me-1"></i> Save Robots.txt Configuration
                </button>
            </form>
        </div>
    </div>

    <!-- Live SEO Audit Checklist -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-transparent py-4 px-4 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-black text-body mb-0">Real-time Chronicle Audits</h5>
                    <p class="text-muted extra-small mb-0">Latest published stories with automated health scores</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Chronicle Title</th>
                            <th>Word Count</th>
                            <th class="text-end pe-4">SEO Health</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audits as $item)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('admin.posts.edit', $item['post']->id) }}" class="fw-bold text-body text-decoration-none hover-primary">
                                    {{ Str::limit($item['post']->title, 40) }}
                                </a>
                            </td>
                            <td class="small text-muted">{{ $item['audit']['word_count'] }} words</td>
                            <td class="text-end pe-4">
                                <span class="badge {{ $item['audit']['score'] >= 70 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-3 py-1 fw-bold">
                                    {{ $item['audit']['score'] }}/100
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
