<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Architectural Refinement | XIVIG Maison Gazette</title>
    
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
                radial-gradient(circle at 50% 15%, rgba(201, 169, 110, 0.09) 0%, transparent 60%),
                radial-gradient(circle at 85% 85%, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
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
        .maintenance-wrapper {
            width: 100%;
            max-width: 620px;
            margin: auto;
            position: relative;
        }
        .maintenance-wrapper::before {
            content: '';
            position: absolute;
            inset: -25px;
            background: radial-gradient(circle, var(--gold-glow) 0%, transparent 70%);
            z-index: 0;
            filter: blur(45px);
            opacity: 0.65;
            pointer-events: none;
        }
        .maintenance-card {
            position: relative;
            z-index: 1;
            background: var(--card-bg);
            border: 1px solid var(--border-glass);
            border-radius: 2.25rem;
            padding: 3.5rem 2.5rem;
            box-shadow: 0 30px 65px -15px rgba(0, 0, 0, 0.88), 0 0 0 1px rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            text-align: center;
            transition: all 0.3s ease;
        }
        .crest-badge {
            width: 54px;
            height: 54px;
            margin: 0 auto 1.25rem;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(201, 169, 110, 0.15) 0%, rgba(20, 26, 32, 0.8) 100%);
            border: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 25px rgba(201, 169, 110, 0.25);
        }
        .crest-badge span {
            font-family: 'Cinzel', serif;
            font-size: 1.55rem;
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
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(201, 169, 110, 0.1);
            color: var(--gold-primary);
            padding: 0.5rem 1.4rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            margin-bottom: 1.75rem;
            border: 1px solid rgba(201, 169, 110, 0.25);
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }
        .status-badge .dot {
            width: 8px;
            height: 8px;
            background: var(--gold-primary);
            border-radius: 50%;
            box-shadow: 0 0 12px var(--gold-primary);
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.15); }
        }
        .maintenance-card h1 {
            font-size: clamp(2rem, 5vw, 2.6rem);
            font-weight: 900;
            margin-bottom: 1rem;
            letter-spacing: -0.03em;
            line-height: 1.15;
            color: #ffffff;
        }
        .maintenance-card p.lead-text {
            color: var(--text-muted);
            font-size: 1.05rem;
            line-height: 1.65;
            margin-bottom: 2.25rem;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }
        /* Timer Grid */
        .timer-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.85rem;
            margin-bottom: 2.25rem;
        }
        .timer-item {
            background: rgba(255, 255, 255, 0.03);
            padding: 1.25rem 0.5rem;
            border-radius: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.25s ease;
        }
        .timer-item:hover {
            border-color: rgba(201, 169, 110, 0.3);
            background: rgba(255, 255, 255, 0.05);
        }
        .timer-item h2 {
            font-size: clamp(1.6rem, 3.5vw, 2.2rem);
            font-weight: 800;
            margin-bottom: 0.25rem;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(135deg, #ffffff 0%, var(--gold-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .timer-item span {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #64748b;
            font-weight: 700;
        }
        /* Progress Architecture */
        .progress-container-m {
            height: 6px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 9999px;
            margin-bottom: 2.5rem;
            overflow: hidden;
            position: relative;
        }
        .progress-fill-m {
            height: 100%;
            background: linear-gradient(90deg, #c9a96e 0%, #dfc28d 50%, #d4af37 100%);
            width: 72%;
            box-shadow: 0 0 15px rgba(201, 169, 110, 0.5);
            border-radius: 9999px;
            animation: shimmer 2.5s infinite linear;
        }
        /* Subscription / Notification */
        .subscribe-group {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.03);
            padding: 0.35rem 0.4rem 0.35rem 1.15rem;
            border: 1px solid rgba(212, 175, 55, 0.25);
            border-radius: 9999px;
            transition: all 0.3s ease;
        }
        .subscribe-group:focus-within {
            border-color: var(--gold-primary);
            box-shadow: 0 0 20px rgba(201, 169, 110, 0.25);
        }
        .subscribe-group input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: #f1f5f9;
            font-size: 0.9rem;
            font-family: inherit;
        }
        .subscribe-group input::placeholder {
            color: #64748b;
        }
        .subscribe-group button {
            background: linear-gradient(135deg, #c9a96e 0%, #a0824b 100%);
            color: #0b0f12;
            border: none;
            padding: 0.65rem 1.4rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .subscribe-group button:hover {
            background: linear-gradient(135deg, #dfc28d 0%, #b89628 100%);
            transform: scale(1.02);
        }
        /* Social Integration */
        .social-bar {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .social-bar a {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .social-bar a:hover {
            background: rgba(201, 169, 110, 0.15);
            border-color: var(--gold-primary);
            color: var(--gold-primary);
            transform: translateY(-2px);
        }
        /* Action buttons */
        .bypass-row {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            justify-content: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }
        @media (min-width: 480px) {
            .bypass-row {
                flex-direction: row;
            }
        }
        .btn-bypass {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .btn-bypass-auth {
            background: rgba(201, 169, 110, 0.12);
            border: 1px solid rgba(201, 169, 110, 0.35);
            color: var(--gold-light);
        }
        .btn-bypass-auth:hover {
            background: linear-gradient(135deg, #c9a96e 0%, #a0824b 100%);
            color: #0b0f12;
            transform: translateY(-2px);
        }
        .btn-bypass-contact {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }
        .btn-bypass-contact:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #f1f5f9;
            transform: translateY(-2px);
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
                radial-gradient(circle at 50% 15%, rgba(201, 169, 110, 0.16) 0%, transparent 60%),
                radial-gradient(circle at 85% 85%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
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
        [data-bs-theme="light"] .maintenance-card {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(201, 169, 110, 0.45);
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.12), 0 0 35px rgba(201, 169, 110, 0.15);
        }
        [data-bs-theme="light"] .crest-badge {
            background: linear-gradient(135deg, rgba(201, 169, 110, 0.2) 0%, rgba(240, 243, 248, 0.9) 100%);
            border-color: rgba(201, 169, 110, 0.5);
        }
        [data-bs-theme="light"] .maintenance-card h1 {
            color: #0f172a;
        }
        [data-bs-theme="light"] .maintenance-card p.lead-text {
            color: #475569;
        }
        [data-bs-theme="light"] .timer-item {
            background: #f8fafc;
            border-color: #e2e8f0;
        }
        [data-bs-theme="light"] .timer-item h2 {
            background: linear-gradient(135deg, #0f172a 0%, var(--gold-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        [data-bs-theme="light"] .timer-item span {
            color: #64748b;
        }
        [data-bs-theme="light"] .subscribe-group {
            background: #f8fafc;
            border-color: rgba(201, 169, 110, 0.45);
        }
        [data-bs-theme="light"] .subscribe-group input {
            color: #0f172a;
        }
        [data-bs-theme="light"] .subscribe-group input::placeholder {
            color: #94a3b8;
        }
        [data-bs-theme="light"] .social-bar a {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #475569;
        }
        [data-bs-theme="light"] .social-bar a:hover {
            background: rgba(201, 169, 110, 0.15);
            border-color: var(--gold-primary);
            color: var(--gold-dark);
        }
        [data-bs-theme="light"] .btn-bypass-contact {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #475569;
        }
        [data-bs-theme="light"] .btn-bypass-contact:hover {
            background: #e2e8f0;
            color: #0f172a;
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

    <div class="maintenance-wrapper">
        <div class="maintenance-card">
            <div class="crest-badge">
                <span>X</span>
            </div>
            <div class="gazette-tag">Maison XIVIG &bull; Temporary Curation</div>

            <div class="status-badge">
                <span class="dot"></span> System Optimization in Progress
            </div>

            <h1>Refining Excellence</h1>
            <p class="lead-text">
                Our architects are deploying upgraded high-frequency caching and typographic engines. The Maison Gazette will reopen with heightened elegance shortly.
            </p>

            <div class="timer-grid">
                <div class="timer-item">
                    <h2 id="days">00</h2>
                    <span>Days</span>
                </div>
                <div class="timer-item">
                    <h2 id="hours">02</h2>
                    <span>Hours</span>
                </div>
                <div class="timer-item">
                    <h2 id="minutes">14</h2>
                    <span>Minutes</span>
                </div>
                <div class="timer-item">
                    <h2 id="seconds">48</h2>
                    <span>Seconds</span>
                </div>
            </div>

            <div class="progress-container-m">
                <div class="progress-fill-m"></div>
            </div>

            <form class="subscribe-group" onsubmit="event.preventDefault(); alert('Thank you. You will be alerted the moment dispatches resume.'); this.reset();">
                <input type="email" placeholder="Enter email for live relaunch notification..." required>
                <button type="submit">Notify Me</button>
            </form>

            <div class="social-bar">
                <a href="#" title="X / Twitter"><i class="bi bi-twitter-x"></i></a>
                <a href="#" title="GitHub"><i class="bi bi-github"></i></a>
                <a href="#" title="Discord"><i class="bi bi-discord"></i></a>
                <a href="mailto:{{ \App\Models\Setting::get('contact_email', 'concierge@xivig.com') }}" title="Direct Concierge"><i class="bi bi-envelope-fill"></i></a>
            </div>

            <div class="bypass-row">
                <a href="{{ route('admin.login') }}" class="btn-bypass btn-bypass-auth">
                    <i class="bi bi-shield-lock-fill"></i> Staff Authentication
                </a>
                <a href="mailto:{{ \App\Models\Setting::get('contact_email', 'concierge@xivig.com') }}" class="btn-bypass btn-bypass-contact">
                    <i class="bi bi-chat-left-dots"></i> Concierge Salon
                </a>
            </div>
        </div>
    </div>

    <script>
        // Automatic Countdown Engine
        const targetDate = new Date();
        targetDate.setHours(targetDate.getHours() + 2);
        targetDate.setMinutes(targetDate.getMinutes() + 15);

        function updateTimer() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance <= 0) {
                document.getElementById("days").innerText = "00";
                document.getElementById("hours").innerText = "00";
                document.getElementById("minutes").innerText = "00";
                document.getElementById("seconds").innerText = "00";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("days").innerText = days.toString().padStart(2, '0');
            document.getElementById("hours").innerText = hours.toString().padStart(2, '0');
            document.getElementById("minutes").innerText = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").innerText = seconds.toString().padStart(2, '0');
        }

        setInterval(updateTimer, 1000);
        updateTimer();

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
