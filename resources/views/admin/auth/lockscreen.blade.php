<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maison Gazette | Locked Administrative Session</title>
    
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
        .lockscreen-avatar {
            width: 96px;
            height: 96px;
            border: 3px solid #c9a96e;
            box-shadow: 0 0 20px rgba(201, 169, 110, 0.3);
        }
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
        [data-bs-theme="light"] .text-light,
        [data-bs-theme="light"] h4 {
            color: #0f172a !important;
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

    <div class="card border-0 rounded-4 bg-dark bg-opacity-75 shadow-lg p-4 p-sm-5 border border-secondary text-center" style="max-width: 440px; width: 100%;">
        
        <!-- User Avatar & Identity -->
        <div class="mb-4">
            <div class="position-relative d-inline-block">
                @if(!empty($user->profile_picture) && file_exists(public_path($user->profile_picture)))
                    <img src="{{ asset($user->profile_picture) }}" alt="{{ $user->name }}" class="rounded-circle object-fit-cover lockscreen-avatar">
                @else
                    <div class="rounded-circle lockscreen-avatar d-flex align-items-center justify-content-center bg-black text-gold fw-bold display-6 mx-auto">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                @endif
                <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-danger border border-dark p-2" title="Session Locked">
                    <i class="bi bi-lock-fill"></i>
                </span>
            </div>
            
            <h4 class="fw-bold tracking-wider mt-3 mb-1 text-light">{{ $user->name ?? 'Administrator' }}</h4>
            <p class="text-secondary small mb-0">{{ $user->email ?? 'admin@xivig.com' }}</p>
            <div class="badge bg-gold-subtle text-gold extra-small px-3 py-1 rounded-pill mt-2">
                <i class="bi bi-shield-lock-fill me-1"></i> Session Suspended
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3 text-start">
                <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
            </div>
        @endif

        @error('password')
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3 text-start">
                <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
            </div>
        @enderror

        <p class="text-secondary small mb-3">Provide your secret administrative passkey to restore your workspace.</p>

        <!-- Unlock Form -->
        <form method="POST" action="{{ route('admin.unlock') }}">
            @csrf
            
            <div class="mb-4">
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control form-control-lg bg-black border-secondary text-light rounded-start-pill px-4 shadow-none" placeholder="Enter Secret Passkey" required autofocus autocomplete="current-password">
                    <button type="submit" class="btn btn-gold px-4 rounded-end-pill" title="Unlock Desk">
                        <i class="bi bi-arrow-right fs-5"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2 small">
                <a href="{{ route('admin.login') }}" class="text-secondary text-decoration-none">
                    <i class="bi bi-person-x me-1"></i> Switch Account
                </a>
                <a href="{{ route('admin.password.request') }}" class="text-gold text-decoration-none">
                    Forgot Passkey?
                </a>
            </div>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-secondary text-secondary extra-small">
            Protected by Session Encryption • XIVIG ARCHITECT OS
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
