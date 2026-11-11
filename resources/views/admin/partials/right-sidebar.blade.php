<!-- Right Settings Offcanvas Panel (Atelier Workspace Studio) -->
<div class="settings-panel offcanvas offcanvas-end" tabindex="-1" id="rightSidebar" aria-labelledby="settingsPanelLabel" data-bs-backdrop="true" data-bs-scroll="false" data-bs-keyboard="true">
    <!-- Studio Header -->
    <div class="offcanvas-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-monogram" style="width: 44px; height: 44px; background: linear-gradient(135deg, #1f1a10 0%, #0d0a06 100%); border: 1.5px solid #d4af37; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);">
                <span style="color: #d4af37; font-weight: 900; font-family: 'Cinzel', serif; font-size: 1.25rem;">X</span>
            </div>
            <div>
                <h5 class="fw-black mb-0 text-body letter-spacing-1" id="settingsPanelLabel" style="font-size: 1.05rem;">Atelier Studio</h5>
                <div class="d-flex align-items-center gap-2 mt-0.5">
                    <span class="badge rounded-pill" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.65rem; padding: 2px 8px;">
                        <i class="bi bi-circle-fill me-1" style="font-size: 5px;"></i> Live Session
                    </span>
                    <span class="extra-small text-muted text-uppercase letter-spacing-1" style="font-size: 0.68rem;">Workspace Architect</span>
                </div>
            </div>
        </div>
        <button type="button" class="btn offcanvas-close-btn shadow-none" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Studio Body -->
    <div class="offcanvas-body custom-scroll">
        @auth
        <!-- 1. Staff & Director Profile Card -->
        <div class="p-3 mb-4 rounded-4 border bg-body-tertiary position-relative overflow-hidden" style="border-color: rgba(212, 175, 55, 0.2) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="extra-small text-uppercase fw-bold text-gold letter-spacing-1">
                    <i class="bi bi-person-badge me-1"></i> Active Desk Officer
                </span>
                <span class="badge bg-body border extra-small text-muted py-1 px-2">
                    <i class="bi bi-clock-history text-gold me-1"></i>{{ Auth::user()->timezone ?? 'UTC' }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if(Auth::user() && Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" alt="" class="rounded-circle object-fit-cover shadow-sm global-user-avatar" style="width: 46px; height: 46px; min-width: 46px; max-width: 46px; aspect-ratio: 1; border: 2px solid #d4af37; flex-shrink: 0;" onerror="this.style.display='none'; const fb = this.parentElement.querySelector('.offcanvas-user-fallback'); if(fb) fb.style.display='flex';">
                @endif
                <div class="rounded-circle align-items-center justify-content-center fw-bold shadow-sm offcanvas-user-fallback global-user-avatar" style="width: 46px; height: 46px; min-width: 46px; background: linear-gradient(135deg, #d4af37, #997b24); color: #000; font-size: 1.15rem; flex-shrink: 0; display: {{ (Auth::user() && Auth::user()->avatar_url) ? 'none' : 'flex' }};">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="overflow-hidden flex-grow-1">
                    <h6 class="fw-bold mb-0 text-body text-truncate">{{ Auth::user()->name }}</h6>
                    <span class="extra-small text-muted d-block text-truncate">{{ Auth::user()->designation ?: (Auth::user()->role === 'admin' ? 'Editorial Director' : 'Staff Editor') }}</span>
                    <span class="extra-small text-secondary text-truncate d-block">{{ Auth::user()->email }}</span>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top border-subtle d-flex justify-content-between align-items-center">
                <span class="extra-small text-muted">Two-Factor Authentication: <strong class="text-success">{{ Auth::user()->two_factor_enabled ? 'Active' : 'Standby' }}</strong></span>
                <a href="{{ route('admin.profile.edit') }}" class="btn btn-link btn-sm text-gold text-decoration-none p-0 extra-small fw-semibold">
                    My Profile &rarr;
                </a>
            </div>
        </div>
        @endauth

        <!-- 2. Atmosphere & Theme Mode (Rich Visual Selector) -->
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="extra-small fw-bold text-uppercase letter-spacing-1 text-muted">
                    <i class="bi bi-palette text-gold me-1"></i> Visual Atmosphere
                </span>
                <span class="extra-small text-gold fw-semibold" id="current-theme-label">Obsidian Noir</span>
            </div>
            <div class="row g-2">
                <!-- Dark Option -->
                <div class="col-4">
                    <div class="theme-card-option active" id="theme-card-dark" onclick="setTheme('dark')">
                        <div class="theme-preview-box preview-dark">
                            <div class="preview-sidebar"></div>
                            <div class="preview-content">
                                <div class="preview-bar"></div>
                                <div class="preview-card"></div>
                            </div>
                        </div>
                        <div class="fw-bold extra-small text-body">Noir</div>
                        <div class="extra-small text-muted" style="font-size: 0.65rem;">Dark Mode</div>
                    </div>
                </div>
                <!-- Light Option -->
                <div class="col-4">
                    <div class="theme-card-option" id="theme-card-light" onclick="setTheme('light')">
                        <div class="theme-preview-box preview-light">
                            <div class="preview-sidebar"></div>
                            <div class="preview-content">
                                <div class="preview-bar"></div>
                                <div class="preview-card"></div>
                            </div>
                        </div>
                        <div class="fw-bold extra-small text-body">Porcelain</div>
                        <div class="extra-small text-muted" style="font-size: 0.65rem;">Light Mode</div>
                    </div>
                </div>
                <!-- Auto Option -->
                <div class="col-4">
                    <div class="theme-card-option" id="theme-card-auto" onclick="setTheme('auto')">
                        <div class="theme-preview-box preview-auto"></div>
                        <div class="fw-bold extra-small text-body">Horizon</div>
                        <div class="extra-small text-muted" style="font-size: 0.65rem;">System Sync</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Atelier Signature Accent Palette -->
        <div class="mb-4 p-3 rounded-4 border bg-body-tertiary">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="extra-small fw-bold text-uppercase letter-spacing-1 text-muted">
                    <i class="bi bi-brush text-gold me-1"></i> Signature Accent Hue
                </span>
                <span class="extra-small text-gold fw-semibold" id="accent-name-display">Maison Gold</span>
            </div>
            <div class="d-flex align-items-center justify-content-between pt-1">
                <button type="button" class="accent-swatch active" data-color="#d4af37" data-name="Maison Gold" style="background-color: #d4af37; color: #000;" onclick="setAccentColor('#d4af37', 'Maison Gold', this)" title="Maison Gold (Signature)">
                    <i class="bi bi-check-lg"></i>
                </button>
                <button type="button" class="accent-swatch" data-color="#6366f1" data-name="Royal Indigo" style="background-color: #6366f1;" onclick="setAccentColor('#6366f1', 'Royal Indigo', this)" title="Royal Indigo">
                    <i class="bi bi-check-lg d-none"></i>
                </button>
                <button type="button" class="accent-swatch" data-color="#10b981" data-name="Emerald Savoir" style="background-color: #10b981;" onclick="setAccentColor('#10b981', 'Emerald Savoir', this)" title="Emerald Savoir">
                    <i class="bi bi-check-lg d-none"></i>
                </button>
                <button type="button" class="accent-swatch" data-color="#e11d48" data-name="Crimson Haute" style="background-color: #e11d48;" onclick="setAccentColor('#e11d48', 'Crimson Haute', this)" title="Crimson Haute">
                    <i class="bi bi-check-lg d-none"></i>
                </button>
                <button type="button" class="accent-swatch" data-color="#0ea5e9" data-name="Sky Horology" style="background-color: #0ea5e9;" onclick="setAccentColor('#0ea5e9', 'Sky Horology', this)" title="Sky Horology">
                    <i class="bi bi-check-lg d-none"></i>
                </button>
                <button type="button" class="accent-swatch" data-color="#8b5cf6" data-name="Amethyst Atelier" style="background-color: #8b5cf6;" onclick="setAccentColor('#8b5cf6', 'Amethyst Atelier', this)" title="Amethyst Atelier">
                    <i class="bi bi-check-lg d-none"></i>
                </button>
            </div>
        </div>

        <!-- 4. Typography & Layout Scale -->
        <div class="mb-4">
            <span class="extra-small fw-bold text-uppercase letter-spacing-1 text-muted d-block mb-2">
                <i class="bi bi-fonts text-gold me-1"></i> Interface Typography Scale
            </span>
            <div class="btn-group w-100" role="group" aria-label="Font Scaling">
                <button type="button" class="btn btn-outline-secondary btn-sm py-2" id="font-scale-compact" onclick="setFontScale(92)">
                    <span class="extra-small">Compact (92%)</span>
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm py-2 active" id="font-scale-standard" onclick="setFontScale(100)">
                    <span class="extra-small fw-bold">Default (100%)</span>
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm py-2" id="font-scale-generous" onclick="setFontScale(108)">
                    <span class="extra-small">Generous (108%)</span>
                </button>
            </div>
        </div>

        <!-- 5. Quick Launchpad Shortcuts -->
        <div class="mb-4">
            <span class="extra-small fw-bold text-uppercase letter-spacing-1 text-muted d-block mb-2">
                <i class="bi bi-lightning-charge text-gold me-1"></i> Quick Launchpad
            </span>
            <div class="row g-2">
                <div class="col-6">
                    <a href="{{ route('admin.activity-logs.index') }}" class="quick-jump-card">
                        <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary flex-shrink-0">
                            <i class="bi bi-journal-text fs-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold extra-small text-body text-truncate">Audit Logs</div>
                            <div class="extra-small text-muted text-truncate" style="font-size: 0.65rem;">Activity Trails</div>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('admin.seo.index') }}" class="quick-jump-card">
                        <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success flex-shrink-0">
                            <i class="bi bi-graph-up-arrow fs-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold extra-small text-body text-truncate">Master SEO</div>
                            <div class="extra-small text-muted text-truncate" style="font-size: 0.65rem;">Robots &amp; Sitemap</div>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('admin.backups.index') }}" class="quick-jump-card">
                        <div class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning flex-shrink-0">
                            <i class="bi bi-shield-lock fs-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold extra-small text-body text-truncate">Backup Vault</div>
                            <div class="extra-small text-muted text-truncate" style="font-size: 0.65rem;">Data Snapshots</div>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('blog.index') }}" target="_blank" class="quick-jump-card">
                        <div class="rounded-3 p-2 bg-info bg-opacity-10 text-info flex-shrink-0">
                            <i class="bi bi-box-arrow-up-right fs-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold extra-small text-body text-truncate">Public Salon</div>
                            <div class="extra-small text-muted text-truncate" style="font-size: 0.65rem;">Live Gazette &rarr;</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- 6. Atelier System Specs & Telemetry -->
        <div class="mb-4">
            <span class="extra-small fw-bold text-uppercase letter-spacing-1 text-muted d-block mb-2">
                <i class="bi bi-cpu text-gold me-1"></i> Atelier Telemetry &amp; Engine
            </span>
            <div class="card border bg-body-tertiary rounded-4 overflow-hidden">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2 px-3 extra-small">
                        <span class="text-muted"><i class="bi bi-hdd-network me-1"></i> Environment</span>
                        <span class="badge bg-success-subtle text-success fw-bold text-uppercase">
                            {{ app()->environment() }}
                        </span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2 px-3 extra-small">
                        <span class="text-muted"><i class="bi bi-file-earmark-code me-1"></i> PHP Runtime</span>
                        <span class="fw-bold font-monospace text-body">PHP {{ PHP_VERSION }}</span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2 px-3 extra-small">
                        <span class="text-muted"><i class="bi bi-box-seam me-1"></i> Framework Engine</span>
                        <span class="fw-bold text-body">Laravel {{ app()->version() }}</span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2 px-3 extra-small">
                        <span class="text-muted"><i class="bi bi-database me-1"></i> Database</span>
                        <span class="fw-bold font-monospace text-body">{{ config('database.default') }}</span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2 px-3 extra-small">
                        <span class="text-muted"><i class="bi bi-shield-check text-gold me-1"></i> Protection</span>
                        <span class="badge bg-primary-subtle text-primary">CSP &amp; XSS Hardened</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 7. Footer / Reset Actions -->
        <div class="pt-2 d-flex flex-column gap-2">
            <button class="btn btn-outline-danger w-100 rounded-pill btn-sm py-2" onclick="resetWorkspaceDefaults()">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Restore Default Atelier Preferences
            </button>
            <button class="btn btn-dark w-100 rounded-pill btn-sm py-2" data-bs-dismiss="offcanvas">
                Done &amp; Close Studio
            </button>
        </div>
    </div>
</div>
