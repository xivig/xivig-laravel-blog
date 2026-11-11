@extends('admin.layouts.admin')

@section('title', 'Services Offered & Atelier Commissions')
@section('page_title', 'Atelier Services & Offerings')
@section('parentTitle', 'Atelier & Commissions')

@section('content')
<div class="container-fluid px-0">

    <!-- 1. Executive Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Total Services</span>
                        <h3 class="fw-black text-body mb-0 mt-1">{{ number_format($metrics['total']) }}</h3>
                        <span class="text-muted extra-small fw-semibold">{{ $metrics['active'] }} Active in Showcase</span>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-briefcase-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Featured Offerings</span>
                        <h3 class="fw-black text-warning mb-0 mt-1">{{ number_format($metrics['featured']) }}</h3>
                        <span class="text-warning extra-small fw-semibold"><i class="bi bi-star-fill me-1"></i>Curator Signature</span>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-gem fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Client Inquiries</span>
                        <h3 class="fw-black text-body mb-0 mt-1">{{ number_format($metrics['total_inquiries']) }}</h3>
                        <span class="text-primary extra-small fw-semibold">Commission Requests</span>
                    </div>
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-chat-square-quote-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Pending Inquiries</span>
                        <h3 class="fw-black text-danger mb-0 mt-1">{{ number_format($metrics['pending_inquiries']) }}</h3>
                        <span class="text-danger extra-small fw-semibold">Requires Curator Attention</span>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-bell-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Controls & Actions Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-black text-body mb-0">Services Catalog</h5>
                    <span class="badge bg-body-secondary text-muted rounded-pill px-2.5 py-1 extra-small">{{ $services->total() }} items</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('services.index') }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Public Showcase
                    </a>
                    <a href="{{ route('admin.services.inquiries') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold border text-body position-relative">
                        <i class="bi bi-inbox-fill me-1 text-primary"></i> Client Inquiries
                        @if($metrics['pending_inquiries'] > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $metrics['pending_inquiries'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i> Create Service
                    </a>
                </div>
            </div>

            <!-- Filter Controls -->
            <form action="{{ route('admin.services.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-secondary border-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-body-secondary border-0" placeholder="Search by title, category, description...">
                        @if(request('search'))
                            <a href="{{ route('admin.services.index', request()->except('search')) }}" class="btn btn-body-secondary border-0 text-muted"><i class="bi bi-x-circle-fill"></i></a>
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="category" class="form-select form-select-sm bg-body-secondary border-0" onchange="this.form.submit()">
                        <option value="all">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm bg-body-secondary border-0" onchange="this.form.submit()">
                        <option value="all">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Public)</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill w-100 fw-bold">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Services Data Registry Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive" style="min-height: 320px;">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold">
                    <tr>
                        <th class="ps-4">Service &amp; Discipline</th>
                        <th>Category</th>
                        <th>Pricing Model</th>
                        <th>Delivery Turnaround</th>
                        <th>Inquiries</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-primary shadow-sm"
                                     style="width: 42px; height: 42px; background: rgba(201, 169, 110, 0.12); border: 1px solid rgba(201, 169, 110, 0.3);">
                                    <i class="bi {{ $service->icon ?: 'bi-gem' }} fs-5"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('admin.services.edit', $service->id) }}" class="fw-bold text-body text-decoration-none hover-primary">
                                            {{ $service->title }}
                                        </a>
                                        @if($service->badge)
                                            <span class="badge bg-warning-subtle text-warning extra-small rounded-pill">{{ $service->badge }}</span>
                                        @endif
                                    </div>
                                    <div class="text-muted extra-small text-truncate" style="max-width: 320px;">
                                        {{ $service->tagline ?: Str::limit(strip_tags($service->description), 50) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-body-secondary text-body extra-small rounded-pill px-2.5 py-1">
                                {{ $service->category }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-body small">{{ $service->formatted_price }}</div>
                            <span class="text-muted extra-small text-capitalize">{{ str_replace('_', ' ', $service->pricing_type) }}</span>
                        </td>
                        <td>
                            <span class="small text-muted">
                                <i class="bi bi-clock-history me-1"></i>{{ $service->delivery_time ?: 'Upon Agreement' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.services.inquiries', ['service_id' => $service->id]) }}" class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 text-decoration-none">
                                <i class="bi bi-chat-quote me-1"></i>{{ $service->inquiries_count }} Requests
                            </a>
                        </td>
                        <td>
                            <form action="{{ route('admin.services.toggle-featured', $service->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm p-1 border-0 bg-transparent" title="Toggle Featured State">
                                    @if($service->is_featured)
                                        <i class="bi bi-star-fill text-warning fs-5"></i>
                                    @else
                                        <i class="bi bi-star text-muted fs-5 opacity-50"></i>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('admin.services.toggle-status', $service->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="border-0 bg-transparent p-0" title="Click to toggle status">
                                    @php $sb = $service->status_badge; @endphp
                                    <span class="badge {{ $sb['class'] }} rounded-pill px-2.5 py-1 extra-small fw-bold">
                                        {{ $sb['label'] }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle border p-2" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Service Actions">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                    <li>
                                        <a class="dropdown-item small py-2" href="{{ route('services.show', $service->slug) }}" target="_blank">
                                            <i class="bi bi-eye me-2 text-info"></i> Public Page Preview
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small py-2" href="{{ route('admin.services.edit', $service->id) }}">
                                            <i class="bi bi-pencil me-2 text-primary"></i> Edit Offering
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small py-2" href="{{ route('admin.services.inquiries', ['service_id' => $service->id]) }}">
                                            <i class="bi bi-chat-left-quote me-2 text-warning"></i> View Inquiries ({{ $service->inquiries_count }})
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Move \'{{ $service->title }}\' to trash?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item small py-2 text-danger">
                                                <i class="bi bi-trash3 me-2"></i> Delete Offering
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="py-4">
                                <div class="rounded-circle bg-body-secondary text-muted d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 72px; height: 72px;">
                                    <i class="bi bi-briefcase fs-1 opacity-50"></i>
                                </div>
                                <h5 class="fw-bold text-body">No Services Configured</h5>
                                <p class="text-muted small mb-3">Add your bespoke services, writing packages, or consulting solutions.</p>
                                <a href="{{ route('admin.services.create') }}" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold">
                                    Create First Offering
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
        <div class="card-footer bg-transparent py-4 px-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted extra-small">
                Showing {{ $services->firstItem() }} to {{ $services->lastItem() }} of {{ $services->total() }} services
            </span>
            <div>
                {{ $services->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
