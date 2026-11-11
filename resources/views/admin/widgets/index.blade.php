@extends('admin.layouts.admin')

@section('title', 'Editorial Widgets Manager | XIVIG Maison Gazette')
@section('page_title', 'Widgets & Modules')

@section('content')
<div class="row g-4">
    <!-- Add Widget Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-black text-body mb-2">Deploy Widget</h5>
            <p class="text-muted extra-small mb-4">Attach dynamic modules to the editorial sidebar, footer, or header zone.</p>

            <form action="{{ route('admin.widgets.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Widget Label</label>
                    <input type="text" name="name" required class="form-control form-control-sm rounded-3" placeholder="Trending Chronicles Sidebar">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Widget Type</label>
                    <select name="type" class="form-select form-select-sm rounded-3">
                        <option value="trending">Trending In Atelier</option>
                        <option value="recent_posts">Recent Publications</option>
                        <option value="popular">Most Liked & Read</option>
                        <option value="newsletter">VIP Newsletter Box</option>
                        <option value="categories">Category List</option>
                        <option value="tags">Aesthetic Tag Cloud</option>
                        <option value="ads">Maison Showcase Ad</option>
                        <option value="custom_html">Custom HTML / Script</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Display Area</label>
                    <select name="area" class="form-select form-select-sm rounded-3">
                        <option value="sidebar">Sidebar</option>
                        <option value="footer">Footer Directory</option>
                        <option value="header">Header Announcement</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Front-end Heading (Optional)</label>
                    <input type="text" name="title" class="form-control form-control-sm rounded-3" placeholder="Trending In The Atelier">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Custom Content / HTML</label>
                    <textarea name="content" rows="3" class="form-control form-control-sm rounded-3" placeholder="Optional raw HTML content..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-primary">
                    <i class="bi bi-grid-plus me-1"></i> Deploy Widget
                </button>
            </form>
        </div>
    </div>

    <!-- Active Widgets List -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent py-4 px-4 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-black text-body mb-0">Configured Widgets</h5>
                    <p class="text-muted extra-small mb-0">Active modular components rendered across the front-end layout</p>
                </div>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
                    {{ $widgets->count() }} Widgets
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Module Name</th>
                            <th>Type</th>
                            <th>Target Area</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($widgets as $widget)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-body">{{ $widget->name }}</div>
                                <span class="extra-small text-muted">{{ $widget->title ?: 'No custom header' }}</span>
                            </td>
                            <td><span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">{{ $widget->type }}</span></td>
                            <td><span class="badge bg-info-subtle text-info rounded-pill px-2">{{ strtoupper($widget->area) }}</span></td>
                            <td>
                                <form action="{{ route('admin.widgets.toggle', $widget->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm p-0 border-0">
                                        <span class="badge {{ $widget->is_active ? 'bg-success-subtle text-success border border-success' : 'bg-warning-subtle text-warning border border-warning' }} rounded-pill px-2 py-1">
                                            {{ $widget->is_active ? 'Active' : 'Disabled' }}
                                        </span>
                                    </button>
                                </form>
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.widgets.destroy', $widget->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm text-danger rounded-circle border" onclick="return confirm('Remove widget?')" title="Delete Widget">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-puzzle display-5 opacity-25 d-block mb-3"></i>
                                <h6>No Widgets Configured Yet</h6>
                                <p class="small">Add a widget module from the left panel to enhance your editorial layout.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
