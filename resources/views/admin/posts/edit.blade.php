@extends('admin.layouts.admin')

@section('title', 'Edit Chronicle: ' . $post->title . ' | XIVIG Maison Gazette')
@section('page_title', 'Edit Chronicle')

@section('content')
<form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- Left: Main Editorial Editor -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="mb-3">
                    <label class="form-label fw-bold text-body">Chronicle Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}" required class="form-control form-control-lg rounded-3">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Slug (URL Path)</label>
                    <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" class="form-control form-control-sm rounded-3">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Editorial Excerpt / Synopsis</label>
                    <textarea name="excerpt" rows="2" class="form-control form-control-sm rounded-3">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>

                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-bold text-body mb-0">Chronicle Body Content <span class="text-danger">*</span></label>
                        <span class="badge bg-gold-subtle text-gold extra-small rounded-pill">
                            <i class="bi bi-magic me-1"></i> WYSIWYG Suite
                        </span>
                    </div>
                    <textarea name="content" id="postContentEditor" rows="16" required class="form-control rounded-3 wysiwyg-editor" placeholder="Compose rich editorial chronicle content...">{{ old('content', $post->content) }}</textarea>
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
                    <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" class="form-control form-control-sm rounded-3">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="form-control form-control-sm rounded-3">{{ old('meta_description', $post->meta_description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords) }}" class="form-control form-control-sm rounded-3">
                </div>
            </div>
        </div>

        <!-- Right: Publishing Options & Metadata -->
        <div class="col-lg-4">
            <!-- Publish Box -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-body mb-0">Publishing Controls</h6>
                    <a href="{{ route('admin.posts.preview', $post->id) }}" target="_blank" class="badge bg-primary text-white text-decoration-none px-3 py-1 rounded-pill" title="Preview Live Draft">
                        <i class="bi bi-eye-fill me-1"></i> Preview Live
                    </a>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Status</label>
                    <select name="status" class="form-select form-select-sm rounded-3" id="postStatusSelect">
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="scheduled" {{ old('status', $post->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status', $post->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="mb-3" id="scheduledAtGroup" style="{{ old('status', $post->status) === 'scheduled' ? '' : 'display: none;' }}">
                    <label class="form-label small fw-semibold text-muted">Scheduled Release Date &amp; Time</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}" class="form-control form-control-sm rounded-3">
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_sticky" value="1" id="checkSticky" {{ old('is_sticky', $post->is_sticky) ? 'checked' : '' }}>
                    <label class="form-check-label small text-body" for="checkSticky">
                        <i class="bi bi-pin-angle-fill text-warning me-1"></i> Pinned / Sticky Chronicle
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="checkFeatured" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label small text-body" for="checkFeatured">
                        <i class="bi bi-star-fill text-primary me-1"></i> Feature of the Maison (Lead Story)
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_editor_pick" value="1" id="checkEditorPick" {{ old('is_editor_pick', $post->is_editor_pick) ? 'checked' : '' }}>
                    <label class="form-check-label small text-body" for="checkEditorPick">
                        <i class="bi bi-award-fill text-gold me-1"></i> Editor's Curation Highlight
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_private" value="1" id="checkPrivate" {{ old('is_private', $post->is_private) ? 'checked' : '' }}>
                    <label class="form-check-label small text-body" for="checkPrivate">
                        <i class="bi bi-eye-slash-fill text-danger me-1"></i> Private (Staff &amp; Editors Only)
                    </label>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Patron Passphrase (Password Protected)</label>
                    <input type="text" name="password" value="{{ old('password', $post->password) }}" class="form-control form-control-sm rounded-3" placeholder="Leave empty for public access">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-primary mb-2">
                    <i class="bi bi-save2-fill me-1"></i> Update Chronicle
                </button>
                <div class="d-flex gap-2 mb-2">
                    <a href="{{ route('admin.posts.preview', $post->id) }}" target="_blank" class="btn btn-light w-50 py-2 rounded-pill border small">
                        <i class="bi bi-eye me-1"></i> Preview
                    </a>
                    <button type="button" onclick="document.getElementById('clonePostForm').submit();" class="btn btn-light w-50 py-2 rounded-pill border small">
                        <i class="bi bi-copy me-1"></i> Clone
                    </button>
                </div>
                <a href="{{ route('admin.posts.revisions', $post->id) }}" class="btn btn-outline-secondary w-100 py-2 rounded-pill border small text-body">
                    <i class="bi bi-clock-history me-1 text-gold"></i> Revision Snapshots &amp; Rollback
                </a>
            </div>

            <!-- Unlimited Gallery Images Portfolio -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-body mb-0">Unlimited Gallery Images</h6>
                    <span class="badge bg-gold-subtle text-gold extra-small rounded-pill">{{ $post->galleryImages->count() }} Plates</span>
                </div>

                @if($post->galleryImages->count() > 0)
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach($post->galleryImages as $gImg)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <img src="{{ asset('storage/' . $gImg->image_path) }}" alt="" class="rounded-2 object-fit-cover flex-shrink-0" width="40" height="40">
                                    <span class="extra-small text-truncate text-body">{{ $gImg->caption ?: basename($gImg->image_path) }}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="deleteGalleryItem({{ $gImg->id }})" title="Delete image">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mb-2">
                    <label class="form-label small fw-semibold text-muted">Upload Additional Plates</label>
                    <input type="file" name="gallery_images[]" multiple accept="image/jpeg,image/png,image/webp,image/avif" class="form-control form-control-sm rounded-3">
                    <div class="extra-small text-muted mt-2">
                        <i class="bi bi-images me-1 text-gold"></i> Select unlimited images to append to the visual gallery. Max 5MB each.
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
                            <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Author</label>
                    <select name="author_id" class="form-select form-select-sm rounded-3">
                        <option value="">Staff Editorial Desk</option>
                        @foreach($authors as $auth)
                            <option value="{{ $auth->id }}" {{ old('author_id', $post->author_id) == $auth->id ? 'selected' : '' }}>{{ $auth->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold text-muted">Reading Time (Minutes)</label>
                    <input type="number" name="reading_time_minutes" value="{{ old('reading_time_minutes', $post->reading_time_minutes) }}" min="1" max="60" class="form-control form-control-sm rounded-3">
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-body mb-3">Featured Photography</h6>
                @if($post->featured_image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="" class="img-fluid rounded-3 mb-2 shadow-xs" style="max-height: 180px; width: 100%; object-fit: cover;">
                        <span class="badge bg-secondary-subtle text-secondary extra-small">Current Image</span>
                    </div>
                @endif
                <div class="mb-3">
                    <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/avif" class="form-control form-control-sm rounded-3">
                    <div class="extra-small text-muted mt-1">Upload to replace current image. Max 5MB.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Image Caption</label>
                    <input type="text" name="featured_image_caption" value="{{ old('featured_image_caption', $post->featured_image_caption) }}" class="form-control form-control-sm rounded-3">
                </div>
                <div>
                    <label class="form-label small fw-semibold text-muted">Image Alt Text (Accessible SEO Description)</label>
                    <input type="text" name="featured_image_alt" value="{{ old('featured_image_alt', $post->featured_image_alt) }}" class="form-control form-control-sm rounded-3" placeholder="Descriptive alt text for search engine crawlers">
                </div>
            </div>

            <!-- Tags Multiselect -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold text-body mb-3">Curated Themes &amp; Tags</h6>
                @php
                    $selectedTags = old('tags', $post->tags->pluck('id')->toArray());
                @endphp
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag{{ $tag->id }}" {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}>
                            <label class="form-check-label small text-body" for="tag{{ $tag->id }}">#{{ $tag->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

<form id="clonePostForm" action="{{ route('admin.posts.clone', $post->id) }}" method="POST" style="display: none;">
    @csrf
</form>

<form id="deleteGalleryForm" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
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

    function deleteGalleryItem(id) {
        if (confirm('Delete this gallery asset from the chronicle?')) {
            const form = document.getElementById('deleteGalleryForm');
            form.action = '/admin/posts/gallery-images/' + id;
            form.submit();
        }
    }
</script>
@include('admin.partials.editor-assets')
@endsection
