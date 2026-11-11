@extends('admin.layouts.admin')

@section('title', 'Role & Permission Security Matrix | XIVIG Maison Gazette')
@section('page_title', 'Access Control & Roles')

@section('content')

<!-- Elite RBAC Matrix Card (Matching Image 2) -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-transparent py-4 px-4 border-0">
        <h4 class="fw-black text-body mb-1">Elite RBAC Matrix</h4>
        <p class="text-muted extra-small mb-0">Define what each role can perform across the system. Changes are instant.</p>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold letter-spacing-1 border-0">
                <tr>
                    <th class="ps-4 py-3" style="min-width: 280px;">PERMISSION AUTHORITY</th>
                    <th class="py-3 text-center" style="min-width: 140px;">EDITOR</th>
                    <th class="py-3 text-center" style="min-width: 140px;">AUTHOR</th>
                    <th class="py-3 text-center" style="min-width: 140px;">USER</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matrixAuthorities as $auth)
                @php
                    $editorHas = in_array($auth['name'], $rolePermissionMap['editor'] ?? []);
                    $authorHas = in_array($auth['name'], $rolePermissionMap['author'] ?? []);
                    $userHas   = in_array($auth['name'], $rolePermissionMap['user'] ?? []);
                @endphp
                <tr>
                    <td class="ps-4 py-3">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-body small">{{ $auth['title'] }}</span>
                            <span class="extra-small text-muted mb-1">{{ $auth['description'] }}</span>
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 extra-small font-monospace py-0 px-2" style="font-size: 0.68rem;">
                                    {{ $auth['key'] }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <!-- Editor Toggle -->
                    <td class="py-3 text-center">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input rbac-toggle-switch cursor-pointer" 
                                   type="checkbox" 
                                   role="switch" 
                                   style="width: 2.5em; height: 1.35em;"
                                   data-role="editor" 
                                   data-permission="{{ $auth['key'] }}"
                                   {{ $editorHas ? 'checked' : '' }}>
                        </div>
                    </td>

                    <!-- Author Toggle -->
                    <td class="py-3 text-center">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input rbac-toggle-switch cursor-pointer" 
                                   type="checkbox" 
                                   role="switch" 
                                   style="width: 2.5em; height: 1.35em;"
                                   data-role="author" 
                                   data-permission="{{ $auth['key'] }}"
                                   {{ $authorHas ? 'checked' : '' }}>
                        </div>
                    </td>

                    <!-- User Toggle -->
                    <td class="py-3 text-center">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input rbac-toggle-switch cursor-pointer" 
                                   type="checkbox" 
                                   role="switch" 
                                   style="width: 2.5em; height: 1.35em;"
                                   data-role="user" 
                                   data-permission="{{ $auth['key'] }}"
                                   {{ $userHas ? 'checked' : '' }}>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Roles Overview Cards -->
<div class="row g-4 mb-4">
    @foreach($roles as $role)
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 dashboard-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge bg-gold-gradient text-dark rounded-pill px-3 py-1 fw-bold text-uppercase extra-small">
                    {{ $role->name }}
                </span>
                <span class="small text-muted">{{ $role->users->count() }} Staff</span>
            </div>
            <h6 class="fw-black text-body mb-2">{{ $role->display_name ?: Str::headline($role->name) }}</h6>
            <p class="text-muted extra-small mb-3">{{ $role->description ?: 'System role with predefined permission scope.' }}</p>

            <div class="border-top pt-3 mt-auto">
                <span class="extra-small text-muted text-uppercase fw-bold d-block mb-2">Granted Authorities ({{ $role->permissions->count() }})</span>
                <div class="d-flex flex-wrap gap-1">
                    @forelse($role->permissions->take(4) as $perm)
                        <span class="badge bg-body-secondary text-body extra-small rounded-pill">{{ $perm->name }}</span>
                    @empty
                        <span class="extra-small text-muted">Inherited / Baseline permissions</span>
                    @endforelse
                    @if($role->permissions->count() > 4)
                        <span class="badge bg-primary-subtle text-primary extra-small rounded-pill">+{{ $role->permissions->count() - 4 }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Users & Assigned Roles Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-transparent py-4 px-4 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-black text-body mb-0">Editorial Staff &amp; Role Assignments</h5>
            <p class="text-muted extra-small mb-0">Manage access privileges across administrators, editors, authors, and contributors</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-body-secondary text-muted extra-small text-uppercase fw-bold letter-spacing-1 border-0">
                <tr>
                    <th class="ps-4 py-3">Staff Member</th>
                    <th class="py-3">Email Address</th>
                    <th class="py-3">Assigned Role</th>
                    <th class="py-3">Permission Level</th>
                    <th class="py-3 text-end pe-4">Reassign Authority</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="ps-4 py-3 fw-bold text-body">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-placeholder rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span>{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="py-3 small text-muted font-monospace">{{ $user->email }}</td>
                    <td class="py-3">
                        <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 extra-small fw-bold text-uppercase">
                            {{ $user->role ?? 'author' }}
                        </span>
                    </td>
                    <td class="py-3 extra-small text-muted">
                        {{ $user->isAdmin() ? 'Full Atelier Governance' : ($user->isEditor() ? 'Editorial Publishing Authority' : 'Chronicle Drafting') }}
                    </td>
                    <td class="py-3 text-end pe-4">
                        <form action="{{ route('admin.roles.assign', $user->id) }}" method="POST" class="d-inline-flex align-items-center gap-2">
                            @csrf
                            <select name="role" class="form-select form-select-sm rounded-pill" style="width: 140px;">
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}" {{ ($user->role ?? 'author') === $r->name ? 'selected' : '' }}>{{ Str::headline($r->name) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-light btn-sm rounded-pill px-3 border fw-semibold text-body">Assign</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="card-footer bg-transparent py-3 px-4 border-top">
            {{ $users->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Instant RBAC Matrix Permission Toggle
        document.querySelectorAll('.rbac-toggle-switch').forEach(function(switchEl) {
            switchEl.addEventListener('change', function() {
                const role = this.dataset.role;
                const permission = this.dataset.permission;
                const enabled = this.checked ? 1 : 0;
                const toggle = this;

                fetch("{{ route('admin.roles.permissions.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        role: role,
                        permission: permission,
                        enabled: enabled
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (typeof EliteAlert !== 'undefined') {
                            EliteAlert.toast('success', data.message || 'Permission updated instantly.');
                        }
                    } else {
                        toggle.checked = !enabled;
                        if (typeof EliteAlert !== 'undefined') {
                            EliteAlert.toast('error', 'Unable to update permission authority.');
                        }
                    }
                })
                .catch(err => {
                    toggle.checked = !enabled;
                    if (typeof EliteAlert !== 'undefined') {
                        EliteAlert.toast('error', 'Network or permission authority error.');
                    }
                });
            });
        });
    });
</script>
@endpush

@endsection
