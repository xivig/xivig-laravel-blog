@extends('admin.layouts.admin')

@section('title', 'Manage Themes & Tags | XIVIG Maison Gazette')
@section('page_title', 'Themes & Tags')

@section('content')
<div class="row g-4">
    <!-- Add Tag Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-black text-body mb-3">Create Theme Tag</h5>
            <p class="text-muted extra-small mb-4">Tag stories across editorial motifs and aesthetics.</p>
            <form action="{{ route('admin.tags.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Tag Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" required class="form-control form-control-sm rounded-3" placeholder="e.g. Leathercraft">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Custom Slug</label>
                    <input type="text" name="slug" class="form-control form-control-sm rounded-3" placeholder="auto-generated if empty">
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-primary py-2">
                    <i class="bi bi-plus-lg me-1"></i> Add Tag
                </button>
            </form>
        </div>
    </div>

    <!-- Tag List Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent py-4 px-4 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-black text-body mb-0">Registered Editorial Tags</h5>
                    <p class="text-muted extra-small mb-0">Cross-cutting themes across the Maison Gazette</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Tag Theme</th>
                            <th>Slug Path</th>
                            <th>Chronicles</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tags as $t)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 fw-bold">
                                    #{{ $t->name }}
                                </span>
                            </td>
                            <td class="text-muted small font-monospace">/tag/{{ $t->slug }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold">
                                    {{ $t->posts_count }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('blog.tag', $t->slug) }}" target="_blank" class="btn btn-light btn-sm rounded-circle border" title="View Public Tag Feed">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <form action="{{ route('admin.tags.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Delete this tag?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm rounded-circle border text-danger" title="Delete Tag">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No tags recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
