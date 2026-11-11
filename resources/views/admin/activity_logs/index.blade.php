@extends('admin.layouts.admin')

@section('title', 'Audit & Activity Logs | XIVIG Maison Gazette')
@section('page_title', 'Audit & Activity Logs')

@section('content')
<!-- KPI Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted extra-small fw-bold text-uppercase">Total Audit Trail</span>
                <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-circle p-2" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-journal-check"></i>
                </div>
            </div>
            <h3 class="fw-black mb-0">{{ number_format($metrics['total']) }}</h3>
            <span class="text-muted extra-small">Recorded operations</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted extra-small fw-bold text-uppercase">Actions Today</span>
                <div class="icon-shape bg-success bg-opacity-10 text-success rounded-circle p-2" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <h3 class="fw-black mb-0 text-success">{{ number_format($metrics['today']) }}</h3>
            <span class="text-muted extra-small">Since midnight</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted extra-small fw-bold text-uppercase">Active Staff Accounts</span>
                <div class="icon-shape bg-info bg-opacity-10 text-info rounded-circle p-2" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <h3 class="fw-black mb-0">{{ number_format($metrics['users']) }}</h3>
            <span class="text-muted extra-small">Operating contributors</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted extra-small fw-bold text-uppercase">Security / Auth</span>
                <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-circle p-2" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
            </div>
            <h3 class="fw-black mb-0 text-warning">{{ number_format($metrics['logins']) }}</h3>
            <span class="text-muted extra-small">Logins &amp; changes</span>
        </div>
    </div>
</div>

<!-- Filter Toolbar & Actions -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-body">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h5 class="fw-black text-body mb-0">System Activity Registry</h5>
            <p class="text-muted extra-small mb-0">Full audit trail of staff actions, logins, and publication revisions in timezone: <strong>{{ \App\Helpers\TimezoneHelper::activeTimezone() }}</strong></p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.activity-logs.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                <i class="bi bi-trash3 me-1"></i> Purge Logs
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label extra-small fw-bold text-muted mb-1">Search Keywords</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control rounded-end-3" placeholder="Description, user, IP..." value="{{ $filters['search'] ?? '' }}">
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label extra-small fw-bold text-muted mb-1">Action Type</label>
            <select name="action" class="form-select form-select-sm rounded-3">
                <option value="">All Actions</option>
                @foreach($actions as $key => $label)
                    <option value="{{ $key }}" {{ ($filters['action'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label extra-small fw-bold text-muted mb-1">Staff Member</label>
            <select name="user_id" class="form-select form-select-sm rounded-3">
                <option value="">All Staff</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label extra-small fw-bold text-muted mb-1">From Date</label>
            <input type="date" name="date_from" class="form-control form-control-sm rounded-3" value="{{ $filters['date_from'] ?? '' }}">
        </div>
        <div class="col-md-2">
            <label class="form-label extra-small fw-bold text-muted mb-1">To Date</label>
            <input type="date" name="date_to" class="form-control form-control-sm rounded-3" value="{{ $filters['date_to'] ?? '' }}">
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100 fw-bold" title="Filter">
                <i class="bi bi-funnel-fill"></i>
            </button>
            @if(array_filter($filters))
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary btn-sm rounded-3" title="Clear Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Logs Data Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-body">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-nowrap">
            <thead class="bg-body-tertiary">
                <tr class="extra-small text-uppercase text-muted fw-black tracking-wider">
                    <th class="ps-4 py-3">Timestamp</th>
                    <th class="py-3">Staff Operator</th>
                    <th class="py-3">Action</th>
                    <th class="py-3">Description</th>
                    <th class="py-3">IP Address</th>
                    <th class="text-end pe-4 py-3">Details / Manage</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($logs as $log)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold small text-body">{{ \App\Helpers\TimezoneHelper::format($log->created_at, 'M d, Y') }}</div>
                            <small class="text-muted extra-small">{{ \App\Helpers\TimezoneHelper::format($log->created_at, 'h:i:s A') }}</small>
                        </td>
                        <td>
                            @if($log->user)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold small text-body">{{ $log->user->name }}</div>
                                        <span class="text-muted extra-small">{{ $log->user->email }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 extra-small">
                                    System / Automated
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ $log->action_badge_class }} px-2 py-1 extra-small fw-bold text-uppercase">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td style="white-space: normal; max-width: 320px;">
                            <span class="small text-body">{{ $log->description }}</span>
                        </td>
                        <td>
                            <span class="font-monospace extra-small text-muted">{{ $log->ip_address ?? '—' }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                @if($log->properties && count($log->properties))
                                    <button type="button" class="btn btn-outline-info btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" title="View Metadata" onclick='showLogProperties(@json($log->properties), "{{ $log->description }}")'>
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                @endif
                                <form action="{{ route('admin.activity-logs.destroy', $log) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this log record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" title="Delete Record">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-journal-x fs-1 text-muted opacity-50 d-block mb-2"></i>
                            <h6 class="fw-bold text-muted">No activity records found</h6>
                            <p class="text-muted extra-small mb-0">System operations matching your filter criteria will appear here.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="p-3 bg-body-tertiary border-top d-flex justify-content-end">
            {{ $logs->links() }}
        </div>
    @endif
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.activity-logs.clear') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-black">Purge Audit Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="small text-muted mb-3">Retaining an active audit trail is critical for editorial accountability. Select the pruning retention policy below:</p>
                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-muted">Retention Policy</label>
                    <select name="days" class="form-select rounded-3">
                        <option value="30">Delete logs older than 30 days (Recommended)</option>
                        <option value="60">Delete logs older than 60 days</option>
                        <option value="90">Delete logs older than 90 days</option>
                        <option value="all">Purge ALL logs permanently</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold">Confirm Purge</button>
            </div>
        </form>
    </div>
</div>

<!-- Properties JSON Modal -->
<div class="modal fade" id="logPropertiesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-black" id="logPropertiesTitle">Log Metadata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <pre class="bg-dark text-light p-3 rounded-4 mb-0 font-monospace small" id="logPropertiesContent" style="max-height: 400px; overflow-y: auto;"></pre>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showLogProperties(props, title) {
    document.getElementById('logPropertiesTitle').textContent = title || 'Log Metadata';
    document.getElementById('logPropertiesContent').textContent = JSON.stringify(props, null, 2);
    new bootstrap.Modal(document.getElementById('logPropertiesModal')).show();
}
</script>
@endpush
@endsection
