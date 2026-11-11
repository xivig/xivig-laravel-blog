@extends('admin.layouts.admin')

@section('title', 'Gallery Management | XIVIG Maison Gazette')
@section('page_title', 'Gallery Management')

@section('content')


<!-- Controls Bar: Tabs & Create Action -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <!-- Active / Trash Tab Pills -->
    <div class="d-inline-flex p-1 bg-body rounded-pill border shadow-xs">
        <a href="{{ route('admin.galleries.index', ['tab' => 'active']) }}" class="btn btn-sm rounded-pill px-4 fw-bold {{ $tab !== 'trash' ? 'btn-dark text-white' : 'btn-light text-muted border-0' }}">
            Active <span class="badge bg-secondary bg-opacity-25 ms-1">{{ $activeCount }}</span>
        </a>
        <a href="{{ route('admin.galleries.index', ['tab' => 'trash']) }}" class="btn btn-sm rounded-pill px-4 fw-bold {{ $tab === 'trash' ? 'btn-dark text-white' : 'btn-light text-muted border-0' }}">
            <i class="bi bi-trash3 me-1"></i> Trash <span class="badge bg-secondary bg-opacity-25 ms-1">{{ $trashCount }}</span>
        </a>
    </div>

    <!-- Create Button -->
    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createGalleryModal">
        <i class="bi bi-plus-lg"></i> Create New Gallery
    </button>
</div>

<!-- Galleries Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold letter-spacing-1 border-0">
                <tr>
                    <th class="ps-4 py-3" style="min-width: 240px;">GALLERY TITLE</th>
                    <th class="py-3" style="min-width: 140px;">ASSETS</th>
                    <th class="py-3" style="min-width: 120px;">STATUS</th>
                    <th class="py-3" style="min-width: 160px;">CREATED AT</th>
                    <th class="py-3 text-end pe-4" style="min-width: 130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($galleries as $gallery)
                <tr>
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('admin.galleries.show', $gallery->id) }}" class="story-thumb-box shadow-xs text-decoration-none flex-shrink-0" title="Inspect Gallery Assets">
                                @if($gallery->cover_image)
                                    <img src="{{ asset(ltrim($gallery->cover_image, '/')) }}" alt="" class="story-thumb-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-100 h-100 d-none align-items-center justify-content-center text-primary bg-primary bg-opacity-10">
                                        <i class="bi bi-images fs-5"></i>
                                    </div>
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10">
                                        <i class="bi bi-images fs-5"></i>
                                    </div>
                                @endif
                            </a>
                            <div>
                                <a href="{{ route('admin.galleries.show', $gallery->id) }}" class="fw-bold text-body text-decoration-none d-block hover-primary">
                                    {{ $gallery->title }}
                                </a>
                                <span class="extra-small text-muted font-monospace">/{{ $gallery->slug }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3">
                        <a href="{{ route('admin.galleries.show', $gallery->id) }}" class="badge bg-body-tertiary text-body border rounded-pill px-3 py-1 extra-small fw-semibold text-decoration-none shadow-xs hover-shadow" title="View all photos">
                            <i class="bi bi-images text-primary me-1"></i> {{ $gallery->items_count }} Assets
                        </a>
                    </td>
                    <td class="py-3">
                        @if($gallery->status === 'active')
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                                Active
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                                Draft
                            </span>
                        @endif
                    </td>
                    <td class="py-3 text-muted extra-small">
                        <i class="bi bi-calendar3 me-1"></i> {{ $gallery->created_at->format('M d, Y') }}
                    </td>
                    <td class="py-3 text-end pe-4">
                        @if($tab === 'trash')
                            <div class="d-inline-flex align-items-center gap-1">
                                <form action="{{ route('admin.galleries.restore', $gallery->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 extra-small">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                    </button>
                                </form>
                                <form action="{{ route('admin.galleries.force-delete', $gallery->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently erase this gallery and all photographs?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 py-1 extra-small">
                                        <i class="bi bi-trash3-fill me-1"></i> Purge
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ route('admin.galleries.show', $gallery->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 py-1 extra-small fw-bold d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-images"></i> View Photos
                                </a>
                                <button type="button" class="btn btn-light btn-sm rounded-pill border px-3 py-1 extra-small text-body fw-semibold" data-bs-toggle="modal" data-bs-target="#editGalleryModal{{ $gallery->id }}">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </button>
                                <form action="{{ route('admin.galleries.destroy', $gallery->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Move gallery to trash?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm rounded-circle border shadow-xs d-inline-flex align-items-center justify-content-center text-danger" style="width: 28px; height: 28px;" title="Trash Gallery">
                                        <i class="bi bi-trash3 extra-small"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </td>
                </tr>

                <!-- Edit Gallery Modal -->
                @if($tab !== 'trash')
                <div class="modal fade" id="editGalleryModal{{ $gallery->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <form action="{{ route('admin.galleries.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-bottom py-3 px-4">
                                    <h5 class="modal-title fw-bold text-body">
                                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Gallery
                                    </h5>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Gallery Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" value="{{ $gallery->title }}" required class="form-control rounded-3">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Description / Atelier Narrative</label>
                                        <textarea name="description" rows="3" class="form-control rounded-3">{{ $gallery->description }}</textarea>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-muted">Publication Status</label>
                                            <select name="status" class="form-select rounded-3">
                                                <option value="active" {{ $gallery->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="draft" {{ $gallery->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-muted">Cover Photo</label>
                                            <input type="file" name="cover_image" class="form-control rounded-3" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Add Additional Photos (Batch)</label>
                                        <input type="file" name="images[]" multiple class="form-control rounded-3 gallery-images-input" accept="image/*">
                                        <div class="file-size-feedback mt-2 extra-small"></div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top py-3 px-4">
                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                @empty
                <!-- Empty State Matching Image 3 -->
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="py-5">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-4 bg-body-secondary mb-3 shadow-xs" style="width: 80px; height: 80px;">
                                <i class="bi bi-folder2-open fs-1 text-muted opacity-50"></i>
                            </div>
                            <h6 class="fw-bold text-muted mb-1">No galleries found.</h6>
                            <p class="extra-small text-muted mb-4">Curate high-resolution photo collections for your editorial pieces.</p>
                            @if($tab !== 'trash')
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#createGalleryModal">
                                    <i class="bi bi-plus-lg me-1"></i> Create First Gallery
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($galleries->hasPages())
        <div class="card-footer bg-transparent py-3 px-4 border-top d-flex justify-content-between align-items-center">
            <span class="extra-small text-muted">Showing {{ $galleries->firstItem() }} to {{ $galleries->lastItem() }} of {{ $galleries->total() }} galleries</span>
            {{ $galleries->links() }}
        </div>
    @endif
</div>

<!-- Create New Gallery Modal -->
<div class="modal fade" id="createGalleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-body">
                        <i class="bi bi-images text-primary me-2"></i> Create New Gallery
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Gallery Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" required placeholder="e.g. Haute Couture Paris Fashion Week" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Description / Atelier Narrative</label>
                        <textarea name="description" rows="3" placeholder="Context and description of the imagery collection..." class="form-control rounded-3"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Publication Status</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="active" selected>Active</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Cover Photo</label>
                            <input type="file" name="cover_image" class="form-control rounded-3" accept="image/*">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Gallery Photos (Select Multiple)</label>
                        <input type="file" name="images[]" multiple class="form-control rounded-3 gallery-images-input" accept="image/*">
                        <div class="file-size-feedback mt-2 extra-small"></div>
                        <div class="form-text extra-small">Supports JPG, PNG, WEBP, AVIF up to 15MB per image.</div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold submit-gallery-btn">Create Gallery</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Monitor file selections in gallery modals
    document.querySelectorAll('.gallery-images-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const feedback = this.closest('.mb-3').querySelector('.file-size-feedback');
            if (!feedback) return;

            const files = Array.from(this.files || []);
            if (files.length === 0) {
                feedback.innerHTML = '';
                return;
            }

            const totalBytes = files.reduce((acc, f) => acc + f.size, 0);
            const totalMb = totalBytes / (1024 * 1024);

            if (totalMb > 50) {
                feedback.innerHTML = `
                    <div class="alert alert-warning py-2 px-3 rounded-3 mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-6"></i>
                        <span>Selected <strong>${files.length}</strong> files (${formatBytes(totalBytes)}). Large batch will be automatically optimized for rapid upload.</span>
                    </div>`;
            } else {
                feedback.innerHTML = `
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                        <i class="bi bi-check2-circle me-1"></i> ${files.length} ${files.length === 1 ? 'photo' : 'photos'} selected (${formatBytes(totalBytes)}) &bull; Ready to upload
                    </span>`;
            }
        });
    });

    // Form submit loading indicator
    document.querySelectorAll('#createGalleryModal form, [id^="editGalleryModal"] form').forEach(function(form) {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Uploading Assets...';
            }
        });
    });
});
</script>
@endpush

@endsection
