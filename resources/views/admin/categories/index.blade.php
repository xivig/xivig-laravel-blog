@extends('admin.layouts.admin')

@section('title', 'Manage Categories | XIVIG Maison Gazette')
@section('page_title', 'Maison Categories')

@section('content')
<div class="row g-4">
    <!-- Add Category Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-black text-body mb-3">Create Category</h5>
            <p class="text-muted extra-small mb-4">Structure editorial disciplines and collections.</p>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" required class="form-control form-control-sm rounded-3" placeholder="e.g. Haute Couture">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Custom Slug</label>
                    <input type="text" name="slug" class="form-control form-control-sm rounded-3" placeholder="auto-generated if empty">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Description</label>
                    <textarea name="description" rows="3" class="form-control form-control-sm rounded-3" placeholder="Category synopsis for archives..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Accent Color</label>
                    <input type="color" name="color" value="#cba358" class="form-control form-control-color w-100 rounded-3">
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-primary py-2">
                    <i class="bi bi-plus-lg me-1"></i> Add Category
                </button>
            </form>
        </div>
    </div>

    <!-- Category List Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent py-4 px-4 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-black text-body mb-0">Active Department Categories</h5>
                    <p class="text-muted extra-small mb-0">Collections organizing all published gazette chronicles</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4" style="width: 40px;">Color</th>
                            <th>Category Name</th>
                            <th>Slug Path</th>
                            <th>Stories</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                        <tr>
                            <td class="ps-4">
                                <span class="d-inline-block rounded-circle shadow-xs" style="width: 20px; height: 20px; background-color: {{ $cat->color }};"></span>
                            </td>
                            <td>
                                <span class="fw-bold text-body d-block">{{ $cat->name }}</span>
                                <span class="extra-small text-muted">{{ Str::limit($cat->description, 40) }}</span>
                            </td>
                            <td class="small text-muted font-monospace">/category/{{ $cat->slug }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold">
                                    {{ $cat->posts_count }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('blog.category', $cat->slug) }}" target="_blank" class="btn btn-light btn-sm rounded-circle border" title="View Public Archive">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category? Associated posts will become uncategorized.');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm rounded-circle border text-danger" title="Delete Category">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No categories recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
