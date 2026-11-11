@extends('admin.layouts.admin')

@section('title', 'Compose Editorial Chronicle | XIVIG Maison Gazette')
@section('page_title', 'Compose New Chronicle')

@section('content')
<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- Left: Main Editorial Editor -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="mb-3">
                    <label class="form-label fw-bold text-body">Chronicle Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="form-control form-control-lg rounded-3" placeholder="e.g. The Architecture of Horological Tourbillons...">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Slug (URL Path)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="form-control form-control-sm rounded-3" placeholder="Leave blank to auto-generate from title">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Editorial Excerpt / Synopsis</label>
                    <textarea name="excerpt" rows="2" class="form-control form-control-sm rounded-3" placeholder="A concise 1-2 sentence synopsis for curation grids...">{{ old('excerpt') }}</textarea>
                </div>

                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-bold text-body mb-0">Chronicle Body Content <span class="text-danger">*</span></label>
                        <span class="badge bg-gold-subtle text-gold extra-small rounded-pill">
                            <i class="bi bi-magic me-1"></i> WYSIWYG Suite
                        </span>
                    </div>
                    <textarea name="content" id="postContentEditor" rows="16" required class="form-control rounded-3 wysiwyg-editor" placeholder="Compose rich editorial chronicle content...">{{ old('content') }}</textarea>
                    <div class="extra-small text-muted mt-2">
                        <i class="bi bi-shield-check text-success me-1"></i> Rich typography, formatted headings, tables, links, and drag &amp; drop image uploads enabled.
                    </div>
                </div>
            </div>

            <!-- SEO & Meta Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-search text-primary fs-5"></i>
                    <h6 class="fw-bold text-body mb-0">Search Engine &amp; Social Optimization</h6>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control form-control-sm rounded-3" placeholder="Target 50-60 characters">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="form-control form-control-sm rounded-3" placeholder="Target 140-160 characters for social snippets...">{{ old('meta_description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" class="form-control form-control-sm rounded-3" placeholder="comma-separated keywords e.g. haute couture, silk, horology">
                </div>
            </div>
        </div>

        <!-- Right: Publishing Options & Metadata -->
        <div class="col-lg-4">
            <!-- Publish Box -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-body mb-3">Publishing Controls</h6>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Status</label>
                    <select name="status" class="form-select form-select-sm rounded-3" id="postStatusSelect">
                        <option value="published" selected>Published</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="draft">Save as Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="mb-3" id="scheduledAtGroup" style="display: none;">
                    <label class="form-label small fw-semibold text-muted">Scheduled Release Date &amp; Time</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control form-control-sm rounded-3">
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_sticky" value="1" id="checkSticky">
                    <label class="form-check-label small text-body" for="checkSticky">
                        <i class="bi bi-pin-angle-fill text-warning me-1"></i> Pinned / Sticky Chronicle
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="checkFeatured">
                    <label class="form-check-label small text-body" for="checkFeatured">
                        <i class="bi bi-star-fill text-primary me-1"></i> Feature of the Maison (Lead Story)
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_editor_pick" value="1" id="checkEditorPick">
                    <label class="form-check-label small text-body" for="checkEditorPick">
                        <i class="bi bi-award-fill text-gold me-1"></i> Editor's Curation Highlight
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_private" value="1" id="checkPrivate">
                    <label class="form-check-label small text-body" for="checkPrivate">
                        <i class="bi bi-eye-slash-fill text-danger me-1"></i> Private (Staff &amp; Editors Only)
                    </label>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Patron Passphrase (Password Protected)</label>
                    <input type="password" name="password" class="form-control form-control-sm rounded-3" placeholder="Leave empty for public access">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-primary">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Publish Chronicle
                </button>
            </div>

            <!-- Unlimited Gallery Images Portfolio -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-body mb-0">Unlimited Gallery Images</h6>
                    <span class="badge bg-gold-subtle text-gold extra-small rounded-pill">Portfolio</span>
                </div>
                <div class="mb-2">
                    <input type="file" name="gallery_images[]" multiple accept="image/jpeg,image/png,image/webp,image/avif" class="form-control form-control-sm rounded-3">
                    <div class="extra-small text-muted mt-2">
                        <i class="bi bi-images me-1 text-gold"></i> Select unlimited images to display in the interactive visual plates gallery. Max 5MB each.
                    </div>
                </div>
            </div>

            <!-- Author & Category -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-body mb-3">Editorial Attribution</h6>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Maison Category <span class="text-danger">*</span></label>
                    <select name="category_id" required class="form-select form-select-sm rounded-3">
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Author</label>
                    <select name="author_id" class="form-select form-select-sm rounded-3">
                        <option value="">Staff Editorial Desk</option>
                        @foreach($authors as $auth)
                            <option value="{{ $auth->id }}" {{ old('author_id') == $auth->id ? 'selected' : '' }}>{{ $auth->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold text-muted">Reading Time (Minutes)</label>
                    <input type="number" name="reading_time_minutes" value="{{ old('reading_time_minutes', 5) }}" min="1" max="60" class="form-control form-control-sm rounded-3">
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-body mb-3">Featured Photography</h6>
                <div class="mb-3">
                    <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/avif" class="form-control form-control-sm rounded-3">
                    <div class="extra-small text-muted mt-1">Recommended: 1600x900px, WebP or JPEG, max 5MB.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Image Caption / Credits</label>
                    <input type="text" name="featured_image_caption" value="{{ old('featured_image_caption') }}" class="form-control form-control-sm rounded-3" placeholder="e.g. Photography by Maison Atelier Paris">
                </div>
                <div>
                    <label class="form-label small fw-semibold text-muted">Image Alt Text (Accessible SEO Description)</label>
                    <input type="text" name="featured_image_alt" value="{{ old('featured_image_alt') }}" class="form-control form-control-sm rounded-3" placeholder="e.g. Close-up of double-faced cashmere coat with hand-stitched lapels">
                    <div class="extra-small text-muted mt-1">Descriptive alt text for screen readers and image search crawlers.</div>
                </div>
            </div>

            <!-- Tags Multiselect -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold text-body mb-3">Curated Themes &amp; Tags</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                            <label class="form-check-label small text-body" for="tag{{ $tag->id }}">#{{ $tag->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('postStatusSelect');
        const wrap = document.getElementById('scheduledAtGroup');
        if (select && wrap) {
            select.addEventListener('change', function() {
                wrap.style.display = this.value === 'scheduled' ? 'block' : 'none';
            });
        }
    });
</script>
@include('admin.partials.editor-assets')
@endsection
