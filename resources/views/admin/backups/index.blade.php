@extends('admin.layouts.admin')

@section('title', 'Database & Assets Backup Vault | XIVIG Maison Gazette')
@section('page_title', 'System Backups')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4 overflow-hidden position-relative welcome-banner-card">
    <div class="row align-items-center position-relative z-2">
        <div class="col-lg-8">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold small mb-3">
                <i class="bi bi-shield-shaded me-1"></i> RESILIENCE VAULT
            </span>
            <h2 class="display-6 fw-black mb-2 text-body">One-Click Database & Content Snapshots</h2>
            <p class="text-muted lead fs-6 mb-4">
                Export and archive complete SQLite database states, chronicles, media records, and patron datasets with zero downtime.
            </p>
            <form action="{{ route('admin.backups.create') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-primary">
                    <i class="bi bi-cloud-arrow-down me-1"></i> Generate Instant Snapshot
                </button>
            </form>
        </div>
        <div class="col-lg-4 text-end d-none d-lg-block">
            <i class="bi bi-safe display-1 text-primary opacity-25"></i>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-transparent py-4 px-4 border-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-black text-body mb-0">Archived Backups</h5>
            <p class="text-muted extra-small mb-0">Locally stored, downloadable database snapshots</p>
        </div>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
            {{ $backups->total() }} Snapshots Available
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-body-secondary text-muted small text-uppercase fw-bold">
                <tr>
                    <th class="ps-4">Archive Filename</th>
                    <th>Type</th>
                    <th>File Size</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $b)
                <tr>
                    <td class="ps-4 font-monospace small text-body fw-bold">
                        <i class="bi bi-file-earmark-zip text-gold me-2"></i> {{ $b->filename }}
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">{{ strtoupper($b->type) }}</span></td>
                    <td class="small">{{ $b->formatted_size }}</td>
                    <td><span class="badge bg-success-subtle text-success rounded-pill px-2">Ready</span></td>
                    <td class="small text-muted">{{ $b->created_at->format('M d, Y - H:i:s') }}</td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.backups.download', $b->id) }}" class="btn btn-light btn-sm text-primary rounded-pill px-3 border me-1 fw-bold">
                            <i class="bi bi-download me-1"></i> Download
                        </a>
                        <form action="{{ route('admin.backups.destroy', $b->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-light btn-sm text-danger rounded-circle border" onclick="return confirm('Purge this snapshot?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-cloud-slash display-5 opacity-25 d-block mb-3"></i>
                        <h6>No Database Snapshots Generated Yet</h6>
                        <p class="small">Click "Generate Instant Snapshot" above to create your first archive.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
