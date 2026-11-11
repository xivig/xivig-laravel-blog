@extends('admin.layouts.admin')

@section('title', 'Error Templates & Previews')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge rounded-pill px-3 py-2 fw-bold text-dark mb-2" style="background: linear-gradient(135deg, #d4af37 0%, #b89628 100%); font-size: 0.72rem; letter-spacing: 0.12em;">
                MAISON ARCHITECTURE &bull; EXCEPTION SUITE
            </span>
            <h1 class="h3 fw-black text-white mb-1">Error Views &amp; Status Templates</h1>
            <p class="text-secondary small mb-0">Standardized luxury editorial exception screens for HTTP client and server diagnostics.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.not-found-logs.index') }}" class="btn btn-outline-warning btn-sm rounded-pill px-3">
                <i class="bi bi-shield-exclamation me-1"></i> 404 Monitor Logs
            </a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-sliders me-1"></i> Maintenance Switch
            </a>
        </div>
    </div>

    <!-- Error Templates Grid -->
    <div class="row g-4">
        <!-- 400 Bad Request -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden" style="background: rgba(18, 24, 30, 0.85); border: 1px solid rgba(255, 255, 255, 0.08) !important;">
                <div class="p-4 d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-bold" style="background: rgba(167, 139, 250, 0.12); color: #c4b5fd; font-size: 0.7rem; border: 1px solid rgba(167, 139, 250, 0.3);">
                                Client Error
                            </span>
                            <span class="fw-black fs-4" style="background: linear-gradient(135deg, #a78bfa 0%, #ec4899 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                400
                            </span>
                        </div>
                        <h4 class="fw-bold text-white mb-2 fs-5">Bad Request</h4>
                        <p class="text-secondary small mb-4" style="line-height: 1.6;">
                            Triggered when an incoming HTTP payload is corrupted, malformed, or has invalid request syntax.
                        </p>
                    </div>
                    <div class="pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                        <span class="text-muted extra-small font-monospace">errors/400.blade.php</span>
                        <a href="{{ route('admin.errors.400') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            Preview <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 403 Access Denied -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden" style="background: rgba(18, 24, 30, 0.85); border: 1px solid rgba(255, 255, 255, 0.08) !important;">
                <div class="p-4 d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-bold" style="background: rgba(244, 63, 94, 0.12); color: #fda4af; font-size: 0.7rem; border: 1px solid rgba(244, 63, 94, 0.3);">
                                Forbidden
                            </span>
                            <span class="fw-black fs-4" style="background: linear-gradient(135deg, #f43f5e 0%, #fb923c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                403
                            </span>
                        </div>
                        <h4 class="fw-bold text-white mb-2 fs-5">Access Denied</h4>
                        <p class="text-secondary small mb-4" style="line-height: 1.6;">
                            Displayed when an authenticated patron lacks the permission matrix required to access an atelier sector.
                        </p>
                    </div>
                    <div class="pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                        <span class="text-muted extra-small font-monospace">errors/403.blade.php</span>
                        <a href="{{ route('admin.errors.403') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            Preview <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 404 Page Not Found -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden" style="background: rgba(18, 24, 30, 0.85); border: 1px solid rgba(56, 189, 248, 0.3) !important;">
                <div class="p-4 d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-bold" style="background: rgba(56, 189, 248, 0.12); color: #7dd3fc; font-size: 0.7rem; border: 1px solid rgba(56, 189, 248, 0.3);">
                                Not Found
                            </span>
                            <span class="fw-black fs-4" style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                404
                            </span>
                        </div>
                        <h4 class="fw-bold text-white mb-2 fs-5">Page Not Found</h4>
                        <p class="text-secondary small mb-4" style="line-height: 1.6;">
                            Rendered when a requested story slug or static asset cannot be located. Features live archive search.
                        </p>
                    </div>
                    <div class="pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                        <span class="text-muted extra-small font-monospace">errors/404.blade.php</span>
                        <a href="{{ route('admin.errors.404') }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                            Preview <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 500 Server Error -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden" style="background: rgba(18, 24, 30, 0.85); border: 1px solid rgba(255, 255, 255, 0.08) !important;">
                <div class="p-4 d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-bold" style="background: rgba(239, 68, 68, 0.12); color: #fca5a5; font-size: 0.7rem; border: 1px solid rgba(239, 68, 68, 0.3);">
                                Server Error
                            </span>
                            <span class="fw-black fs-4" style="background: linear-gradient(135deg, #ef4444 0%, #f97316 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                500
                            </span>
                        </div>
                        <h4 class="fw-bold text-white mb-2 fs-5">Internal Server Error</h4>
                        <p class="text-secondary small mb-4" style="line-height: 1.6;">
                            Graceful failure template shown during runtime server exceptions with retry and home fallbacks.
                        </p>
                    </div>
                    <div class="pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                        <span class="text-muted extra-small font-monospace">errors/500.blade.php</span>
                        <a href="{{ route('admin.errors.500') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            Preview <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 503 Service Unavailable -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden" style="background: rgba(18, 24, 30, 0.85); border: 1px solid rgba(255, 255, 255, 0.08) !important;">
                <div class="p-4 d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-bold" style="background: rgba(20, 184, 166, 0.12); color: #5eead4; font-size: 0.7rem; border: 1px solid rgba(20, 184, 166, 0.3);">
                                Unavailable
                            </span>
                            <span class="fw-black fs-4" style="background: linear-gradient(135deg, #14b8a6 0%, #06b6d4 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                503
                            </span>
                        </div>
                        <h4 class="fw-bold text-white mb-2 fs-5">Service Unavailable</h4>
                        <p class="text-secondary small mb-4" style="line-height: 1.6;">
                            Temporary service sequencer with animated gold pulsing loader dots and rapid retry trigger.
                        </p>
                    </div>
                    <div class="pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                        <span class="text-muted extra-small font-monospace">errors/503.blade.php</span>
                        <a href="{{ route('admin.errors.503') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            Preview <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden" style="background: rgba(18, 24, 30, 0.85); border: 1px solid rgba(212, 175, 55, 0.3) !important;">
                <div class="p-4 d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-bold text-dark" style="background: linear-gradient(135deg, #d4af37 0%, #b89628 100%); font-size: 0.7rem;">
                                Active Curation
                            </span>
                            <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-2 fs-5">Refining Excellence</h4>
                        <p class="text-secondary small mb-4" style="line-height: 1.6;">
                            Elite architectural curation view with live countdown timer, progress bar, subscription notification form, and staff bypass.
                        </p>
                    </div>
                    <div class="pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                        <span class="text-muted extra-small font-monospace">errors/maintenance.blade.php</span>
                        <a href="{{ route('admin.errors.maintenance') }}" target="_blank" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">
                            Preview <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
