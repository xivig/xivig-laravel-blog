@extends('admin.layouts.admin')

@section('title', 'Visitor & Audience Intelligence | XIVIG Maison Gazette')
@section('page_title', 'Audience Analytics')

@section('content')
<!-- KPI Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-primary-subtle text-primary">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small fw-bold">Telemetry</span>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Unique Visitors</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($totalVisitors) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Global Luxury Readers</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-success-subtle text-success">
                    <i class="bi bi-eye-fill fs-4"></i>
                </div>
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 small fw-bold">Volume</span>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Page Views</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($totalPageViews) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Chronicle Impressions</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-info-subtle text-info">
                    <i class="bi bi-search fs-4"></i>
                </div>
                <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1 small fw-bold">Intent</span>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Popular Queries</h6>
            <h3 class="fw-black text-body mb-0">{{ $popularSearches->count() }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Catalog Search Analytics</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-warning-subtle text-warning">
                    <i class="bi bi-laptop fs-4"></i>
                </div>
                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1 small fw-bold">Hardware</span>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Primary Device</h6>
            <h3 class="fw-black text-body mb-0">{{ array_key_first($devices) ?? 'Desktop' }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Audience Viewport Type</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Top Pages & Reading Velocity -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-transparent py-4 px-4 border-0">
                <h5 class="fw-black text-body mb-0">Top Requested URLs</h5>
                <p class="text-muted extra-small mb-0">Highest frequency paths visited by patrons</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Path / Resource</th>
                            <th class="text-end pe-4">Page Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topPages as $tp)
                        <tr>
                            <td class="ps-4 font-monospace small text-body fw-bold">{{ $tp->path }}</td>
                            <td class="text-end pe-4 fw-bold text-primary">{{ number_format($tp->views) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center py-4 text-muted small">No page views recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Search Keyword Analytics -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent py-4 px-4 border-0">
                <h5 class="fw-black text-body mb-0">Search Intent & Keywords</h5>
                <p class="text-muted extra-small mb-0">What readers are discovering via the live command palette</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Query Keyword</th>
                            <th>Volume</th>
                            <th class="text-end pe-4">Results Found</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($popularSearches as $ps)
                        <tr>
                            <td class="ps-4 fw-bold text-body">&ldquo;{{ $ps->query }}&rdquo;</td>
                            <td><span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">{{ $ps->searches }} searches</span></td>
                            <td class="text-end pe-4 text-success fw-bold">{{ $ps->total_results }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted small">No search queries recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Devices, Browsers & Technology -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-black text-body mb-3">Device Breakdown</h5>
            @foreach($devices as $device => $count)
            <div class="mb-3">
                <div class="d-flex justify-content-between small fw-bold mb-1">
                    <span>{{ $device }}</span>
                    <span>{{ $count }} ({{ $totalVisitors > 0 ? round(($count / $totalVisitors) * 100) : 0 }}%)</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary rounded-pill" style="width: {{ $totalVisitors > 0 ? ($count / $totalVisitors) * 100 : 0 }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-black text-body mb-3">Browser Distribution</h5>
            @foreach($browsers as $browser => $count)
            <div class="mb-3">
                <div class="d-flex justify-content-between small fw-bold mb-1">
                    <span>{{ $browser }}</span>
                    <span>{{ $count }}</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-gold rounded-pill" style="width: {{ $totalVisitors > 0 ? ($count / $totalVisitors) * 100 : 0 }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
