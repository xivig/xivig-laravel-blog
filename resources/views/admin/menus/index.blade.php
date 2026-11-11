@extends('admin.layouts.admin')

@section('title', 'Navigation & Menu Builder | XIVIG Maison Gazette')
@section('page_title', 'Menu Builder')

@section('content')
<div class="row g-4">
    <!-- Add New Menu Location / Item -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-black text-body mb-2">Create Menu</h5>
            <p class="text-muted extra-small mb-4">Define a new navigation zone (Header, Footer, Sidebar, Mobile Drawer).</p>

            <form action="{{ route('admin.menus.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Menu Name</label>
                    <input type="text" name="name" required class="form-control form-control-sm rounded-3" placeholder="Primary Masthead Menu">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Theme Location</label>
                    <select name="location" class="form-select form-select-sm rounded-3">
                        <option value="header">Header (Top Navbar)</option>
                        <option value="footer">Footer (Bottom Directory)</option>
                        <option value="sidebar">Editorial Sidebar</option>
                        <option value="mobile">Mobile Navigation Drawer</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-primary">
                    <i class="bi bi-plus-lg me-1"></i> Create Menu
                </button>
            </form>
        </div>
    </div>

    <!-- Active Menus & Links -->
    <div class="col-lg-8">
        @forelse($menus as $menu)
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-transparent py-4 px-4 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-black text-body mb-0">{{ $menu->name }}</h5>
                    <span class="badge bg-gold text-dark extra-small rounded-pill text-uppercase">{{ strtoupper($menu->location) }} LOCATION</span>
                </div>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
                    {{ $menu->items->count() }} Links Configured
                </span>
            </div>

            <!-- Add Item Accordion / Form -->
            <div class="px-4 pb-3">
                <form action="{{ route('admin.menus.items.add', $menu->id) }}" method="POST" class="row g-2 align-items-center bg-body-tertiary p-3 rounded-4 border mb-3">
                    @csrf
                    <div class="col-md-4">
                        <input type="text" name="title" required class="form-control form-control-sm rounded-3" placeholder="Link Title (e.g. Chronicles)">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="url" required class="form-control form-control-sm rounded-3" placeholder="URL (/ or /category/slug)">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold">
                            <i class="bi bi-link-45deg"></i> Add Item
                        </button>
                    </div>
                </form>

                <!-- Items List -->
                <div class="list-group list-group-flush rounded-3 border">
                    @forelse($menu->items as $item)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-grip-vertical text-muted"></i>
                            <div>
                                <span class="fw-bold text-body">{{ $item->title }}</span>
                                <span class="text-muted extra-small font-monospace d-block">{{ $item->url }}</span>
                            </div>
                        </div>
                        <form action="{{ route('admin.menus.items.delete', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" onclick="return confirm('Remove link?')" title="Remove link">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">No navigation links added to this menu yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
            <i class="bi bi-menu-button-wide display-4 opacity-25 d-block mb-3"></i>
            <h5>No Navigation Menus Registered</h5>
            <p class="small">Create your first menu zone on the left to activate custom navigation.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
