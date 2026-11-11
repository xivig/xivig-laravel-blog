@extends('admin.layouts.admin')

@section('title', 'Create Static Page | XIVIG Maison Gazette')
@section('page_title', 'New Static Page')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 max-w-1000 mx-auto">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-subtle">
        <div>
            <h5 class="fw-black text-body mb-1">Author New Static Page</h5>
            <p class="text-muted extra-small mb-0">Create dedicated architectural documents (About, Legal, Guidelines, Ethics).</p>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            &larr; Return to Pages
        </a>
    </div>

    <form action="{{ route('admin.pages.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Page Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="form-control rounded-3" placeholder="e.g. About Maison XIVIG">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Custom Slug (Optional)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="form-control form-control-sm rounded-3 font-monospace" placeholder="e.g. about-maison">
                </div>
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label small fw-bold mb-0">Content Body <span class="text-danger">*</span></label>
                        <span class="badge bg-gold-subtle text-gold extra-small rounded-pill">
                            <i class="bi bi-magic me-1"></i> WYSIWYG Suite
                        </span>
                    </div>
                    <textarea name="content" id="pageContentEditor" rows="14" required class="form-control rounded-3 wysiwyg-editor" placeholder="Compose rich static page content...">{!! old('content') !!}</textarea>
                    <div class="extra-small text-muted mt-2">
                        <i class="bi bi-shield-check text-success me-1"></i> Full HTML typography, headings, tables, links, and drag &amp; drop image uploads enabled.
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-body-tertiary border-0 rounded-4 p-3 mb-3">
                    <h6 class="fw-bold small text-uppercase mb-3">Publishing Options</h6>
                    <div class="mb-3">
                        <label class="form-label extra-small fw-semibold text-muted">Layout Template</label>
                        <select name="template" class="form-select form-select-sm rounded-3">
                            <option value="default" selected>Default Editorial</option>
                            <option value="fullwidth">Full Width Canvas</option>
                            <option value="minimal">Minimal Legal</option>
                        </select>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublishedSwitch" checked>
                        <label class="form-check-label small fw-semibold" for="isPublishedSwitch">Publish Immediately</label>
                    </div>
                </div>

                <div class="card bg-body-tertiary border-0 rounded-4 p-3">
                    <h6 class="fw-bold small text-uppercase mb-3">Search Engine Optimization</h6>
                    <div class="mb-3">
                        <label class="form-label extra-small fw-semibold text-muted">SEO Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control form-control-sm rounded-3" placeholder="Defaults to page title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label extra-small fw-semibold text-muted">Meta Description</label>
                        <textarea name="meta_description" rows="3" class="form-control form-control-sm rounded-3" placeholder="120–160 chars synopsis...">{!! old('meta_description') !!}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label extra-small fw-semibold text-muted">Meta Keywords</label>
                        <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" class="form-control form-control-sm rounded-3" placeholder="about, maison, history">
                    </div>
                </div>
            </div>

            <div class="col-12 border-top border-subtle pt-3 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Publish Page</button>
            </div>
        </div>
    </form>
</div>
@include('admin.partials.editor-assets')
@endsection
