@extends('admin.layouts.admin')

@section('title', $gallery->title . ' - Gallery Showcase | XIVIG Maison Gazette')
@section('page_title', 'Gallery Showcase')

@section('content')

<!-- Header Breadcrumb Nav Strip -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-center bg-body position-relative overflow-hidden" style="border-bottom: 3px solid var(--elite-primary, #6366f1) !important;">
    <h3 class="fw-black text-primary mb-1">{{ $gallery->title }}</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center mb-0 extra-small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.galleries.index') }}" class="text-decoration-none text-muted">Gallery Management</a></li>
            <li class="breadcrumb-item active text-primary" aria-current="page">{{ Str::limit($gallery->title, 35) }}</li>
        </ol>
    </nav>
</div>

<!-- Gallery Meta Overview Card -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-body">
    <div class="row align-items-center g-4">
        <div class="col-md-auto text-center text-md-start">
            <div class="rounded-4 overflow-hidden shadow-sm mx-auto position-relative bg-body-secondary" style="width: 120px; height: 120px;">
                @if($gallery->cover_image)
                    <img src="{{ asset(ltrim($gallery->cover_image, '/')) }}" alt="{{ $gallery->title }}" class="w-100 h-100 object-fit-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-100 h-100 d-none align-items-center justify-content-center text-primary bg-primary bg-opacity-10">
                        <i class="bi bi-images fs-1"></i>
                    </div>
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10">
                        <i class="bi bi-images fs-1"></i>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <h4 class="fw-black text-body mb-0">{{ $gallery->title }}</h4>
                @if($gallery->status === 'active')
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">Active</span>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">Draft</span>
                @endif
            </div>
            <p class="text-muted small mb-3" style="max-width: 680px;">
                {{ $gallery->description ?: 'No narrative description provided for this collection.' }}
            </p>
            <div class="d-flex flex-wrap align-items-center gap-3 extra-small text-muted">
                <span><i class="bi bi-image text-primary me-1"></i><strong>{{ $gallery->items->count() }}</strong> photographs</span>
                <span>&bull;</span>
                <span><i class="bi bi-link-45deg me-1"></i><span class="font-monospace">/{{ $gallery->slug }}</span></span>
                <span>&bull;</span>
                <span><i class="bi bi-calendar3 me-1"></i>Created {{ $gallery->created_at->format('M d, Y') }}</span>
            </div>
        </div>
        <div class="col-md-auto text-end d-flex flex-column flex-sm-row gap-2">
            <a href="{{ route('admin.galleries.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-semibold border shadow-xs">
                <i class="bi bi-arrow-left me-1"></i> Back to Galleries
            </a>
            <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addPhotosModal">
                <i class="bi bi-cloud-arrow-up-fill"></i> Upload More Photos
            </button>
        </div>
    </div>
</div>

<!-- Photos Grid -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-body">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-black text-body mb-1">Curated Photographs</h5>
            <p class="text-muted extra-small mb-0">Click any photograph to inspect in high-resolution lightbox or manage asset assignments.</p>
        </div>
        <span class="badge bg-body-secondary text-body border rounded-pill px-3 py-1 extra-small fw-semibold">
            Showing all {{ $gallery->items->count() }} assets
        </span>
    </div>

    @if($gallery->items->count() > 0)
        <div class="row g-4" id="galleryPhotoGrid">
            @foreach($gallery->items as $index => $item)
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="card border rounded-4 overflow-hidden h-100 shadow-xs photo-card transition-all position-relative bg-body">
                    <!-- Photo Header Thumbnail -->
                    <div class="position-relative overflow-hidden cursor-pointer" style="height: 220px; background: rgba(0,0,0,0.04);" onclick="openLightbox('{{ asset(ltrim($item->image_path, '/')) }}', '{{ addslashes($item->title ?? 'Photograph #' . ($index + 1)) }}')">
                        <img src="{{ asset(ltrim($item->image_path, '/')) }}" alt="{{ $item->title }}" class="w-100 h-100 object-fit-cover photo-thumb-hover transition-all" onerror="this.src='{{ asset('assets/admin/images/image-placeholder.png') }}';">
                        
                        <!-- Order Badge -->
                        <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white extra-small rounded-pill px-2 py-1 font-monospace">
                            #{{ $index + 1 }}
                        </span>

                        <!-- Cover Badge -->
                        @if($gallery->cover_image === $item->image_path)
                            <span class="position-absolute top-0 end-0 m-2 badge bg-gold text-dark extra-small rounded-pill px-2.5 py-1 fw-bold shadow-xs">
                                <i class="bi bi-star-fill me-1"></i> Cover Photo
                            </span>
                        @endif

                        <!-- Hover Overlay with Zoom Icon -->
                        <div class="photo-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-40 opacity-0 transition-all">
                            <span class="btn btn-sm btn-light rounded-circle shadow p-2" title="View Fullscreen">
                                <i class="bi bi-arrows-fullscreen fs-6 text-dark"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Photo Card Details & Actions -->
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="mb-2">
                            <span class="small fw-bold text-body text-truncate d-block" title="{{ $item->title ?? 'Photograph' }}">
                                {{ $item->title ?? 'Photograph #' . ($index + 1) }}
                            </span>
                            <span class="extra-small text-muted font-monospace d-block text-truncate">
                                {{ basename($item->image_path) }}
                            </span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-1">
                            <!-- Set Cover Button -->
                            @if($gallery->cover_image !== $item->image_path)
                                <form action="{{ route('admin.galleries.items.cover', [$gallery->id, $item->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-light btn-sm rounded-pill border px-2 py-0.5 extra-small text-muted" title="Set as Gallery Cover">
                                        <i class="bi bi-star me-1 text-gold"></i> Set Cover
                                    </button>
                                </form>
                            @else
                                <span class="extra-small text-gold fw-bold">
                                    <i class="bi bi-check2-circle me-1"></i> Main Cover
                                </span>
                            @endif

                            <div class="d-inline-flex align-items-center gap-1">
                                <!-- Direct Download / Open -->
                                <a href="{{ asset(ltrim($item->image_path, '/')) }}" target="_blank" download class="btn btn-light btn-sm rounded-circle border shadow-xs d-inline-flex align-items-center justify-content-center text-primary" style="width: 28px; height: 28px;" title="Download High-Res">
                                    <i class="bi bi-download extra-small"></i>
                                </a>

                                <!-- Delete Photo Button -->
                                <form action="{{ route('admin.galleries.items.destroy', [$gallery->id, $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this photograph from the gallery?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm rounded-circle border shadow-xs d-inline-flex align-items-center justify-content-center text-danger" style="width: 28px; height: 28px;" title="Delete Photograph">
                                        <i class="bi bi-trash3 extra-small"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <div class="py-5">
                <div class="d-inline-flex align-items-center justify-content-center rounded-4 bg-body-secondary mb-3 shadow-xs" style="width: 80px; height: 80px;">
                    <i class="bi bi-images fs-1 text-muted opacity-50"></i>
                </div>
                <h5 class="fw-bold text-body mb-1">No photographs uploaded yet.</h5>
                <p class="text-muted extra-small mb-4">Curate this editorial collection by uploading high-resolution imagery.</p>
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-primary" data-bs-toggle="modal" data-bs-target="#addPhotosModal">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload First Photos
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Upload Additional Photos Modal -->
<div class="modal fade" id="addPhotosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form action="{{ route('admin.galleries.items.store', $gallery->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-body">
                        <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i> Add Gallery Photos
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Select Photographs (Batch)</label>
                        <input type="file" name="images[]" multiple required class="form-control rounded-3 gallery-images-input" accept="image/*">
                        <div class="file-size-feedback mt-2 extra-small"></div>
                        <div class="form-text extra-small">Supports JPG, PNG, WEBP, AVIF up to 15MB each.</div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Upload Photographs</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fullscreen Photo Lightbox Modal -->
<div class="modal fade" id="photoLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content rounded-4 border-0 shadow-2xl bg-dark text-white overflow-hidden">
            <div class="modal-header border-0 py-3 px-4 bg-dark bg-opacity-75 d-flex justify-content-between align-items-center">
                <h6 class="modal-title fw-bold text-light mb-0" id="lightboxTitle">Photograph Preview</h6>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="lightboxDownloadBtn" download class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 extra-small">
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 text-center position-relative d-flex align-items-center justify-content-center bg-black" style="min-height: 480px; max-height: 80vh;">
                <img src="" alt="" id="lightboxImg" class="img-fluid object-fit-contain w-100" style="max-height: 78vh;">
            </div>
        </div>
    </div>
</div>

<style>
    .photo-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important;
        border-color: var(--elite-primary, #6366f1) !important;
    }
    .photo-card:hover .photo-overlay {
        opacity: 1 !important;
    }
    .photo-card:hover .photo-thumb-hover {
        transform: scale(1.04);
    }
    .transition-all {
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>

@push('scripts')
<script>
function openLightbox(url, title) {
    const modalEl = document.getElementById('photoLightboxModal');
    const imgEl = document.getElementById('lightboxImg');
    const titleEl = document.getElementById('lightboxTitle');
    const downloadBtn = document.getElementById('lightboxDownloadBtn');

    if (imgEl && modalEl) {
        imgEl.src = url;
        if (titleEl) titleEl.textContent = title || 'Photograph Preview';
        if (downloadBtn) downloadBtn.href = url;
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // File size calculator for upload modal
    const input = document.querySelector('#addPhotosModal .gallery-images-input');
    if (input) {
        input.addEventListener('change', function() {
            const feedback = this.closest('.mb-3').querySelector('.file-size-feedback');
            if (!feedback) return;
            const files = Array.from(this.files || []);
            if (files.length === 0) {
                feedback.innerHTML = '';
                return;
            }
            const totalBytes = files.reduce((acc, f) => acc + f.size, 0);
            const totalMb = (totalBytes / (1024 * 1024)).toFixed(2);
            feedback.innerHTML = `
                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                    <i class="bi bi-check2-circle me-1"></i> ${files.length} photos selected (${totalMb} MB) &bull; Ready to upload
                </span>`;
        });
    }
});
</script>
@endpush

@endsection
