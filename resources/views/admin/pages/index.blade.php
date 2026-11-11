@extends('admin.layouts.admin')

@section('title', 'Manage Static CMS Pages | XIVIG Maison Gazette')
@section('page_title', 'Static Pages')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent py-4 px-4 border-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-black text-body mb-1">Static Editorial Pages</h5>
                    <p class="text-muted extra-small mb-0">Manage dedicated legal disclaimers, about atelier chronicles, and policy documents.</p>
                </div>
                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-primary">
                    <i class="bi bi-plus-lg me-1"></i> New Static Page
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Page Title</th>
                            <th>Slug / URL</th>
                            <th>Template</th>
                            <th class="text-center">Status</th>
                            <th>Last Modified</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $page)
                        <tr>
                            <td class="ps-4 fw-bold text-body">
                                {{ $page->title }}
                            </td>
                            <td class="font-monospace small">
                                <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-decoration-none">
                                    /page/{{ $page->slug }} <i class="bi bi-box-arrow-up-right extra-small"></i>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                    {{ ucfirst($page->template) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($page->is_published)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Published</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">Draft</span>
                                @endif
                            </td>
                            <td class="extra-small text-muted">
                                {{ $page->updated_at ? $page->updated_at->format('M d, Y') : 'Recent' }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2" title="Edit Page">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this static page?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete Page">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-richtext display-5 d-block mb-2 opacity-50"></i>
                                No static pages created yet. Click "+ New Static Page" to add an About Us or Privacy Policy.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pages->hasPages())
                <div class="card-footer bg-transparent py-3 border-0 d-flex justify-content-center">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
