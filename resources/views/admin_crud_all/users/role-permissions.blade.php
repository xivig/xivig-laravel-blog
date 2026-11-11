@extends('admin.layouts.admin')

@section('title', 'RBAC & User Clearance Matrix')
@section('page_title', 'Role-Based Access Control (RBAC)')
@section('parentTitle', 'System & Governance')

@section('content')

<div class="row g-4">
    <!-- User Role Management Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-card">
            <div class="card-header bg-transparent border-0 p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-black text-body mb-1">Direct User Role Assignment</h5>
                    <p class="text-muted small mb-0">Instantly swap authority clearance roles for individual users. Administrator role bypasses granular restrictions.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back to User Directory
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-body-secondary sticky-top">
                            <tr>
                                <th class="ps-4 py-3 text-muted extra-small fw-bold text-uppercase tracking-widest">User Profile</th>
                                <th class="py-3 text-muted extra-small fw-bold text-uppercase tracking-widest">Email Address</th>
                                <th class="py-3 text-muted extra-small fw-bold text-uppercase tracking-widest" style="width: 260px;">Role Assignment</th>
                                <th class="pe-4 py-3 text-muted extra-small fw-bold text-uppercase tracking-widest text-end">Account Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm">
                                            @if($user->profile_picture)
                                                <img src="{{ $user->profile_picture }}" class="rounded-circle shadow-sm" width="38" height="38" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="mb-0 fw-bold small text-body text-decoration-none hover-gold">
                                                {{ $user->name }}
                                            </a>
                                            @if($user->id === auth()->id())
                                                <span class="badge rounded-pill bg-warning text-dark extra-small ms-1">You</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-muted small">{{ $user->email }}</span></td>
                                <td>
                                    <select class="form-select role-selector rounded-pill extra-small fw-bold border-opacity-25 shadow-sm" 
                                            data-user-id="{{ $user->id }}"
                                            style="padding: 0.45rem 1.25rem;">
                                        <option value="admin" {{ in_array($user->role, ['admin', 'super_admin']) ? 'selected' : '' }}>Administrator</option>
                                        <option value="editor" {{ $user->role == 'editor' ? 'selected' : '' }}>Editor</option>
                                        <option value="author" {{ $user->role == 'author' ? 'selected' : '' }}>Author</option>
                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Standard User</option>
                                    </select>
                                </td>
                                <td class="pe-4 text-end">
                                    @if($user->deleted_at)
                                        <span class="badge rounded-pill bg-danger-subtle text-danger extra-small fw-bold px-3 py-1">Trashed</span>
                                    @else
                                        <span class="badge rounded-pill bg-success-subtle text-success extra-small fw-bold px-3 py-1">Active</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Role-Permission Matrix -->
    <div class="col-12">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-card">
            <div class="card-header bg-transparent border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-black text-body mb-1">Elite RBAC Authority Matrix</h5>
                        <p class="text-muted small mb-0">Define what each clearance tier can perform across the Maison Gazette system. Changes apply instantly.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 extra-small fw-bold">Live Synchronized</span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="ps-4 py-4 fw-black text-uppercase tracking-widest small" style="min-width: 280px;">Permission Authority</th>
                                <th class="py-4 text-center fw-black text-uppercase tracking-widest small" style="width: 140px;">Editor</th>
                                <th class="py-4 text-center fw-black text-uppercase tracking-widest small" style="width: 140px;">Author</th>
                                <th class="py-4 text-center fw-black text-uppercase tracking-widest small" style="width: 140px;">User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="ps-4 py-3.5">
                                    <div class="fw-bold text-body mb-0.5">{{ $permission->name }}</div>
                                    @if(!empty($permission->description))
                                        <div class="extra-small text-muted">{{ $permission->description }}</div>
                                    @endif
                                    <code class="extra-small text-warning opacity-75">{{ $permission->name }}</code>
                                </td>
                                @foreach(['editor', 'author', 'user'] as $role)
                                @php
                                    $hasPerm = in_array($permission->name, $rolePermissionMap[$role] ?? []);
                                @endphp
                                <td class="text-center py-3.5">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input permission-toggle" type="checkbox"
                                               data-role="{{ $role }}"
                                               data-permission-id="{{ $permission->id }}"
                                               {{ $hasPerm ? 'checked' : '' }}
                                               style="cursor: pointer; transform: scale(1.2);">
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = '{{ csrf_token() }}';

    // Direct User Role Update
    document.querySelectorAll('.role-selector').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.getAttribute('data-user-id');
            const newRole = this.value;
            const originalBg = this.style.backgroundColor;

            this.style.opacity = '0.5';

            fetch('{{ route("admin.users.update-role") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    role: newRole
                })
            })
            .then(res => res.json())
            .then(data => {
                this.style.opacity = '1';
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message || 'Role updated successfully',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                } else {
                    alert(data.message || 'Failed to update role.');
                }
            })
            .catch(err => {
                this.style.opacity = '1';
                console.error(err);
                alert('An error occurred while updating user role.');
            });
        });
    });

    // Permission Switch Toggle
    document.querySelectorAll('.permission-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const role = this.getAttribute('data-role');
            const permId = this.getAttribute('data-permission-id');
            const action = this.checked ? 'assign' : 'revoke';

            this.disabled = true;

            fetch('{{ route("admin.users.update-permission") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    role: role,
                    permission_id: permId,
                    action: action
                })
            })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: `Permission ${action === 'assign' ? 'granted to' : 'revoked from'} ${role}`,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                } else {
                    this.checked = !this.checked;
                    alert('Failed to update permission.');
                }
            })
            .catch(err => {
                this.disabled = false;
                this.checked = !this.checked;
                console.error(err);
                alert('An error occurred while updating permission.');
            });
        });
    });
});
</script>

@endsection
