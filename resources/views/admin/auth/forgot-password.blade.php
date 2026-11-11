<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maison Gazette | Password Recovery</title>
    
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
        [data-bs-theme="light"] .input-group-text {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #64748b !important;
        }
        [data-bs-theme="light"] .form-control {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        [data-bs-theme="light"] .form-control::placeholder {
            color: #94a3b8 !important;
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

    <div class="card border-0 rounded-4 bg-dark bg-opacity-75 shadow-lg p-4 p-sm-5 border border-secondary" style="max-width: 440px; width: 100%;">
        
        <div class="text-center mb-4">
            <div class="brand-monogram mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.75rem;">
                <i class="bi bi-shield-lock-fill text-gold"></i>
            </div>
            <h3 class="fw-bold tracking-wider mb-1">Passkey Recovery</h3>
            <p class="text-secondary small">Provide your editorial credentials to initialize secure passkey reset</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success py-2 px-3 small rounded-3 mb-4">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Reset Dispatched</strong>
                </div>
                <div>{!! session('status') !!}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.email') }}">
            @csrf
            
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Editorial Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-black border-secondary text-secondary">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus 
                           class="form-control bg-black border-secondary text-light shadow-none @error('email') is-invalid @enderror" 
                           placeholder="architect@xivig.com">
                </div>
                @error('email')
                    <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-gold w-100 py-2 rounded-pill fw-semibold shadow-sm mb-3">
                <i class="bi bi-send me-1"></i> Send Password Reset Link
            </button>

            <div class="text-center mt-3">
                <a href="{{ route('admin.login') }}" class="text-secondary small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Return to Authentication Desk
                </a>
            </div>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-secondary text-secondary extra-small">
            Protected by Cryptographic Tokens • XIVIG ARCHITECT OS
        </div>
    </div>

    <script>
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
