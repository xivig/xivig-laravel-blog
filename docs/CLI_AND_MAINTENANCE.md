# CLI Commands, Scheduling & Seeding - XIVIG Maison Gazette

`xivig-laravel-blog` includes 8 custom Artisan commands for staff provisioning, scheduled publishing, backups, SEO auditing, and release hygiene.

---

## 1. Custom Artisan Commands

| Command | Options / Arguments | Description | Source |
|---|---|---|---|
| `admin:provision` | `--name=`, `--email=`, `--password=`, `--role=admin\|editor`, `--random-password` | Creates or updates an administrative/editorial user and logs an audit entry | [`ProvisionAdminCommand.php`](../app/Console/Commands/ProvisionAdminCommand.php) |
| `admin:reset-password` | `email`, `--password=` | Resets an admin user password via CLI and clears pending 2FA codes | [`ResetAdminPasswordCommand.php`](../app/Console/Commands/ResetAdminPasswordCommand.php) |
| `blog:backup` | — | Generates a daily database snapshot archive in `storage/app/backups/` | [`BackupDatabaseCommand.php`](../app/Console/Commands/BackupDatabaseCommand.php) |
| `blog:clear-logs` | `--days=60` | Purges activity, 404, and visitor telemetry logs older than N days | [`ClearOldLogsCommand.php`](../app/Console/Commands/ClearOldLogsCommand.php) |
| `blog:publish-scheduled` | — | Publishes scheduled posts whose `published_at` timestamp has arrived | [`PublishScheduledPostsCommand.php`](../app/Console/Commands/PublishScheduledPostsCommand.php) |
| `blog:seo-audit` | — | Runs automated SEO checks across published articles | [`SeoAuditCommand.php`](../app/Console/Commands/SeoAuditCommand.php) |
| `sitemap:generate` | — | Regenerates XML sitemap output (`public/sitemap.xml`) | [`GenerateSitemapCommand.php`](../app/Console/Commands/GenerateSitemapCommand.php) |
| `deploy:clean` | `--backups`, `--logs`, `--sessions`, `--cache`, `--all`, `--check`, `--package` | Purges dev clutter, audits production readiness (`--check`), or builds a clean release `.zip` inside `dist/` (`--package`) | [`CleanDeploymentCommand.php`](../app/Console/Commands/CleanDeploymentCommand.php) |

---

## 2. Scheduler Configuration (`routes/console.php`)

[`routes/console.php`](../routes/console.php) defines:

| Cadence | Command |
|---|---|
| `everyMinute()` | `blog:publish-scheduled` |
| `daily()` | `sitemap:generate` |
| `daily()` | `blog:seo-audit` |
| `daily()` | `blog:backup` |
| `weekly()` | `blog:clear-logs --days=30` |

Run Laravel's scheduler in production via cron:

```cron
* * * * * cd /var/www/xivig-laravel-blog && php artisan schedule:run >> /dev/null 2>&1
```

---

## 3. Seeding & Credential Behavior (`DatabaseSeeder`)

[`database/seeders/DatabaseSeeder.php`](../database/seeders/DatabaseSeeder.php) reads:

- `INITIAL_ADMIN_EMAIL` (default: `admin@xivig.com`)
- `INITIAL_ADMIN_NAME` (default: `Maison Administrator`)
- `INITIAL_ADMIN_PASSWORD`:
  - Uses explicit env value when provided.
  - Uses `admin123456` automatically in `local`/`testing` environments.
  - Generates a cryptographically secure 24-character password and prints it once during seeding in `production` when `INITIAL_ADMIN_PASSWORD` is empty.

[`deploy:clean --check`](../app/Console/Commands/CleanDeploymentCommand.php) explicitly verifies that `admin@xivig.com` no longer retains `admin123456`, `APP_DEBUG` is `false`, `APP_ENV` is `production`, `.gitignore` exists, and no dev SQLite DB is exposed in `public/`.
