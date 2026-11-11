@extends('admin.layouts.admin')

@section('title', 'Subscriber Management & VIP Patrons')
@section('page_title', 'Patron Registry & Subscriptions')
@section('parentTitle', 'Communication & Desk')

@section('content')
<div class="container-fluid px-0">

    <!-- 1. Executive Metrics & KPI Overview -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Total Readers</span>
                        <h3 class="fw-black text-body mb-0 mt-1">{{ number_format($metrics['total']) }}</h3>
                        <span class="text-success extra-small fw-semibold">
                            <i class="bi bi-arrow-up-right me-1"></i>+{{ $metrics['recent'] }} in last 30d
                        </span>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Active Patrons</span>
                        <h3 class="fw-black text-success mb-0 mt-1">{{ number_format($metrics['active']) }}</h3>
                        <span class="text-muted extra-small fw-semibold">
                            {{ $metrics['total'] > 0 ? round(($metrics['active'] / $metrics['total']) * 100, 1) : 0 }}% retention rate
                        </span>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">VIP Atelier Patrons</span>
                        <h3 class="fw-black text-warning mb-0 mt-1">{{ number_format($metrics['vip']) }}</h3>
                        <span class="text-warning extra-small fw-semibold">
                            <i class="bi bi-star-fill me-1"></i>Tier 1 Private List
                        </span>
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
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Inactive / Opt-Out</span>
                        <h3 class="fw-black text-danger mb-0 mt-1">{{ number_format($metrics['unsubscribed']) }}</h3>
                        <span class="text-muted extra-small fw-semibold">
                            {{ $metrics['total'] > 0 ? round(($metrics['unsubscribed'] / $metrics['total']) * 100, 1) : 0 }}% opt-out rate
                        </span>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-person-x-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Controls & Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <!-- Navigation Tabs -->
                <ul class="nav nav-pills gap-2 flex-grow-1" id="subscriberFilterTabs">
                    @php
                        $currentStatus = request('status', 'all');
                        $currentTier = request('tier', 'all');
                    @endphp
                    <li class="nav-item">
                        <a href="{{ route('admin.subscribers.index') }}" class="nav-link rounded-pill px-3 py-1.5 extra-small fw-bold {{ $currentStatus === 'all' && $currentTier === 'all' ? 'active' : '' }}">
                            All Patrons ({{ $metrics['total'] }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.subscribers.index', ['status' => 'subscribed']) }}" class="nav-link rounded-pill px-3 py-1.5 extra-small fw-bold {{ $currentStatus === 'subscribed' ? 'active' : '' }}">
                            <span class="badge bg-success rounded-circle p-1 me-1"></span> Active ({{ $metrics['active'] }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.subscribers.index', ['tier' => 'vip']) }}" class="nav-link rounded-pill px-3 py-1.5 extra-small fw-bold {{ $currentTier === 'vip' ? 'active' : '' }}">
                            <i class="bi bi-star-fill text-warning me-1"></i> VIP Atelier ({{ $metrics['vip'] }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.subscribers.index', ['status' => 'unsubscribed']) }}" class="nav-link rounded-pill px-3 py-1.5 extra-small fw-bold {{ $currentStatus === 'unsubscribed' ? 'active' : '' }}">
                            Unsubscribed ({{ $metrics['unsubscribed'] }})
                        </a>
                    </li>
                </ul>

                <!-- Action Triggers -->
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <button type="button" class="btn btn-primary rounded-pill px-3 py-2 btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
                        <i class="bi bi-person-plus-fill me-1"></i> Enlist Patron
                    </button>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#importSubscriberModal">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Batch Import
                    </button>
                    <button type="button" class="btn btn-warning rounded-pill px-3 py-2 btn-sm fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#broadcastModal">
                        <i class="bi bi-send-fill me-1"></i> Broadcast Chronicle
                    </button>
                    <a href="{{ route('admin.subscribers.export', request()->query()) }}" class="btn btn-light rounded-pill px-3 py-2 btn-sm fw-bold border text-body" title="Export as CSV">
                        <i class="bi bi-download me-1"></i> Export CSV
                    </a>
                </div>
            </div>

            <hr class="opacity-10 my-3">

            <!-- Search & Sort Row -->
            <form action="{{ route('admin.subscribers.index') }}" method="GET" class="row g-2 align-items-center">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('tier')) <input type="hidden" name="tier" value="{{ request('tier') }}"> @endif

                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-secondary border-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-body-secondary border-0" placeholder="Search by email, name, notes...">
                        @if(request('search'))
                            <a href="{{ route('admin.subscribers.index', request()->except('search')) }}" class="btn btn-body-secondary border-0 text-muted"><i class="bi bi-x-circle-fill"></i></a>
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="tier" class="form-select form-select-sm bg-body-secondary border-0" onchange="this.form.submit()">
                        <option value="all" {{ request('tier') === 'all' || !request('tier') ? 'selected' : '' }}>All Patron Tiers</option>
                        <option value="standard" {{ request('tier') === 'standard' ? 'selected' : '' }}>Standard Gazette Reader</option>
                        <option value="vip" {{ request('tier') === 'vip' ? 'selected' : '' }}>VIP Atelier Patron</option>
                        <option value="press" {{ request('tier') === 'press' ? 'selected' : '' }}>Press & Syndicate</option>
                        <option value="partner" {{ request('tier') === 'partner' ? 'selected' : '' }}>Maison Partner</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="sort" class="form-select form-select-sm bg-body-secondary border-0" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Joined: Newest First</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Joined: Oldest First</option>
                        <option value="alpha" {{ request('sort') === 'alpha' ? 'selected' : '' }}>Alphabetical (A-Z)</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill w-100 fw-bold">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Bulk Operations Bar (Appears when checkboxes selected) -->
    <form id="bulkForm" action="{{ route('admin.subscribers.bulk') }}" method="POST">
        @csrf
        <div id="bulkBar" class="card border-0 shadow-sm rounded-4 mb-3 bg-primary bg-opacity-10 d-none">
            <div class="card-body py-2 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check2-circle text-primary fs-5"></i>
                    <span class="fw-bold text-body small"><span id="selectedCount">0</span> patrons selected</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select name="action" class="form-select form-select-sm border-0 shadow-sm bg-body fw-bold" style="min-width: 180px;" required>
                        <option value="" disabled selected>Select Bulk Action...</option>
                        <option value="subscribe">Activate (Subscribed)</option>
                        <option value="unsubscribe">Mark as Unsubscribed</option>
                        <option value="set_vip">Promote to VIP Atelier</option>
                        <option value="set_standard">Reassign to Standard</option>
                        <option value="delete">Remove Selected</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="return confirm('Execute this bulk action for selected patrons?');">
                        Apply
                    </button>
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-2 border" id="deselectAllBtn">Cancel</button>
                </div>
            </div>
        </div>

        <!-- 4. Data Registry Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive" style="min-height: 320px;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4" style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Patron Dossier</th>
                            <th>Membership Tier</th>
                            <th>Status</th>
                            <th>Acquisition Source</th>
                            <th>Subscribed On</th>
                            <th>Last Broadcast</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $sub)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" name="ids[]" value="{{ $sub->id }}" class="form-check-input row-select">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                         style="width: 38px; height: 38px; font-size: 0.85rem; background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #c9a96e;">
                                        {{ strtoupper(substr($sub->name ?: $sub->email, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-body">{{ $sub->name ?: 'Patron Reader' }}</div>
                                        <div class="text-muted extra-small d-flex align-items-center gap-1">
                                            <i class="bi bi-envelope"></i> {{ $sub->email }}
                                        </div>
                                        @if($sub->notes)
                                            <div class="text-muted extra-small fst-italic text-truncate" style="max-width: 240px;" title="{{ $sub->notes }}">
                                                <i class="bi bi-chat-left-text me-1 text-primary"></i>{{ $sub->notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php $tb = $sub->tier_badge; @endphp
                                <span class="badge {{ $tb['class'] }} rounded-pill px-2.5 py-1 extra-small fw-bold">
                                    @if($sub->tier === 'vip') <i class="bi bi-gem me-1"></i> @endif
                                    {{ $tb['label'] }}
                                </span>
                            </td>
                            <td>
                                @php $sb = $sub->status_badge; @endphp
                                <span class="badge {{ $sb['class'] }} rounded-pill px-2.5 py-1 extra-small fw-bold">
                                    {{ $sb['label'] }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted extra-small text-uppercase fw-semibold">
                                    {{ str_replace('_', ' ', $sub->source ?: 'web_portal') }}
                                </span>
                            </td>
                            <td>
                                <div class="text-body small fw-semibold">{{ $sub->created_at->format('M d, Y') }}</div>
                                <div class="text-muted extra-small">{{ $sub->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($sub->last_broadcast_at)
                                    <div class="text-body small">{{ $sub->last_broadcast_at->format('M d, Y') }}</div>
                                    <div class="text-muted extra-small">{{ $sub->last_broadcast_at->diffForHumans() }}</div>
                                @else
                                    <span class="text-muted extra-small">Never broadcast</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle border p-2" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Patron Actions">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                        <li>
                                            <button type="button" class="dropdown-item small py-2 edit-subscriber-btn"
                                                    data-id="{{ $sub->id }}"
                                                    data-email="{{ $sub->email }}"
                                                    data-name="{{ $sub->name }}"
                                                    data-tier="{{ $sub->tier }}"
                                                    data-status="{{ $sub->status }}"
                                                    data-notes="{{ $sub->notes }}"
                                                    data-action="{{ route('admin.subscribers.update', $sub->id) }}">
                                                <i class="bi bi-pencil-square me-2 text-primary"></i> Edit Patron Dossier
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item small py-2" href="mailto:{{ $sub->email }}">
                                                <i class="bi bi-envelope-at me-2 text-info"></i> Direct Email
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.subscribers.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Remove patron \'{{ $sub->email }}\' from the registry?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item small py-2 text-danger">
                                                    <i class="bi bi-trash3 me-2"></i> Remove Patron
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
                                        <i class="bi bi-envelope-x fs-1 opacity-50"></i>
                                    </div>
                                    <h5 class="fw-bold text-body">No Subscribers Found</h5>
                                    <p class="text-muted small mb-3">No patron dossiers match your search or filter criteria.</p>
                                    <button type="button" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
                                        Enlist First Patron
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subscribers->hasPages())
            <div class="card-footer bg-transparent py-4 px-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted extra-small">
                    Showing {{ $subscribers->firstItem() }} to {{ $subscribers->lastItem() }} of {{ $subscribers->total() }} patrons
                </span>
                <div>
                    {{ $subscribers->links() }}
                </div>
            </div>
            @endif
        </div>
    </form>
</div>

<!-- MODAL: Enlist New Patron -->
<div class="modal fade" id="addSubscriberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-body-secondary">
                <h5 class="modal-title fw-bold text-body"><i class="bi bi-person-plus text-primary me-2"></i>Enlist New Patron</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.subscribers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Patron Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="reader@maison-gazette.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Full Name / Salutation</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Monsieur Henri de Rochefort">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Membership Tier</label>
                            <select name="tier" class="form-select rounded-3" required>
                                <option value="standard" selected>Standard Gazette Reader</option>
                                <option value="vip">VIP Atelier Patron</option>
                                <option value="press">Press &amp; Syndicate</option>
                                <option value="partner">Maison Partner</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Subscription Status</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="subscribed" selected>Subscribed (Active)</option>
                                <option value="pending">Pending Confirmation</option>
                                <option value="unsubscribed">Unsubscribed</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Atelier Notes &amp; Curatorial Dossier</label>
                        <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="Collector of bespoke chronographs, attended Salon 2026..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-body-tertiary">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Enlist Patron</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Edit Patron Dossier -->
<div class="modal fade" id="editSubscriberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-body-secondary">
                <h5 class="modal-title fw-bold text-body"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Patron Dossier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSubscriberForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Patron Email Address <span class="text-danger">*</span></label>
                        <input type="email" id="editEmail" name="email" class="form-control rounded-3" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Full Name / Salutation</label>
                        <input type="text" id="editName" name="name" class="form-control rounded-3">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Membership Tier</label>
                            <select id="editTier" name="tier" class="form-select rounded-3" required>
                                <option value="standard">Standard Gazette Reader</option>
                                <option value="vip">VIP Atelier Patron</option>
                                <option value="press">Press &amp; Syndicate</option>
                                <option value="partner">Maison Partner</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Subscription Status</label>
                            <select id="editStatus" name="status" class="form-select rounded-3" required>
                                <option value="subscribed">Subscribed (Active)</option>
                                <option value="pending">Pending Confirmation</option>
                                <option value="unsubscribed">Unsubscribed</option>
                                <option value="bounced">Bounced</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Curatorial Notes</label>
                        <textarea id="editNotes" name="notes" class="form-control rounded-3" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-body-tertiary">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Batch Import Patrons -->
<div class="modal fade" id="importSubscriberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-body-secondary">
                <h5 class="modal-title fw-bold text-body"><i class="bi bi-cloud-arrow-up text-primary me-2"></i>Batch Patron Import</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.subscribers.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Target Assignment Tier</label>
                        <select name="import_tier" class="form-select rounded-3" required>
                            <option value="standard" selected>Standard Gazette Reader</option>
                            <option value="vip">VIP Atelier Patron</option>
                            <option value="press">Press &amp; Syndicate</option>
                            <option value="partner">Maison Partner</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Upload CSV File</label>
                        <input type="file" name="import_file" class="form-control rounded-3" accept=".csv,.txt">
                        <span class="text-muted extra-small">Supports standard CSV with headers or bare email list.</span>
                    </div>

                    <div class="text-center my-3 text-muted extra-small text-uppercase fw-bold letter-spacing-1">— Or Paste Direct List —</div>

                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Paste Email Addresses (One per line)</label>
                        <textarea name="raw_emails" class="form-control rounded-3 font-monospace small" rows="5" placeholder="collector@artisan.fr&#10;Madame Curie <curie@sorbonne.fr>&#10;horology.fan@geneva.ch"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-body-tertiary">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Process Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Broadcast Atelier Chronicle -->
<div class="modal fade" id="broadcastModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-body-secondary">
                <h5 class="modal-title fw-bold text-body"><i class="bi bi-send text-warning me-2"></i>Dispatch Atelier Chronicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.subscribers.broadcast') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Chronicle Subject Line <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control rounded-3" placeholder="Maison Gazette: Volume XIV — The Art of Horological Complications" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Audience Segment</label>
                            <select name="target_tier" class="form-select rounded-3" required>
                                <option value="all">All Active Patrons ({{ $metrics['active'] }})</option>
                                <option value="vip">VIP Atelier Only ({{ $metrics['vip'] }})</option>
                                <option value="standard">Standard Gazette Only</option>
                                <option value="press">Press &amp; Syndicate Only</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Editorial Chronicle Content <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control rounded-3" rows="7" placeholder="Distinguished Readers,&#10;&#10;In our latest edition, we examine the delicate boundary between mechanical horology and sculptural art..." required></textarea>
                    </div>

                    <div class="p-3 bg-body-secondary rounded-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted mb-1">Dispatch Preview / Test Recipient</label>
                        <div class="input-group input-group-sm">
                            <input type="email" name="test_email" class="form-control" placeholder="Optional: Enter email to receive a private test sample first">
                            <button type="submit" class="btn btn-outline-dark fw-bold">Send Test Only</button>
                        </div>
                        <span class="text-muted extra-small">Leave blank to queue dispatch to all selected patrons.</span>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-body-tertiary">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark shadow-sm">
                        <i class="bi bi-broadcast me-1"></i> Broadcast to Audience
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Select All Checkbox Handler
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-select');
    const bulkBar = document.getElementById('bulkBar');
    const selectedCount = document.getElementById('selectedCount');
    const deselectAllBtn = document.getElementById('deselectAllBtn');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.row-select:checked');
        if (checked.length > 0) {
            bulkBar.classList.remove('d-none');
            selectedCount.textContent = checked.length;
        } else {
            bulkBar.classList.add('d-none');
            if (selectAll) selectAll.checked = false;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkBar();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!cb.checked && selectAll) selectAll.checked = false;
            updateBulkBar();
        });
    });

    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function () {
            rowCheckboxes.forEach(cb => cb.checked = false);
            if (selectAll) selectAll.checked = false;
            updateBulkBar();
        });
    }

    // Edit Modal Populate
    const editBtns = document.querySelectorAll('.edit-subscriber-btn');
    const editModal = new bootstrap.Modal(document.getElementById('editSubscriberModal'));
    const editForm = document.getElementById('editSubscriberForm');
    const editEmail = document.getElementById('editEmail');
    const editName = document.getElementById('editName');
    const editTier = document.getElementById('editTier');
    const editStatus = document.getElementById('editStatus');
    const editNotes = document.getElementById('editNotes');

    editBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            editForm.action = this.dataset.action;
            editEmail.value = this.dataset.email || '';
            editName.value = this.dataset.name || '';
            editTier.value = this.dataset.tier || 'standard';
            editStatus.value = this.dataset.status || 'subscribed';
            editNotes.value = this.dataset.notes || '';
            editModal.show();
        });
    });
});
</script>
@endsection
