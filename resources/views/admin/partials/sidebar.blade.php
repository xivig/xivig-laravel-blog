<!-- SIDEBAR -->
<aside class="sidebar d-flex flex-column vh-100" id="sidebar">
    <div class="sidebar__brand flex-shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="sidebar__brand-wrapper text-decoration-none d-flex align-items-center gap-2">
            <img src="{{ asset('assets/admin/images/xivig-logo.png') }}" alt="Logo" class="sidebar__logo" onerror="this.style.display='none'"/>
            <span class="sidebar__brand-name fw-black tracking-wider text-uppercase" style="font-size: 1.1rem; letter-spacing: 1.5px;">XIVIG GAZETTE</span>
        </a>
        <button class="sidebar__close d-xl-none" id="sidebar-close" aria-label="Close Sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar__content custom-scroll flex-grow-1">
        <ul class="sidebar__menu" id="accordion-menu">
            <li class="sidebar__header text-muted small fw-bold text-uppercase mt-4 mb-2 ps-4"
                style="font-size: 0.72rem; letter-spacing: 1.2px; opacity: 0.6;">NAVIGATION
            </li>

            <!-- Dashboard -->
            <li class="sidebar__item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="sidebar__link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                    <span class="sidebar__icon bi bi-speedometer2"></span>
                    <span class="sidebar__text">Dashboard</span>
                </a>
            </li>

            <!-- Apps & Productivity (Grouped Dropdown) -->
            @php $isAppsActive = request()->routeIs('admin.calendar.*', 'admin.task-manager.*', 'admin.file-manager.*'); @endphp
            <li class="sidebar__item sidebar__item--has-dropdown {{ $isAppsActive ? 'active show open' : '' }}">
                <a href="javascript:void(0);" class="sidebar__link {{ $isAppsActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="sidebar__icon bi bi-grid-1x2-fill"></span>
                        <span class="sidebar__text">Apps &amp; Productivity</span>
                    </div>
                    <i class="bi bi-chevron-down sidebar__chevron"></i>
                </a>
                <div class="sidebar__submenu-wrapper" style="{{ $isAppsActive ? 'grid-template-rows: 1fr; margin-bottom: 10px; display: grid;' : 'display: none; grid-template-rows: 0fr;' }}">
                    <ul class="sidebar__submenu">
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.calendar.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}">
                                <i class="bi bi-calendar3"></i> Calendar &amp; Schedules
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.task-manager.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.task-manager.*') ? 'active' : '' }}">
                                <i class="bi bi-kanban"></i> Task Manager (Architect.OS)
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.file-manager.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.file-manager.*') ? 'active' : '' }}">
                                <i class="bi bi-hdd-network"></i> File Manager &amp; Drive
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="sidebar__header text-muted small fw-bold text-uppercase mt-4 mb-2 ps-4"
                style="font-size: 0.72rem; letter-spacing: 1.2px; opacity: 0.6;">ATELIER DESKS
            </li>

            <!-- 1. Editorial Atelier (Grouped Dropdown) -->
            @php $isEditorialActive = request()->routeIs('admin.posts.*', 'admin.categories.*', 'admin.tags.*', 'admin.pages.*'); @endphp
            <li class="sidebar__item sidebar__item--has-dropdown {{ $isEditorialActive ? 'active show open' : '' }}">
                <a href="javascript:void(0);" class="sidebar__link {{ $isEditorialActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="sidebar__icon bi bi-journal-richtext"></span>
                        <span class="sidebar__text">Editorial Atelier</span>
                    </div>
                    <i class="bi bi-chevron-down sidebar__chevron"></i>
                </a>
                <div class="sidebar__submenu-wrapper" style="{{ $isEditorialActive ? 'grid-template-rows: 1fr; margin-bottom: 10px; display: grid;' : 'display: none; grid-template-rows: 0fr;' }}">
                    <ul class="sidebar__submenu">
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.posts.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.posts.index') ? 'active' : '' }}">
                                <i class="bi bi-card-text"></i> All Chronicles
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.posts.create') }}" class="sidebar__sub-link {{ request()->routeIs('admin.posts.create') ? 'active' : '' }}">
                                <i class="bi bi-pen"></i> + Write Story
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.categories.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <i class="bi bi-folder2-open"></i> Maison Categories
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.tags.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                                <i class="bi bi-tags"></i> Themes &amp; Tags
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.pages.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-richtext"></i> Static Pages
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- 2. Communication & Desk (Grouped Dropdown) -->
            @php $isCommActive = request()->routeIs('admin.contacts.*', 'admin.emails.*', 'admin.chat.*', 'admin.comments.*', 'admin.subscribers.*'); @endphp
            <li class="sidebar__item sidebar__item--has-dropdown {{ $isCommActive ? 'active show open' : '' }}">
                <a href="javascript:void(0);" class="sidebar__link {{ $isCommActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="sidebar__icon bi bi-chat-square-quote-fill"></span>
                        <span class="sidebar__text">Desk &amp; Communications</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        @if(($totalDeskAlerts ?? 0) > 0)
                            <span class="badge rounded-pill bg-danger extra-small px-2 py-0.5" style="font-size: 0.7rem;">{{ $totalDeskAlerts }}</span>
                        @endif
                        <i class="bi bi-chevron-down sidebar__chevron"></i>
                    </div>
                </a>
                <div class="sidebar__submenu-wrapper" style="{{ $isCommActive ? 'grid-template-rows: 1fr; margin-bottom: 10px; display: grid;' : 'display: none; grid-template-rows: 0fr;' }}">
                    <ul class="sidebar__submenu">
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.contacts.index') }}" class="sidebar__sub-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-chat-square-text-fill"></i> Patron Inquiries &amp; Messages
                                </div>
                                @if(($unreadInquiriesCount ?? 0) > 0)
                                    <span class="badge rounded-pill bg-warning text-dark extra-small px-2 py-0.5">{{ $unreadInquiriesCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.emails.index') }}" class="sidebar__sub-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.emails.*') ? 'active' : '' }}">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-inbox-fill"></i> Dispatches &amp; Mailbox
                                </div>
                                @if(($unreadEmailsCount ?? 0) > 0)
                                    <span class="badge rounded-pill bg-primary extra-small px-2 py-0.5">{{ $unreadEmailsCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.chat.index') }}" class="sidebar__sub-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-chat-dots-fill"></i> Live Staff Chat
                                </div>
                                @if(($unreadChatCount ?? 0) > 0)
                                    <span class="badge rounded-pill bg-danger extra-small px-2 py-0.5">{{ $unreadChatCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.comments.index') }}" class="sidebar__sub-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-chat-left-quote"></i> Discourse / Comments
                                </div>
                                @if(($unreadNotificationsCount ?? 0) > 0)
                                    <span class="badge rounded-pill bg-warning text-dark extra-small px-2 py-0.5">{{ $unreadNotificationsCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.subscribers.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.subscribers.*') ? 'active' : '' }}">
                                <i class="bi bi-people-fill"></i> Subscriber &amp; Patrons
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- 3. Design & Structure (Grouped Dropdown) -->
            @php $isDesignActive = request()->routeIs('admin.media.*', 'admin.galleries.*', 'admin.menus.*', 'admin.widgets.*', 'admin.image-cropper.*'); @endphp
            <li class="sidebar__item sidebar__item--has-dropdown {{ $isDesignActive ? 'active show open' : '' }}">
                <a href="javascript:void(0);" class="sidebar__link {{ $isDesignActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="sidebar__icon bi bi-palette2"></span>
                        <span class="sidebar__text">Design &amp; Media</span>
                    </div>
                    <i class="bi bi-chevron-down sidebar__chevron"></i>
                </a>
                <div class="sidebar__submenu-wrapper" style="{{ $isDesignActive ? 'grid-template-rows: 1fr; margin-bottom: 10px; display: grid;' : 'display: none; grid-template-rows: 0fr;' }}">
                    <ul class="sidebar__submenu">
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.galleries.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}">
                                <i class="bi bi-images"></i> Gallery Management
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.media.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                                <i class="bi bi-folder2-open"></i> Media Library
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.image-cropper.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.image-cropper.*') ? 'active' : '' }}">
                                <i class="bi bi-crop"></i> Image Cropper Studio
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.menus.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                                <i class="bi bi-menu-button-wide"></i> Navigation Menus
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.widgets.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.widgets.*') ? 'active' : '' }}">
                                <i class="bi bi-puzzle"></i> Widgets &amp; Modules
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- 4. Atelier Services & Commissions (Grouped Dropdown) -->
            @php $isServicesActive = request()->routeIs('admin.services.*'); @endphp
            <li class="sidebar__item sidebar__item--has-dropdown {{ $isServicesActive ? 'active show open' : '' }}">
                <a href="javascript:void(0);" class="sidebar__link {{ $isServicesActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="sidebar__icon bi bi-briefcase-fill"></span>
                        <span class="sidebar__text">Services &amp; Offerings</span>
                    </div>
                    <i class="bi bi-chevron-down sidebar__chevron"></i>
                </a>
                <div class="sidebar__submenu-wrapper" style="{{ $isServicesActive ? 'grid-template-rows: 1fr; margin-bottom: 10px; display: grid;' : 'display: none; grid-template-rows: 0fr;' }}">
                    <ul class="sidebar__submenu">
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.services.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.services.index') ? 'active' : '' }}">
                                <i class="bi bi-grid-fill"></i> Services Catalog
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.services.create') }}" class="sidebar__sub-link {{ request()->routeIs('admin.services.create') ? 'active' : '' }}">
                                <i class="bi bi-plus-circle-fill"></i> Add New Offering
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.services.inquiries') }}" class="sidebar__sub-link {{ request()->routeIs('admin.services.inquiries*') ? 'active' : '' }} d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-chat-square-quote-fill"></i> Client Inquiries
                                </div>
                                @php
                                    $pendingInquiriesCount = \App\Models\ServiceInquiry::where('status', 'pending')->count();
                                @endphp
                                @if($pendingInquiriesCount > 0)
                                    <span class="badge rounded-pill bg-danger extra-small px-2 py-0.5">{{ $pendingInquiriesCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('services.index') }}" target="_blank" class="sidebar__sub-link">
                                <i class="bi bi-box-arrow-up-right"></i> Public Showcase
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- 5. Monetization & Commercial (Grouped Dropdown) -->
            @php $isMonetizationActive = request()->routeIs('admin.ads.*', 'admin.transactions.*'); @endphp
            <li class="sidebar__item sidebar__item--has-dropdown {{ $isMonetizationActive ? 'active show open' : '' }}">
                <a href="javascript:void(0);" class="sidebar__link {{ $isMonetizationActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="sidebar__icon bi bi-cash-stack"></span>
                        <span class="sidebar__text">Monetization</span>
                    </div>
                    <i class="bi bi-chevron-down sidebar__chevron"></i>
                </a>
                <div class="sidebar__submenu-wrapper" style="{{ $isMonetizationActive ? 'grid-template-rows: 1fr; margin-bottom: 10px; display: grid;' : 'display: none; grid-template-rows: 0fr;' }}">
                    <ul class="sidebar__submenu">
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.ads.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}">
                                <i class="bi bi-megaphone-fill"></i> Ads Manager
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.transactions.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                                <i class="bi bi-receipt"></i> Transactions
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- 4. SEO & Diagnostics (Grouped Dropdown) -->
            @php $isSeoActive = request()->routeIs('admin.seo.*', 'admin.analytics.*', 'admin.redirects.*', 'admin.not-found-logs.*', 'admin.backups.*', 'admin.errors.*'); @endphp
            <li class="sidebar__item sidebar__item--has-dropdown {{ $isSeoActive ? 'active show open' : '' }}">
                <a href="javascript:void(0);" class="sidebar__link {{ $isSeoActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="sidebar__icon bi bi-search-heart"></span>
                        <span class="sidebar__text">SEO &amp; Diagnostics</span>
                    </div>
                    <i class="bi bi-chevron-down sidebar__chevron"></i>
                </a>
                <div class="sidebar__submenu-wrapper" style="{{ $isSeoActive ? 'grid-template-rows: 1fr; margin-bottom: 10px; display: grid;' : 'display: none; grid-template-rows: 0fr;' }}">
                    <ul class="sidebar__submenu">
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.seo.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                                <i class="bi bi-globe"></i> Master SEO Studio
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.analytics.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                                <i class="bi bi-graph-up-arrow"></i> Audience Analytics
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.redirects.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.redirects.*') ? 'active' : '' }}">
                                <i class="bi bi-arrow-left-right"></i> 301/302 Redirects
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.not-found-logs.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.not-found-logs.*') ? 'active' : '' }}">
                                <i class="bi bi-shield-exclamation"></i> 404 Error Monitor
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.errors.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.errors.*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-x"></i> Error View Templates
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.backups.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
                                <i class="bi bi-safe"></i> System Backup Vault
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- 5. System & Governance (Grouped Dropdown) -->
            @php $isSysActive = request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.activity-logs.*', 'admin.settings.*', 'admin.profile.*'); @endphp
            <li class="sidebar__item sidebar__item--has-dropdown {{ $isSysActive ? 'active show open' : '' }}">
                <a href="javascript:void(0);" class="sidebar__link {{ $isSysActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="sidebar__icon bi bi-shield-lock-fill"></span>
                        <span class="sidebar__text">System &amp; Governance</span>
                    </div>
                    <i class="bi bi-chevron-down sidebar__chevron"></i>
                </a>
                <div class="sidebar__submenu-wrapper" style="{{ $isSysActive ? 'grid-template-rows: 1fr; margin-bottom: 10px; display: grid;' : 'display: none; grid-template-rows: 0fr;' }}">
                    <ul class="sidebar__submenu">
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.users.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.users.*') && !request()->routeIs('admin.users.roles-matrix') ? 'active' : '' }}">
                                <i class="bi bi-people-fill"></i> User Accounts
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.users.roles-matrix') }}" class="sidebar__sub-link {{ request()->routeIs('admin.users.roles-matrix') ? 'active' : '' }}">
                                <i class="bi bi-shield-check"></i> RBAC &amp; Clearance Matrix
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.roles.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <i class="bi bi-person-badge"></i> Staff Roles &amp; Access
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.activity-logs.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                                <i class="bi bi-clock-history"></i> Audit &amp; Activity Logs
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.settings.index') }}" class="sidebar__sub-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <i class="bi bi-sliders"></i> Atelier &amp; SEO Settings
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.profile.edit') }}" class="sidebar__sub-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                                <i class="bi bi-person-bounding-box"></i> My Profile &amp; 2FA
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('admin.lockscreen') }}" class="sidebar__sub-link {{ request()->routeIs('admin.lockscreen') ? 'active' : '' }}">
                                <i class="bi bi-lock-fill"></i> Lock Session
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- 7. Public & Feeds (Grouped Dropdown) -->
            <li class="sidebar__item sidebar__item--has-dropdown">
                <a href="javascript:void(0);" class="sidebar__link d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="sidebar__icon bi bi-globe2"></span>
                        <span class="sidebar__text">Public &amp; Feeds</span>
                    </div>
                    <i class="bi bi-chevron-down sidebar__chevron"></i>
                </a>
                <div class="sidebar__submenu-wrapper" style="display: none; grid-template-rows: 0fr;">
                    <ul class="sidebar__submenu">
                        <li class="sidebar__sub-item">
                            <a href="{{ route('blog.index') }}" target="_blank" class="sidebar__sub-link">
                                <i class="bi bi-box-arrow-up-right"></i> Public Gazette
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('contact') }}" target="_blank" class="sidebar__sub-link">
                                <i class="bi bi-envelope-paper"></i> Contact Salon
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('blog.feed') }}" target="_blank" class="sidebar__sub-link">
                                <i class="bi bi-rss"></i> RSS 2.0 Feed
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('blog.sitemap') }}" target="_blank" class="sidebar__sub-link">
                                <i class="bi bi-diagram-3"></i> XML Sitemap
                            </a>
                        </li>
                        <li class="sidebar__sub-item">
                            <a href="{{ route('robots.txt') }}" target="_blank" class="sidebar__sub-link">
                                <i class="bi bi-robot"></i> Robots.txt
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>

    <!-- Pinned Profile Card at Bottom -->
    <div class="sidebar__footer flex-shrink-0 p-3">
        <div class="card elite-profile-card border-0 rounded-4 shadow-sm w-100 overflow-hidden">
            <div class="card-body p-3 position-relative z-2">
                <div class="d-flex align-items-center gap-2 mb-0">
                    <!-- User Avatar -->
                    <a href="{{ route('admin.profile.edit') }}" class="position-relative flex-shrink-0 text-decoration-none" title="Edit Profile" style="width: 42px; height: 42px; display: block;">
                        @if(Auth::user() && Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" alt="" class="rounded-circle object-fit-cover shadow-sm global-user-avatar" style="width: 42px; height: 42px; min-width: 42px; max-width: 42px; aspect-ratio: 1; border: 2px solid rgba(212, 175, 55, 0.4);" onerror="this.style.display='none'; const fb = this.parentElement.querySelector('.avatar-placeholder'); if(fb) fb.style.display='flex';">
                        @endif
                        <div class="avatar-placeholder rounded-circle bg-primary text-white align-items-center justify-content-center fw-black shadow-sm global-user-avatar"
                             style="width: 42px; height: 42px; min-width: 42px; font-size: 1.15rem; display: {{ (Auth::user() && Auth::user()->avatar_url) ? 'none' : 'flex' }};">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="user-status-dot position-absolute bottom-0 end-0 bg-success rounded-circle"
                              style="width: 10px; height: 10px; border: 2px solid #000;"
                              title="Online"></span>
                    </a>

                    <!-- User Name & Role -->
                    <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                        <a href="{{ route('admin.profile.edit') }}" class="text-decoration-none">
                            <h6 class="fw-bold mb-0 text-truncate text-light"
                                style="font-size: 0.875rem; line-height: 1.25;"
                                title="{{ Auth::user()->name ?? 'Administrator' }}">
                                {{ Auth::user()->name ?? 'Administrator' }}
                            </h6>
                        </a>
                        <span class="badge bg-gold text-dark extra-small fw-bold px-2 py-0 mt-1" style="font-size: 0.65rem;">
                            {{ strtoupper(Auth::user()->role ?? 'STAFF') }}
                        </span>
                    </div>

                    <!-- Lock & Logout Actions -->
                    <div class="d-flex align-items-center gap-1 ms-auto flex-shrink-0">
                        <a href="{{ route('admin.lockscreen') }}" class="btn btn-outline-warning btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center"
                           style="width: 32px; height: 32px;" title="Lock Session">
                            <i class="bi bi-lock-fill" style="font-size: 0.85rem;"></i>
                        </a>
                        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;" title="Logout">
                                <i class="bi bi-box-arrow-right" style="font-size: 0.85rem;"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>
