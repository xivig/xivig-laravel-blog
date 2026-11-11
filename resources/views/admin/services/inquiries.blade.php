@extends('admin.layouts.admin')

@section('title', 'Client Commission Inquiries & Briefs')
@section('page_title', 'Commission Inquiries & Client Briefs')
@section('parentTitle', 'Atelier Services')

@section('content')
<div class="container-fluid px-0">

    <!-- 1. Executive Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Total Inquiries</span>
                        <h3 class="fw-black text-body mb-0 mt-1">{{ number_format($metrics['total']) }}</h3>
                        <span class="text-muted extra-small fw-semibold">Client Commission Briefs</span>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-inbox-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Pending Review</span>
                        <h3 class="fw-black text-warning mb-0 mt-1">{{ number_format($metrics['pending']) }}</h3>
                        <span class="text-warning extra-small fw-semibold">Requires Atelier Response</span>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Client Contacted</span>
                        <h3 class="fw-black text-info mb-0 mt-1">{{ number_format($metrics['contacted']) }}</h3>
                        <span class="text-info extra-small fw-semibold">Dialogue in Progress</span>
                    </div>
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-chat-dots-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-body position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small text-uppercase fw-bold letter-spacing-1">Retained / Converted</span>
                        <h3 class="fw-black text-success mb-0 mt-1">{{ number_format($metrics['converted']) }}</h3>
                        <span class="text-success extra-small fw-semibold">Engagements Finalized</span>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-check-all fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Controls Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-black text-body mb-0">Client Inquiries &amp; Commissions</h5>
                    <span class="badge bg-body-secondary text-muted rounded-pill px-2.5 py-1 extra-small">{{ $inquiries->total() }} dossiers</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold border text-body">
                        <i class="bi bi-briefcase me-1"></i> Manage Services
                    </a>
                    <a href="{{ route('services.index') }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Public Showcase
                    </a>
                </div>
            </div>

            <!-- Filter Controls -->
            <form action="{{ route('admin.services.inquiries') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-secondary border-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-body-secondary border-0" placeholder="Search by client name, email, company, brief...">
                        @if(request('search'))
                            <a href="{{ route('admin.services.inquiries', request()->except('search')) }}" class="btn btn-body-secondary border-0 text-muted"><i class="bi bi-x-circle-fill"></i></a>
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm bg-body-secondary border-0" onchange="this.form.submit()">
                        <option value="all">All Dossier Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>New / Pending</option>
                        <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Under Review</option>
                        <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Client Contacted</option>
                        <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Commission Retained</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="service_id" class="form-select form-select-sm bg-body-secondary border-0" onchange="this.form.submit()">
                        <option value="all">All Requested Services</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}" {{ request('service_id') == $svc->id ? 'selected' : '' }}>{{ $svc->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill w-100 fw-bold">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Inquiries Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive" style="min-height: 320px;">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold">
                    <tr>
                        <th class="ps-4">Client Identity</th>
                        <th>Commission Discipline</th>
                        <th>Budget &amp; Timeline</th>
                        <th>Brief Excerpt</th>
                        <th>Status</th>
                        <th>Submitted On</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $inquiry)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                     style="width: 38px; height: 38px; font-size: 0.85rem; background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #c9a96e;">
                                    {{ strtoupper(substr($inquiry->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-body">{{ $inquiry->name }}</div>
                                    <div class="text-muted extra-small">
                                        <i class="bi bi-envelope"></i> {{ $inquiry->email }}
                                    </div>
                                    @if($inquiry->company)
                                        <div class="text-muted extra-small">
                                            <i class="bi bi-building"></i> {{ $inquiry->company }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($inquiry->service)
                                <a href="{{ route('admin.services.edit', $inquiry->service->id) }}" class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 text-decoration-none">
                                    <i class="bi {{ $inquiry->service->icon ?: 'bi-gem' }} me-1"></i>{{ $inquiry->service->title }}
                                </a>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">General Inquiries</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-body small fw-semibold">{{ $inquiry->budget ?: 'Undisclosed' }}</div>
                            <span class="text-muted extra-small"><i class="bi bi-calendar-event me-1"></i>{{ $inquiry->timeline ?: 'Flexible' }}</span>
                        </td>
                        <td>
                            <div class="text-body small text-truncate" style="max-width: 260px;" title="{{ $inquiry->message }}">
                                "{{ $inquiry->message }}"
                            </div>
                            @if($inquiry->notes)
                                <div class="text-warning extra-small fst-italic text-truncate" style="max-width: 260px;">
                                    <i class="bi bi-sticky me-1"></i>Note: {{ $inquiry->notes }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @php $sb = $inquiry->status_badge; @endphp
                            <span class="badge {{ $sb['class'] }} rounded-pill px-2.5 py-1 extra-small fw-bold">
                                {{ $sb['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="text-body small fw-semibold">{{ $inquiry->created_at->format('M d, Y') }}</div>
                            <div class="text-muted extra-small">{{ $inquiry->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle border p-2" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Inquiry Actions">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                    <li>
                                        <button type="button" class="dropdown-item small py-2 view-inquiry-btn"
                                                data-id="{{ $inquiry->id }}"
                                                data-name="{{ $inquiry->name }}"
                                                data-email="{{ $inquiry->email }}"
                                                data-company="{{ $inquiry->company }}"
                                                data-phone="{{ $inquiry->phone }}"
                                                data-service="{{ $inquiry->service ? $inquiry->service->title : 'General Commission' }}"
                                                data-budget="{{ $inquiry->budget }}"
                                                data-timeline="{{ $inquiry->timeline }}"
                                                data-message="{{ $inquiry->message }}"
                                                data-status="{{ $inquiry->status }}"
                                                data-notes="{{ $inquiry->notes }}"
                                                data-action="{{ route('admin.services.inquiries.update', $inquiry->id) }}">
                                            <i class="bi bi-file-earmark-text me-2 text-primary"></i> Inspect Dossier
                                        </button>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small py-2" href="mailto:{{ $inquiry->email }}?subject=Re:%20Maison%20Gazette%20Commission%20Inquiry%20({{ urlencode($inquiry->service ? $inquiry->service->title : 'Bespoke Services') }})">
                                            <i class="bi bi-reply me-2 text-info"></i> Direct Email Reply
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.services.inquiries.destroy', $inquiry->id) }}" method="POST" onsubmit="return confirm('Remove inquiry dossier from {{ $inquiry->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item small py-2 text-danger">
                                                <i class="bi bi-trash3 me-2"></i> Remove Dossier
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="rounded-circle bg-body-secondary text-muted d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 72px; height: 72px;">
                                    <i class="bi bi-inbox fs-1 opacity-50"></i>
                                </div>
                                <h5 class="fw-bold text-body">No Inquiries Found</h5>
                                <p class="text-muted small mb-0">Client commission requests will appear here once submitted through your public services showcase.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inquiries->hasPages())
        <div class="card-footer bg-transparent py-4 px-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted extra-small">
                Showing {{ $inquiries->firstItem() }} to {{ $inquiries->lastItem() }} of {{ $inquiries->total() }} inquiries
            </span>
            <div>
                {{ $inquiries->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- MODAL: Inspect & Moderate Commission Dossier -->
<div class="modal fade" id="inquiryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-body-secondary">
                <h5 class="modal-title fw-bold text-body"><i class="bi bi-file-earmark-person text-primary me-2"></i>Client Commission Dossier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="inquiryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-body-secondary rounded-3 h-100">
                                <span class="text-muted extra-small text-uppercase fw-bold d-block mb-1">Client Contact</span>
                                <h6 class="fw-bold text-body mb-1" id="modalClientName"></h6>
                                <div class="small text-muted"><i class="bi bi-envelope me-1"></i><span id="modalClientEmail"></span></div>
                                <div class="small text-muted"><i class="bi bi-building me-1"></i><span id="modalClientCompany"></span></div>
                                <div class="small text-muted"><i class="bi bi-telephone me-1"></i><span id="modalClientPhone"></span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-body-secondary rounded-3 h-100">
                                <span class="text-muted extra-small text-uppercase fw-bold d-block mb-1">Commission Scope</span>
                                <div class="badge bg-primary-subtle text-primary rounded-pill mb-2" id="modalServiceName"></div>
                                <div class="small text-body"><strong class="text-muted">Target Budget:</strong> <span id="modalBudget"></span></div>
                                <div class="small text-body"><strong class="text-muted">Expected Timeline:</strong> <span id="modalTimeline"></span></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Client Brief &amp; Specifications</label>
                        <div class="p-3 rounded-3 bg-body border font-serif small" id="modalMessage" style="white-space: pre-wrap; min-height: 100px;"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Dossier Status</label>
                            <select name="status" id="modalStatus" class="form-select rounded-3" required>
                                <option value="pending">New / Pending</option>
                                <option value="reviewed">Under Review</option>
                                <option value="contacted">Client Contacted</option>
                                <option value="converted">Commission Retained</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Internal Curator Notes</label>
                            <input type="text" name="notes" id="modalNotes" class="form-control rounded-3" placeholder="Add staff observations, negotiated rate, or follow-up date...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-body-tertiary d-flex justify-content-between">
                    <a id="modalMailto" href="#" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
                        <i class="bi bi-reply-fill me-1"></i> Reply via Email
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Updates</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const viewBtns = document.querySelectorAll('.view-inquiry-btn');
    const modalEl = document.getElementById('inquiryModal');
    const inquiryModal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('inquiryForm');

    const nameEl = document.getElementById('modalClientName');
    const emailEl = document.getElementById('modalClientEmail');
    const companyEl = document.getElementById('modalClientCompany');
    const phoneEl = document.getElementById('modalClientPhone');
    const serviceEl = document.getElementById('modalServiceName');
    const budgetEl = document.getElementById('modalBudget');
    const timelineEl = document.getElementById('modalTimeline');
    const messageEl = document.getElementById('modalMessage');
    const statusEl = document.getElementById('modalStatus');
    const notesEl = document.getElementById('modalNotes');
    const mailtoBtn = document.getElementById('modalMailto');

    viewBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            form.action = this.dataset.action;
            nameEl.textContent = this.dataset.name || 'Client';
            emailEl.textContent = this.dataset.email || 'N/A';
            companyEl.textContent = this.dataset.company || 'Private Individual';
            phoneEl.textContent = this.dataset.phone || 'None provided';
            serviceEl.textContent = this.dataset.service || 'General Inquiry';
            budgetEl.textContent = this.dataset.budget || 'Undisclosed';
            timelineEl.textContent = this.dataset.timeline || 'Flexible';
            messageEl.textContent = this.dataset.message || '';
            statusEl.value = this.dataset.status || 'pending';
            notesEl.value = this.dataset.notes || '';

            mailtoBtn.href = 'mailto:' + encodeURIComponent(this.dataset.email) +
                '?subject=' + encodeURIComponent('Re: Maison Gazette Commission — ' + (this.dataset.service || 'Bespoke Services'));

            inquiryModal.show();
        });
    });
});
</script>
@endsection
