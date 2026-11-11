@extends('admin.layouts.admin')

@section('title', 'Ads Manager & Monetization | XIVIG Maison Gazette')
@section('page_title', 'Ads Manager')

@section('content')



<!-- Performance Metrics Bar -->
<div class="row g-4 mb-4 kpi-row">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-primary-subtle text-primary">
                    <i class="bi bi-megaphone-fill"></i>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    {{ $activeCampaigns }} Active
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Total Campaigns</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($totalCampaigns) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Across Maison editorial slots</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-info-subtle text-info">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    Reach
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Ad Impressions</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($totalImpressions) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Direct patron impressions served</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-success-subtle text-success">
                    <i class="bi bi-cursor-fill"></i>
                </div>
                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    Engagement
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Verified Clicks</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($totalClicks) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">High-intent reader conversions</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-warning-subtle text-warning">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    CTR Rate
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Average CTR</h6>
            <h3 class="fw-black text-body mb-0">{{ $overallCtr }}%</h3>
            <span class="extra-small text-muted mt-2 d-block">Click-through performance velocity</span>
        </div>
    </div>
</div>

<!-- Main Sponsored Content Table Card (Matching Image 4) -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-transparent py-4 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="fw-black text-body mb-0">Sponsored Content</h5>
            <p class="text-muted extra-small mb-0">Manage premium ad placements and performance tracking.</p>
        </div>
        <button type="button" class="btn btn-dark rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-xs" data-bs-toggle="modal" data-bs-target="#newCampaignModal">
            <i class="bi bi-plus-lg"></i> New Campaign
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold letter-spacing-1 border-0">
                <tr>
                    <th class="ps-4 py-3" style="min-width: 220px;">CAMPAIGN</th>
                    <th class="py-3" style="min-width: 170px;">TYPE &amp; PLACEMENT</th>
                    <th class="py-3" style="min-width: 150px;">PERFORMANCE</th>
                    <th class="py-3 text-center" style="min-width: 110px;">STATUS</th>
                    <th class="py-3 text-end pe-4" style="min-width: 130px;">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ads as $ad)
                @php
                    $ctr = $ad->impressions > 0 ? round(($ad->clicks / $ad->impressions) * 100, 2) : 0.0;
                    $slotLabel = match($ad->slot) {
                        'header'     => 'Header Showcase',
                        'sidebar'    => 'Editorial Sidebar',
                        'in_article' => 'In-Chronicle Placement',
                        'footer'     => 'Footer Banner',
                        default      => Str::headline($ad->slot),
                    };
                @endphp
                <tr>
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="story-thumb-box shadow-xs">
                                @if($ad->image_path)
                                    <img src="{{ asset(ltrim($ad->image_path, '/')) }}" alt="" class="story-thumb-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-100 h-100 d-none align-items-center justify-content-center text-primary bg-primary bg-opacity-10">
                                        <i class="bi bi-megaphone fs-5"></i>
                                    </div>
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10">
                                        <i class="bi bi-code-slash fs-5"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                <span class="fw-bold text-body d-block text-truncate" style="max-width: 200px;">{{ $ad->title }}</span>
                                @if($ad->target_url)
                                    <a href="{{ $ad->target_url }}" target="_blank" class="extra-small text-primary text-decoration-none text-truncate d-block" style="max-width: 200px;">
                                        {{ Str::limit($ad->target_url, 30) }} <i class="bi bi-box-arrow-up-right extra-small"></i>
                                    </a>
                                @else
                                    <span class="extra-small text-muted font-monospace">HTML / Script Unit</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="py-3">
                        <span class="badge bg-body-tertiary text-body border rounded-pill px-3 py-1 extra-small fw-semibold">
                            <i class="bi bi-pin-angle-fill text-gold me-1"></i> {{ $slotLabel }}
                        </span>
                    </td>
                    <td class="py-3">
                        <div class="extra-small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Impressions:</span>
                                <strong class="text-body font-monospace">{{ number_format($ad->impressions) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Clicks:</span>
                                <strong class="text-success font-monospace">{{ number_format($ad->clicks) }} ({{ $ctr }}%)</strong>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 text-center">
                        <form action="{{ route('admin.ads.toggle', $ad->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm p-0 border-0 shadow-none" title="Toggle Campaign Status">
                                @if($ad->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                                        <span class="status-pulse published"></span> Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                                        <span class="status-pulse draft"></span> Paused
                                    </span>
                                @endif
                            </button>
                        </form>
                    </td>
                    <td class="py-3 text-end pe-4">
                        <div class="d-inline-flex align-items-center gap-1">
                            <button type="button" class="btn btn-light btn-sm rounded-pill border px-3 py-1 extra-small text-body fw-semibold" data-bs-toggle="modal" data-bs-target="#editCampaignModal{{ $ad->id }}">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>
                            <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this advertising campaign?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm rounded-circle border shadow-xs d-inline-flex align-items-center justify-content-center text-danger" style="width: 28px; height: 28px;" title="Purge Campaign">
                                    <i class="bi bi-trash3 extra-small"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Edit Campaign Modal -->
                <div class="modal fade" id="editCampaignModal{{ $ad->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <form action="{{ route('admin.ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-bottom py-3 px-4">
                                    <h5 class="modal-title fw-bold text-body">
                                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Campaign
                                    </h5>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Campaign Name <span class="text-danger">*</span></label>
                                        <input type="text" name="title" value="{{ $ad->title }}" required class="form-control rounded-3">
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-muted">Placement Slot</label>
                                            <select name="slot" class="form-select rounded-3">
                                                <option value="header" {{ $ad->slot === 'header' ? 'selected' : '' }}>Header Showcase</option>
                                                <option value="sidebar" {{ $ad->slot === 'sidebar' ? 'selected' : '' }}>Sidebar Placement</option>
                                                <option value="in_article" {{ $ad->slot === 'in_article' ? 'selected' : '' }}>In-Article Banner</option>
                                                <option value="footer" {{ $ad->slot === 'footer' ? 'selected' : '' }}>Footer Showcase</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-muted">Status</label>
                                            <select name="is_active" class="form-select rounded-3">
                                                <option value="1" {{ $ad->is_active ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$ad->is_active ? 'selected' : '' }}>Paused</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Target Destination URL</label>
                                        <input type="url" name="target_url" value="{{ $ad->target_url }}" placeholder="https://brand.com/campaign" class="form-control rounded-3">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Creative Banner Image</label>
                                        <input type="file" name="image" class="form-control rounded-3" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Custom Ad Embed Code (Optional)</label>
                                        <textarea name="custom_code" rows="3" class="form-control rounded-3 font-monospace small" placeholder="<script>... or AdSense unit">{{ $ad->custom_code }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top py-3 px-4">
                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Update Campaign</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <!-- Empty State Matching Image 4 -->
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="py-5">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-4 bg-body-secondary mb-3 shadow-xs" style="width: 80px; height: 80px;">
                                <i class="bi bi-megaphone fs-1 text-muted opacity-50"></i>
                            </div>
                            <h6 class="fw-bold text-muted mb-1">No active campaigns found.</h6>
                            <p class="extra-small text-muted mb-4">Launch high-yield brand sponsorships and direct advertiser units across the Gazette.</p>
                            <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-primary" data-bs-toggle="modal" data-bs-target="#newCampaignModal">
                                Start First Campaign
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ads->hasPages())
        <div class="card-footer bg-transparent py-3 px-4 border-top d-flex justify-content-between align-items-center">
            <span class="extra-small text-muted">Showing {{ $ads->firstItem() }} to {{ $ads->lastItem() }} of {{ $ads->total() }} campaigns</span>
            {{ $ads->links() }}
        </div>
    @endif
</div>

<!-- New Campaign Modal -->
<div class="modal fade" id="newCampaignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-body">
                        <i class="bi bi-megaphone text-primary me-2"></i> Launch New Campaign
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Campaign Name <span class="text-danger">*</span></label>
                        <input type="text" name="title" required placeholder="e.g. Vacheron Constantin Horology Feature" class="form-control rounded-3">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Placement Slot</label>
                            <select name="slot" class="form-select rounded-3">
                                <option value="sidebar" selected>Editorial Sidebar</option>
                                <option value="header">Header Showcase</option>
                                <option value="in_article">In-Article Banner</option>
                                <option value="footer">Footer Showcase</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Initial Status</label>
                            <select name="is_active" class="form-select rounded-3">
                                <option value="1" selected>Active &bull; Live</option>
                                <option value="0">Draft &bull; Paused</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Target Destination URL</label>
                        <input type="url" name="target_url" placeholder="https://advertiser.com/landing-page" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Creative Banner Image</label>
                        <input type="file" name="image" class="form-control rounded-3" accept="image/*">
                        <div class="form-text extra-small">High-res banners (JPG, PNG, WEBP, SVG) up to 5MB.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Custom Ad Embed Code (Optional)</label>
                        <textarea name="custom_code" rows="3" class="form-control rounded-3 font-monospace small" placeholder="<script>... or HTML ad code"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Launch Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
