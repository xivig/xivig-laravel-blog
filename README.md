# XIVIG Maison Gazette (`xivig-laravel-blog`)

A full-featured luxury editorial blog and publishing suite built with **Laravel 12** (`PHP ^8.2`), **Bootstrap 5**, **Summernote WYSIWYG**, **CropperJS**, and **`mews/captcha`**, featuring a public digital gazette, read-only JSON REST API v1, multi-role administration panel, and built-in security/deployment tooling.

---

## Key Highlights

- **Public Editorial Magazine**: Sticky & featured article hero, category/tag/author archives, live JSON search (`/api/search`), password-protected articles (`/article/{id}/unlock`), likes & rate-limited comments with honeypots, bespoke services showcase (`/services`), newsletter subscriptions, dynamic CMS pages, and XSL-styled XML sitemap (`/sitemap.xml`) + RSS feed (`/feed.xml`).
- **Role-Based Admin Suite (`/admin`)**: Full CRUD for posts (with revision history & rollback, gallery images, clone, and preview mode), categories, tags, comment & reply moderation, pages, 301/302 redirects, 404 logs, media library, galleries, ads & transactions, subscribers (CSV import/export & broadcast), bespoke service inquiries, menus, widgets, SEO Studio, analytics, and activity logs.
- **Built-in Workspace Modules**: Live staff chat, contact inquiry desk with reply workflow, webmail inbox/compose, calendar events, task manager, cloud file manager, image cropper studio, lockscreen, and two-factor OTP verification.
- **Security & Ops Readiness**: Public self-registration disabled in favor of `admin:provision`, global `SecurityHeadersMiddleware`, HTML sanitization via model observers, arithmetic/image CAPTCHA, rate limiting, and `deploy:clean` release packaging & pre-flight security checks.

---

## Requirements

- **PHP** `^8.2` (`8.3.14` verified) with `pdo_sqlite` / `pdo_mysql`, `gd`, `mbstring`, `openssl`, `xml`, `fileinfo`, `zip`
- **Composer** `2.x`
- **Database**: SQLite (local/testing) or MySQL 8.0+ / MariaDB 10.5+ (production)

> No Node.js / npm build step is required — pre-compiled Bootstrap 5 admin assets and vendor libraries are bundled directly in `public/build/` and `public/vendor/`.

---

## Quick Start (Local SQLite)

```bash
cd xivig-laravel-blog
composer install
cp .env.example .env
php artisan key:generate
```

Update `.env` to use SQLite for local development:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Create the SQLite database file, run migrations & seeders, and link public storage:

```bash
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

> [!IMPORTANT]
> In **local** and **testing** environments, `DatabaseSeeder` seeds the default administrator `admin@xivig.com` / `admin123456` unless `INITIAL_ADMIN_EMAIL`, `INITIAL_ADMIN_NAME`, and `INITIAL_ADMIN_PASSWORD` are set in `.env`. In **production**, omitting `INITIAL_ADMIN_PASSWORD` generates a 24-character random password. Run `php artisan deploy:clean --check` to audit credentials before going live.

---

## Custom Artisan CLI Commands

```bash
# Provision a new admin or editor with a 24-char random password
php artisan admin:provision --email=chief@xivig.com --role=admin --random-password

# Reset an existing admin password
php artisan admin:reset-password admin@xivig.com

# Run automated SEO audit & regenerate sitemap
php artisan blog:seo-audit
php artisan sitemap:generate

# Publish scheduled articles & generate DB backup
php artisan blog:publish-scheduled
php artisan blog:backup

# Clean dev artifacts, run security audit, or create release archive in dist/
php artisan deploy:clean --all
php artisan deploy:clean --check
php artisan deploy:clean --package
```

---

## Testing

The test suite runs against an in-memory SQLite database (`phpunit.xml`) with CAPTCHA disabled for test automation:

```bash
php artisan test
```

Verified suite: **19 feature test classes, 185 passing test cases (1,102 assertions)**.

---

## Documentation Index

| Document | Description |
|---|---|
| [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) | Tech stack, directory structure, repository pattern, observers, middleware stack, and security model |
| [`docs/ROUTES_AND_API.md`](docs/ROUTES_AND_API.md) | Complete public routes, read-only REST API v1 endpoints, auth endpoints, and admin workspace routes |
| [`docs/CLI_AND_MAINTENANCE.md`](docs/CLI_AND_MAINTENANCE.md) | All 8 custom Artisan commands, cron scheduler (`routes/console.php`), and seeding credentials |
| [`DEPLOYMENT.md`](DEPLOYMENT.md) | Ubuntu/Nginx production deployment runbook, PHP-FPM config, cron setup, and release packaging |
