<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $globalSettings['title'] . ' | ' . $globalSettings['tagline'])</title>
    <meta name="description" content="@yield('meta_description', $globalSettings['description'])">
    <meta name="keywords" content="@yield('meta_keywords', 'luxury, fashion, haute couture, horology, editorial, gazette')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    @if(!empty($globalSettings['google_verification'] ?? null))
    <meta name="google-site-verification" content="{{ $globalSettings['google_verification'] }}">
    @endif

    <!-- Search Engine Indexing Governance -->
    @if(isset($noindex) && $noindex)
    <meta name="robots" content="noindex, nofollow">
    @else
    <meta name="robots" content="@yield('meta_robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
    @endif

    <!-- Open Graph & Social Cards -->
    <meta property="og:title" content="@yield('title', $globalSettings['title'])">
    <meta property="og:description" content="@yield('meta_description', $globalSettings['description'])">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:image" content="@yield('og_image', asset('assets/images/branding/logo-CDpcaf-E.png'))">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('title', $globalSettings['title'])">
    <meta name="twitter:description" content="@yield('meta_description', $globalSettings['description'])">
    <meta name="twitter:image" content="@yield('og_image', asset('assets/images/branding/logo-CDpcaf-E.png'))">
    <meta name="twitter:site" content="@yield('twitter_site', '@XIVIGMaison')">
    <meta name="twitter:creator" content="@yield('twitter_creator', '@XIVIGMaison')">

    <!-- Theme Controller (Zero Flash) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('xivig_blog_theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (systemDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <!-- Local Frameworks, Typography & Ultramodern Stylesheet -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/blog-maison.css') }}">

    <!-- RSS 2.0 & Sitemap Auto-Discovery -->
    <link rel="alternate" type="application/rss+xml" title="{{ $globalSettings['title'] ?? 'XIVIG Maison Gazette' }} &#8212; RSS 2.0 Feed" href="{{ route('blog.feed') }}">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('blog.sitemap') }}">

    <!-- Structured Data (JSON-LD) -->
    @hasSection('structured_data')
        @yield('structured_data')
    @else
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'NewsMediaOrganization',
            'name' => $globalSettings['title'] ?? 'XIVIG Maison Gazette',
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('assets/images/branding/logo-CDpcaf-E.png'),
            ],
            'description' => $globalSettings['description'] ?? 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics.',
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
        </script>
    @endif
</head>
<body class="d-flex flex-column min-vh-100 bg-body text-body">

    <!-- Top Reading Progress Bar (For Article Pages) -->
    <div id="readingProgressBar" class="reading-progress-bar"></div>

    <!-- Luxury Top Announcement & Multi-Capital Clocks Bar -->
    <div class="top-announcement py-2 px-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-gold text-dark fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Gazette Édition</span>
                
                <!-- Live World Clocks -->
                <div class="capital-clocks-ticker d-none d-md-flex align-items-center">
                    <span class="capital-clock-item">
                        <span class="capital-clock-city">PARIS</span>
                        <span id="clockParis" class="capital-clock-time">--:--</span>
                    </span>
                    <span class="opacity-25">•</span>
                    <span class="capital-clock-item">
                        <span class="capital-clock-city">GENÈVE</span>
                        <span id="clockGeneva" class="capital-clock-time">--:--</span>
                    </span>
                    <span class="opacity-25">•</span>
                    <span class="capital-clock-item">
                        <span class="capital-clock-city">NYC</span>
                        <span id="clockNYC" class="capital-clock-time">--:--</span>
                    </span>
                    <span class="opacity-25">•</span>
                    <span class="capital-clock-item">
                        <span class="capital-clock-city">TOKYO</span>
                        <span id="clockTokyo" class="capital-clock-time">--:--</span>
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 small">
                <span class="text-secondary d-none d-lg-inline">{{ now()->format('l, F j, Y') }}</span>
                <span class="text-secondary opacity-25 d-none d-lg-inline">|</span>
                <a href="{{ route('blog.feed') }}" class="text-secondary text-decoration-none hover-gold d-flex align-items-center gap-1" title="RSS 2.0 Syndication">
                    <i class="bi bi-rss-fill text-warning"></i> <span class="d-none d-sm-inline">RSS Feed</span>
                </a>
                <span class="text-secondary opacity-25">|</span>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="text-gold fw-semibold text-decoration-none d-flex align-items-center gap-1">
                        <i class="bi bi-shield-lock-fill"></i> <span class="d-none d-sm-inline">Admin Portal</span>
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="text-secondary text-decoration-none hover-gold d-flex align-items-center gap-1">
                        <i class="bi bi-person-fill"></i> <span class="d-none d-sm-inline">Staff Login</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Sticky Glassmorphic Header -->
    <header class="site-header sticky-top">
        <div class="container header-container">
            <div class="header-inner-row d-flex justify-content-between align-items-center">
                <!-- Brand / Luxury Emblem -->
                <a href="{{ route('blog.index') }}" class="brand-emblem-wrap flex-shrink-0" title="Maison XIVIG Gazette">
                    <div class="brand-monogram">
                        <span class="brand-monogram-inner">X</span>
                    </div>
                    <div>
                        <div class="brand-title">XIVIG</div>
                        <div class="brand-subtitle">MAISON GAZETTE</div>
                    </div>
                </a>

                <!-- Desktop Dynamic Atelier Navigation -->
                <nav class="site-nav-desktop d-none d-xl-flex align-items-center gap-1 gap-xxl-2" aria-label="Primary Navigation">
                    @if(isset($headerMenu) && $headerMenu && $headerMenu->rootItems->isNotEmpty())
                        @php
                            $primaryItems = $headerMenu->rootItems->take(3);
                            $moreItems = $headerMenu->rootItems->count() > 3 ? $headerMenu->rootItems->slice(3) : collect();
                            $currentUrl = url()->current();
                        @endphp

                        @foreach($primaryItems as $menuItem)
                            @php
                                $hasChildren = $menuItem->children->isNotEmpty();
                                $itemUrl = $menuItem->url ? (str_starts_with($menuItem->url, 'http') ? $menuItem->url : url($menuItem->url)) : '#';
                                
                                // Determine active state
                                $isActive = false;
                                if ($menuItem->url === '/' || $menuItem->url === route('blog.index')) {
                                    $isActive = request()->routeIs('blog.index') && !request('category') && !request('tag') && !request('author');
                                } elseif ($menuItem->url) {
                                    $isActive = $currentUrl === $itemUrl || request()->is(ltrim($menuItem->url, '/'));
                                }
                                
                                if (!$isActive && $hasChildren) {
                                    $isActive = $menuItem->children->contains(function ($child) use ($currentUrl) {
                                        $childUrl = $child->url ? (str_starts_with($child->url, 'http') ? $child->url : url($child->url)) : '';
                                        return $currentUrl === $childUrl || ($child->url && request()->is(ltrim($child->url, '/')));
                                    });
                                }
                            @endphp

                            @if($hasChildren)
                                <div class="nav-editorial-dropdown position-relative">
                                    <a href="{{ $itemUrl }}" class="nav-editorial-link {{ $isActive ? 'active' : '' }}" target="{{ $menuItem->target ?: '_self' }}">
                                        @if($menuItem->icon)<i class="{{ $menuItem->icon }} extra-small text-gold"></i>@endif
                                        <span>{{ $menuItem->title }}</span>
                                        <i class="bi bi-chevron-down nav-chevron-icon ms-1"></i>
                                    </a>
                                    <div class="nav-dropdown-menu">
                                        <div class="nav-dropdown-inner">
                                            @foreach($menuItem->children as $child)
                                                @php
                                                    $childUrl = $child->url ? (str_starts_with($child->url, 'http') ? $child->url : url($child->url)) : '#';
                                                    $isChildActive = $currentUrl === $childUrl || ($child->url && request()->is(ltrim($child->url, '/')));
                                                @endphp
                                                <a href="{{ $childUrl }}" class="nav-dropdown-item {{ $isChildActive ? 'active' : '' }}" target="{{ $child->target ?: '_self' }}">
                                                    @if($child->icon)<i class="{{ $child->icon }} text-gold me-2"></i>@endif
                                                    <span>{{ $child->title }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ $itemUrl }}" class="nav-editorial-link {{ $isActive ? 'active' : '' }}" target="{{ $menuItem->target ?: '_self' }}">
                                    @if($menuItem->icon)<i class="{{ $menuItem->icon }} extra-small text-gold"></i>@endif
                                    <span>{{ $menuItem->title }}</span>
                                </a>
                            @endif
                        @endforeach

                        {{-- Elegant Overflow "Maison ▾" Dropdown for menu items > 5 --}}
                        @if($moreItems->isNotEmpty())
                            @php
                                $isMoreActive = $moreItems->contains(function($item) use ($currentUrl) {
                                    $itemUrl = $item->url ? (str_starts_with($item->url, 'http') ? $item->url : url($item->url)) : '';
                                    return $currentUrl === $itemUrl || ($item->url && request()->is(ltrim($item->url, '/')));
                                });
                            @endphp
                            <div class="nav-editorial-dropdown position-relative">
                                <a href="#" class="nav-editorial-link {{ $isMoreActive ? 'active' : '' }}" role="button">
                                    <span>Maison</span>
                                    <i class="bi bi-chevron-down nav-chevron-icon ms-1"></i>
                                </a>
                                <div class="nav-dropdown-menu">
                                    <div class="nav-dropdown-inner">
                                        @foreach($moreItems as $mItem)
                                            @php
                                                $mItemUrl = $mItem->url ? (str_starts_with($mItem->url, 'http') ? $mItem->url : url($mItem->url)) : '#';
                                                $isMItemActive = $currentUrl === $mItemUrl || ($mItem->url && request()->is(ltrim($mItem->url, '/')));
                                            @endphp
                                            <a href="{{ $mItemUrl }}" class="nav-dropdown-item {{ $isMItemActive ? 'active' : '' }}" target="{{ $mItem->target ?: '_self' }}">
                                                @if($mItem->icon)<i class="{{ $mItem->icon }} text-gold me-2"></i>@endif
                                                <span>{{ $mItem->title }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                    @else
                        {{-- Fallback Navigation --}}
                        <a href="{{ route('blog.index') }}" class="nav-editorial-link {{ request()->routeIs('blog.index') && !request('category') ? 'active' : '' }}">
                            <span>Chronicles</span>
                        </a>
                        @foreach($globalCategories->take(3) as $cat)
                            <a href="{{ route('blog.category', $cat->slug) }}" class="nav-editorial-link {{ request()->is('category/' . $cat->slug) ? 'active' : '' }}">
                                <span>{{ $cat->name }}</span>
                            </a>
                        @endforeach
                    @endif
                    <a href="{{ route('services.index') }}" class="nav-editorial-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                        <i class="bi bi-gem extra-small text-gold"></i>
                        <span>Services</span>
                    </a>
                </nav>

                <!-- Actions: Search Trigger, Theme Switcher & Mobile Menu Trigger -->
                <div class="header-actions d-flex align-items-center gap-2 flex-shrink-0">
                    <!-- Instant Search Command Trigger -->
                    <button type="button" class="btn btn-search-capsule flex-shrink-0" data-bs-toggle="modal" data-bs-target="#searchModal" title="Search articles (Ctrl + K)">
                        <i class="bi bi-search text-gold small"></i>
                        <span class="d-none d-sm-inline small search-text">Search</span>
                        <span class="search-kbd-badge d-none d-md-inline-flex align-items-center">Ctrl K</span>
                    </button>

                    <!-- Theme Switcher -->
                    <button class="btn btn-theme-toggle flex-shrink-0" id="themeToggleBtn" title="Toggle Night/Day Mode">
                        <i class="bi bi-moon-stars-fill text-gold" id="themeIcon"></i>
                    </button>

                    <!-- Mobile Drawer Toggle -->
                    <button class="btn btn-theme-toggle d-xl-none flex-shrink-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenuOffcanvas" aria-controls="mobileMenuOffcanvas" title="Open Menu">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Offcanvas Mobile Navigation Menu -->
    <div class="offcanvas offcanvas-end glass-panel" tabindex="-1" id="mobileMenuOffcanvas" aria-labelledby="mobileMenuOffcanvasLabel">
        <div class="offcanvas-header border-bottom border-subtle p-4">
            <div class="d-flex align-items-center gap-2">
                <div class="brand-monogram" style="width: 38px; height: 38px;">
                    <span class="brand-monogram-inner fs-6">X</span>
                </div>
                <div>
                    <div class="brand-title fs-5">XIVIG</div>
                    <div class="brand-subtitle" style="font-size: 0.52rem;">MAISON GAZETTE</div>
                </div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4 d-flex flex-column justify-content-between">
            <div class="d-flex flex-column gap-2">
                <div class="mb-2">
                    <span class="text-gold extra-small text-uppercase fw-bold letter-spacing-2">Atelier Navigation</span>
                </div>

                @if(isset($headerMenu) && $headerMenu && $headerMenu->rootItems->isNotEmpty())
                    @foreach($headerMenu->rootItems as $menuItem)
                        @php
                            $itemUrl = $menuItem->url ? (str_starts_with($menuItem->url, 'http') ? $menuItem->url : url($menuItem->url)) : '#';
                            $isActive = request()->url() === $itemUrl || ($menuItem->url === '/' && request()->routeIs('blog.index') && !request('category'));
                        @endphp
                        <div class="mobile-nav-item mb-1">
                            <a href="{{ $itemUrl }}" class="mobile-editorial-link {{ $isActive ? 'active' : '' }}" target="{{ $menuItem->target ?: '_self' }}">
                                <span class="d-flex align-items-center gap-2">
                                    @if($menuItem->icon)<i class="{{ $menuItem->icon }} text-gold"></i>
                                    @elseif($menuItem->url === '/')<i class="bi bi-grid-fill text-gold"></i>
                                    @elseif(str_contains($menuItem->url, 'category'))<i class="bi bi-folder2-open text-gold"></i>
                                    @elseif(str_contains($menuItem->url, 'contact'))<i class="bi bi-envelope-fill text-gold"></i>
                                    @else<i class="bi bi-compass text-gold"></i>@endif
                                    <span>{{ $menuItem->title }}</span>
                                </span>
                                @if($menuItem->children->isNotEmpty())
                                    <i class="bi bi-chevron-down extra-small opacity-50"></i>
                                @endif
                            </a>
                            @if($menuItem->children->isNotEmpty())
                                <div class="mobile-subnav ps-4 pt-1 pb-2 d-flex flex-column gap-1">
                                    @foreach($menuItem->children as $child)
                                        <a href="{{ url($child->url) }}" class="mobile-subnav-link {{ request()->url() === url($child->url) ? 'active' : '' }}" target="{{ $child->target ?: '_self' }}">
                                            <i class="bi bi-dash text-gold opacity-75 me-1"></i>
                                            <span>{{ $child->title }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <a href="{{ route('blog.index') }}" class="mobile-editorial-link {{ request()->routeIs('blog.index') && !request('category') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill text-gold me-2"></i> All Chronicles
                    </a>
                    <a href="{{ route('services.index') }}" class="mobile-editorial-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                        <i class="bi bi-gem text-gold me-2"></i> Bespoke Services &amp; Atelier
                    </a>
                    @foreach($globalCategories as $cat)
                        <a href="{{ route('blog.category', $cat->slug) }}" class="mobile-editorial-link {{ request()->is('category/' . $cat->slug) ? 'active' : '' }}">
                            <i class="bi bi-folder2-open text-gold me-2"></i> {{ $cat->name }}
                            <span class="badge bg-gold-subtle text-gold rounded-pill extra-small ms-auto">{{ $cat->published_posts_count }}</span>
                        </a>
                    @endforeach
                @endif

                <div class="border-top border-subtle pt-4 mt-3">
                    <button class="btn btn-search-capsule w-100 justify-content-center py-2 mb-3" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="bi bi-search text-gold"></i>
                        <span>Search the Maison</span>
                        <span class="search-kbd-badge">Ctrl K</span>
                    </button>
                    <button class="btn btn-outline-editorial w-100 py-2 d-flex align-items-center justify-content-center gap-2" id="mobileThemeToggleBtn">
                        <i class="bi bi-moon-stars-fill text-gold" id="mobileThemeIcon"></i>
                        <span>Switch Display Atmosphere</span>
                    </button>
                </div>
            </div>

            <div class="border-top border-subtle pt-4">
                <div class="d-flex justify-content-center gap-3 fs-5 text-secondary mb-3">
                    <a href="{{ $globalSocials['instagram']->url ?? 'https://instagram.com' }}" target="_blank" rel="noopener noreferrer" class="text-secondary hover-gold" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="{{ $globalSocials['twitter']->url ?? 'https://x.com' }}" target="_blank" rel="noopener noreferrer" class="text-secondary hover-gold" title="X (Twitter)"><i class="bi bi-twitter-x"></i></a>
                    <a href="{{ $globalSocials['linkedin']->url ?? 'https://linkedin.com' }}" target="_blank" rel="noopener noreferrer" class="text-secondary hover-gold" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="{{ route('blog.sitemap.html') }}" class="text-secondary hover-gold" title="Editorial Sitemap"><i class="bi bi-diagram-3"></i></a>
                </div>
                <div class="text-center extra-small text-secondary">
                    &copy; {{ date('Y') }} Maison XIVIG. Haute Couture & Horology.
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Body -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Luxury Architectural Editorial Footer -->
    <footer class="footer-maison py-5 mt-5">
        <div class="container pt-3">
            <div class="row g-4 justify-content-between mb-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="brand-monogram">
                            <span class="brand-monogram-inner">X</span>
                        </div>
                        <div>
                            <div class="brand-title fs-4">XIVIG</div>
                            <div class="brand-subtitle">MAISON GAZETTE</div>
                        </div>
                    </div>
                    <p class="text-secondary small pe-lg-4 mb-4">
                        The definitive editorial journal of Maison XIVIG. Chronicling the rarefied crossroads of haute couture, precision horology, bespoke craftsmanship, and transcendent aesthetic philosophy.
                    </p>
                    <div class="d-flex gap-3 fs-5">
                        <a href="{{ $globalSocials['instagram']->url ?? 'https://instagram.com' }}" target="_blank" rel="noopener noreferrer" class="text-secondary hover-gold" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="{{ $globalSocials['twitter']->url ?? 'https://x.com' }}" target="_blank" rel="noopener noreferrer" class="text-secondary hover-gold" title="X (Twitter)"><i class="bi bi-twitter-x"></i></a>
                        <a href="{{ $globalSocials['linkedin']->url ?? 'https://linkedin.com' }}" target="_blank" rel="noopener noreferrer" class="text-secondary hover-gold" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="{{ $globalSocials['youtube']->url ?? 'https://youtube.com' }}" target="_blank" rel="noopener noreferrer" class="text-secondary hover-gold" title="YouTube"><i class="bi bi-youtube"></i></a>
                        <a href="{{ route('blog.sitemap.html') }}" class="text-secondary hover-gold" title="Editorial Sitemap"><i class="bi bi-diagram-3"></i></a>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="fw-bold text-uppercase small letter-spacing-2 mb-3 text-gold">Curations</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        @foreach($globalCategories->take(5) as $cat)
                            <li>
                                <a href="{{ route('blog.category', $cat->slug) }}" class="text-secondary text-decoration-none hover-gold d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-chevron-right extra-small text-gold opacity-50"></i> {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="fw-bold text-uppercase small letter-spacing-2 mb-3 text-gold">The Journal</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        @if(isset($footerMenu) && $footerMenu && $footerMenu->rootItems->isNotEmpty())
                            @foreach($footerMenu->rootItems as $fitem)
                                <li>
                                    <a href="{{ $fitem->url ? (str_starts_with($fitem->url, 'http') ? $fitem->url : url($fitem->url)) : '#' }}" class="text-secondary text-decoration-none hover-gold d-inline-flex align-items-center gap-1" target="{{ $fitem->target ?: '_self' }}">
                                        <i class="bi bi-chevron-right extra-small text-gold opacity-50"></i> {{ $fitem->title }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li>
                                <a href="{{ route('blog.index') }}" class="text-secondary text-decoration-none hover-gold d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-chevron-right extra-small text-gold opacity-50"></i> All Chronicles
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('services.index') }}" class="text-secondary text-decoration-none hover-gold d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-chevron-right extra-small text-gold opacity-50"></i> Bespoke Services &amp; Atelier
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" class="text-secondary text-decoration-none hover-gold d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-chevron-right extra-small text-gold opacity-50"></i> Contact Editorial Desk
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('blog.sitemap.html') }}" class="text-secondary text-decoration-none hover-gold d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-chevron-right extra-small text-gold opacity-50"></i> Editorial Sitemap
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.login') }}" class="text-secondary text-decoration-none hover-gold d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-chevron-right extra-small text-gold opacity-50"></i> Staff Editorial Desk
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="col-lg-4">
                    <div class="vip-dispatch-box p-4 rounded-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-gold text-dark fw-bold text-uppercase" style="font-size: 0.65rem;">Privé</span>
                            <h6 class="fw-bold text-uppercase small letter-spacing-1 mb-0 text-gold">Maison Gazette Dispatch</h6>
                        </div>
                        <p class="text-secondary small mb-3">
                            Receive private weekly dispatches covering atelier secrets, runway critiques, and horological reviews directly in your inbox.
                        </p>
                        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-integrated-bar">
                            @csrf
                            <!-- Anti-spam Honeypot -->
                            <div style="display:none !important;" aria-hidden="true">
                                <input type="text" name="company_verify_field" tabindex="-1" autocomplete="off">
                            </div>
                            <div class="newsletter-input-wrap">
                                <input type="email" name="email" required placeholder="Enter your email address..." class="newsletter-input" autocomplete="email">
                                <button type="submit" class="btn-newsletter-submit">Subscribe</button>
                            </div>
                        </form>
                        @if(session('newsletter_success'))
                            <div class="alert alert-success alert-dismissible fade show mt-2 py-1 px-3 small rounded-pill" role="alert">
                                <i class="bi bi-check-circle-fill me-1"></i> {{ session('newsletter_success') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Global Édition Locations & Legal -->
            <div class="border-top border-subtle pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 small text-secondary">
                <div class="d-flex flex-wrap align-items-center gap-2 text-uppercase letter-spacing-1 extra-small">
                    <span class="text-gold fw-bold">Ateliers:</span>
                    <span>Paris</span> • <span>Geneva</span> • <span>Milan</span> • <span>London</span> • <span>New York</span> • <span>Tokyo</span>
                </div>
                <div class="d-flex gap-3 extra-small">
                    <a href="{{ route('page.show', 'privacy-policy') }}" class="text-secondary text-decoration-none hover-gold">Privacy Policy</a>
                    <span>•</span>
                    <a href="{{ route('page.show', 'terms-of-dispatch') }}" class="text-secondary text-decoration-none hover-gold">Terms of Dispatch</a>
                    <span>•</span>
                    <a href="{{ route('page.show', 'editorial-ethics') }}" class="text-secondary text-decoration-none hover-gold">Editorial Ethics</a>
                </div>
            </div>

            <div class="text-center pt-3 text-secondary extra-small opacity-75">
                &copy; {{ date('Y') }} Maison XIVIG. Crafted for high-end luxury editorial publishing. Production ready.
            </div>
        </div>
    </footer>

    <!-- Instant Predictive Command Palette Modal (Ctrl + K) -->
    <div class="modal fade command-palette-modal" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-bottom border-subtle p-3 px-4">
                    <div class="d-flex align-items-center gap-3 w-100">
                        <i class="bi bi-search text-gold fs-5"></i>
                        <input type="text" id="liveSearchInput" class="form-control form-control-lg bg-transparent border-0 shadow-none text-body" placeholder="Search stories, haute horology, designers..." autocomplete="off">
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <div id="liveSearchResults" class="live-search-container">
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-search display-6 opacity-25 d-block mb-2"></i>
                            <span>Type at least 2 characters to discover curated chronicles...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-subtle py-2 px-4 d-flex justify-content-between align-items-center small text-muted">
                    <div class="d-flex align-items-center gap-2">
                        <span>Press <kbd>ESC</kbd> to exit</span>
                        <span class="opacity-25">•</span>
                        <span><kbd>Ctrl K</kbd> anywhere</span>
                    </div>
                    <a href="{{ route('blog.search') }}" id="fullSearchBtn" class="text-gold text-decoration-none fw-semibold">
                        View Full Archive &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/blog-main.js') }}"></script>
    @yield('scripts')
</body>
</html>
