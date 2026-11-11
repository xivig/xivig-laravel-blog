<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maison Gazette | Create Elite Account</title>
    
    <script>
        (function() {
            var t = localStorage.getItem('xivig_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', t);
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    
    <!-- Premium Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Cinzel:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --gold-primary: #c9a96e;
            --gold-light: #dfc28d;
            --gold-dark: #9f7d43;
            --auth-bg-dark: #07090d;
        }

        body.auth-page {
            background: 
                radial-gradient(circle at 12% 18%, rgba(201, 169, 110, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 88% 82%, rgba(99, 102, 241, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(15, 23, 42, 0.8) 0%, transparent 100%),
                var(--auth-bg-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #f8fafc;
            padding: 2.5rem 1.25rem;
        }

        body.auth-page::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(201, 169, 110, 0.08) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 0;
        }

        .auth-card {
            width: 100%;
            max-width: 620px; /* Wider and spacious for dual-column fields */
            background: rgba(16, 20, 28, 0.82);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(201, 169, 110, 0.25);
            border-radius: 30px;
            box-shadow: 
                0 30px 70px -15px rgba(0, 0, 0, 0.85),
                0 0 50px rgba(201, 169, 110, 0.08);
            padding: 3.25rem 3rem;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        @media (max-width: 576px) {
            .auth-card {
                padding: 2.25rem 1.5rem;
                border-radius: 22px;
            }
        }

        .brand-monogram-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 62px;
            height: 62px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(201, 169, 110, 0.25), rgba(201, 169, 110, 0.05));
            border: 1px solid rgba(201, 169, 110, 0.4);
            box-shadow: 0 0 25px rgba(201, 169, 110, 0.25);
            color: var(--gold-primary);
            font-size: 1.8rem;
            font-family: 'Cinzel', serif;
            font-weight: 800;
            margin-bottom: 1.25rem;
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 0.35rem;
        }

        .auth-subtitle {
            color: #94a3b8;
            font-size: 0.92rem;
            margin-bottom: 2rem;
        }

        .social-auth {
            display: flex;
            gap: 0.85rem;
            margin-bottom: 1.75rem;
        }

        .social-btn {
            flex: 1;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .social-btn:hover {
            background: rgba(201, 169, 110, 0.12);
            border-color: var(--gold-primary);
            color: var(--gold-primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
        }

        .auth-divider {
            display: flex;
            align-items: center;
            margin-bottom: 1.75rem;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .auth-divider::before, .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.08);
        }

        .auth-divider span {
            padding: 0 1rem;
        }

        .auth-input-group {
            background: rgba(0, 0, 0, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .auth-input-group:focus-within {
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 4px rgba(201, 169, 110, 0.15), 0 0 20px rgba(201, 169, 110, 0.2);
        }

        .auth-input-group .input-group-text {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding-left: 1.15rem;
            font-size: 1.1rem;
        }

        .auth-input-group .form-control {
            background: transparent !important;
            border: none;
            color: #ffffff;
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
        }

        .auth-input-group .form-control:focus {
            box-shadow: none;
            color: #ffffff;
        }

        .auth-input-group .form-control::placeholder {
            color: #64748b;
            opacity: 0.7;
        }

        .captcha-panel {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .captcha-frame {
            background: #ffffff;
            border-radius: 12px;
            padding: 4px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            height: 50px;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.15);
            cursor: pointer;
        }

        .captcha-frame img {
            height: 42px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
        }

        .btn-reload-captcha {
            height: 50px;
            width: 50px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: var(--gold-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
        }

        .btn-reload-captcha:hover {
            background: rgba(201, 169, 110, 0.15);
            border-color: var(--gold-primary);
            color: var(--gold-light);
            transform: rotate(30deg);
        }

        .spin-icon {
            display: inline-block;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .btn-elite-gold {
            background: linear-gradient(135deg, #dfc28d 0%, #c9a96e 50%, #9f7d43 100%);
            color: #0b0d11;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.5px;
            border: none;
            border-radius: 16px;
            padding: 0.95rem 1.5rem;
            box-shadow: 0 8px 24px -4px rgba(201, 169, 110, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
        }

        .btn-elite-gold:hover {
            background: linear-gradient(135deg, #ebd6ae 0%, #d8b87e 50%, #af8c4f 100%);
            color: #000000;
            transform: translateY(-2px);
            box-shadow: 0 12px 30px -4px rgba(201, 169, 110, 0.55);
        }

        .text-gold {
            color: var(--gold-primary) !important;
        }

        .text-gold:hover {
            color: var(--gold-light) !important;
        }

        /* -------------------------------------------------------------------------
           THEME TOGGLE BUTTON & LIGHT THEME LUXURY STYLING
        ------------------------------------------------------------------------- */
        .theme-toggle-wrap {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
        }

        .theme-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.55rem 1.15rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            border: 1px solid rgba(201, 169, 110, 0.35);
            background: rgba(14, 19, 26, 0.88);
            color: #dfc28d;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        .theme-toggle-btn:hover {
            transform: translateY(-2px);
            border-color: var(--gold-primary);
            box-shadow: 0 12px 30px rgba(201, 169, 110, 0.3);
            color: #ffffff;
        }

        .theme-toggle-btn i {
            font-size: 1rem;
            line-height: 1;
        }

        .theme-icon-sun {
            display: none;
            color: #f59e0b;
        }

        .theme-icon-moon {
            display: inline-block;
            color: #dfc28d;
        }

        /* LIGHT THEME LUXURY OVERRIDES */
        [data-bs-theme="light"] {
            --auth-bg-dark: #f5f7fa;
        }

        [data-bs-theme="light"] body.auth-page {
            background: 
                radial-gradient(circle at 12% 18%, rgba(201, 169, 110, 0.2) 0%, transparent 45%),
                radial-gradient(circle at 88% 82%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.95) 0%, transparent 100%),
                #f4f6fa !important;
            color: #1e293b;
        }

        [data-bs-theme="light"] body.auth-page::before {
            background-image: radial-gradient(rgba(201, 169, 110, 0.25) 1px, transparent 1px);
        }

        [data-bs-theme="light"] .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.92);
            border-color: rgba(201, 169, 110, 0.5);
            color: #9f7d43;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        [data-bs-theme="light"] .theme-toggle-btn:hover {
            color: #0f172a;
            border-color: var(--gold-primary);
            box-shadow: 0 10px 28px rgba(201, 169, 110, 0.25);
        }

        [data-bs-theme="light"] .theme-icon-sun {
            display: inline-block;
        }

        [data-bs-theme="light"] .theme-icon-moon {
            display: none;
        }

        [data-bs-theme="light"] .auth-card {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(201, 169, 110, 0.45);
            box-shadow: 
                0 25px 60px -15px rgba(15, 23, 42, 0.12),
                0 0 35px rgba(201, 169, 110, 0.15);
        }

        [data-bs-theme="light"] .brand-monogram-box {
            background: linear-gradient(135deg, rgba(201, 169, 110, 0.2), rgba(201, 169, 110, 0.06));
            border-color: rgba(201, 169, 110, 0.55);
            box-shadow: 0 0 20px rgba(201, 169, 110, 0.2);
            color: var(--gold-dark);
        }

        [data-bs-theme="light"] .auth-title {
            color: #0f172a;
        }

        [data-bs-theme="light"] .auth-subtitle {
            color: #64748b;
        }

        [data-bs-theme="light"] .social-btn {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #334155;
        }

        [data-bs-theme="light"] .social-btn:hover {
            background: rgba(201, 169, 110, 0.15);
            border-color: var(--gold-primary);
            color: var(--gold-dark);
            box-shadow: 0 6px 18px rgba(201, 169, 110, 0.25);
        }

        [data-bs-theme="light"] .auth-divider {
            color: #94a3b8;
        }

        [data-bs-theme="light"] .auth-divider::before,
        [data-bs-theme="light"] .auth-divider::after {
            background: #e2e8f0;
        }

        [data-bs-theme="light"] .form-label,
        [data-bs-theme="light"] label {
            color: #334155 !important;
        }

        [data-bs-theme="light"] .auth-input-group {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        [data-bs-theme="light"] .auth-input-group:focus-within {
            background: #ffffff;
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 4px rgba(201, 169, 110, 0.2), 0 0 20px rgba(201, 169, 110, 0.15);
        }

        [data-bs-theme="light"] .auth-input-group .input-group-text {
            color: #64748b;
        }

        [data-bs-theme="light"] .auth-input-group .form-control {
            color: #0f172a !important;
        }

        [data-bs-theme="light"] .auth-input-group .form-control::placeholder {
            color: #94a3b8;
        }

        [data-bs-theme="light"] .captcha-panel {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        [data-bs-theme="light"] .captcha-frame {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        [data-bs-theme="light"] .btn-reload-captcha {
            background: #ffffff;
            border-color: #cbd5e1;
            color: var(--gold-dark);
        }

        [data-bs-theme="light"] .btn-reload-captcha:hover {
            background: rgba(201, 169, 110, 0.15);
            border-color: var(--gold-primary);
            color: var(--gold-primary);
        }

        [data-bs-theme="light"] .form-check-label {
            color: #475569 !important;
        }

        [data-bs-theme="light"] .text-secondary {
            color: #64748b !important;
        }
    </style>
</head>
<body class="auth-page">

    <!-- Floating Theme Switcher -->
    <div class="theme-toggle-wrap">
        <button type="button" class="theme-toggle-btn" id="themeToggleBtn" aria-label="Toggle Light/Dark Theme" title="Switch Theme">
            <i class="bi bi-sun-fill theme-icon-sun"></i>
            <i class="bi bi-moon-stars-fill theme-icon-moon"></i>
            <span class="theme-toggle-text">Theme</span>
        </button>
    </div>

    <div class="auth-card">
        
        <!-- Header & Monogram -->
        <div class="text-center">
            <div class="brand-monogram-box">X</div>
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-subtitle">Join the elite network of Xivig architects &amp; editorial salon.</p>
        </div>

        <!-- Social Connectors -->
        <div class="social-auth">
            <a href="javascript:void(0);" class="social-btn" title="Register with GitHub">
                <i class="fa-brands fa-github"></i>
            </a>
            <a href="javascript:void(0);" class="social-btn text-danger" title="Register with Google">
                <i class="fa-brands fa-google"></i>
            </a>
            <a href="javascript:void(0);" class="social-btn text-primary" title="Register with LinkedIn">
                <i class="fa-brands fa-linkedin-in"></i>
            </a>
        </div>

        <div class="auth-divider">
            <span>OR REGISTER WITH EDITORIAL CREDENTIALS</span>
        </div>

        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-4 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <form action="{{ route('admin.register.submit') }}" method="POST">
            @csrf

            <!-- 2-Column Row: Full Name & Editorial Email -->
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Full Name</label>
                    <div class="input-group auth-input-group @error('name') border-danger @enderror">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus class="form-control" placeholder="Alexander Vance">
                    </div>
                    @error('name')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Editorial Email</label>
                    <div class="input-group auth-input-group @error('email') border-danger @enderror">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}" required class="form-control" placeholder="architect@xivig.com">
                    </div>
                    @error('email')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- 2-Column Row: Password & Confirmation -->
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Secret Passkey</label>
                    <div class="input-group auth-input-group @error('password') border-danger @enderror">
                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                        <input type="password" name="password" required autocomplete="new-password" class="form-control" placeholder="Min 8 characters">
                    </div>
                    @error('password')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Confirm Passkey</label>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                        <input type="password" name="password_confirmation" required autocomplete="new-password" class="form-control" placeholder="Repeat passkey">
                    </div>
                </div>
            </div>

            <!-- Security Verification / Math Captcha Panel (Wide & Seamless) -->
            <div class="captcha-panel">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-secondary">
                        <i class="bi bi-shield-check text-gold me-1"></i> Security Verification
                    </span>
                    <span class="extra-small text-muted">Solve math challenge</span>
                </div>

                <div class="row g-2 align-items-stretch">
                    <div class="col-sm-7 d-flex align-items-center gap-2">
                        <div class="captcha-frame flex-grow-1" title="Click to refresh equation">
                            {!! captcha_img('math') !!}
                        </div>
                        <button type="button" class="btn btn-reload-captcha reload-captcha flex-shrink-0" title="Reload Challenge">
                            <i class="bi bi-arrow-clockwise fs-5"></i>
                        </button>
                    </div>
                    <div class="col-sm-5">
                        <div class="input-group auth-input-group h-100 @error('captcha') border-danger @enderror">
                            <span class="input-group-text"><i class="bi bi-calculator text-gold"></i></span>
                            <input type="text" name="captcha" class="form-control" placeholder="Answer" required autocomplete="off">
                        </div>
                    </div>
                </div>

                @error('captcha')
                    <span class="text-danger small mt-2 d-block">
                        <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- Terms Checkbox -->
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="termsCheck" required checked>
                    <label class="form-check-label text-secondary small" for="termsCheck">
                        I accept the <a href="javascript:void(0);" class="text-gold text-decoration-none">Editorial Governance Standards</a> &amp; Terms
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-elite-gold mb-4">
                <i class="bi bi-person-plus-fill me-2"></i> Register Editorial Account
            </button>

            <!-- Navigation Links -->
            <div class="text-center small mb-3">
                <span class="text-secondary">Already hold an editorial seat?</span>
                <a href="{{ route('admin.login') }}" class="text-gold text-decoration-none fw-bold ms-1">Sign In &rarr;</a>
            </div>

            <div class="text-center">
                <a href="{{ route('blog.index') }}" class="text-secondary extra-small text-decoration-none opacity-75 hover-opacity-100">
                    &larr; Return to Public Gazette
                </a>
            </div>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-25 text-secondary extra-small opacity-50 text-uppercase" style="letter-spacing: 0.1em;">
            Protected by CSRF Token &amp; Cryptographic Captcha • XIVIG ARCHITECT OS
        </div>
    </div>

    <!-- Captcha Reload Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reloadBtn = document.querySelector('.reload-captcha');
            const captchaFrame = document.querySelector('.captcha-frame');

            function refreshCaptcha() {
                if (!reloadBtn) return;
                const icon = reloadBtn.querySelector('i');
                reloadBtn.disabled = true;
                if (icon) icon.classList.add('spin-icon');

                fetch('/captcha/api/math')
                    .then(response => response.json())
                    .then(data => {
                        const img = document.querySelector('.captcha-frame img');
                        if (img && data.img) {
                            img.src = data.img;
                        }
                    })
                    .catch(() => {
                        const img = document.querySelector('.captcha-frame img');
                        if (img) img.src = '/captcha/math?' + Math.random();
                    })
                    .finally(() => {
                        reloadBtn.disabled = false;
                        if (icon) icon.classList.remove('spin-icon');
                    });
            }

            if (reloadBtn) reloadBtn.addEventListener('click', refreshCaptcha);
            if (captchaFrame) captchaFrame.addEventListener('click', refreshCaptcha);
        });

        // Theme Toggle Switcher Engine
        (function() {
            const toggleBtn = document.getElementById('themeToggleBtn');
            if (!toggleBtn) return;

            function syncThemeUI() {
                const current = document.documentElement.getAttribute('data-bs-theme') || 'dark';
                const textSpan = toggleBtn.querySelector('.theme-toggle-text');
                if (textSpan) {
                    textSpan.textContent = current === 'dark' ? 'Light' : 'Dark';
                }
            }

            syncThemeUI();

            toggleBtn.addEventListener('click', function() {
                const current = document.documentElement.getAttribute('data-bs-theme') || 'dark';
                const next = current === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-bs-theme', next);
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem('xivig_theme', next);
                syncThemeUI();
            });
        })();
    </script>

</body>
</html>
