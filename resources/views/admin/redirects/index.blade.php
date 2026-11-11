@extends('admin.layouts.admin')

@section('title', '301 & 302 Redirect Manager | XIVIG Maison Gazette')
@section('page_title', 'URL Redirect Manager')

@section('content')
<div class="row g-4">
    <!-- Add Redirect Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-black text-body mb-2">Create Redirect Rule</h5>
            <p class="text-muted extra-small mb-4">Establish 301 SEO redirects to preserve domain authority and prevent broken links.</p>

            <form action="{{ route('admin.redirects.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Source Path <span class="text-danger">*</span></label>
                    <input type="text" name="source_url" id="sourceUrlInput" required class="form-control form-control-sm rounded-3" placeholder="/old-article-slug">
                    <div class="extra-small text-muted mt-1">Relative path starting with a slash</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Target URL <span class="text-danger">*</span></label>
                    <input type="text" name="target_url" required class="form-control form-control-sm rounded-3" placeholder="/article/new-slug or https://...">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">HTTP Status Code</label>
                    <select name="status_code" class="form-select form-select-sm rounded-3">
                        <option value="301" selected>301 Moved Permanently (SEO Recommended)</option>
                        <option value="302">302 Found (Temporary)</option>
                        <option value="307">307 Temporary Redirect</option>
                        <option value="308">308 Permanent Redirect</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-primary py-2">
                    <i class="bi bi-arrow-left-right me-1"></i> Establish Redirect
                </button>
            </form>
        </div>
    </div>

    <!-- Redirects List Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent py-4 px-4 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-black text-body mb-0">Active Redirect Rules</h5>
                    <p class="text-muted extra-small mb-0">Preserves incoming backlinks and search engine crawl equity</p>
                </div>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
                    {{ $redirects->total() }} Rules Active
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Source Path</th>
                            <th>Target Destination</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Hits</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($redirects as $r)
                        <tr>
                            <td class="ps-4 fw-semibold font-monospace small">
                                {{ $r->source_url }}
                            </td>
                            <td class="small text-truncate" style="max-width: 200px;" title="{{ $r->target_url }}">
                                <a href="{{ $r->target_url }}" target="_blank" class="text-decoration-none">
                                    {{ $r->target_url }} <i class="bi bi-box-arrow-up-right extra-small"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $r->status_code == 301 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill">
                                    {{ $r->status_code }}
                                </span>
                            </td>
                            <td class="text-center fw-bold small">
                                {{ number_format($r->hits) }}
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.redirects.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this redirect rule?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete Rule">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-arrow-left-right display-5 d-block mb-2 opacity-50"></i>
                                No custom redirects established yet. Create your first rule on the left.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($redirects->hasPages())
                <div class="card-footer bg-transparent py-3 border-0 d-flex justify-content-center">
                    {{ $redirects->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
