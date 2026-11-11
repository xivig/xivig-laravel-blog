# Architecture Guide - XIVIG Maison Gazette

`xivig-laravel-blog` (**XIVIG Maison Gazette**) is a standalone **Laravel 12** editorial magazine platform and multi-role publishing suite built with PHP `^8.2`, Bootstrap 5, Summernote WYSIWYG, CropperJS, and `mews/captcha`.

---

## 1. Tech Stack & Dependencies

| Layer | Technology | Source |
|---|---|---|
| Runtime | PHP `^8.2` (tested on `8.3.14`) | [`composer.json`](../composer.json) |
| Framework | Laravel Framework `v12.72.0` (`^11.0 \| ^12.0`) | [`composer.json`](../composer.json) |
| CAPTCHA & Image Processing | `mews/captcha` (`^3.4`) + `intervention/image` (`^4.x`) | [`composer.json`](../composer.json) |
| Frontend & Admin UI | Pre-compiled Bootstrap 5 bundles + Bootstrap Icons + FontAwesome + jQuery + Summernote + CropperJS | [`public/`](../public/) |
| Database | SQLite (local/testing) or MySQL 8.0+ / MariaDB 10.5+ (production) | [`config/database.php`](../config/database.php) |
| Testing | PHPUnit 11 + `RefreshDatabase` (SQLite `:memory:`) | [`phpunit.xml`](../phpunit.xml) |

> [!NOTE]
> The repository includes pre-compiled admin CSS/JS/font bundles in `public/build/` and vendor assets in `public/vendor/`, so no Node.js build step is required to run or deploy the application.

---

## 2. Directory Overview

```text
xivig-laravel-blog/
|-- app/
|   |-- Console/Commands/      # 8 custom Artisan commands (CLI provisioning, backups, SEO, cleanup)
|   |-- Enums/                 # CommentStatus, MenuLocation, PostStatus, RedirectType, UserRole, WidgetType
|   |-- Helpers/               # AnalyticsHelper, ImageHelper, SeoHelper, TimezoneHelper
|   |-- Http/
|   |   |-- Controllers/
|   |   |   |-- Admin/         # 31 admin controllers + AuthController
|   |   |   |-- Api/           # PostApiController, CategoryApiController (read-only API v1)
|   |   |   |-- Frontend/      # Public ServiceController
|   |   |   |-- BlogController.php
|   |   |   `-- Controller.php
|   |   |-- Middleware/        # SecurityHeadersMiddleware, RedirectMiddleware, TrackVisitorMiddleware, etc.
|   |   |-- Requests/
|   |   `-- Resources/         # API Resource transformers
|   |-- Mail/                  # PasswordResetMail, TwoFactorOtpMail
|   |-- Models/                # 41 Eloquent domain models
|   |-- Observers/             # PostObserver, PageObserver (HTML sanitization & lifecycle hooks)
|   |-- Policies/              # PostPolicy, PagePolicy
|   |-- Providers/             # AppServiceProvider (repository bindings, shared views, rate limiters)
|   |-- Repositories/          # Contracts + Eloquent implementations (Post, Page, Category)
|   |-- Services/              # Analytics, Backup, HtmlSanitizer, Menu, SecureUpload, Seo, Sitemap, Widget
|   `-- Traits/                # Filterable, HasSeo, HasSlug, TrackActivity
|-- bootstrap/
|   |-- app.php                # Routing, middleware stack, exception rendering, 404 DB logger
|   `-- autoload.php           # Loads Composer vendor/autoload.php
|-- config/                    # app, auth, captcha, database, filesystems, session
|-- database/
|   |-- migrations/            # 31 ordered schema migrations (2026_01_01_000001 .. 000031)
|   `-- seeders/DatabaseSeeder.php
|-- docs/                      # Architecture, routes/API, CLI/maintenance, deployment guides
|-- public/
|   |-- assets/                # maison stylesheet, blog-admin stylesheet, fonts, images, js
|   |-- build/                 # Pre-compiled Vite/Rolldown assets & webfonts
|   `-- vendor/                # Local Bootstrap, Bootstrap Icons, jQuery, CropperJS, Summernote
|-- routes/
|   |-- api.php                # /api/search & /api/v1/* endpoints
|   |-- console.php            # Scheduled maintenance commands
|   `-- web.php                # Public editorial, auth, and /admin workspace routes
`-- tests/Feature/             # 19 feature test suites (185 test cases)
```

---

## 3. Application Bootstrap & Request Lifecycle

[`bootstrap/app.php`](../bootstrap/app.php) configures:

- **Routing**: Loads `routes/web.php`, `routes/api.php`, `routes/console.php`, and `/up` health check.
- **Global Web Middleware**:
  - `SecurityHeadersMiddleware` (adds security HTTP headers on responses)
  - `RedirectMiddleware` (processes configured 301/302 URL redirects)
  - `TrackVisitorMiddleware` (logs visitor/page-view telemetry)
  - `CheckSiteMaintenance` (serves the maintenance page when enabled, bypassing staff/admin auth routes)
- **Middleware Aliases**: `admin.auth`, `role`, `permission`.
- **Exception Handling**: Converts `PostTooLargeException` into user-friendly redirects/JSON and logs unhandled 404 paths into the `not_found_logs` table via `NotFoundLog` (skipping static asset extensions).

[`AppServiceProvider`](../app/Providers/AppServiceProvider.php) handles:

- Enforcing `Schema::defaultStringLength(191)` for MySQL `utf8mb4` index compatibility.
- Registering repository bindings:
  - `PostRepositoryInterface` -> `PostRepository`
  - `PageRepositoryInterface` -> `PageRepository`
  - `CategoryRepositoryInterface` -> `CategoryRepository`
- Observing `Post` (`PostObserver`) and `Page` (`PageObserver`) for automatic HTML sanitization (`HtmlSanitizerService`).
- Using Bootstrap 5 pagination (`Paginator::useBootstrapFive()`).
- Sharing global frontend view variables (`globalCategories`, `globalSettings`, `headerMenu`, `footerMenu`, `globalActiveAds`, `globalHeaderAd`, `globalFooterAd`, `globalSocials`).

---

## 4. Security Architecture

- **Public Registration Disabled**: Self-registration endpoints redirect to login (`SecurityHardeningTest`), enforcing CLI (`admin:provision`) or administrator-created staff accounts.
- **2FA Verification & Lockout**: `AuthController` supports two-factor OTP emails (`TwoFactorOtpMail`) stored hashed with attempt limits and lockouts.
- **Login & Endpoint Throttling**: `RateLimiter` protects login, 2FA attempts, 2FA resends, comments/contact submissions, likes, and live search.
- **Arithmetic CAPTCHA & Honeypots**: `mews/captcha` and session arithmetic checks guard login/contact flows; public forms (comments, contact, service inquiries) also discard automated honeypot submissions.
- **HTML Sanitization & Safe Uploads**: `HtmlSanitizerService` neutralizes XSS vectors/event handlers on posts and pages, while `SecureUploadService` validates allowed MIME types.
- **Storage Disk Resolution**: [`config/filesystems.php`](../config/filesystems.php) automatically adapts the `public` disk root on Windows environments when `public/storage` is a real directory rather than a symlink.
