<meta charset="UTF-8">
<title>@yield('title', 'Admin Desk | XIVIG Maison Gazette')</title>

<!-- Site favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/admin/images/apple-touch-icon.png') }}" />
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/admin/images/favicon-32x32.png') }}" />
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/admin/images/favicon-16x16.png') }}" />

<!-- Mobile Specific Metas -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Theme Detection & Critical Style Initialization (Prevents flashing) -->
<script>
    (function() {
        const savedTheme = localStorage.getItem('xivig-global-theme') || 'dark';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
        const bgColor = savedTheme === 'dark' ? '#0b0f12' : '#f8fafc';
        document.write(`<style>body{background-color: ${bgColor} !important; visibility: visible;}</style>`);
    })();
</script>

<!-- Critical Preloader Style (Non-blocking failsafe) -->
<style>
    .preloader {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
        opacity: 0 !important;
    }
    body:not(.is-loaded) {
        overflow: auto;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.body.classList.add('is-loaded');
        const p = document.querySelector('.preloader');
        if (p) {
            p.style.display = 'none';
            p.classList.add('preloader--hidden');
        }
        window.dispatchEvent(new CustomEvent('elite:loaded'));
    });
</script>

<!-- Premium Typography: Plus Jakarta Sans & Inter (Google Fonts + Local Fallback) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Local Typography & Bootstrap 5 Icons -->
<link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/admin/css/vendor-core.css') }}">

<!-- Global jQuery Exposure -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script>
    window.jQuery = window.$ = jQuery;
</script>

<!-- SweetAlert2 Enterprise Luxury Suite -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Master Architecture & Production Stylesheets -->
<link rel="stylesheet" href="{{ asset('build/assets/preload-helper-BZ5jLx49.css') }}">
<link rel="stylesheet" href="{{ asset('build/assets/main-kzam4ZaD.css') }}">
<link rel="stylesheet" href="{{ asset('build/assets/main-BpjFF285.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/blog-admin.css') }}">
<script type="module" src="{{ asset('build/assets/main-DZTvYctr.js') }}"></script>

<style>
    /* Global Button Outline Architecture */
    .btn:not(.border-0):not(.header-profile-btn) {
        border-width: 1px !important;
        border-style: solid !important;
    }
    .btn.border-0,
    .header-profile-btn,
    .header-profile-btn.btn {
        border: none !important;
        border-width: 0 !important;
        outline: none !important;
        box-shadow: none !important;
    }

    /* Header Section Divider & Profile Isolation */
    .header__section-divider {
        width: 1px;
        height: 28px;
        background: rgba(0, 0, 0, 0.12);
        margin: 0 14px 0 8px;
        flex-shrink: 0;
    }
    [data-bs-theme="dark"] .header__section-divider {
        background: rgba(255, 255, 255, 0.15);
    }
    .header__profile-container {
        padding-left: 2px;
    }
    .header-profile-btn,
    .header-profile-btn.btn {
        border: none !important;
        border-width: 0 !important;
        outline: none !important;
        box-shadow: none !important;
        background: transparent !important;
        padding: 3px 8px 3px 4px !important;
        border-radius: 50px !important;
        transition: background-color 0.2s ease, transform 0.15s ease !important;
    }
    .header-profile-btn:hover,
    .header-profile-btn:focus,
    .header-profile-btn[aria-expanded="true"] {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        background: rgba(0, 0, 0, 0.04) !important;
    }
    [data-bs-theme="dark"] .header-profile-btn:hover,
    [data-bs-theme="dark"] .header-profile-btn:focus,
    [data-bs-theme="dark"] .header-profile-btn[aria-expanded="true"] {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        background: rgba(255, 255, 255, 0.06) !important;
    }

    /* Admin Global Spotlight & Search System */
    .header__search {
        position: relative;
        width: 100%;
        max-width: 440px;
        margin-left: 0.5rem;
    }
    .header__search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.09) !important;
        border-radius: 50px;
        padding: 2px 10px 2px 14px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    [data-bs-theme="dark"] .header__search-wrapper {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.12) !important;
    }
    .header__search-wrapper:focus-within {
        border-color: var(--elite-primary, #6366f1) !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18), 0 4px 14px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    [data-bs-theme="dark"] .header__search-wrapper:focus-within {
        background: #171d22;
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2), 0 4px 18px rgba(0, 0, 0, 0.4);
    }
    .header__search-icon-btn {
        border: none !important;
        background: transparent !important;
        color: #64748b;
        padding: 0;
        margin-right: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.95rem;
        transition: color 0.15s ease;
    }
    .header__search-icon-btn:hover {
        color: var(--elite-primary, #6366f1);
    }
    .header__search-input {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        outline: none !important;
        flex-grow: 1;
        font-size: 0.85rem;
        color: var(--bs-body-color, #1e293b);
        height: 34px;
        padding: 0;
        min-width: 80px;
    }
    .header__search-input::placeholder {
        color: #94a3b8;
        font-size: 0.82rem;
    }
    .header__search-clear-btn {
        border: none !important;
        background: transparent !important;
        color: #94a3b8;
        cursor: pointer;
        padding: 2px 6px;
        font-size: 0.95rem;
        transition: color 0.15s ease;
    }
    .header__search-clear-btn:hover {
        color: #ef4444;
    }
    .header__search-kbd {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        padding-left: 6px;
        pointer-events: none;
    }
    .header__search-kbd kbd {
        font-size: 0.65rem;
        padding: 2px 5px;
        border-radius: 4px;
        background: rgba(0, 0, 0, 0.05);
        color: #64748b;
        border: 1px solid rgba(0, 0, 0, 0.08);
        font-family: inherit;
        font-weight: 700;
        box-shadow: 0 1px 1px rgba(0,0,0,0.05);
    }
    [data-bs-theme="dark"] .header__search-kbd kbd {
        background: rgba(255, 255, 255, 0.08);
        color: #94a3b8;
        border-color: rgba(255, 255, 255, 0.12);
    }
    .admin-search-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        width: 520px;
        max-width: calc(100vw - 30px);
        z-index: 2500;
        overflow: hidden;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.18), 0 0 0 1px rgba(0, 0, 0, 0.06) !important;
        animation: adminSearchSlideDown 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes adminSearchSlideDown {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    [data-bs-theme="dark"] .admin-search-dropdown {
        background-color: #15191c !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 25px 55px -10px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
    }
    .admin-search-dropdown-content {
        max-height: 400px;
        overflow-y: auto;
        padding: 6px 0;
    }
    .admin-search-group-title {
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #94a3b8;
        padding: 8px 16px 4px;
        margin: 0;
    }
    .admin-search-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 16px;
        text-decoration: none !important;
        color: var(--bs-body-color, #1e293b);
        transition: background-color 0.15s ease, transform 0.1s ease;
        cursor: pointer;
    }
    .admin-search-item:hover,
    .admin-search-item.active {
        background-color: rgba(99, 102, 241, 0.08);
        color: var(--elite-primary, #6366f1);
    }
    [data-bs-theme="dark"] .admin-search-item:hover,
    [data-bs-theme="dark"] .admin-search-item.active {
        background-color: rgba(212, 175, 55, 0.12);
        color: #d4af37;
    }
    .admin-search-item-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.95rem;
    }

    /* ===================================================
       Executive Editorial Dashboard Luxury Aesthetic
       =================================================== */
    .dashboard-luxury-banner {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(212, 175, 55, 0.08) 100%);
        border: 1px solid rgba(212, 175, 55, 0.25) !important;
        position: relative;
        overflow: hidden;
    }
    [data-bs-theme="dark"] .dashboard-luxury-banner {
        background: linear-gradient(135deg, rgba(20, 26, 31, 0.95) 0%, rgba(25, 30, 36, 0.95) 100%);
        border-color: rgba(212, 175, 55, 0.2) !important;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5) !important;
    }
    .bg-gold-gradient {
        background: linear-gradient(135deg, #d4af37 0%, #b89628 100%) !important;
        color: #0b0f12 !important;
    }
    .text-gold {
        color: #d4af37 !important;
    }
    .border-gold {
        border-color: rgba(212, 175, 55, 0.35) !important;
    }

    /* Executive KPI Metric Cards */
    .dashboard-kpi-card {
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease, border-color 0.25s ease;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
    }
    [data-bs-theme="dark"] .dashboard-kpi-card {
        border-color: rgba(255, 255, 255, 0.07) !important;
        background-color: #141a1f !important;
    }
    .dashboard-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.12) !important;
        border-color: rgba(99, 102, 241, 0.3) !important;
    }
    [data-bs-theme="dark"] .dashboard-kpi-card:hover {
        box-shadow: 0 14px 32px -6px rgba(0, 0, 0, 0.6) !important;
        border-color: rgba(212, 175, 55, 0.35) !important;
    }

    /* Editorial Story Table Polish */
    .dashboard-story-row {
        transition: background-color 0.15s ease;
    }
    .dashboard-story-row:hover {
        background-color: rgba(99, 102, 241, 0.03) !important;
    }
    [data-bs-theme="dark"] .dashboard-story-row:hover {
        background-color: rgba(255, 255, 255, 0.03) !important;
    }
    .story-thumb-box {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    [data-bs-theme="dark"] .story-thumb-box {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .story-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Pulsating Status Indicator */
    .status-pulse {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
        position: relative;
    }
    .status-pulse.published {
        background-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    .status-pulse.draft {
        background-color: #f59e0b;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
    }
    .status-pulse.scheduled {
        background-color: #0ea5e9;
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2);
    }

    /* Modern Telemetry Bar Chart Architecture */
    .telemetry-chart-track {
        height: 180px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 10px 0;
    }
    .telemetry-col {
        flex: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        position: relative;
    }
    .telemetry-bar-slot {
        width: 32px;
        height: 100%;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        background: rgba(0, 0, 0, 0.03);
        border-radius: 8px 8px 4px 4px;
        position: relative;
        overflow: hidden;
    }
    [data-bs-theme="dark"] .telemetry-bar-slot {
        background: rgba(255, 255, 255, 0.04);
    }
    .telemetry-bar-fill {
        width: 100%;
        border-radius: 6px 6px 2px 2px;
        background: linear-gradient(180deg, #6366f1 0%, #4338ca 100%);
        transition: height 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s ease, filter 0.2s ease;
        position: relative;
    }
    .telemetry-bar-fill.is-peak {
        background: linear-gradient(180deg, #d4af37 0%, #b89628 100%);
    }
    .telemetry-bar-slot:hover .telemetry-bar-fill {
        filter: brightness(1.15);
        transform: scaleY(1.02);
        transform-origin: bottom;
    }

    /* Action Hover Tiles */
    .hover-action-tile {
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
    }
    [data-bs-theme="dark"] .hover-action-tile {
        border-color: rgba(255, 255, 255, 0.08) !important;
        background: rgba(255, 255, 255, 0.02) !important;
    }
    .hover-action-tile:hover {
        transform: translateX(4px);
        background: rgba(99, 102, 241, 0.06) !important;
        border-color: rgba(99, 102, 241, 0.3) !important;
    }
    [data-bs-theme="dark"] .hover-action-tile:hover {
        background: rgba(212, 175, 55, 0.08) !important;
        border-color: rgba(212, 175, 55, 0.3) !important;
    }

    /* Top Story Rank Badges */
    .rank-medal {
        width: 26px;
        height: 26px;
        min-width: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.75rem;
    }
    .rank-medal-1 {
        background: linear-gradient(135deg, #d4af37 0%, #fef08a 50%, #b45309 100%);
        color: #1a1a1a;
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.35);
    }
    .rank-medal-2 {
        background: linear-gradient(135deg, #cbd5e1 0%, #f1f5f9 50%, #64748b 100%);
        color: #0f172a;
    }
    .rank-medal-3 {
        background: linear-gradient(135deg, #d97706 0%, #fed7aa 50%, #78350f 100%);
        color: #ffffff;
    }
    .rank-medal-default {
        background: rgba(0, 0, 0, 0.06);
        color: #64748b;
    }
    [data-bs-theme="dark"] .rank-medal-default {
        background: rgba(255, 255, 255, 0.08);
        color: #94a3b8;
    }
    .btn-outline-secondary {
        border-color: #cbd5e1 !important;
        color: #475569 !important;
        background-color: transparent !important;
    }
    .btn-outline-secondary:hover, .btn-outline-secondary:focus, .btn-outline-secondary:active, .btn-outline-secondary.active {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        border-color: #94a3b8 !important;
    }
    [data-bs-theme="dark"] .btn-outline-secondary {
        border-color: rgba(255, 255, 255, 0.22) !important;
        color: #cbd5e1 !important;
        background-color: transparent !important;
    }
    [data-bs-theme="dark"] .btn-outline-secondary:hover, [data-bs-theme="dark"] .btn-outline-secondary:focus, [data-bs-theme="dark"] .btn-outline-secondary:active, [data-bs-theme="dark"] .btn-outline-secondary.active {
        background-color: rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.45) !important;
    }
    .btn-outline-primary {
        border-color: var(--elite-primary, #6366f1) !important;
        color: var(--elite-primary, #6366f1) !important;
        background-color: transparent !important;
    }
    .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active, .btn-outline-primary.active {
        background-color: var(--elite-primary, #6366f1) !important;
        color: #ffffff !important;
        border-color: var(--elite-primary, #6366f1) !important;
    }
    .btn-outline-danger {
        border-color: #ef4444 !important;
        color: #ef4444 !important;
        background-color: transparent !important;
    }
    .btn-outline-danger:hover, .btn-outline-danger:focus, .btn-outline-danger:active, .btn-outline-danger.active {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        border-color: #ef4444 !important;
    }
    .btn-outline-warning {
        border-color: #d4af37 !important;
        color: #d4af37 !important;
        background-color: transparent !important;
    }
    .btn-outline-warning:hover, .btn-outline-warning:focus, .btn-outline-warning:active, .btn-outline-warning.active {
        background-color: #d4af37 !important;
        color: #0b0f12 !important;
        border-color: #d4af37 !important;
    }
    .btn-outline-success {
        border-color: #10b981 !important;
        color: #10b981 !important;
        background-color: transparent !important;
    }
    .btn-outline-success:hover, .btn-outline-success:focus, .btn-outline-success:active, .btn-outline-success.active {
        background-color: #10b981 !important;
        color: #ffffff !important;
        border-color: #10b981 !important;
    }
    .btn-outline-info {
        border-color: #0ea5e9 !important;
        color: #0ea5e9 !important;
        background-color: transparent !important;
    }
    .btn-outline-info:hover, .btn-outline-info:focus, .btn-outline-info:active, .btn-outline-info.active {
        background-color: #0ea5e9 !important;
        color: #ffffff !important;
        border-color: #0ea5e9 !important;
    }
    .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.25) !important;
        color: #f8fafc !important;
        background-color: transparent !important;
    }
    [data-bs-theme="light"] .btn-outline-light {
        border-color: #cbd5e1 !important;
        color: #334155 !important;
    }
    .btn-outline-dark {
        border-color: #1e293b !important;
        color: #1e293b !important;
        background-color: transparent !important;
    }
    [data-bs-theme="dark"] .btn-outline-dark {
        border-color: rgba(255, 255, 255, 0.25) !important;
        color: #f8fafc !important;
    }
    [data-bs-theme="light"] .btn-light {
        border: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        color: #1e293b !important;
    }
    [data-bs-theme="light"] .btn-light:hover {
        background-color: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }
    [data-bs-theme="dark"] .btn-light {
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: #f1f5f9 !important;
    }
    [data-bs-theme="dark"] .btn-light:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
    }
    .header__action-btn {
        width: 38px !important;
        height: 38px !important;
        border-radius: 50% !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        background: rgba(0, 0, 0, 0.02) !important;
        color: #475569 !important;
        font-size: 1.05rem !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    [data-bs-theme="dark"] .header__action-btn {
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        background: rgba(255, 255, 255, 0.04) !important;
        color: #94a3b8 !important;
    }
    .header__action-btn:hover, .header__action-btn:focus {
        border-color: var(--elite-primary, #6366f1) !important;
        color: var(--elite-primary, #6366f1) !important;
        background: rgba(99, 102, 241, 0.08) !important;
        transform: translateY(-1px) !important;
    }
    [data-bs-theme="dark"] .header__action-btn:hover {
        border-color: #d4af37 !important;
        color: #d4af37 !important;
        background: rgba(212, 175, 55, 0.1) !important;
    }

    /* DataTables & Form Control Tweaks */
    .form-control:focus, .form-select:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.2) !important;
    }
    
    /* Global Admin Pagination System */
    .pagination {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px;
        margin-bottom: 0 !important;
        list-style: none !important;
        padding-left: 0 !important;
    }
    .pagination .page-item {
        display: inline-block !important;
    }
    .pagination .page-link {
        border-radius: 50px !important;
        margin: 0 2px !important;
        min-width: 36px !important;
        height: 36px !important;
        padding: 0 12px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: var(--bs-body-color, #495057) !important;
        background-color: var(--bs-tertiary-bg, #f8f9fa) !important;
        border: 1px solid var(--bs-border-color, #dee2e6) !important;
        text-decoration: none !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        transition: all 0.2s ease-in-out !important;
        white-space: nowrap !important;
        line-height: 1 !important;
    }
    .pagination .page-link:hover:not(.disabled) {
        background-color: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
        color: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.25);
    }
    .pagination .page-item.active .page-link {
        background-color: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
    }

    /* Dark Mode Pagination Harmony */
    [data-bs-theme="dark"] .pagination .page-link {
        background-color: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        color: #cbd5e1 !important;
    }
    [data-bs-theme="dark"] .pagination .page-link:hover:not(.disabled) {
        background-color: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
        color: #fff !important;
    }

    /* Dark Theme Card, Header, and Table Colors */
    [data-bs-theme="dark"] .table {
        --bs-table-bg: transparent;
        --bs-table-color: #e2e8f0;
        --bs-table-border-color: rgba(255, 255, 255, 0.08);
    }
    [data-bs-theme="dark"] .card {
        background-color: #141a1f !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-bs-theme="dark"] .page-header {
        background-color: #141a1f !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    /* Universal Stat & KPI Circle Icons */
    .stat-icon-circle,
    .quick-stat-icon {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        max-width: 52px !important;
        min-height: 52px !important;
        max-height: 52px !important;
        border-radius: 50% !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        padding: 0 !important;
        line-height: 1 !important;
    }
    .stat-icon-circle i,
    .quick-stat-icon i {
        font-size: 1.35rem !important;
        line-height: 1 !important;
        display: inline-block !important;
    }

    /* Badges & Pill Badges Integrity */
    .badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1.35 !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }
    .badge.rounded-pill {
        border-radius: 50rem !important;
    }

    /* Equal height and vertical alignment for stat / KPI rows */
    .stat-row > [class*="col-"],
    .kpi-row > [class*="col-"] {
        display: flex;
    }
    .stat-row > [class*="col-"] > .card,
    .kpi-row > [class*="col-"] > .card {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Sidebar Submenu Fixes & Accordion Animation */
    .sidebar__item--has-dropdown .sidebar__chevron {
        transition: transform 0.25s ease-in-out;
        margin-left: auto;
        font-size: 0.75rem;
    }
    .sidebar__item--has-dropdown.show .sidebar__chevron,
    .sidebar__item--has-dropdown.open .sidebar__chevron {
        transform: rotate(180deg);
    }
    .sidebar__submenu {
        list-style: none;
        padding-left: 2.75rem;
        margin-bottom: 0;
    }
    .sidebar__sub-item {
        margin-bottom: 2px;
    }
    .sidebar__sub-link {
        font-size: 0.875rem !important;
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    .sidebar__item.show > .sidebar__submenu-wrapper,
    .sidebar__item.open > .sidebar__submenu-wrapper {
        grid-template-rows: 1fr !important;
        display: grid !important;
        margin-bottom: 10px !important;
        overflow: visible !important;
    }
    .sidebar__item.show .sidebar__sub-link,
    .sidebar__item.open .sidebar__sub-link {
        opacity: 0.75 !important;
        transform: translateX(0) !important;
        visibility: visible !important;
        display: flex !important;
    }
    .sidebar__item.show .sidebar__sub-link:hover,
    .sidebar__item.open .sidebar__sub-link:hover,
    .sidebar__item.show .sidebar__sub-link.active,
    .sidebar__item.open .sidebar__sub-link.active {
        opacity: 1 !important;
        color: #fff !important;
        font-weight: 600 !important;
    }

    /* Profile Card in Sidebar */
    .sidebar .elite-profile-card {
        background: rgba(255, 255, 255, 0.05) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35) !important;
        transition: all 0.25s ease-in-out !important;
    }
    .sidebar .elite-profile-card:hover {
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(99, 102, 241, 0.3) !important;
        transform: translateY(-2px);
    }

    /* ========================================================
       ELITE WORKSPACE OFFCANVAS (STUDIO ARCHITECT)
       ======================================================== */
    .header__action-btn:hover #workspaceGearIcon,
    .header__action-btn:focus #workspaceGearIcon {
        transform: rotate(90deg);
        color: #d4af37 !important;
    }
    #workspaceGearIcon {
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), color 0.2s ease;
        display: inline-block;
    }

    .settings-panel.offcanvas {
        width: 440px !important;
        max-width: 95vw !important;
        z-index: 10050 !important;
        border: none !important;
        box-shadow: -20px 0 60px rgba(0, 0, 0, 0.6) !important;
    }

    .offcanvas-backdrop {
        z-index: 10040 !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        background-color: rgba(5, 8, 12, 0.65) !important;
    }

    [data-bs-theme="dark"] .settings-panel.offcanvas {
        background: linear-gradient(180deg, #0e1318 0%, #090d10 100%) !important;
        color: #f1f5f9 !important;
        border-left: 1px solid rgba(212, 175, 55, 0.25) !important;
    }

    [data-bs-theme="light"] .settings-panel.offcanvas {
        background: #ffffff !important;
        color: #0f172a !important;
        border-left: 1px solid rgba(0, 0, 0, 0.08) !important;
    }

    .settings-panel .offcanvas-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        padding: 1.25rem 1.5rem !important;
    }

    [data-bs-theme="dark"] .settings-panel .offcanvas-header {
        border-bottom-color: rgba(212, 175, 55, 0.15) !important;
    }

    [data-bs-theme="light"] .settings-panel .offcanvas-header {
        border-bottom-color: rgba(0, 0, 0, 0.06) !important;
    }

    .settings-panel .offcanvas-body {
        padding: 1.5rem !important;
        scrollbar-width: thin;
        scrollbar-color: rgba(212, 175, 55, 0.3) transparent;
    }

    .settings-panel .offcanvas-body::-webkit-scrollbar {
        width: 5px;
    }
    .settings-panel .offcanvas-body::-webkit-scrollbar-thumb {
        background: rgba(212, 175, 55, 0.3);
        border-radius: 10px;
    }

    /* Elite Theme Option Cards */
    .theme-card-option {
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 14px;
        padding: 12px;
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.08);
        transition: all 0.25s ease;
        text-align: center;
        user-select: none;
        position: relative;
    }

    [data-bs-theme="light"] .theme-card-option {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .theme-card-option:hover {
        transform: translateY(-2px);
        border-color: rgba(212, 175, 55, 0.4);
    }

    .theme-card-option.active {
        border-color: #d4af37 !important;
        background: rgba(212, 175, 55, 0.08) !important;
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.15) !important;
    }

    .theme-preview-box {
        height: 52px;
        border-radius: 8px;
        display: flex;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 8px;
        position: relative;
    }

    .theme-preview-box.preview-dark {
        background: #0f141a;
    }
    .theme-preview-box.preview-dark .preview-sidebar {
        width: 25%;
        background: #07090b;
        border-right: 1px solid rgba(212, 175, 55, 0.2);
    }
    .theme-preview-box.preview-dark .preview-content {
        flex: 1;
        padding: 6px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .theme-preview-box.preview-dark .preview-bar {
        height: 6px;
        background: rgba(212, 175, 55, 0.4);
        border-radius: 4px;
        width: 60%;
    }
    .theme-preview-box.preview-dark .preview-card {
        height: 18px;
        background: #171d24;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .theme-preview-box.preview-light {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .theme-preview-box.preview-light .preview-sidebar {
        width: 25%;
        background: #e2e8f0;
        border-right: 1px solid #cbd5e1;
    }
    .theme-preview-box.preview-light .preview-content {
        flex: 1;
        padding: 6px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .theme-preview-box.preview-light .preview-bar {
        height: 6px;
        background: #3b82f6;
        border-radius: 4px;
        width: 60%;
    }
    .theme-preview-box.preview-light .preview-card {
        height: 18px;
        background: #ffffff;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    .theme-preview-box.preview-auto {
        background: linear-gradient(135deg, #0f141a 50%, #f8fafc 50%);
    }

    /* Accent Swatches */
    .accent-swatch {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 13px;
        position: relative;
    }
    .accent-swatch:hover {
        transform: scale(1.15);
    }
    .accent-swatch.active {
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px currentColor;
        transform: scale(1.1);
    }

    /* Quick Launchpad Cards */
    .quick-jump-card {
        border-radius: 12px;
        padding: 12px 14px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.25s ease;
        text-decoration: none !important;
        color: inherit !important;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    [data-bs-theme="light"] .quick-jump-card {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .quick-jump-card:hover {
        border-color: rgba(212, 175, 55, 0.4);
        background: rgba(212, 175, 55, 0.08);
        transform: translateY(-2px);
    }

    /* Close Button */
    .offcanvas-close-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: inherit;
        transition: all 0.2s ease;
    }
    [data-bs-theme="light"] .offcanvas-close-btn {
        background: rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.12) !important;
    }
    .offcanvas-close-btn:hover {
        background: rgba(212, 175, 55, 0.2);
        border-color: #d4af37;
        color: #d4af37;
        transform: rotate(90deg);
    }

    /* ========================================================
       SWEETALERT2 LUXURY ATELIER THEME
       ======================================================== */
    .elite-swal-popup {
        border-radius: 20px !important;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif !important;
        padding: 2rem 1.75rem !important;
    }
    [data-bs-theme="dark"] .elite-swal-popup {
        background: #0f141a !important;
        color: #f1f5f9 !important;
        border: 1px solid rgba(212, 175, 55, 0.28) !important;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8) !important;
    }
    [data-bs-theme="light"] .elite-swal-popup {
        background: #ffffff !important;
        color: #0f172a !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
    }
    .elite-swal-popup .swal2-title {
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
        font-size: 1.35rem !important;
    }
    [data-bs-theme="dark"] .elite-swal-popup .swal2-title {
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .elite-swal-popup .swal2-html-container {
        color: #94a3b8 !important;
    }
    .swal2-container {
        z-index: 10060 !important;
    }
    .swal2-container.swal2-top-end,
    .swal2-container.swal2-top-right,
    .elite-swal-toast-container {
        top: 85px !important;
        right: 24px !important;
        padding: 0 !important;
        z-index: 10060 !important;
    }
    @media (max-width: 768px) {
        .swal2-container.swal2-top-end,
        .swal2-container.swal2-top-right,
        .elite-swal-toast-container {
            top: 70px !important;
            right: 12px !important;
        }
    }
    .elite-swal-toast {
        border-radius: 16px !important;
        padding: 0.85rem 1.4rem !important;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25) !important;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif !important;
        overflow: hidden !important;
        position: relative !important;
    }
    [data-bs-theme="dark"] .elite-swal-toast {
        background: #141b22 !important;
        color: #f8fafc !important;
        border: 1px solid rgba(212, 175, 55, 0.35) !important;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6) !important;
    }
    [data-bs-theme="light"] .elite-swal-toast {
        background: #ffffff !important;
        color: #0f172a !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12) !important;
    }
    .elite-swal-toast .swal2-timer-progress-bar {
        height: 3px !important;
        background: var(--elite-primary, #6366f1) !important;
    }
    [data-bs-theme="dark"] .elite-swal-toast .swal2-timer-progress-bar {
        background: linear-gradient(90deg, #d4af37, #fde047) !important;
    }
    [data-bs-theme="light"] .elite-swal-toast .swal2-timer-progress-bar {
        background: linear-gradient(90deg, #6366f1, #3b82f6) !important;
    }

    /* ========================================================
       UNIVERSAL TABLE DROPDOWN UNCLIPPING & ELEVATION MATRIX
       ======================================================== */
    .table-responsive .dropdown-menu,
    .table .dropdown-menu,
    .has-table .dropdown-menu,
    .elite-datatable .dropdown-menu {
        z-index: 1075 !important;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.28), 0 0 0 1px rgba(255, 255, 255, 0.08) !important;
    }

    [data-bs-theme="dark"] .table-responsive .dropdown-menu,
    [data-bs-theme="dark"] .table .dropdown-menu,
    [data-bs-theme="dark"] .has-table .dropdown-menu,
    [data-bs-theme="dark"] .elite-datatable .dropdown-menu {
        background-color: #161c22 !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
    }

    /* Prevent artificial table clipping on desktop viewports */
    @media (min-width: 992px) {
        .card.has-table > .table-responsive:not(.force-scroll),
        .card:not(.force-scroll) > .table-responsive {
            overflow: visible !important;
        }
    }
</style>

@stack('styles')
