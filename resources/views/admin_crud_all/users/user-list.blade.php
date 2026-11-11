@extends('admin.layouts.admin')

@section('title', isset($isAuthor) ? 'Author Management' : 'User Management')
@section('page_title', isset($isAuthor) ? 'Author Atelier' : 'User Accounts & Directory')
@section('parentTitle', 'System & Governance')

@section('content')

<!-- Metric Overview Cards -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 h-100 bg-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">Total {{ isset($isAuthor) ? 'Authors' : 'Users' }}</p>
                    <h3 class="fw-black mb-0 text-body">{{ $totalUsers ?? $users->total() }}</h3>
                </div>
                <div class="icon-shape rounded-3 p-3 bg-primary-subtle text-primary">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 h-100 bg-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">Active Accounts</p>
                    <h3 class="fw-black mb-0 text-success">{{ $activeCount ?? $users->total() }}</h3>
                </div>
                <div class="icon-shape rounded-3 p-3 bg-success-subtle text-success">
                    <i class="bi bi-person-check-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 h-100 bg-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">Staff &amp; Admins</p>
                    <h3 class="fw-black mb-0 text-warning">{{ $adminsCount ?? 0 }}</h3>
                </div>
                <div class="icon-shape rounded-3 p-3 bg-warning-subtle text-warning">
                    <i class="bi bi-shield-lock-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 h-100 bg-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">In Trash</p>
                    <h3 class="fw-black mb-0 text-danger">{{ $trashedCount ?? 0 }}</h3>
                </div>
                <div class="icon-shape rounded-3 p-3 bg-danger-subtle text-danger">
                    <i class="bi bi-trash3-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Elite Navigation Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <!-- Active / Trash Filter Tabs -->
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="nav-pills-elite p-1 bg-body-tertiary shadow-sm rounded-pill d-inline-flex border">
            <a href="{{ route('admin.users.index') }}" class="nav-link px-4 py-2 rounded-pill small fw-bold {{ request('status') !== 'trash' ? 'active bg-dark text-white' : 'text-body-secondary' }}">
                <i class="bi bi-check2-circle me-1"></i> Active Directory
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'trash']) }}" class="nav-link px-4 py-2 rounded-pill small fw-bold {{ request('status') === 'trash' ? 'active bg-danger text-white' : 'text-body-secondary' }}">
                <i class="bi bi-trash3 me-1"></i> Trash Vault
                @if(($trashedCount ?? 0) > 0)
                    <span class="badge rounded-pill bg-danger-subtle text-danger ms-1 px-2" style="font-size: 0.7rem;">{{ $trashedCount }}</span>
                @endif
            </a>
        </div>

        <!-- Role Filter Dropdown -->
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle px-3 py-2 fw-semibold" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-funnel me-1"></i> {{ request('role') ? ucfirst(request('role')) : 'All Roles' }}
            </button>
            <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                <li><a class="dropdown-item {{ !request('role') ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'))) }}">All Roles</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item {{ request('role') === 'admin' ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'admin'])) }}"><i class="bi bi-shield-fill text-warning me-2"></i> Administrator</a></li>
                <li><a class="dropdown-item {{ request('role') === 'editor' ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'editor'])) }}"><i class="bi bi-pencil-fill text-info me-2"></i> Editor</a></li>
                <li><a class="dropdown-item {{ request('role') === 'author' ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'author'])) }}"><i class="bi bi-feather text-primary me-2"></i> Author</a></li>
                <li><a class="dropdown-item {{ request('role') === 'user' ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'user'])) }}"><i class="bi bi-person text-secondary me-2"></i> Patron User</a></li>
            </ul>
        </div>
    </div>

    <!-- Search & Action Buttons -->
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex align-items-center">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control rounded-start-pill px-3 py-2 bg-body" placeholder="Search users by name, email..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary rounded-end-pill px-3" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            @if(request('search'))
                <a href="{{ route('admin.users.index', request()->except('search', 'page')) }}" class="btn btn-sm btn-link text-muted ms-1" title="Clear Search">
                    <i class="bi bi-x-circle-fill"></i>
                </a>
            @endif
        </form>

        <a href="{{ route('admin.users.roles-matrix') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-2 fw-bold shadow-sm">
            <i class="bi bi-shield-lock me-1"></i> RBAC Matrix
        </a>

        <a href="{{ route('admin.users.create') }}" class="btn btn-gold btn-sm rounded-pill px-4 py-2 fw-bold shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Add User
        </a>
    </div>
</div>

<!-- Bulk Action Sticky Toolbar -->
<div id="bulk-toolbar" class="card border-0 shadow-lg rounded-pill px-4 py-2 bg-dark text-white mb-4 d-none" style="position: sticky; top: 80px; z-index: 1020; border: 1px solid rgba(201, 169, 110, 0.35) !important;">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-warning text-dark rounded-pill fw-bold" id="selected-count">0</span>
            <span class="small fw-bold">Accounts Selected</span>
        </div>
        <div class="d-flex gap-3 align-items-center">
            @if(request('status') === 'trash')
                <button type="button" class="btn btn-link text-success p-0 fw-bold small text-decoration-none" onclick="runBulk('restore')">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                </button>
                <div class="vr bg-white opacity-25"></div>
                <button type="button" class="btn btn-link text-danger p-0 fw-bold small text-decoration-none" onclick="runBulk('force_delete')">
                    <i class="bi bi-x-circle me-1"></i> Permanent Delete
                </button>
            @else
                <button type="button" class="btn btn-link text-danger p-0 fw-bold small text-decoration-none" onclick="runBulk('delete')">
                    <i class="bi bi-trash3 me-1"></i> Move to Trash
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Hidden Bulk Action Form -->
<form id="bulk-action-form" action="{{ route('admin.users.bulk') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input">
    <div id="bulk-ids-container"></div>
</form>

<!-- Users Data Card -->
<div class="card border-0 shadow-sm rounded-4 has-table bg-card">
    <div class="table-responsive" style="min-height: 280px;">
        <table class="table table-hover align-middle mb-0 elite-datatable">
            <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold letter-spacing-1 border-0">
                <tr>
                    <th class="ps-4 py-3" style="width: 48px;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all" aria-label="Select all rows">
                        </div>
                    </th>
                    <th class="py-3">User Identity</th>
                    <th class="py-3">Authority / Role</th>
                    <th class="py-3">Contact</th>
                    <th class="py-3">Security &amp; 2FA</th>
                    <th class="py-3">Member Since</th>
                    <th class="text-end pe-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $initial = strtoupper(substr($user->name, 0, 1));
                    $avatar = $user->profile_picture;
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="form-check">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $user->id }}" aria-label="Select {{ $user->name }}">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="position-relative me-3">
                                @if($avatar)
                                    <img src="{{ $avatar }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary-subtle text-primary fw-black d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; font-size: 1.05rem;">
                                        {{ $initial }}
                                    </div>
                                @endif
                                @if($user->deleted_at)
                                    <span class="position-absolute bottom-0 end-0 bg-danger border border-2 border-white rounded-circle" style="width: 12px; height: 12px;" title="Trashed"></span>
                                @else
                                    <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" style="width: 12px; height: 12px;" title="Active"></span>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="fw-bold text-body text-decoration-none mb-0 d-block hover-gold">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())
                                        <span class="badge rounded-pill bg-warning text-dark extra-small ms-1">You</span>
                                    @endif
                                </a>
                                <span class="text-muted extra-small">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
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
                                    <i class="bi bi-person me-1"></i> User
                                </span>
                        @endswitch
                    </td>
                    <td>
                        @if($user->phone || $user->designation)
                            <div class="small text-body">{{ $user->designation ?? '—' }}</div>
                            <div class="extra-small text-muted">{{ $user->phone ?? '' }}</div>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        @if($user->two_factor_enabled)
                            <span class="badge rounded-pill bg-success-subtle text-success extra-small fw-bold px-2.5 py-1">
                                <i class="bi bi-shield-check me-1"></i> 2FA Active
                            </span>
                        @else
                            <span class="badge rounded-pill bg-secondary-subtle text-muted extra-small fw-semibold px-2.5 py-1">
                                <i class="bi bi-shield-slash me-1"></i> Standard
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="small text-muted">{{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary rounded-circle border shadow-sm" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-boundary="viewport"
                                    data-bs-popper-config='{"strategy":"fixed"}' 
                                    aria-expanded="false" 
                                    style="width: 34px; height: 34px; padding: 0;"
                                    title="User Actions">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3" style="min-width: 190px;">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('admin.users.show', $user->id) }}">
                                        <i class="bi bi-person-lines-fill me-2 text-info"></i> View Details
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('admin.users.edit', $user->id) }}">
                                        <i class="bi bi-pencil-square me-2 text-primary"></i> Edit Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('admin.users.duplicate', $user->id) }}">
                                        <i class="bi bi-files me-2 text-secondary"></i> Duplicate
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @if($user->trashed())
                                    <li>
                                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-2 text-success">
                                                <i class="bi bi-arrow-counterclockwise me-2"></i> Restore Account
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="dropdown-item py-2 text-danger delete-confirm"
                                                    data-title="Delete Permanently?"
                                                    data-message="Permanently eradicate {{ $user->name }}? This action cannot be reversed.">
                                                <i class="bi bi-x-circle me-2"></i> Delete Permanently
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    @if($user->id !== auth()->id())
                                        <li>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item py-2 text-danger delete-confirm"
                                                        data-title="Move to Trash?"
                                                        data-message="Suspend and move {{ $user->name }} to the trash vault?">
                                                    <i class="bi bi-trash3 me-2"></i> Move to Trash
                                                </button>
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <span class="dropdown-item py-2 text-muted disabled">
                                                <i class="bi bi-slash-circle me-2"></i> Current Session
                                            </span>
                                        </li>
                                    @endif
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="py-4">
                            <i class="bi bi-people display-4 opacity-25"></i>
                            <h5 class="fw-bold mt-3 mb-1">No User Accounts Found</h5>
                            <p class="small text-muted mb-3">No profiles matched your search or filter specifications.</p>
                            @if(request()->anyFilled(['search', 'role', 'status']))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                                </a>
                            @else
                                <a href="{{ route('admin.users.create') }}" class="btn btn-gold btn-sm rounded-pill px-4">
                                    <i class="bi bi-person-plus-fill me-1"></i> Create First User
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination & Stats Footer -->
    <div class="px-4 py-3 border-top bg-body-tertiary d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="small text-muted">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} accounts
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Universal NotificationService Confirm fallback
    window.NotificationService = window.NotificationService || {
        confirm: function(title, message, btnText) {
            if (typeof Swal !== 'undefined') {
                return Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#c9a96e',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: btnText || 'Yes, proceed',
                    customClass: { popup: 'elite-swal-popup' }
                });
            }
            return Promise.resolve({ isConfirmed: window.confirm(message || title) });
        }
    };

    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const toolbar = document.getElementById('bulk-toolbar');
    const countSpan = document.getElementById('selected-count');

    const updateToolbar = () => {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        if (countSpan) countSpan.innerText = checkedCount;
        if (toolbar) {
            if (checkedCount > 0) {
                toolbar.classList.remove('d-none');
            } else {
                toolbar.classList.add('d-none');
            }
        }
    };

    selectAll?.addEventListener('change', () => {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateToolbar();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateToolbar);
    });

    window.runBulk = (action) => {
        const form = document.getElementById('bulk-action-form');
        const actionInput = document.getElementById('bulk-action-input');
        const idsContainer = document.getElementById('bulk-ids-container');

        idsContainer.innerHTML = '';
        document.querySelectorAll('.row-checkbox:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            idsContainer.appendChild(input);
        });

        let message = "Perform this action on selected accounts?";
        if (action === 'force_delete') message = "PERMANENTLY delete selected accounts? This cannot be undone.";

        window.NotificationService.confirm("Bulk Account Operation", message, "Confirm")
            .then(res => {
                if (res.isConfirmed) {
                    actionInput.value = action;
                    form.submit();
                }
            });
    };

});
</script>

<style>
.hover-gold:hover { color: #c9a96e !important; }
.nav-pills-elite a.active { box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
.row-checkbox { cursor: pointer; transform: scale(1.1); }
.card.has-table { overflow: visible !important; }
.card.has-table > .table-responsive { overflow: visible !important; min-height: 280px; }
.table-responsive .dropdown-menu { z-index: 1075 !important; }
</style>

@endsection
