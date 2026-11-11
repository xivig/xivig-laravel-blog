<!-- Header -->
<header class="header">
    <div class="header__container">

        <div class="header__left">
            <button class="header__menu-toggle menu-icon" aria-label="Toggle Sidebar" id="sidebar-toggle-btn">
                <i class="bi bi-list"></i>
            </button>

            <!-- Admin Search Bar with Live Spotlight -->
            <div class="header__search position-relative" id="adminHeaderSearchContainer">
                <form action="{{ route('admin.search') }}" method="GET" class="header__search-wrapper" id="adminHeaderSearchForm">
                    <button type="submit" class="header__search-icon-btn shadow-none" title="Search">
                        <i class="bi bi-search header__search-icon"></i>
                    </button>
                    <input type="text" name="query" id="adminHeaderSearchInput" class="header__search-input" placeholder="Search chronicles, tags, contacts... (Ctrl+K)" value="{{ request('query') }}" autocomplete="off" spellcheck="false">
                    <button type="button" class="header__search-clear-btn d-none shadow-none" id="adminSearchClearBtn" title="Clear search">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                    <span class="header__search-kbd d-none d-xl-inline-flex" title="Shortcut: Ctrl + K">
                        <kbd>Ctrl</kbd><kbd>K</kbd>
                    </span>
                </form>

                <!-- Live Floating Spotlight Dropdown -->
                <div class="admin-search-dropdown shadow-2xl rounded-4 border bg-body d-none" id="adminSearchDropdown">
                    <div class="admin-search-dropdown-header py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                        <span class="extra-small fw-bold text-uppercase letter-spacing-1 text-muted">
                            <i class="bi bi-lightning-charge text-gold me-1"></i> Quick Spotlight
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-body-secondary text-muted extra-small" id="searchResultCountBadge">0 results</span>
                            <span class="extra-small text-muted font-monospace"><kbd>ESC</kbd></span>
                        </div>
                    </div>
                    <div class="admin-search-dropdown-content custom-scroll" id="adminSearchDropdownContent">
                        <!-- Populated dynamically via JS -->
                    </div>
                    <div class="admin-search-dropdown-footer py-2 px-3 border-top bg-body-tertiary text-center">
                        <a href="#" class="extra-small text-primary fw-bold text-decoration-none" id="fullSearchLink">
                            View All Search Results &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="header__right">
            <div class="header__actions d-flex align-items-center">
                <!-- Theme Toggle Button -->
                <button class="header__action-btn me-2" id="quickThemeToggle" title="Toggle Light/Dark Theme" type="button">
                    <i class="bi bi-moon-stars" id="quickThemeIcon"></i>
                </button>

                <!-- Live Chat Quick Button -->
                <a href="{{ route('admin.chat.index') }}" class="header__action-btn position-relative me-2 text-decoration-none" title="Live Staff Chat">
                    <i class="bi bi-chat-dots"></i>
                    @if(($unreadChatCount ?? 0) > 0)
                        <span class="badge rounded-pill bg-danger" style="position: absolute; top: 5px; right: 5px; font-size: 0.6rem;">
                            {{ $unreadChatCount }}
                        </span>
                    @endif
                </a>

                <!-- Mailbox Quick Button -->
                <a href="{{ route('admin.emails.index') }}" class="header__action-btn position-relative me-2 text-decoration-none" title="Dispatches & Mailbox">
                    <i class="bi bi-inbox"></i>
                    @if(($unreadEmailsCount ?? 0) > 0)
                        <span class="badge rounded-pill bg-primary" style="position: absolute; top: 5px; right: 5px; font-size: 0.6rem;">
                            {{ $unreadEmailsCount }}
                        </span>
                    @endif
                </a>

                <!-- Workspace Settings Button -->
                <button class="header__action-btn me-2" data-bs-toggle="offcanvas" data-bs-target="#rightSidebar" aria-controls="rightSidebar" title="Workspace Studio & Architect" type="button" id="workspaceSettingsBtn">
                    <i class="bi bi-gear" id="workspaceGearIcon"></i>
                </button>

                <!-- Notifications Dropdown -->
                <div class="elite-hover-dropdown position-relative me-2">
                    <button class="header__action-btn header__action-btn--badge notification-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                            <span class="badge rounded-pill bg-danger notification-badge" style="position: absolute; top: 5px; right: 5px; font-size: 0.6rem;">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-2xl p-0 overflow-hidden border-0 mt-2" style="width: 360px; border-radius: 1.25rem; z-index: 1050;">
                        <div class="dropdown-header bg-dark text-white py-4 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-black text-white mb-0">Editorial Alerts</h5>
                                <p class="text-white opacity-50 extra-small mb-0">You have {{ $unreadNotificationsCount ?? 0 }} unread alerts</p>
                            </div>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-black small">{{ $unreadNotificationsCount ?? 0 }} Pending</span>
                        </div>
                        <div class="notification-items custom-scroll bg-body" style="max-height: 380px; overflow-y: auto;">
                            @forelse($headerNotifications ?? [] as $notification)
                                <a class="dropdown-item border-bottom py-3 px-4 d-flex gap-3 align-items-start transition-all" href="{{ route('admin.comments.index') }}">
                                    <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-circle p-2 flex-shrink-0 shadow-sm" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-chat-quote-fill fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 fw-bold small text-body">{{ $notification->author_name }}</h6>
                                            <small class="text-muted extra-small ms-2">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-secondary extra-small text-truncate" style="white-space: normal;">{{ Str::limit($notification->content, 60) }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-5 bg-body">
                                    <div class="bg-body-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                        <i class="bi bi-bell-slash fs-2 text-muted opacity-50"></i>
                                    </div>
                                    <p class="mb-0 fw-bold text-muted small">No pending moderation.</p>
                                    <p class="extra-small text-muted opacity-75">All comments and actions are up to date.</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-3 bg-body-tertiary border-top text-center">
                            <a href="{{ route('admin.comments.index') }}" class="fw-bold small text-primary text-decoration-none">
                                View Comment Moderation <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Header Vertical Section Divider -->
                <div class="header__section-divider d-none d-sm-block"></div>

                <!-- User Profile Dropdown (Dedicated Isolated Section) -->
                <div class="header__profile-container dropdown">
                    <button class="header-profile-btn btn p-1 border-0 shadow-none d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="headerProfileDropdownBtn">
                        @if(Auth::user() && Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" alt="" class="rounded-circle object-fit-cover shadow-sm global-user-avatar" style="width: 40px; height: 40px; min-width: 40px; max-width: 40px; aspect-ratio: 1; border: 1.5px solid rgba(212, 175, 55, 0.4);" onerror="this.style.display='none'; const fb = this.parentElement.querySelector('.avatar-placeholder'); if(fb) fb.style.display='flex';">
                        @endif
                        <div class="avatar-placeholder rounded-circle bg-primary text-white align-items-center justify-content-center fw-black shadow-sm global-user-avatar" style="width: 40px; height: 40px; min-width: 40px; font-size: 1rem; display: {{ (Auth::user() && Auth::user()->avatar_url) ? 'none' : 'flex' }};">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="d-none d-lg-block text-start" style="line-height: 1.2;">
                            <span class="fw-bold d-block small text-body">{{ Auth::user()->name ?? 'Administrator' }}</span>
                            <span class="extra-small text-muted text-uppercase">{{ Auth::user()->role ?? 'Editor' }}</span>
                        </div>
                        <i class="bi bi-chevron-down d-none d-lg-block text-muted small ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="min-width: 230px;">
                        <li class="p-2 border-bottom mb-1">
                            <span class="fw-bold d-block small text-body">{{ Auth::user()->name }}</span>
                            <span class="extra-small text-muted">{{ Auth::user()->email }}</span>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('admin.profile.edit') }}">
                                <i class="bi bi-person-bounding-box text-primary"></i> My Profile &amp; Timezone
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('admin.chat.index') }}">
                                <i class="bi bi-chat-dots-fill text-info"></i> Live Staff Chat
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('admin.emails.index') }}">
                                <i class="bi bi-inbox-fill text-success"></i> Mailbox &amp; Dispatches
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('admin.activity-logs.index') }}">
                                <i class="bi bi-clock-history text-warning"></i> Audit &amp; Activity Logs
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('admin.settings.index') }}">
                                <i class="bi bi-sliders text-secondary"></i> Site Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('blog.index') }}" target="_blank">
                                <i class="bi bi-box-arrow-up-right text-success"></i> Visit Public Gazette
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2 text-warning" href="{{ route('admin.lockscreen') }}">
                                <i class="bi bi-lock-fill"></i> Lock Session
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-3 py-2 small text-danger d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-right"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

    </div>
</header>
