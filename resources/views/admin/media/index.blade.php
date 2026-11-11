@extends('admin.layouts.admin')

@section('title', 'Media Assets Library & Vault | XIVIG Maison Gazette')
@section('page_title', 'Media Assets Vault')

@section('content')


<!-- Storage Telemetry & Metrics Row -->
<div class="row g-4 mb-4 kpi-row">
    <!-- Total Digital Assets -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-primary-subtle text-primary">
                    <i class="bi bi-images"></i>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    Vault Index
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Active Assets</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($activeCount) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">{{ $trashCount }} assets in trash bin</span>
        </div>
    </div>

    <!-- Total Storage Volume -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-info-subtle text-info">
                    <i class="bi bi-hdd-network"></i>
                </div>
                <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    Storage
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Total Disk Space</h6>
            <h3 class="fw-black text-body mb-0">{{ $metrics['total_size'] ?? '0 MB' }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Curated media volume</span>
        </div>
    </div>

    <!-- Format Distribution -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-success-subtle text-success">
                    <i class="bi bi-file-earmark-image"></i>
                </div>
                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    Web-Ready
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Modern Formats</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format(($metrics['webp_count'] ?? 0) + ($metrics['avif_count'] ?? 0)) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">
                {{ $metrics['webp_count'] ?? 0 }} WEBP &bull; {{ $metrics['jpg_count'] ?? 0 }} JPG &bull; {{ $metrics['png_count'] ?? 0 }} PNG
            </span>
        </div>
    </div>

    <!-- CDN & Security Status -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-warning-subtle text-warning">
                    <i class="bi bi-shield-check"></i>
                </div>
                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    Active
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Security &amp; Cache</h6>
            <h3 class="fw-black text-body mb-0">Hardened</h3>
            <span class="extra-small text-muted mt-2 d-block">Direct path &amp; symlink mirror synced</span>
        </div>
    </div>
</div>

<!-- Drag-and-Drop Luxury Upload Suite (Only in Active Tab) -->
@if($tab !== 'trash')
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-body">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-black text-body mb-1">
                <i class="bi bi-cloud-arrow-up text-primary me-2"></i> Upload Digital Media
            </h5>
            <p class="text-muted extra-small mb-0">Upload high-resolution editorial photography, banners, and chronicles assets. Supports batch uploads up to 15MB each.</p>
        </div>
        <button class="btn btn-sm btn-light border rounded-pill px-3 py-1 extra-small fw-semibold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#uploadBoxCollapse" aria-expanded="true">
            <i class="bi bi-chevron-down"></i> Toggle Upload
        </button>
    </div>

    <div class="collapse show" id="uploadBoxCollapse">
        <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" id="mediaUploadForm">
            @csrf
            <div class="upload-dropzone p-4 p-md-5 rounded-4 text-center position-relative transition-all border border-2 border-dashed bg-body-tertiary" id="dropZoneContainer">
                <input type="file" name="files[]" id="mediaFileInput" multiple accept="image/jpeg,image/png,image/webp,image/avif" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" style="z-index: 5;">
                
                <div class="upload-dropzone-content py-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3 shadow-xs" style="width: 72px; height: 72px;">
                        <i class="bi bi-cloud-arrow-up-fill fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-body mb-1">Drag &amp; drop photos here, or <span class="text-primary text-decoration-underline cursor-pointer">browse files</span></h5>
                    <p class="extra-small text-muted mb-3">Supports JPG, PNG, WEBP, and AVIF up to 15MB per file</p>
                    <div id="selectedFilesSummary" class="extra-small"></div>
                </div>
            </div>

            <div class="row g-3 align-items-center mt-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0 rounded-start-pill ps-3 text-muted">
                            <i class="bi bi-universal-access"></i>
                        </span>
                        <input type="text" name="alt_text" placeholder="Default accessibility narrative / alt text (optional)..." class="form-control rounded-end-pill extra-small shadow-none">
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-primary w-100 w-md-auto d-inline-flex align-items-center justify-content-center gap-2" id="submitUploadBtn">
                        <i class="bi bi-cloud-arrow-up-fill"></i> Upload to Vault
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Curated Media Vault Gallery Card -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-body">
    <!-- Section Title & Counter Matching User Screenshot -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-black text-body mb-0">Curated Media Assets</h4>
            <p class="text-muted extra-small mb-0">Showing {{ $mediaAssets->total() }} assets in editorial vault</p>
        </div>

        <!-- Active / Trash Tab Pills -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="d-inline-flex p-1 bg-body rounded-pill border shadow-xs">
                <a href="{{ route('admin.media.index', ['tab' => 'active', 'sort' => $sort, 'type' => $type, 'search' => $search]) }}" class="btn btn-sm rounded-pill px-4 fw-bold {{ $tab !== 'trash' ? 'btn-dark text-white' : 'btn-light text-muted border-0' }}">
                    Active <span class="badge bg-secondary bg-opacity-25 ms-1">{{ $activeCount }}</span>
                </a>
                <a href="{{ route('admin.media.index', ['tab' => 'trash', 'sort' => $sort, 'type' => $type, 'search' => $search]) }}" class="btn btn-sm rounded-pill px-4 fw-bold {{ $tab === 'trash' ? 'btn-dark text-white' : 'btn-light text-muted border-0' }}">
                    <i class="bi bi-trash3 me-1"></i> Trash <span class="badge bg-secondary bg-opacity-25 ms-1">{{ $trashCount }}</span>
                </a>
            </div>

            <!-- Select All Button -->
            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1-5 extra-small fw-semibold text-body d-inline-flex align-items-center gap-1.5" id="selectAllToggleBtn">
                <i class="bi bi-check2-square text-primary"></i> <span id="selectAllBtnText">Select All</span>
            </button>
        </div>
    </div>

    <!-- Controls Header: Search & Format/Sort Filters Matching Screenshot -->
    <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mb-4 pb-3 border-bottom">
            <!-- Search Form -->
            <form action="{{ route('admin.media.index') }}" method="GET" class="d-inline-flex">
                <input type="hidden" name="tab" value="{{ $tab }}">
                @if($type)<input type="hidden" name="type" value="{{ $type }}">@endif
                @if($sort)<input type="hidden" name="sort" value="{{ $sort }}">@endif
                <div class="input-group input-group-sm">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search assets..." class="form-control rounded-start-pill ps-3 border-end-0 extra-small">
                    <button type="submit" class="btn btn-light border border-start-0 rounded-end-pill px-3 extra-small">
                        <i class="bi bi-search text-muted"></i>
                    </button>
                </div>
            </form>

            <!-- Format Filter Pills -->
            <div class="d-inline-flex p-1 bg-body-tertiary rounded-pill border shadow-xs">
                <a href="{{ route('admin.media.index', ['tab' => $tab, 'sort' => $sort, 'search' => $search]) }}" class="btn btn-sm rounded-pill px-3 extra-small fw-semibold {{ !$type || $type === 'all' ? 'btn-dark text-white' : 'btn-light text-muted border-0' }}">
                    All
                </a>
                <a href="{{ route('admin.media.index', ['tab' => $tab, 'type' => 'jpeg', 'sort' => $sort, 'search' => $search]) }}" class="btn btn-sm rounded-pill px-3 extra-small fw-semibold {{ $type === 'jpeg' ? 'btn-dark text-white' : 'btn-light text-muted border-0' }}">
                    JPG
                </a>
                <a href="{{ route('admin.media.index', ['tab' => $tab, 'type' => 'png', 'sort' => $sort, 'search' => $search]) }}" class="btn btn-sm rounded-pill px-3 extra-small fw-semibold {{ $type === 'png' ? 'btn-dark text-white' : 'btn-light text-muted border-0' }}">
                    PNG
                </a>
                <a href="{{ route('admin.media.index', ['tab' => $tab, 'type' => 'webp', 'sort' => $sort, 'search' => $search]) }}" class="btn btn-sm rounded-pill px-3 extra-small fw-semibold {{ $type === 'webp' ? 'btn-dark text-white' : 'btn-light text-muted border-0' }}">
                    WEBP
                </a>
            </div>

            <!-- Sort Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm btn-light border rounded-pill px-3 py-1-5 extra-small fw-semibold dropdown-toggle text-body" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-arrow-down-up me-1"></i>
                    @if($sort === 'oldest') Oldest First
                    @elseif($sort === 'size_desc') Largest Size
                    @elseif($sort === 'size_asc') Smallest Size
                    @else Newest First @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 extra-small">
                    <li><a class="dropdown-item {{ $sort === 'latest' || !$sort ? 'active fw-bold' : '' }}" href="{{ route('admin.media.index', ['tab' => $tab, 'sort' => 'latest', 'type' => $type, 'search' => $search]) }}">Newest First</a></li>
                    <li><a class="dropdown-item {{ $sort === 'oldest' ? 'active fw-bold' : '' }}" href="{{ route('admin.media.index', ['tab' => $tab, 'sort' => 'oldest', 'type' => $type, 'search' => $search]) }}">Oldest First</a></li>
                    <li><a class="dropdown-item {{ $sort === 'size_desc' ? 'active fw-bold' : '' }}" href="{{ route('admin.media.index', ['tab' => $tab, 'sort' => 'size_desc', 'type' => $type, 'search' => $search]) }}">Largest Size</a></li>
                    <li><a class="dropdown-item {{ $sort === 'size_asc' ? 'active fw-bold' : '' }}" href="{{ route('admin.media.index', ['tab' => $tab, 'sort' => 'size_asc', 'type' => $type, 'search' => $search]) }}">Smallest Size</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Media Grid: STRICT 4 COLUMNS ON DESKTOP (col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12) -->
    <div class="row g-4" id="mediaAssetGrid">
        @forelse($mediaAssets as $media)
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 media-item-col" data-id="{{ $media->id }}">
            <div class="card border rounded-4 overflow-hidden h-100 shadow-xs media-vault-card transition-all position-relative bg-body" id="mediaCard{{ $media->id }}">
                
                <!-- Multiselect Checkbox Overlay (Top Right) -->
                <div class="media-select-wrap position-absolute top-0 end-0 m-2.5 z-3" onclick="event.stopPropagation();">
                    <input type="checkbox" class="form-check-input media-checkbox shadow-sm cursor-pointer" value="{{ $media->id }}" id="mediaCheck{{ $media->id }}" aria-label="Select asset {{ $media->original_name }}">
                </div>

                <!-- Thumbnail Container with Safe Position Fixed Badges (NO .ratio stretching bug) -->
                <div class="media-thumb-container position-relative overflow-hidden cursor-pointer" style="height: 220px; background: rgba(0,0,0,0.03);" onclick="openMediaLightbox('{{ $media->url }}', '{{ addslashes($media->original_name) }}', '{{ $media->dimensions_label ?? 'Standard' }}', '{{ $media->formatted_size }}', '{{ $media->extension }}')">
                    <img src="{{ $media->url }}" alt="{{ $media->alt_text }}" class="w-100 h-100 object-fit-cover media-thumb-img transition-all" loading="lazy" onerror="this.src='{{ asset('assets/admin/images/image-placeholder.png') }}';">

                    <!-- Format Pill Badge (Top Left Corner) -->
                    <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white extra-small rounded-pill px-2.5 py-1 font-monospace shadow-xs media-corner-badge">
                        {{ $media->extension }}
                    </span>

                    <!-- Dimensions Pill Badge (Bottom Right Corner) -->
                    @if($media->dimensions)
                        <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 text-white extra-small rounded-pill px-2.5 py-1 font-monospace shadow-xs media-corner-badge">
                            {{ $media->dimensions }}
                        </span>
                    @endif

                    <!-- Clean Hover Overlay with Zoom Icon (Fades in ONLY on mouse hover) -->
                    <div class="media-hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-40 opacity-0 transition-all pointer-events-none">
                        <span class="btn btn-sm btn-light rounded-circle shadow p-2" title="Inspect Fullscreen">
                            <i class="bi bi-arrows-fullscreen text-dark"></i>
                        </span>
                    </div>
                </div>

                <!-- Card Body & Action Controls -->
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="mb-2">
                        <span class="small fw-bold text-body text-truncate d-block" title="{{ $media->original_name }}">
                            {{ $media->original_name }}
                        </span>
                        <div class="d-flex align-items-center justify-content-between extra-small text-muted mt-1">
                            <span>{{ $media->formatted_size }}</span>
                            <span>{{ $media->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center gap-1.5 pt-2 border-top">
                        @if($tab === 'trash')
                            <!-- Restore Button -->
                            <form action="{{ route('admin.media.restore', $media->id) }}" method="POST" class="d-inline flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm rounded-pill w-100 py-1 extra-small fw-semibold">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                </button>
                            </form>

                            <!-- Permanent Purge Button -->
                            <form action="{{ route('admin.media.force-delete', $media->id) }}" method="POST" class="d-inline delete-media-form flex-shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-light btn-sm rounded-circle border shadow-xs d-inline-flex align-items-center justify-content-center text-danger purge-single-btn" style="width: 30px; height: 30px;" title="Permanently Erase" data-name="{{ $media->original_name }}">
                                    <i class="bi bi-trash3-fill extra-small"></i>
                                </button>
                            </form>
                        @else
                            <!-- Copy Link -->
                            <button type="button" class="btn btn-light btn-sm rounded-pill border px-2 py-1 extra-small fw-semibold text-body flex-grow-1 text-truncate" onclick="copyMediaUrl('{{ $media->url }}')" title="Copy Direct URL">
                                <i class="bi bi-link-45deg me-1 text-primary"></i> Copy Link
                            </button>

                            <!-- Download High-Res -->
                            <a href="{{ $media->url }}" target="_blank" download class="btn btn-light btn-sm rounded-circle border shadow-xs d-inline-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 30px; height: 30px;" title="Download High-Res">
                                <i class="bi bi-download extra-small"></i>
                            </a>

                            <!-- Soft Delete / Move to Trash -->
                            <form action="{{ route('admin.media.destroy', $media->id) }}" method="POST" class="d-inline delete-media-form flex-shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-light btn-sm rounded-circle border shadow-xs d-inline-flex align-items-center justify-content-center text-danger trash-single-btn" style="width: 30px; height: 30px;" title="Move to Trash" data-name="{{ $media->original_name }}">
                                    <i class="bi bi-trash3 extra-small"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center text-muted">
            <div class="py-5">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-body-secondary mb-3 shadow-xs" style="width: 80px; height: 80px;">
                    <i class="bi bi-images fs-1 text-muted opacity-50"></i>
                </div>
                <h5 class="fw-bold text-body mb-1">
                    {{ $tab === 'trash' ? 'Trash bin is empty' : 'No media assets found' }}
                </h5>
                <p class="extra-small text-muted mb-4">
                    @if($tab === 'trash')
                        No deleted imagery currently staged in the recovery vault.
                    @elseif($search || $type)
                        No imagery matched your current search or format filters.
                    @else
                        Upload photography using the drag &amp; drop zone above to populate the digital vault.
                    @endif
                </p>
                @if($search || $type)
                    <a href="{{ route('admin.media.index', ['tab' => $tab]) }}" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-bold">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                    </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    @if($mediaAssets->hasPages())
    <div class="card-footer bg-transparent py-4 border-top d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
        <span class="extra-small text-muted">
            Showing {{ $mediaAssets->firstItem() }} to {{ $mediaAssets->lastItem() }} of {{ $mediaAssets->total() }} assets
        </span>
        {{ $mediaAssets->links() }}
    </div>
    @endif
</div>

<!-- Floating Luxury Multiselect Bulk Action Toolbar -->
<div class="media-bulk-bar shadow-2xl rounded-pill p-2 px-4 bg-dark text-white d-none align-items-center justify-content-between gap-3 position-fixed bottom-0 start-50 translate-middle-x mb-4 z-5" id="mediaBulkBar">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-gold text-dark fw-black rounded-pill px-2.5 py-1 extra-small" id="selectedCountBadge">0</span>
        <span class="extra-small fw-bold text-light">assets selected</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if($tab === 'trash')
            <button type="button" class="btn btn-success btn-sm rounded-pill px-3 py-1 extra-small fw-bold" id="bulkRestoreTriggerBtn">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Restore Selected
            </button>
            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 py-1 extra-small fw-bold" id="bulkPurgeTriggerBtn">
                <i class="bi bi-trash3-fill me-1"></i> Purge Selected
            </button>
        @else
            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 py-1 extra-small fw-bold" id="bulkDeleteTriggerBtn">
                <i class="bi bi-trash3 me-1"></i> Move Selected to Trash
            </button>
        @endif
        <button type="button" class="btn btn-light btn-sm rounded-pill px-3 py-1 extra-small fw-semibold" id="deselectAllBtn">
            Deselect
        </button>
    </div>
</div>

<!-- Hidden Bulk Action Forms -->
<form action="{{ route('admin.media.bulk-destroy') }}" method="POST" id="bulkDestroyForm" class="d-none">
    @csrf
    <div id="bulkDestroyInputs"></div>
</form>

<form action="{{ route('admin.media.bulk-restore') }}" method="POST" id="bulkRestoreForm" class="d-none">
    @csrf
    <div id="bulkRestoreInputs"></div>
</form>

<form action="{{ route('admin.media.bulk-force-delete') }}" method="POST" id="bulkForceDeleteForm" class="d-none">
    @csrf
    <div id="bulkForceDeleteInputs"></div>
</form>

<!-- Fullscreen Media Lightbox Modal -->
<div class="modal fade" id="mediaLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content rounded-4 border-0 shadow-2xl bg-dark text-white overflow-hidden">
            <div class="modal-header border-0 py-3 px-4 bg-dark bg-opacity-75 d-flex justify-content-between align-items-center">
                <div class="overflow-hidden pe-3">
                    <h6 class="modal-title fw-bold text-light mb-0 text-truncate" id="lightboxMediaTitle">Asset Preview</h6>
                    <div class="extra-small text-muted" id="lightboxMediaMeta">Resolution: -- &bull; Size: --</div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3 py-1 extra-small fw-semibold" id="lightboxCopyBtn" onclick="copyLightboxUrl()">
                        <i class="bi bi-link-45deg me-1"></i> Copy URL
                    </button>
                    <a href="#" id="lightboxDownloadBtn" download class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 extra-small">
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 text-center position-relative d-flex align-items-center justify-content-center bg-black" style="min-height: 480px; max-height: 80vh;">
                <img src="" alt="" id="lightboxMediaImg" class="img-fluid object-fit-contain w-100" style="max-height: 78vh;">
            </div>
        </div>
    </div>
</div>

<style>
    /* Fixed Safe Badges (Never stretch into circles) */
    .media-corner-badge {
        width: auto !important;
        height: auto !important;
        max-width: max-content !important;
        display: inline-block !important;
        border-radius: 50px !important;
        font-size: 0.68rem !important;
        letter-spacing: 0.5px;
        z-index: 2;
    }

    /* Media Vault Card Styling */
    .media-vault-card {
        border-color: rgba(0, 0, 0, 0.08) !important;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease !important;
    }
    [data-bs-theme="dark"] .media-vault-card {
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    .media-vault-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px rgba(0,0,0,0.1) !important;
        border-color: var(--elite-primary, #6366f1) !important;
    }
    .media-vault-card:hover .media-hover-overlay {
        opacity: 1 !important;
    }
    .media-vault-card:hover .media-thumb-img {
        transform: scale(1.04);
    }
    .media-vault-card.is-selected {
        border-color: var(--elite-primary, #6366f1) !important;
        box-shadow: 0 0 0 2px var(--elite-primary, #6366f1), 0 8px 20px rgba(99, 102, 241, 0.2) !important;
        background: rgba(99, 102, 241, 0.03) !important;
    }

    /* Custom Checkbox on thumbnail */
    .media-checkbox {
        width: 22px !important;
        height: 22px !important;
        border-radius: 6px !important;
        border: 2px solid #ffffff !important;
        background-color: rgba(0, 0, 0, 0.45);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .media-checkbox:checked {
        background-color: var(--elite-primary, #6366f1) !important;
        border-color: #ffffff !important;
    }

    /* Floating Multiselect Bar */
    .media-bulk-bar {
        border: 1px solid rgba(212, 175, 55, 0.35);
        backdrop-filter: blur(12px);
        background: rgba(15, 23, 42, 0.95) !important;
        z-index: 1040 !important;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4) !important;
    }

    .upload-dropzone {
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        border-color: rgba(99, 102, 241, 0.3) !important;
    }
    .upload-dropzone:hover,
    .upload-dropzone.drag-active {
        border-color: var(--elite-primary, #6366f1) !important;
        background: rgba(99, 102, 241, 0.05) !important;
        transform: translateY(-2px);
    }
    .transition-all {
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .pointer-events-none {
        pointer-events: none;
    }
</style>

@push('scripts')
<script>
let currentLightboxUrl = '';

function copyMediaUrl(url) {
    navigator.clipboard.writeText(url).then(function() {
        if (window.EliteAlert) {
            window.EliteAlert.toast('Direct asset URL copied to clipboard!', 'success');
        } else if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'URL copied to clipboard!',
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            alert('URL copied to clipboard!');
        }
    });
}

function copyLightboxUrl() {
    if (currentLightboxUrl) {
        copyMediaUrl(currentLightboxUrl);
    }
}

function openMediaLightbox(url, title, dimensions, size, ext) {
    currentLightboxUrl = url;
    const modalEl = document.getElementById('mediaLightboxModal');
    const imgEl = document.getElementById('lightboxMediaImg');
    const titleEl = document.getElementById('lightboxMediaTitle');
    const metaEl = document.getElementById('lightboxMediaMeta');
    const downloadBtn = document.getElementById('lightboxDownloadBtn');

    if (imgEl && modalEl) {
        imgEl.src = url;
        if (titleEl) titleEl.textContent = title || 'Asset Preview';
        if (metaEl) metaEl.textContent = `${dimensions} • ${size} • ${ext}`;
        if (downloadBtn) downloadBtn.href = url;
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // -------------------------------------------------------------
    // 1. Multiselect Checkbox Logic
    // -------------------------------------------------------------
    const checkboxes = document.querySelectorAll('.media-checkbox');
    const bulkBar = document.getElementById('mediaBulkBar');
    const countBadge = document.getElementById('selectedCountBadge');
    const selectAllBtn = document.getElementById('selectAllToggleBtn');
    const selectAllBtnText = document.getElementById('selectAllBtnText');
    const deselectBtn = document.getElementById('deselectAllBtn');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.media-checkbox:checked');
        const count = checked.length;

        if (countBadge) countBadge.textContent = count;

        if (count > 0) {
            if (bulkBar) {
                bulkBar.classList.remove('d-none');
                bulkBar.classList.add('d-flex');
            }
            if (selectAllBtnText) selectAllBtnText.textContent = (count === checkboxes.length) ? 'Deselect All' : 'Select All';
        } else {
            if (bulkBar) {
                bulkBar.classList.add('d-none');
                bulkBar.classList.remove('d-flex');
            }
            if (selectAllBtnText) selectAllBtnText.textContent = 'Select All';
        }

        // Highlight selected cards
        checkboxes.forEach(cb => {
            const card = document.getElementById('mediaCard' + cb.value);
            if (card) {
                if (cb.checked) {
                    card.classList.add('is-selected');
                } else {
                    card.classList.remove('is-selected');
                }
            }
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const checked = document.querySelectorAll('.media-checkbox:checked');
            const shouldCheck = (checked.length !== checkboxes.length);
            checkboxes.forEach(cb => cb.checked = shouldCheck);
            updateBulkBar();
        });
    }

    if (deselectBtn) {
        deselectBtn.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = false);
            updateBulkBar();
        });
    }

    function populateBulkInputs(containerId, formId) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        const checked = document.querySelectorAll('.media-checkbox:checked');
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
    }

    // Bulk Soft-Delete Trigger
    const bulkDeleteBtn = document.getElementById('bulkDeleteTriggerBtn');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const count = document.querySelectorAll('.media-checkbox:checked').length;
            if (count === 0) return;

            const confirmMsg = `Move ${count} selected media assets to the trash bin?`;
            const action = function() {
                populateBulkInputs('bulkDestroyInputs', 'bulkDestroyForm');
                document.getElementById('bulkDestroyForm').submit();
            };

            if (window.EliteAlert) {
                window.EliteAlert.confirm('Move Assets to Trash?', confirmMsg, 'Move to Trash', action);
            } else if (confirm(confirmMsg)) {
                action();
            }
        });
    }

    // Bulk Restore Trigger
    const bulkRestoreBtn = document.getElementById('bulkRestoreTriggerBtn');
    if (bulkRestoreBtn) {
        bulkRestoreBtn.addEventListener('click', function() {
            const count = document.querySelectorAll('.media-checkbox:checked').length;
            if (count === 0) return;

            const confirmMsg = `Restore ${count} selected media assets back to active library?`;
            const action = function() {
                populateBulkInputs('bulkRestoreInputs', 'bulkRestoreForm');
                document.getElementById('bulkRestoreForm').submit();
            };

            if (window.EliteAlert) {
                window.EliteAlert.confirm('Restore Assets?', confirmMsg, 'Restore Assets', action);
            } else if (confirm(confirmMsg)) {
                action();
            }
        });
    }

    // Bulk Purge Trigger
    const bulkPurgeBtn = document.getElementById('bulkPurgeTriggerBtn');
    if (bulkPurgeBtn) {
        bulkPurgeBtn.addEventListener('click', function() {
            const count = document.querySelectorAll('.media-checkbox:checked').length;
            if (count === 0) return;

            const confirmMsg = `PERMANENTLY erase ${count} selected media assets and their files from the disk? This action cannot be reversed!`;
            const action = function() {
                populateBulkInputs('bulkForceDeleteInputs', 'bulkForceDeleteForm');
                document.getElementById('bulkForceDeleteForm').submit();
            };

            if (window.EliteAlert) {
                window.EliteAlert.confirm('Permanently Purge Assets?', confirmMsg, 'Yes, Permanently Purge', action);
            } else if (confirm(confirmMsg)) {
                action();
            }
        });
    }

    // -------------------------------------------------------------
    // 2. Single Item Delete Confirmations
    // -------------------------------------------------------------
    document.querySelectorAll('.trash-single-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.delete-media-form');
            const name = this.getAttribute('data-name') || 'this asset';

            if (window.EliteAlert) {
                window.EliteAlert.confirm(
                    'Move to Trash?',
                    `Move "${name}" to the trash bin? It can be restored later.`,
                    'Move to Trash',
                    function() { form.submit(); }
                );
            } else if (confirm(`Move "${name}" to trash?`)) {
                form.submit();
            }
        });
    });

    document.querySelectorAll('.purge-single-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.delete-media-form');
            const name = this.getAttribute('data-name') || 'this asset';

            if (window.EliteAlert) {
                window.EliteAlert.confirm(
                    'Permanently Erase Asset?',
                    `Permanently delete "${name}" and erase its file from disk? This cannot be undone.`,
                    'Permanently Delete',
                    function() { form.submit(); }
                );
            } else if (confirm(`Permanently erase "${name}"?`)) {
                form.submit();
            }
        });
    });

    // -------------------------------------------------------------
    // 3. Drag & Drop Visual Handling
    // -------------------------------------------------------------
    const fileInput = document.getElementById('mediaFileInput');
    const dropZone = document.getElementById('dropZoneContainer');
    const summary = document.getElementById('selectedFilesSummary');
    const uploadForm = document.getElementById('mediaUploadForm');
    const submitBtn = document.getElementById('submitUploadBtn');

    if (fileInput && dropZone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, e => {
                e.preventDefault();
                dropZone.classList.add('drag-active');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, e => {
                e.preventDefault();
                dropZone.classList.remove('drag-active');
            }, false);
        });

        fileInput.addEventListener('change', function() {
            const files = Array.from(this.files || []);
            if (files.length === 0) {
                summary.innerHTML = '';
                return;
            }
            const totalBytes = files.reduce((acc, f) => acc + f.size, 0);
            const totalMb = (totalBytes / (1024 * 1024)).toFixed(2);
            summary.innerHTML = `
                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1-5">
                    <i class="bi bi-check2-circle me-1"></i> ${files.length} ${files.length === 1 ? 'file' : 'files'} selected (${totalMb} MB) &bull; Ready to upload
                </span>`;
        });
    }

    if (uploadForm && submitBtn) {
        uploadForm.addEventListener('submit', function() {
            if (fileInput && fileInput.files.length > 0) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Uploading to Vault...';
            }
        });
    }
});
</script>
@endpush

@endsection
