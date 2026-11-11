@extends('admin.layouts.admin')

@section('title', '404 Error Monitor & Broken Link Logs | XIVIG Maison Gazette')
@section('page_title', '404 Error Monitor')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent py-4 px-4 border-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-black text-body mb-1">Incoming 404 Not Found Incidents</h5>
                    <p class="text-muted extra-small mb-0">Tracks broken incoming links, external crawler errors, and missing URL requests in real-time.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2 fw-semibold">
                        {{ $logs->total() }} Missing Paths Logged
                    </span>
                    @if($logs->count() > 0)
                        <form action="{{ route('admin.not-found-logs.clear') }}" method="POST" onsubmit="return confirm('Clear all 404 monitoring records?');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                                <i class="bi bi-trash me-1"></i> Clear Log
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Requested Path</th>
                            <th class="text-center">Hits Count</th>
                            <th>Referer Source</th>
                            <th>IP & User Agent</th>
                            <th>Last Incident</th>
                            <th class="text-end pe-4">Resolution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4 font-monospace small fw-bold text-danger">
                                {{ $log->url }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger text-white rounded-pill px-2 py-1">
                                    {{ number_format($log->hits) }}
                                </span>
                            </td>
                            <td class="small text-truncate" style="max-width: 220px;" title="{{ $log->referer ?: 'Direct / Unknown' }}">
                                {{ $log->referer ?: 'Direct Access' }}
                            </td>
                            <td class="extra-small text-muted text-truncate" style="max-width: 200px;" title="{{ $log->user_agent }}">
                                <span class="d-block text-body font-monospace">{{ $log->ip_address ?: 'Unknown' }}</span>
                                <span>{{ Str::limit($log->user_agent, 40) }}</span>
                            </td>
                            <td class="extra-small text-muted">
                                {{ $log->last_seen_at ? $log->last_seen_at->diffForHumans() : 'Recent' }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="{{ route('admin.redirects.index') }}?source={{ urlencode($log->url) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 extra-small fw-bold">
                                        <i class="bi bi-arrow-left-right me-1"></i> Fix with 301
                                    </a>
                                    <form action="{{ route('admin.not-found-logs.destroy', $log->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Dismiss Log">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-check-circle-fill text-success display-4 d-block mb-2 opacity-75"></i>
                                <h6 class="fw-bold mb-1">Zero Broken Links Detected</h6>
                                <p class="small text-muted mb-0">All incoming requests are resolving correctly across the Maison domain.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="card-footer bg-transparent py-3 border-0 d-flex justify-content-center">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
