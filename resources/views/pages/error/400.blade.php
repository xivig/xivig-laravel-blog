<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 Bad Request | XIVIG Maison Gazette</title>
    
    <script>
        (function() {
            var t = localStorage.getItem('xivig_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', t);
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-main: #070a0e;
            --card-bg: rgba(14, 19, 24, 0.94);
            --gold-primary: #c9a96e;
            --gold-light: #dfc28d;
            --gold-dark: #a0824b;
            --gold-glow: rgba(201, 169, 110, 0.28);
            --border-glass: rgba(212, 175, 55, 0.22);
            --text-muted: #94a3b8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--bg-main);
            background-image:
                radial-gradient(circle at 50% 20%, rgba(201, 169, 110, 0.08) 0%, transparent 60%),
                radial-gradient(circle at 80% 80%, rgba(167, 139, 250, 0.05) 0%, transparent 50%),
                linear-gradient(rgba(255, 255, 255, 0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 48px 48px, 48px 48px;
            color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.25rem;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .error-wrapper {
            width: 100%;
            max-width: 560px;
            margin: auto;
            position: relative;
        }
        .error-wrapper::before {
            content: '';
            position: absolute;
            inset: -25px;
            background: radial-gradient(circle, var(--gold-glow) 0%, transparent 70%);
            z-index: 0;
            filter: blur(40px);
            opacity: 0.6;
            pointer-events: none;
        }
        .error-card {
            position: relative;
            z-index: 1;
            background: var(--card-bg);
            border: 1px solid var(--border-glass);
            border-radius: 2rem;
            padding: 3.5rem 2.5rem;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.85), 0 0 0 1px rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            text-align: center;
            transition: all 0.3s ease;
        }
        .crest-badge {
            width: 52px;
            height: 52px;
            margin: 0 auto 1.25rem;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(201, 169, 110, 0.15) 0%, rgba(20, 26, 32, 0.8) 100%);
            border: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 25px rgba(201, 169, 110, 0.2);
        }
        .crest-badge span {
            font-family: 'Cinzel', serif;
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold-primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gazette-tag {
            font-family: 'Cinzel', serif;
            font-size: 0.72rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--gold-primary);
            margin-bottom: 1.25rem;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(167, 139, 250, 0.08);
            border: 1px solid rgba(167, 139, 250, 0.25);
            color: #c4b5fd;
            padding: 0.45rem 1.2rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
        }
        .status-pill .dot {
            width: 8px;
            height: 8px;
            background: #a78bfa;
            border-radius: 50%;
            box-shadow: 0 0 10px #a78bfa;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.9); }
        }
        .error-code {
            font-size: clamp(5.5rem, 15vw, 7.5rem);
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: -0.05em;
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, #a78bfa 0%, #ec4899 50%, #c9a96e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #f8fafc;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
        }
        .error-description {
            color: var(--text-muted);
            font-size: 1.02rem;
            line-height: 1.65;
            margin-bottom: 2.25rem;
            max-width: 440px;
            margin-left: auto;
            margin-right: auto;
        }
        .action-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            justify-content: center;
        }
        @media (min-width: 480px) {
            .action-group {
                flex-direction: row;
            }
        }
        .btn-elite {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem 1.75rem;
            border-radius: 9999px;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .btn-elite-primary {
            background: linear-gradient(135deg, #c9a96e 0%, #a0824b 100%);
            color: #0b0f12;
            box-shadow: 0 10px 25px -5px rgba(201, 169, 110, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-elite-primary:hover {
            background: linear-gradient(135deg, #dfc28d 0%, #b89628 100%);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(201, 169, 110, 0.6);
            color: #05070a;
        }
        .btn-elite-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .btn-elite-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--gold-primary);
            color: #ffffff;
            transform: translateY(-2px);
        }
        .error-footer {
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 0.75rem;
            color: #64748b;
            letter-spacing: 0.05em;
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
        .theme-toggle-btn i { font-size: 1rem; line-height: 1; }
        .theme-icon-sun { display: none; color: #f59e0b; }
        .theme-icon-moon { display: inline-block; color: #dfc28d; }

        /* LIGHT THEME LUXURY OVERRIDES */
        [data-bs-theme="light"] body {
            background-color: #f6f8fb !important;
            background-image:
                radial-gradient(circle at 50% 20%, rgba(201, 169, 110, 0.16) 0%, transparent 60%),
                radial-gradient(circle at 80% 80%, rgba(167, 139, 250, 0.08) 0%, transparent 50%),
                linear-gradient(rgba(0, 0, 0, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.03) 1px, transparent 1px) !important;
            color: #1e293b;
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
        [data-bs-theme="light"] .theme-icon-sun { display: inline-block; }
        [data-bs-theme="light"] .theme-icon-moon { display: none; }
        [data-bs-theme="light"] .error-card {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(201, 169, 110, 0.45);
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.12), 0 0 35px rgba(201, 169, 110, 0.15);
        }
        [data-bs-theme="light"] .crest-badge {
            background: linear-gradient(135deg, rgba(201, 169, 110, 0.2) 0%, rgba(240, 243, 248, 0.9) 100%);
            border-color: rgba(201, 169, 110, 0.5);
        }
        [data-bs-theme="light"] .error-title {
            color: #0f172a;
        }
        [data-bs-theme="light"] .error-description {
            color: #475569;
        }
        [data-bs-theme="light"] .status-pill {
            background: rgba(0, 0, 0, 0.04);
            border-color: rgba(0, 0, 0, 0.1);
            color: #334155;
        }
        [data-bs-theme="light"] .btn-elite-secondary {
            background: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }
        [data-bs-theme="light"] .btn-elite-secondary:hover {
            background: #e2e8f0;
            color: #0f172a;
            border-color: var(--gold-primary);
        }
        [data-bs-theme="light"] .error-footer {
            border-top-color: rgba(0, 0, 0, 0.08);
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <!-- Floating Theme Switcher -->
    <div class="theme-toggle-wrap">
        <button type="button" class="theme-toggle-btn" id="themeToggleBtn" aria-label="Toggle Light/Dark Theme" title="Switch Theme">
            <i class="bi bi-sun-fill theme-icon-sun"></i>
            <i class="bi bi-moon-stars-fill theme-icon-moon"></i>
            <span class="theme-toggle-text">Theme</span>
        </button>
    </div>

    <div class="error-wrapper">
        <div class="error-card">
            <div class="crest-badge">
                <span>X</span>
            </div>
            <div class="gazette-tag">Maison Gazette &bull; Architectural Gateway</div>

            <div class="status-pill">
                <span class="dot"></span>
                HTTP 400 &bull; Malformed Request
            </div>

            <h1 class="error-code">400</h1>
            <h2 class="error-title">Bad Request</h2>

            <p class="error-description">
                Your browser transmitted a request that our server architecture could not understand or parse. Please inspect the query parameters or return to the Gazette.
            </p>

            <div class="action-group">
                <a href="{{ url('/') }}" class="btn-elite btn-elite-primary">
                    <i class="bi bi-arrow-left"></i> Return to Gazette
                </a>
                <a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}" class="btn-elite btn-elite-secondary">
                    <i class="bi bi-shield-lock"></i> {{ auth()->check() ? 'Admin Desk' : 'Staff Access' }}
                </a>
            </div>

            <div class="error-footer">
                XIVIG MAISON GAZETTE &bull; DIGITAL PRESS EDITION &bull; 2026
            </div>
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
