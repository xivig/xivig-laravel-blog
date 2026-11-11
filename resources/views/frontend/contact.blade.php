@extends('frontend.layouts.app')

@section('title', 'Editorial Correspondence & Inquiries | ' . $globalSettings['title'])
@section('meta_description', 'Direct correspondence to the editorial atelier of Maison XIVIG.')

@section('content')
<div class="container py-4 py-md-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb extra-small text-uppercase letter-spacing-1 align-items-center mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('blog.index') }}" class="text-decoration-none text-secondary hover-gold d-inline-flex align-items-center gap-1">
                    <i class="bi bi-house-door"></i> Chronicles
                </a>
            </li>
            <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">Correspondence</li>
        </ol>
    </nav>

    <!-- Hero Header -->
    <div class="text-center max-w-750 mx-auto mb-5">
        <span class="badge bg-gold text-dark text-uppercase letter-spacing-1 px-3 py-1 rounded-pill mb-3 fw-bold">
            Atelier Salon
        </span>
        <h1 class="display-4 fw-bold mb-3">Editorial Correspondence</h1>
        <p class="text-secondary fs-5 mb-0">
            Address editorial queries, horological inquiries, haute couture collaborations, and dispatches directly to the Maison desk.
        </p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="bento-card p-4 p-md-5">
                @if(session('contact_success'))
                    <div class="alert alert-success rounded-4 p-4 mb-4 d-flex align-items-center gap-3">
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Dispatch Transmitted</h6>
                            <p class="small mb-0">{{ session('contact_success') }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <!-- Honeypot -->
                    <div style="display:none !important;" aria-hidden="true">
                        <input type="text" name="contact_honeypot" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="form-control bg-body border-subtle rounded-3 py-2 shadow-none @error('name') is-invalid @enderror" placeholder="e.g. Countess Éléonore">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="form-control bg-body border-subtle rounded-3 py-2 shadow-none @error('email') is-invalid @enderror" placeholder="e.g. eleonore@salon.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Subject of Inquiry</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="form-control bg-body border-subtle rounded-3 py-2 shadow-none @error('subject') is-invalid @enderror" placeholder="e.g. Horological Critique Collaboration">
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Dispatch Message *</label>
                        <textarea name="message" rows="5" required class="form-control bg-body border-subtle rounded-3 shadow-none small @error('message') is-invalid @enderror" placeholder="Convey your correspondence with precision...">{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Atelier Security Arithmetic Challenge -->
                    <div class="p-3 mb-4 rounded-3 border bg-body-tertiary">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <label class="form-label extra-small fw-bold text-uppercase letter-spacing-1 mb-0 text-muted">
                                <i class="bi bi-shield-check text-gold me-1"></i> Security Verification *
                            </label>
                            <button type="button" id="refresh-captcha-btn" class="btn btn-link btn-sm text-decoration-none text-gold p-0 extra-small">
                                <i class="bi bi-arrow-clockwise"></i> New Calculation
                            </button>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div class="px-3 py-2 bg-dark text-white rounded-3 fw-bold font-monospace letter-spacing-1" id="captcha-display" style="border: 1px solid rgba(212, 175, 55, 0.35); min-width: 120px; text-align: center;">
                                    {{ session('contact_captcha_num1', 4) }} + {{ session('contact_captcha_num2', 3) }} = ?
                                </div>
                            </div>
                            <div class="col">
                                <input type="number" name="captcha" id="captcha-input" required class="form-control bg-body border-subtle rounded-3 py-2 shadow-none @error('captcha') is-invalid @enderror" placeholder="Calculate and enter the sum..." autocomplete="off">
                            </div>
                        </div>
                        @error('captcha')
                            <div class="text-danger small mt-2">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $message }}
                            </div>
                        @enderror
                        <div class="extra-small text-muted mt-2">
                            Solve the arithmetic challenge above to verify human patronage.
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                        <span class="extra-small text-muted">Transmitted securely under Maison privacy protocols.</span>
                        <button type="submit" class="btn btn-gold rounded-pill px-5 py-2 fw-bold">
                            Send Correspondence &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <div class="editorial-sidebar-card">
                    <div class="sidebar-widget-title">
                        <i class="bi bi-geo-alt-fill"></i> Atelier Bureaus
                    </div>
                    <ul class="list-unstyled small d-flex flex-column gap-3 mb-0">
                        <li>
                            <strong class="text-gold d-block">Paris Editorial Office</strong>
                            <span class="text-secondary extra-small">Place Vendôme, 75001 Paris, France</span>
                        </li>
                        <li>
                            <strong class="text-gold d-block">Geneva Horology Desk</strong>
                            <span class="text-secondary extra-small">Rue du Rhône, 1204 Genève, Switzerland</span>
                        </li>
                        <li>
                            <strong class="text-gold d-block">Milan Savoir-Faire</strong>
                            <span class="text-secondary extra-small">Via Montenapoleone, 20121 Milano, Italy</span>
                        </li>
                    </ul>
                </div>

                <div class="vip-dispatch-box">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-shield-check text-gold fs-5"></i>
                        <span class="extra-small text-gold fw-bold text-uppercase letter-spacing-1">Press Inquiries</span>
                    </div>
                    <h6 class="fw-bold mb-2">Press & Embargoed Dispatches</h6>
                    <p class="text-secondary extra-small mb-0">
                        For runway accreditation, horological Baselworld reviews, or confidential embargo previews, please indicate in the subject line.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('refresh-captcha-btn');
    const display = document.getElementById('captcha-display');
    const input = document.getElementById('captcha-input');

    if (refreshBtn && display) {
        refreshBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = refreshBtn.querySelector('i');
            if (icon) icon.classList.add('rotate-360');
            refreshBtn.style.opacity = '0.5';

            fetch('{{ route("contact.captcha.refresh") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.question) {
                    display.textContent = data.question;
                    if (input) {
                        input.value = '';
                        input.focus();
                    }
                }
            })
            .catch(err => console.error('Captcha refresh error:', err))
            .finally(() => {
                refreshBtn.style.opacity = '1';
                if (icon) icon.classList.remove('rotate-360');
            });
        });
    }
});
</script>
@endsection
