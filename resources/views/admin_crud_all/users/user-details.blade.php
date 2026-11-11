@extends('admin.layouts.admin')

@section('title', 'User Dossier: ' . $user->name)
@section('page_title', 'Staff & User Profile Dossier')
@section('parentTitle', 'User Directory')

@section('content')

<div class="row g-4">
    <!-- Profile Card & Quick Contacts -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-card mb-4">
            <div class="position-relative d-inline-block mx-auto mb-3">
                @if($user->profile_picture)
                    <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}" class="rounded-circle border border-warning border-3 p-1 shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary-subtle text-primary border border-warning border-3 d-flex align-items-center justify-content-center fw-black shadow-sm" style="width: 120px; height: 120px; font-size: 3rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                @if($user->deleted_at)
                    <span class="position-absolute bottom-0 end-0 bg-danger border border-white border-3 rounded-circle" style="width: 22px; height: 22px;" title="Trashed"></span>
                @else
                    <span class="position-absolute bottom-0 end-0 bg-success border border-white border-3 rounded-circle" style="width: 22px; height: 22px;" title="Active"></span>
                @endif
            </div>

            <h4 class="fw-black mb-1 text-body">{{ $user->name }}</h4>
            <p class="text-muted small mb-2">{{ $user->designation ?? 'Maison Gazette Contributor' }}</p>

            <div class="mb-3">
                @switch($user->role)
                    @case('super_admin')
                    @case('admin')
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-1.5 fw-bold extra-small">
                            <i class="bi bi-shield-fill me-1"></i> Administrator
                        </span>
                        @break
                    @case('editor')
                        <span class="badge rounded-pill bg-info-subtle text-info px-3 py-1.5 fw-bold extra-small">
                            <i class="bi bi-pen-fill me-1"></i> Editor
                        </span>
                        @break
                    @case('author')
                        <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-1.5 fw-bold extra-small">
                            <i class="bi bi-feather me-1"></i> Author
                        </span>
                        @break
                    @default
                        <span class="badge rounded-pill bg-secondary-subtle text-body-secondary px-3 py-1.5 fw-bold extra-small">
                            <i class="bi bi-person me-1"></i> Patron User
                        </span>
                @endswitch
            </div>

            <div class="d-flex gap-2 justify-content-center mt-2">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-gold btn-sm rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Edit Profile
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <hr class="my-4 opacity-10">

            <!-- Contact Information -->
            <div class="text-start">
                <h6 class="fw-bold mb-3 extra-small text-uppercase text-muted letter-spacing-1">Contact Telemetry</h6>
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-body-tertiary rounded-3 p-2 me-3 text-gold">
                        <i class="bi bi-envelope fs-6"></i>
                    </div>
                    <div>
                        <div class="extra-small text-muted">Email Address</div>
                        <span class="small fw-semibold text-body">{{ $user->email }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-body-tertiary rounded-3 p-2 me-3 text-gold">
                        <i class="bi bi-telephone fs-6"></i>
                    </div>
                    <div>
                        <div class="extra-small text-muted">Phone Number</div>
                        <span class="small fw-semibold text-body">{{ $user->phone ?? 'Unlisted' }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-body-tertiary rounded-3 p-2 me-3 text-gold">
                        <i class="bi bi-clock fs-6"></i>
                    </div>
                    <div>
                        <div class="extra-small text-muted">Configured Timezone</div>
                        <span class="small fw-semibold text-body">{{ $user->timezone }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-body-tertiary rounded-3 p-2 me-3 text-gold">
                        <i class="bi bi-shield-check fs-6"></i>
                    </div>
                    <div>
                        <div class="extra-small text-muted">Two-Factor Authentication</div>
                        <span class="small fw-semibold {{ $user->two_factor_enabled ? 'text-success' : 'text-secondary' }}">
                            {{ $user->two_factor_enabled ? 'Active & Enforced' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content & Metrics -->
    <div class="col-lg-8">
        <!-- Stats Row -->
        <div class="row g-3 g-md-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-primary text-white">
                    <p class="small fw-bold text-uppercase opacity-75 mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">Chronicles / Posts</p>
                    <h3 class="fw-black mb-0">{{ $postsCount ?? ($user->posts_count ?? 0) }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-success text-white">
                    <p class="small fw-bold text-uppercase opacity-75 mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">Comments Authored</p>
                    <h3 class="fw-black mb-0">{{ $commentsCount ?? ($user->comments_count ?? 0) }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-card border">
                    <p class="small fw-bold text-uppercase text-muted mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">Registered</p>
                    <h5 class="fw-black mb-0 text-gold">{{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}</h5>
                </div>
            </div>
        </div>

        <!-- Biography -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-card">
            <h5 class="fw-black mb-3 text-body">Biography &amp; Editorial Bio</h5>
            <div class="text-body-secondary small leading-relaxed">
                @if($user->bio)
                    {!! nl2br(e($user->bio)) !!}
                @else
                    <p class="text-muted fst-italic mb-0">No biographical statement provided for this account.</p>
                @endif
            </div>
        </div>

        <!-- Activity History -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-card overflow-hidden">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-black mb-0 text-body">Recent Account Activity</h5>
                <span class="badge bg-body-tertiary text-muted extra-small">Last 10 Actions</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold letter-spacing-1">
                        <tr>
                            <th class="py-2.5">Action</th>
                            <th class="py-2.5">Description</th>
                            <th class="py-2.5">IP Address</th>
                            <th class="py-2.5 text-end pe-3">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                        <tr>
                            <td class="small fw-bold text-gold">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</td>
                            <td class="small text-body">{{ $log->description }}</td>
                            <td class="extra-small font-monospace text-muted">{{ $log->ip_address ?? '—' }}</td>
                            <td class="extra-small text-muted text-end pe-3">{{ $log->created_at ? $log->created_at->diffForHumans() : '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted small">
                                No recent activity logged for this account.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
