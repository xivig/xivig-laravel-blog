@extends('admin.layouts.admin')

@section('title', 'Transactions & Revenue | XIVIG Maison Gazette')
@section('page_title', 'Transactions')

@section('content')



<!-- Revenue & Settlement KPI Row -->
<div class="row g-4 mb-4 kpi-row">
    <!-- Gross Volume -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-success-subtle text-success">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    Settled
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Gross Revenue</h6>
            <h3 class="fw-black text-body mb-0">${{ number_format($metrics['total_volume'] ?? 0, 2) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Across all completed payments</span>
        </div>
    </div>

    <!-- Total Records -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-primary-subtle text-primary">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    Ledger
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Total Transactions</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($metrics['total_count'] ?? 0) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Lifetime subscriber ledger</span>
        </div>
    </div>

    <!-- Completed Count -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-info-subtle text-info">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    Success
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Completed</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format($metrics['completed_count'] ?? 0) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">Verified patron payments</span>
        </div>
    </div>

    <!-- Pending & Refunded -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="stat-icon-circle bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold">
                    Attention
                </span>
            </div>
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-wider mb-1">Pending / Refund</h6>
            <h3 class="fw-black text-body mb-0">{{ number_format(($metrics['pending_count'] ?? 0) + ($metrics['refunded_count'] ?? 0)) }}</h3>
            <span class="extra-small text-muted mt-2 d-block">
                {{ $metrics['pending_count'] ?? 0 }} pending &bull; {{ $metrics['refunded_count'] ?? 0 }} refunded
            </span>
        </div>
    </div>
</div>

<!-- Main Transaction History Card (Matching User Screenshot) -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-body">
    <!-- Header of the Card -->
    <div class="p-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h4 class="fw-black text-body mb-1">Transaction History</h4>
            <p class="text-muted extra-small mb-0">Record of all subscriptions and payments.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Status Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm btn-light border rounded-pill px-3 py-2 extra-small fw-semibold dropdown-toggle text-body" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-funnel me-1"></i> {{ $status ? ucfirst($status) : 'All Statuses' }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 extra-small">
                    <li><a class="dropdown-item {{ !$status ? 'active fw-bold' : '' }}" href="{{ route('admin.transactions.index') }}">All Statuses</a></li>
                    <li><a class="dropdown-item {{ $status === 'completed' ? 'active fw-bold' : '' }}" href="{{ route('admin.transactions.index', ['status' => 'completed']) }}">Completed</a></li>
                    <li><a class="dropdown-item {{ $status === 'pending' ? 'active fw-bold' : '' }}" href="{{ route('admin.transactions.index', ['status' => 'pending']) }}">Pending</a></li>
                    <li><a class="dropdown-item {{ $status === 'refunded' ? 'active fw-bold' : '' }}" href="{{ route('admin.transactions.index', ['status' => 'refunded']) }}">Refunded</a></li>
                    <li><a class="dropdown-item {{ $status === 'failed' ? 'active fw-bold' : '' }}" href="{{ route('admin.transactions.index', ['status' => 'failed']) }}">Failed</a></li>
                </ul>
            </div>

            <!-- Record New Transaction Button -->
            <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#recordTransactionModal">
                <i class="bi bi-plus-lg"></i> Record Payment
            </button>
        </div>
    </div>

    <!-- Table Section Matching Screenshot Columns -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold letter-spacing-1 border-0">
                <tr>
                    <th class="ps-4 py-3" style="min-width: 170px;">REFERENCE</th>
                    <th class="py-3" style="min-width: 220px;">CUSTOMER</th>
                    <th class="py-3" style="min-width: 130px;">AMOUNT</th>
                    <th class="py-3" style="min-width: 140px;">METHOD</th>
                    <th class="py-3" style="min-width: 130px;">STATUS</th>
                    <th class="py-3 text-end pe-4" style="min-width: 140px;">DATE</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="font-monospace fw-bold text-body small">#{{ $trx->reference }}</span>
                            <button type="button" class="btn btn-link p-0 text-muted extra-small shadow-none" onclick="navigator.clipboard.writeText('{{ $trx->reference }}'); if(window.EliteAlert) EliteAlert.toast('Reference copied!', 'info');" title="Copy Reference">
                                <i class="bi bi-copy"></i>
                            </button>
                        </div>
                        @if($trx->description)
                            <span class="extra-small text-muted d-block text-truncate" style="max-width: 220px;">{{ $trx->description }}</span>
                        @endif
                    </td>
                    <td class="py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-placeholder rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.8rem;">
                                {{ strtoupper(substr($trx->customer_name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="fw-bold text-body d-block small">{{ $trx->customer_name }}</span>
                                <span class="extra-small text-muted font-monospace">{{ $trx->customer_email ?: 'patron@xivig.com' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3">
                        <span class="fw-black text-body small d-block">
                            {{ $trx->formatted_amount }}
                        </span>
                        <span class="extra-small text-muted text-uppercase">{{ $trx->currency }}</span>
                    </td>
                    <td class="py-3">
                        <span class="badge bg-body-tertiary text-body border rounded-pill px-3 py-1 extra-small fw-semibold">
                            <i class="{{ $trx->method_icon }} text-primary me-1"></i> {{ $trx->payment_method }}
                        </span>
                    </td>
                    <td class="py-3">
                        <span class="badge {{ $trx->status_badge_class }} rounded-pill px-3 py-1 extra-small fw-bold">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </td>
                    <td class="py-3 text-end pe-4">
                        <span class="text-muted extra-small d-block">
                            {{ $trx->created_at->format('M d, Y') }}
                        </span>
                        <span class="extra-small text-secondary font-monospace">
                            {{ $trx->created_at->format('h:i A') }}
                        </span>
                    </td>
                </tr>
                @empty
                <!-- Empty State Exactly Matching User Screenshot -->
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="py-5">
                            <p class="text-muted mb-0" style="font-size: 1rem;">No transactions found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
        <div class="card-footer bg-transparent py-3 px-4 border-top d-flex justify-content-between align-items-center">
            <span class="extra-small text-muted">Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions</span>
            {{ $transactions->links() }}
        </div>
    @endif
</div>

<!-- Record Manual Transaction Modal -->
<div class="modal fade" id="recordTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form action="{{ route('admin.transactions.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-body">
                        <i class="bi bi-cash-coin text-primary me-2"></i> Record New Payment
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Customer / Patron Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" required placeholder="e.g. Jean-Luc Moreau" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Customer Email Address</label>
                        <input type="email" name="customer_email" placeholder="e.g. patron@parisian.fr" class="form-control rounded-3">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-muted">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-body border-end-0 rounded-start-3">$</span>
                                <input type="number" step="0.01" min="0.01" name="amount" required placeholder="149.00" class="form-control rounded-end-3">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-muted">Currency</label>
                            <select name="currency" class="form-select rounded-3">
                                <option value="USD" selected>USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                                <option value="INR">INR (₹)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Payment Method</label>
                            <select name="payment_method" class="form-select rounded-3">
                                <option value="Stripe" selected>Stripe</option>
                                <option value="PayPal">PayPal</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Bank Wire">Bank Wire</option>
                                <option value="Apple Pay">Apple Pay</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Payment Status</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="completed" selected>Completed</option>
                                <option value="pending">Pending</option>
                                <option value="refunded">Refunded</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Narrative / Plan Description</label>
                        <input type="text" name="description" placeholder="e.g. Maison Gazette Annual Haute Horology Tier" class="form-control rounded-3">
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Record Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
