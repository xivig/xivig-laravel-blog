<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maison Gazette | Two-Factor Verification</title>
    
    <script>
        (function() {
            var t = localStorage.getItem('xivig_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', t);
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/blog-admin.css') }}">
    <style>
        .text-gold {
            color: #c9a96e !important;
        }
        .btn-gold {
            background-color: #c9a96e;
            color: #0b0d11;
            border: none;
            transition: all 0.25s ease;
        }
        .btn-gold:hover {
            background-color: #dfc28d;
            color: #0b0d11;
        }
        .otp-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 1.5rem 0;
        }
        .otp-input {
            width: 48px;
            height: 56px;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background-color: #0b0d11;
            color: #fff;
            transition: all 0.2s ease;
        }
        .otp-input:focus {
            outline: none;
            border-color: #c9a96e;
            box-shadow: 0 0 12px rgba(201, 169, 110, 0.4);
        }

        /* Theme Switcher Button */
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
            border-color: #c9a96e;
            box-shadow: 0 12px 30px rgba(201, 169, 110, 0.3);
            color: #ffffff;
        }
        .theme-toggle-btn i { font-size: 1rem; line-height: 1; }
        .theme-icon-sun { display: none; color: #f59e0b; }
        .theme-icon-moon { display: inline-block; color: #dfc28d; }

        /* Light Theme Overrides */
        [data-bs-theme="light"] body {
            background-color: #f4f6fa !important;
            color: #1e293b !important;
        }
        [data-bs-theme="light"] .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.92);
            border-color: rgba(201, 169, 110, 0.5);
            color: #9f7d43;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }
        [data-bs-theme="light"] .theme-toggle-btn:hover {
            color: #0f172a;
            border-color: #c9a96e;
        }
        [data-bs-theme="light"] .theme-icon-sun { display: inline-block; }
        [data-bs-theme="light"] .theme-icon-moon { display: none; }
        [data-bs-theme="light"] .card {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-color: rgba(201, 169, 110, 0.45) !important;
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.12) !important;
        }
        [data-bs-theme="light"] h3 {
            color: #0f172a !important;
        }
        [data-bs-theme="light"] .otp-input {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        [data-bs-theme="light"] .otp-input:focus {
            background-color: #ffffff !important;
            border-color: #c9a96e !important;
        }
    </style>
</head>
<body class="bg-black text-light d-flex align-items-center justify-content-center min-vh-100 p-3">

    <!-- Floating Theme Switcher -->
    <div class="theme-toggle-wrap">
        <button type="button" class="theme-toggle-btn" id="themeToggleBtn" aria-label="Toggle Light/Dark Theme" title="Switch Theme">
            <i class="bi bi-sun-fill theme-icon-sun"></i>
            <i class="bi bi-moon-stars-fill theme-icon-moon"></i>
            <span class="theme-toggle-text">Theme</span>
        </button>
    </div>

    <div class="card border-0 rounded-4 bg-dark bg-opacity-75 shadow-lg p-4 p-sm-5 border border-secondary text-center" style="max-width: 460px; width: 100%;">
        
        <div class="mb-4">
            <div class="brand-monogram mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                <i class="bi bi-shield-check text-gold"></i>
            </div>
            <h3 class="fw-bold tracking-wider mb-1">Two-Step Verification</h3>
            <p class="text-secondary small mb-1">
                Enter the 6-digit cryptographic security code dispatched to:
            </p>
            <p class="fw-bold text-gold small">{{ $user->email ?? ($email ?? 'your email') }}</p>
        </div>

        @if(session('status'))
            <div class="alert alert-info py-2 px-3 small rounded-3 mb-3 text-start">
                <i class="bi bi-info-circle me-1"></i> {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3 text-start">
                <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small rounded-3 mb-3 text-start">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @error('code')
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3 text-start">
                <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
            </div>
        @enderror

        <!-- 2FA OTP Form -->
        <form method="POST" action="{{ route('admin.two-factor.verify') }}" id="twoFactorForm">
            @csrf
            
            <div class="otp-container">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autofocus required>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
            </div>
            
            <input type="hidden" name="code" id="fullCode">

            <button type="submit" class="btn btn-gold w-100 py-2.5 rounded-pill fw-semibold shadow-sm mb-3">
                <i class="bi bi-unlock-fill me-1"></i> Verify &amp; Access Workspace
            </button>
        </form>

        <form method="POST" action="{{ route('admin.two-factor.resend') }}" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-link text-gold small text-decoration-none p-0">
                <i class="bi bi-arrow-repeat me-1"></i> Resend Verification Code
            </button>
        </form>

        <div class="text-center mt-3 pt-3 border-top border-secondary text-secondary extra-small">
            <a href="{{ route('admin.login') }}" class="text-secondary text-decoration-none">
                <i class="bi bi-box-arrow-left me-1"></i> Cancel &amp; Return to Login
            </a>
        </div>
    </div>

    <!-- OTP Input Script -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const inputs = document.querySelectorAll('.otp-input');
        const codeInput = document.getElementById('fullCode');
        const form = document.getElementById('twoFactorForm');

        function updateCode() {
            let combined = "";
            inputs.forEach(input => combined += input.value);
            codeInput.value = combined;
        }

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.inputType === "deleteContentBackward") return;
                
                const value = e.target.value;
                if (value.length > 1) {
                    e.target.value = value.charAt(0);
                }

                if (value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateCode();

                if (codeInput.value.length === 6) {
                    form.submit();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const data = (e.clipboardData || window.clipboardData).getData('text').trim();
                if (/^\d{6}$/.test(data)) {
                    data.split('').forEach((char, i) => {
                        if (inputs[i]) inputs[i].value = char;
                    });
                    updateCode();
                    inputs[5].focus();
                    form.submit();
                }
            });
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
    });
    </script>

</body>
</html>
