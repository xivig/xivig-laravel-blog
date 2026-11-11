@extends('admin.layouts.admin')

@section('title', 'Salon Inquiries & Correspondence | XIVIG Maison Gazette')
@section('page_title', 'Patron Inquiries & Salon Desk')

@section('content')
<!-- KPI Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted extra-small fw-bold text-uppercase">Total Inquiries</span>
                <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-circle p-2" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-chat-left-dots-fill fs-6"></i>
                </div>
            </div>
            <h3 class="fw-black mb-0">{{ number_format($metrics['total']) }}</h3>
            <span class="text-muted extra-small">Lifetime salon communications</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted extra-small fw-bold text-uppercase">Pending Review</span>
                <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-circle p-2" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-envelope-exclamation-fill fs-6"></i>
                </div>
            </div>
            <h3 class="fw-black mb-0 text-warning">{{ number_format($metrics['unread']) }}</h3>
            <span class="text-muted extra-small">Awaiting editor attention</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted extra-small fw-bold text-uppercase">Received Today</span>
                <div class="icon-shape bg-success bg-opacity-10 text-success rounded-circle p-2" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-calendar-check-fill fs-6"></i>
                </div>
            </div>
            <h3 class="fw-black mb-0 text-success">{{ number_format($metrics['today']) }}</h3>
            <span class="text-muted extra-small">Arrived in current 24h cycle</span>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body mb-4">
    <!-- Toolbar & Filter Bar -->
    <div class="p-3 border-bottom bg-body-tertiary">
        <form action="{{ route('admin.contacts.bulk') }}" method="POST" id="bulkForm" class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            @csrf
            <div class="d-flex align-items-center gap-2">
                <div class="form-check ms-2 mb-0">
                    <input class="form-check-input" type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                </div>

                <div class="btn-group btn-group-sm">
                    <button type="submit" name="action" value="read" class="btn btn-outline-secondary" title="Mark as Reviewed">
                        <i class="bi bi-check2-all me-1"></i> Mark Reviewed
                    </button>
                    <button type="submit" name="action" value="unread" class="btn btn-outline-secondary" title="Mark as Pending">
                        <i class="bi bi-envelope me-1"></i> Mark Pending
                    </button>
                    <button type="submit" name="action" value="delete" class="btn btn-outline-danger" title="Purge Selected" onclick="return confirm('Purge selected inquiries from registry?');">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="max-width: 240px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" form="filterForm" class="form-control rounded-end-3" placeholder="Search patron, email, text..." value="{{ $filters['search'] ?? '' }}">
                </div>

                <select name="status" form="filterForm" class="form-select form-select-sm rounded-3" style="width: 140px;" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Statuses</option>
                    <option value="unread" {{ ($filters['status'] ?? '') === 'unread' ? 'selected' : '' }}>Pending Review</option>
                    <option value="read" {{ ($filters['status'] ?? '') === 'read' ? 'selected' : '' }}>Reviewed</option>
                </select>

                <button type="submit" form="filterForm" class="btn btn-primary btn-sm rounded-3">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary btn-sm rounded-3" title="Clear Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>

        <form action="{{ route('admin.contacts.index') }}" method="GET" id="filterForm" class="d-none"></form>
    </div>

    <!-- Inquiries Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-body-tertiary">
                <tr class="extra-small text-uppercase text-muted fw-bold tracking-wider">
                    <th class="ps-3" style="width: 40px;"></th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 220px;">Correspondent</th>
                    <th>Inquiry Details &amp; Message</th>
                    <th style="width: 140px;">Received</th>
                    <th class="text-end pe-4" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($inquiries as $item)
                    <tr class="{{ !$item->is_read ? 'bg-primary bg-opacity-10 fw-bold' : '' }}">
                        <td class="ps-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input item-checkbox" form="bulkForm" type="checkbox" name="selected[]" value="{{ $item->id }}">
                            </div>
                        </td>
                        <td>
                            @if(!$item->is_read)
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1 extra-small fw-bold">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> Pending
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2.5 py-1 extra-small">
                                    Reviewed
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.contacts.show', $item) }}" class="text-decoration-none text-body d-block">
                                <div class="fw-bold small text-truncate">{{ $item->name }}</div>
                                <span class="text-muted extra-small font-monospace">{{ $item->email }}</span>
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.contacts.show', $item) }}" class="text-decoration-none text-body d-block">
                                <div class="text-truncate" style="max-width: 520px;">
                                    <span class="{{ !$item->is_read ? 'fw-bold text-body' : 'text-body' }}">{{ $item->subject ?: 'Inquiry via Salon Desk' }}</span>
                                    <span class="text-muted fw-normal extra-small ms-1">— {{ Str::limit($item->message, 80) }}</span>
                                </div>
                            </a>
                        </td>
                        <td class="text-nowrap">
                            <span class="text-muted extra-small">
                                {{ \App\Helpers\TimezoneHelper::format($item->created_at, 'M d, Y h:i A') }}
                            </span>
                        </td>
                        <td class="text-end pe-4 text-nowrap">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.contacts.show', $item) }}" class="btn btn-outline-primary btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="View &amp; Reply">
                                    <i class="bi bi-reply-fill"></i>
                                </a>
                                <form action="{{ route('admin.contacts.read', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="{{ $item->is_read ? 'Mark Unread' : 'Mark Reviewed' }}">
                                        <i class="bi {{ $item->is_read ? 'bi-envelope' : 'bi-check2' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.contacts.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Purge this inquiry record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-chat-square-quote fs-1 text-muted opacity-50 d-block mb-2"></i>
                            <h6 class="fw-bold text-muted">No contact inquiries found</h6>
                            <p class="text-muted extra-small mb-0">Public correspondence submitted through the Maison Salon will register here.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($inquiries->hasPages())
        <div class="p-3 bg-body-tertiary border-top d-flex justify-content-end">
            {{ $inquiries->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleSelectAll(master) {
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.checked = master.checked;
    });
}
</script>
@endpush
@endsection
