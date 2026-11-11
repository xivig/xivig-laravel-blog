@extends('admin.layouts.admin')

@section('title', 'Staff Profile & Security | XIVIG Maison Gazette')
@section('page_title', 'My Profile & Preferences')

@section('content')
<div class="row g-4">
    <!-- Left Column: User Summary Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center mb-4 bg-body position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 w-100 bg-primary opacity-10" style="height: 90px;"></div>
            
            <div class="position-relative mt-4 mb-3">
                <div class="avatar-wrapper mx-auto position-relative" style="width: 110px; height: 110px;">
                    <img src="{{ $user->avatar_url ?? '' }}" alt="" id="avatarPreview" class="rounded-circle shadow-sm object-fit-cover w-100 h-100 border border-4 border-body {{ $user->avatar_url ? '' : 'd-none' }}" onerror="this.classList.add('d-none'); document.getElementById('avatarFallback').classList.remove('d-none');">
                    <div id="avatarFallback" class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-black shadow-sm w-100 h-100 border border-4 border-body {{ $user->avatar_url ? 'd-none' : '' }}" style="font-size: 2.5rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <label for="avatarInput" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow cursor-pointer border border-2 border-body d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; cursor: pointer; transition: transform 0.2s;" title="Upload Profile Photograph">
                        <i class="bi bi-camera-fill extra-small"></i>
                    </label>
                    <div id="avatarLoading" class="position-absolute top-0 start-0 w-100 h-100 rounded-circle bg-dark bg-opacity-75 d-none d-flex align-items-center justify-content-center" style="z-index: 10;">
                        <div class="spinner-border text-gold spinner-border-sm" role="status"></div>
                    </div>
                </div>
                @if($user->avatar_url)
                    <div class="mt-2 text-center" id="removeAvatarWrap">
                        <form action="{{ route('admin.profile.avatar.remove') }}" method="POST" class="d-inline" onsubmit="return confirm('Remove your profile photograph?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 extra-small mt-1">
                                <i class="bi bi-trash3 me-1"></i> Remove Photo
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <h5 class="fw-black text-body mb-1">{{ $user->name }}</h5>
            <p class="text-muted extra-small mb-2">{{ $user->designation ?? 'Editorial Contributor' }}</p>
            
            <div class="d-flex justify-content-center gap-2 mb-3">
                <span class="badge bg-gold text-dark fw-bold px-3 py-1 rounded-pill extra-small text-uppercase">
                    {{ $user->role }}
                </span>
                @if($user->two_factor_enabled)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 extra-small">
                        <i class="bi bi-shield-check me-1"></i> 2FA Armed
                    </span>
                @else
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 extra-small">
                        2FA Inactive
                    </span>
                @endif
            </div>

            <hr class="my-3 opacity-10">

            <ul class="list-unstyled text-start extra-small mb-0 d-flex flex-column gap-2 text-muted">
                <li class="d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-envelope me-2 text-primary"></i> Email:</span>
                    <strong class="text-body font-monospace">{{ $user->email }}</strong>
                </li>
                @if($user->phone)
                    <li class="d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-telephone me-2 text-primary"></i> Phone:</span>
                        <strong class="text-body">{{ $user->phone }}</strong>
                    </li>
                @endif
                <li class="d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-globe2 me-2 text-primary"></i> Timezone:</span>
                    <strong class="text-body">{{ $user->timezone }}</strong>
                </li>
                <li class="d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-clock me-2 text-primary"></i> Local Time:</span>
                    <strong class="text-primary">{{ \App\Helpers\TimezoneHelper::format(now(), 'h:i A') }}</strong>
                </li>
                <li class="d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-calendar-check me-2 text-primary"></i> Joined:</span>
                    <strong class="text-body">{{ \App\Helpers\TimezoneHelper::format($user->created_at, 'M Y') }}</strong>
                </li>
            </ul>
        </div>

        <!-- Recent Activity Feed Mini-card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-body">
            <h6 class="fw-black text-body mb-3 text-uppercase small d-flex align-items-center justify-content-between">
                <span>Recent Actions</span>
                <a href="{{ route('admin.activity-logs.index', ['user_id' => $user->id]) }}" class="extra-small text-primary text-decoration-none">View All</a>
            </h6>
            <div class="d-flex flex-column gap-3">
                @forelse($recentActivities as $act)
                    <div class="d-flex align-items-start gap-2 extra-small">
                        <span class="badge rounded-circle p-1 mt-1 {{ $act->action_badge_class }}" style="width: 10px; height: 10px;"></span>
                        <div class="flex-grow-1">
                            <div class="fw-semibold text-body">{{ $act->description }}</div>
                            <span class="text-muted extra-small">{{ \App\Helpers\TimezoneHelper::format($act->created_at, 'M d, h:i A') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted extra-small mb-0">No recent activity recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Settings Forms -->
    <div class="col-lg-8">
        <!-- Personal Info & Timezone Form -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-body">
            <h5 class="fw-black text-body mb-1">Personal Details &amp; Regional Localization</h5>
            <p class="text-muted extra-small mb-4">Manage your editorial credentials, author bio, and personal preferred timezone.</p>

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Full Legal / Byline Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-control rounded-3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Official Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control rounded-3">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Editorial Designation / Title</label>
                        <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" placeholder="e.g. Senior Haute Couture Critic" class="form-control rounded-3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Direct Phone / Atelier Hotline</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+33 1 42 68 55 00" class="form-control rounded-3">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Author Bio &amp; Atelier Synopsis</label>
                    <textarea name="bio" rows="3" class="form-control rounded-3" placeholder="Brief biographical overview displayed in article footers...">{{ old('bio', $user->bio) }}</textarea>
                </div>

                <!-- Timezone Selector -->
                <div class="p-3 bg-body-tertiary rounded-4 border mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small fw-bold text-body mb-0">
                            <i class="bi bi-clock-history text-primary me-1"></i> My Preferred Timezone
                        </label>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 extra-small">
                            Current Time: {{ \App\Helpers\TimezoneHelper::format(now(), 'M d, Y h:i A', $user) }}
                        </span>
                    </div>
                    <p class="text-muted extra-small mb-2">All article timestamps, audit logs, and incoming dispatches will be translated to your selected timezone.</p>
                    <select name="timezone" class="form-select form-select-sm rounded-3">
                        @foreach($timezones as $region => $list)
                            <optgroup label="{{ $region }}">
                                @foreach($list as $tzKey => $tzLabel)
                                    <option value="{{ $tzKey }}" {{ ($user->timezone ?? 'UTC') === $tzKey ? 'selected' : '' }}>
                                        {{ $tzLabel }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-primary py-2">
                    <i class="bi bi-check2-circle me-1"></i> Save Profile Details
                </button>
            </form>
        </div>

        <!-- Security & Password Change -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-body">
            <h5 class="fw-black text-body mb-1">Security &amp; Passphrase</h5>
            <p class="text-muted extra-small mb-4">Update your master credentials to maintain maximum protection over the Maison editorial desk.</p>

            <form action="{{ route('admin.profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Current Password <span class="text-danger">*</span></label>
                    <input type="password" name="current_password" required class="form-control rounded-3" placeholder="••••••••••••">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">New Secure Passphrase <span class="text-danger">*</span></label>
                        <input type="password" name="password" required minlength="8" class="form-control rounded-3" placeholder="At least 8 characters">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Confirm New Passphrase <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" required minlength="8" class="form-control rounded-3" placeholder="Repeat passphrase">
                    </div>
                </div>

                <button type="submit" class="btn btn-outline-warning rounded-pill px-4 fw-bold py-2">
                    <i class="bi bi-shield-lock-fill me-1"></i> Update Security Passphrase
                </button>
            </form>
        </div>

        <!-- Two-Factor Authentication & Session Management -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-body">
            <h5 class="fw-black text-body mb-1">Two-Factor Authentication &amp; Sessions</h5>
            <p class="text-muted extra-small mb-4">Additional security layers and active browser session management.</p>

            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 bg-body-tertiary rounded-4 border mb-3 gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-shape {{ $user->two_factor_enabled ? 'bg-success text-success' : 'bg-secondary text-secondary' }} bg-opacity-10 rounded-circle p-2" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi {{ $user->two_factor_enabled ? 'bi-shield-check' : 'bi-shield-x' }} fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-body">Two-Factor Authentication (2FA)</h6>
                        <span class="text-muted extra-small">Add an extra verification safeguard to your login sequence.</span>
                    </div>
                </div>
                <form action="{{ route('admin.profile.two-factor') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn {{ $user->two_factor_enabled ? 'btn-outline-danger' : 'btn-success' }} btn-sm rounded-pill px-3 fw-bold">
                        {{ $user->two_factor_enabled ? 'Deactivate 2FA' : 'Activate 2FA' }}
                    </button>
                </form>
            </div>

            <!-- Other Sessions Management -->
            <div class="p-3 bg-body-tertiary rounded-4 border">
                <h6 class="fw-bold text-body small mb-1">Active Device Sessions</h6>
                <p class="text-muted extra-small mb-3">If you suspect your credentials were left active on another machine, invalidate all other active sessions.</p>

                <form action="{{ route('admin.profile.other-sessions') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="password" name="password" placeholder="Confirm your password to logout others" required class="form-control form-control-sm rounded-3" style="max-width: 320px;">
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold flex-shrink-0">
                        <i class="bi bi-box-arrow-right me-1"></i> Invalidate Other Devices
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarFallback = document.getElementById('avatarFallback');
    const avatarLoading = document.getElementById('avatarLoading');

    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            if (!this.files || !this.files[0]) return;
            const file = this.files[0];

            // 1. Instant client-side preview
            const reader = new FileReader();
            reader.onload = function(e) {
                if (avatarPreview) {
                    avatarPreview.src = e.target.result;
                    avatarPreview.classList.remove('d-none');
                }
                if (avatarFallback) {
                    avatarFallback.classList.add('d-none');
                }
            };
            reader.readAsDataURL(file);

            // 2. Instant seamless AJAX upload
            if (avatarLoading) avatarLoading.classList.remove('d-none');

            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("admin.profile.avatar") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.avatar_url) {
                    // Update main preview
                    if (avatarPreview) avatarPreview.src = data.avatar_url;

                    // Update all user avatars across the page (header, sidebar, offcanvas)
                    document.querySelectorAll('img.global-user-avatar').forEach(img => {
                        img.src = data.avatar_url;
                        img.style.display = 'inline-block';
                    });
                    document.querySelectorAll('.avatar-placeholder, .offcanvas-user-fallback').forEach(ph => {
                        const parent = ph.parentElement;
                        const existingImg = parent ? parent.querySelector('img.global-user-avatar') : null;
                        if (existingImg) {
                            existingImg.src = data.avatar_url;
                            existingImg.style.display = 'inline-block';
                            ph.style.display = 'none';
                        } else if (parent) {
                            const newImg = document.createElement('img');
                            newImg.src = data.avatar_url;
                            newImg.alt = 'User Avatar';
                            newImg.className = 'rounded-circle object-fit-cover shadow-sm global-user-avatar';
                            newImg.style.width = ph.style.width || '40px';
                            newImg.style.height = ph.style.height || '40px';
                            newImg.style.minWidth = ph.style.minWidth || '40px';
                            newImg.style.aspectRatio = '1';
                            parent.insertBefore(newImg, ph);
                            ph.style.display = 'none';
                        }
                    });

                    // Luxury SweetAlert2 notification
                    if (window.EliteAlert) {
                        window.EliteAlert.toast(data.message || 'Profile photograph updated successfully.', 'success');
                    }
                } else if (data.errors || data.message) {
                    if (window.EliteAlert) {
                        window.EliteAlert.alert('Upload Failed', data.message || 'Please ensure the file is an image under 5MB.', 'error');
                    } else {
                        alert(data.message || 'Upload failed. Please ensure the file is an image under 5MB.');
                    }
                }
            })
            .catch(err => {
                console.error('Avatar upload error:', err);
            })
            .finally(() => {
                if (avatarLoading) avatarLoading.classList.add('d-none');
            });
        });
    }
});
</script>
@endpush
@endsection
