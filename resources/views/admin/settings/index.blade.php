@extends('admin.layouts.admin')

@section('title', 'Gazette Settings | XIVIG Maison Gazette')
@section('page_title', 'Maison Gazette Settings')

@section('content')

<!-- 1. Site Maintenance Card (Matching Image 1) -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex align-items-center gap-2">
            <span class="rounded-circle bg-danger bg-opacity-10 text-danger d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                <i class="bi bi-dash-circle-fill"></i>
            </span>
            <h5 class="fw-black text-danger mb-0">Site Maintenance</h5>
        </div>
        <div class="form-check form-switch mb-0">
            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="siteMaintenanceToggle" style="width: 2.8em; height: 1.5em;" {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
        </div>
    </div>
    <p class="text-muted extra-small mb-3">Control public access during architectural refinement.</p>

    <!-- Warning Redirection Banner -->
    <div class="alert border-0 rounded-4 p-3 d-flex align-items-start gap-3 mb-0 {{ ($settings['maintenance_mode'] ?? '0') === '1' ? '' : 'd-none' }}" id="maintenanceWarningAlert" style="background-color: #fef3c7; color: #92400e;">
        <div class="flex-shrink-0 mt-1">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
        </div>
        <div>
            <strong class="d-block mb-1 small fw-bold">Warning: Public Redirection Active</strong>
            <span class="extra-small" style="line-height: 1.5; color: #78350f;">
                While active, the public landing page and blog will be inaccessible to standard users. Administrators still have bypass access.
            </span>
        </div>
    </div>
</div>

<!-- 2. External Integrations Card (Matching Image 1) -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
            <i class="bi bi-dash-circle-fill"></i>
        </span>
        <h5 class="fw-black text-body mb-0">External Integrations</h5>
    </div>
    <p class="text-muted extra-small mb-4">Configure third-party analytics, intelligence endpoints, and newsletter provider credentials.</p>

    <form action="{{ route('admin.settings.integrations') }}" method="POST">
        @csrf
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <label class="form-label extra-small fw-bold text-uppercase tracking-wider text-muted">GOOGLE ANALYTICS G-ID</label>
                <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}" placeholder="G-XXXXXXXXXX" class="form-control rounded-3 py-2 bg-body-tertiary border">
            </div>
            <div class="col-md-6">
                <label class="form-label extra-small fw-bold text-uppercase tracking-wider text-muted">OPENAI API KEY (AI CONTENT)</label>
                <div class="input-group">
                    <input type="password" name="openai_api_key" id="openaiApiKeyInput" value="{{ old('openai_api_key', $settings['openai_api_key'] ?? '') }}" placeholder="sk-proj-••••••••••••••••••••••••" class="form-control rounded-start-3 py-2 bg-body-tertiary border">
                    <button class="btn btn-outline-secondary border bg-body-tertiary toggle-pw-btn" type="button" data-target="openaiApiKeyInput">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label extra-small fw-bold text-uppercase tracking-wider text-muted">MAILCHIMP API KEY</label>
                <div class="input-group">
                    <input type="password" name="mailchimp_api_key" id="mailchimpApiKeyInput" value="{{ old('mailchimp_api_key', $settings['mailchimp_api_key'] ?? '') }}" placeholder="••••••••••••••••••••••••-us1" class="form-control rounded-start-3 py-2 bg-body-tertiary border">
                    <button class="btn btn-outline-secondary border bg-body-tertiary toggle-pw-btn" type="button" data-target="mailchimpApiKeyInput">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label extra-small fw-bold text-uppercase tracking-wider text-muted">MAILCHIMP LIST ID</label>
                <input type="text" name="mailchimp_list_id" value="{{ old('mailchimp_list_id', $settings['mailchimp_list_id'] ?? '') }}" placeholder="XXXXXXXXXX" class="form-control rounded-3 py-2 bg-body-tertiary border">
            </div>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-primary">
            Sync Integrations
        </button>
    </form>
</div>

<!-- 3. Core Editorial Configuration -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-black text-body mb-2">Editorial Configuration &amp; Branding</h5>
            <p class="text-muted extra-small mb-4">System properties governing publication titles, SEO descriptions, and pagination.</p>

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Gazette Title <span class="text-danger">*</span></label>
                    <input type="text" name="site_title" value="{{ old('site_title', $settings['site_title']) }}" required class="form-control rounded-3">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Gazette Tagline</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}" class="form-control rounded-3">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Editorial Synopsis &amp; Search Description</label>
                    <textarea name="site_description" rows="3" class="form-control rounded-3">{{ old('site_description', $settings['site_description']) }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Editorial Contact Email <span class="text-danger">*</span></label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" required class="form-control form-control-sm rounded-3">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Stories Per Page <span class="text-danger">*</span></label>
                        <input type="number" name="posts_per_page" value="{{ old('posts_per_page', $settings['posts_per_page']) }}" required min="3" max="50" class="form-control form-control-sm rounded-3">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Comments Moderation</label>
                        <select name="comment_auto_approve" class="form-select form-select-sm rounded-3">
                            <option value="1" {{ $settings['comment_auto_approve'] == '1' ? 'selected' : '' }}>Auto-Approve</option>
                            <option value="0" {{ $settings['comment_auto_approve'] == '0' ? 'selected' : '' }}>Manual Review</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4 p-3 bg-body-tertiary rounded-4 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small fw-bold text-body mb-0">
                            <i class="bi bi-clock-history text-primary me-1"></i> Default System Timezone
                        </label>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 extra-small">
                            Active: {{ \App\Helpers\TimezoneHelper::format(now(), 'M d, Y h:i A') }}
                        </span>
                    </div>
                    <p class="text-muted extra-small mb-2">Global baseline timezone used for editorial scheduling, activity audit timestamps, and dispatches.</p>
                    <select name="timezone" class="form-select form-select-sm rounded-3">
                        @foreach($timezones as $region => $list)
                            <optgroup label="{{ $region }}">
                                @foreach($list as $tzKey => $tzLabel)
                                    <option value="{{ $tzKey }}" {{ ($settings['timezone'] ?? 'UTC') === $tzKey ? 'selected' : '' }}>
                                        {{ $tzLabel }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-primary py-2">
                    <i class="bi bi-save2-fill me-1"></i> Save Configuration
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-black text-body mb-3 text-uppercase small">Syndication &amp; Feeds</h6>
            <div class="d-flex flex-column gap-3 extra-small">
                <div class="p-3 bg-body-tertiary rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-body">RSS 2.0 Feed</span>
                        <a href="{{ route('blog.feed') }}" target="_blank" class="text-primary fw-bold text-decoration-none">
                            Open <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                    <span class="text-muted font-monospace">/feed.xml</span>
                </div>
                <div class="p-3 bg-body-tertiary rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-body">Dynamic XML Sitemap</span>
                        <a href="{{ route('blog.sitemap') }}" target="_blank" class="text-primary fw-bold text-decoration-none">
                            Open <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                    <span class="text-muted font-monospace">/sitemap.xml</span>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-black text-body mb-3 text-uppercase small">Engine Information</h6>
            <ul class="list-group list-group-flush rounded-3 border extra-small">
                <li class="list-group-item bg-transparent d-flex justify-content-between py-2">
                    <span class="text-muted">Storage Driver</span>
                    <span class="fw-bold">Local Disk</span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between py-2">
                    <span class="text-muted">Database</span>
                    <span class="fw-bold">SQLite v3</span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between py-2">
                    <span class="text-muted">Rate Limiting</span>
                    <span class="badge bg-success-subtle text-success">Armed</span>
                </li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Site Maintenance Instant Toggle
        const maintToggle = document.getElementById('siteMaintenanceToggle');
        const warningAlert = document.getElementById('maintenanceWarningAlert');

        if (maintToggle) {
            maintToggle.addEventListener('change', function() {
                const isEnabled = this.checked ? 1 : 0;
                const toggle = this;

                fetch("{{ route('admin.settings.maintenance.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        maintenance_mode: isEnabled
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (isEnabled) {
                            warningAlert.classList.remove('d-none');
                        } else {
                            warningAlert.classList.add('d-none');
                        }
                        if (typeof EliteAlert !== 'undefined') {
                            EliteAlert.toast('success', data.message);
                        }
                    } else {
                        toggle.checked = !isEnabled;
                        if (typeof EliteAlert !== 'undefined') {
                            EliteAlert.toast('error', 'Failed to toggle maintenance mode.');
                        }
                    }
                })
                .catch(() => {
                    toggle.checked = !isEnabled;
                    if (typeof EliteAlert !== 'undefined') {
                        EliteAlert.toast('error', 'Network communication error.');
                    }
                });
            });
        }

        // Toggle Password / API Key Visibility
        document.querySelectorAll('.toggle-pw-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });
    });
</script>
@endpush

@endsection
