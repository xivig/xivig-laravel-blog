@extends('admin.layouts.admin')

@section('title', 'Manage Chronicles | XIVIG Maison Gazette')
@section('page_title', 'Chronicles & Stories')

@section('content')
<!-- Filter Toolbar -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <form action="{{ route('admin.posts.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-body-tertiary border-end-0 text-muted ps-3">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm border-start-0 rounded-end-pill px-2" placeholder="Search by title or excerpt...">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm rounded-pill px-3">
                <option value="">All Statuses</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select form-select-sm rounded-pill px-3">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 w-100 fw-bold shadow-primary">
                Filter
            </button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-light btn-sm rounded-pill px-3 border" title="Reset Filters">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<!-- Posts Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-transparent py-4 px-4 border-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-black text-body mb-0">Editorial Chronicles Vault</h5>
            <p class="text-muted extra-small mb-0">Total of {{ $posts->total() }} stories published and in draft</p>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-primary">
            <i class="bi bi-plus-lg me-1"></i> New Story
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                <tr>
                    <th class="ps-4" style="width: 70px;">Media</th>
                    <th>Chronicle Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Telemetry</th>
                    <th>Date</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td class="ps-4">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="" class="rounded-3 object-fit-cover shadow-xs" width="48" height="48">
                        @else
                            <div class="rounded-3 bg-body-tertiary border d-flex align-items-center justify-content-center text-muted" style="width:48px;height:48px;">
                                <i class="bi bi-image fs-5"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="fw-bold text-body text-decoration-none d-block mb-1">
                            {{ $post->title }}
                        </a>
                        <div class="d-flex flex-wrap align-items-center gap-1 extra-small text-muted">
                            <span>By {{ $post->author ? $post->author->name : 'Staff Editor' }}</span>
                            <span>&bull;</span>
                            <span>{{ $post->reading_time }}m read</span>
                            @if($post->is_sticky)
                                <span class="badge bg-warning text-dark extra-small rounded-pill px-2">
                                    <i class="bi bi-pin-angle-fill me-1"></i>Sticky
                                </span>
                            @endif
                            @if($post->is_featured)
                                <span class="badge bg-primary-subtle text-primary extra-small rounded-pill px-2">
                                    <i class="bi bi-star-fill me-1"></i>Featured
                                </span>
                            @endif
                            @if($post->isPasswordProtected())
                                <span class="badge bg-secondary-subtle text-secondary extra-small rounded-pill px-2">
                                    <i class="bi bi-lock-fill me-1"></i>Protected
                                </span>
                            @endif
                            @if($post->is_private)
                                <span class="badge bg-dark text-warning extra-small rounded-pill px-2">
                                    <i class="bi bi-eye-slash-fill me-1"></i>Private
                                </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                            {{ $post->category ? $post->category->name : 'Uncategorized' }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.posts.toggleStatus', $post->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="badge {{ $post->status === 'published' ? 'bg-success-subtle text-success border border-success' : 'bg-warning-subtle text-warning border border-warning' }} rounded-pill px-3 py-1 cursor-pointer" title="Click to toggle status">
                                {{ ucfirst($post->status) }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div class="small fw-bold text-body">{{ number_format($post->views_count) }} views</div>
                        <div class="extra-small text-muted"><i class="bi bi-heart-fill text-danger me-1"></i>{{ number_format($post->likes_count) }} likes</div>
                    </td>
                    <td>
                        <span class="small text-muted">{{ $post->published_at ? $post->published_at->format('M d, Y') : 'Draft' }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-1 align-items-center">
                            <a href="{{ route('admin.posts.preview', $post->id) }}" target="_blank" class="btn btn-light btn-sm rounded-circle border text-primary p-2" title="Preview Live Draft">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('admin.posts.clone', $post->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-light btn-sm rounded-circle border text-success p-2" title="Duplicate / Clone Chronicle">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </form>
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-light btn-sm rounded-pill border px-3 text-body" title="Edit Chronicle">
                                Manage
                            </a>
                            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Move chronicle to trash?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm rounded-circle border text-danger p-2" title="Trash">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-50"></i>
                        No chronicles found matching your filter criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
    <div class="card-footer bg-transparent py-4 px-4 border-top d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
