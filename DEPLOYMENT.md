# Maison XIVIG Gazette &bull; Production Deployment Guide

This document outlines the architecture, database setup, and operational runbook for deploying the **XIVIG Maison Gazette** platform to production environments.

---

## 1. System Requirements

* **Operating System**: Ubuntu 22.04 LTS / Debian 12 / Rocky Linux 9
* **PHP**: 8.2 or 8.3 with extensions:
  * `pdo`, `pdo_mysql` (or `pdo_pgsql`), `mbstring`, `openssl`, `xml`, `ctype`, `json`, `curl`, `bcmath`, `fileinfo`, `intl`, `gd` / `imagick`, `zip`
* **Database**: MySQL 8.0+ or MariaDB 10.5+ (InnoDB engine, `utf8mb4_unicode_ci`)
* **Web Server**: Nginx (recommended) or Apache 2.4+
* **Process Manager / Cache**: Redis 7+ (optional for session/cache acceleration)

---

## 2. Production Database Setup (MySQL 8.0+)

### Create Database & Dedicated User
Connect to your MySQL server as `root` and provision a dedicated, least-privilege user:

```sql
CREATE DATABASE xivig_gazette_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'xivig_db_user'@'localhost' IDENTIFIED BY 'StrongRandomPassword!2027#';
GRANT ALL PRIVILEGES ON xivig_gazette_prod.* TO 'xivig_db_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Configure `.env`
Copy `.env.example` to `.env` on your production server:

```bash
cp .env.example .env
```

Set the database connection variables:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gazette.xivig.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=xivig_gazette_prod
DB_USERNAME=xivig_db_user
DB_PASSWORD=StrongRandomPassword!2027#

# Initial Admin credentials (or omit to auto-generate a secure random 24-char password)
INITIAL_ADMIN_EMAIL=admin@xivig.com
INITIAL_ADMIN_NAME="Maison Administrator"
INITIAL_ADMIN_PASSWORD=YourVaultGeneratedAdminPass!2027
```

---

## 3. Application Deployment Runbook

### Step A: Install Dependencies & Key Generation
```bash
# Generate encryption key (if not already set)
php artisan key:generate --force

# Run database migrations
php artisan migrate --force

# Seed base editorial taxonomies & initial admin
php artisan db:seed --force
```

### Step B: Provision Staff Administrators (CLI)
Staff accounts are never provisioned via public registration. Provision administrators via the secure CLI command:

```bash
# Provision with an interactive prompt or random 24-character password:
php artisan admin:provision --random-password --email=chief.editor@xivig.com

# Or specify credentials explicitly:
php artisan admin:provision --name="Éléonore de Saint-Germain" --email=eleonore@xivig.com --password="YourSecurePassword!#" --role=editor
```

### Step C: Storage Symlink & Permission Hardening
Ensure the public storage symlink points to `storage/app/public`:

```bash
php artisan storage:link

# Set proper ownership and permissions (Ubuntu www-data)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Step D: Production Optimization Caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step E: Deployment Hygiene Audit
Verify production readiness with the built-in audit suite:

```bash
php artisan deploy:clean --check
```

---

## 4. Web Server Configuration

### Recommended Nginx Virtual Host Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name gazette.xivig.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name gazette.xivig.com;

    # Document root MUST point to /public
    root /var/www/xivig-laravel-blog/public;
    index index.php index.html;

    # SSL Certificates (Let's Encrypt / Certbot)
    ssl_certificate /etc/letsencrypt/live/gazette.xivig.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/gazette.xivig.com/privkey.pem;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=()";

    # Gzip Compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Block direct download of sensitive file types
    location ~* \.(sqlite|sqlite3|db|sql|bak|backup|log|env|ini|conf|ya?ml|sh|bat)$ {
        deny all;
        return 404;
    }

    # Hide dotfiles (e.g. .git, .env)
    location ~ /\.(?!well-known) {
        deny all;
    }

    # Static Assets Caching
    location ~* \.(jpg|jpeg|png|webp|avif|gif|svg|ico|css|js|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
        try_files $uri =404;
    }

    # Front Controller
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM FastCGI
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    client_max_body_size 20M;
}
```

---

## 5. Background Cron & Automation Scheduler

To ensure scheduled chronicle publishing, log rotation, and automated backups, configure the standard Linux cron job:

```bash
crontab -e -u www-data
```

Add the following single entry:

```cron
* * * * * cd /var/www/xivig-laravel-blog && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks include:
- `blog:publish-scheduled`: Every minute (publishes embargoed chronicles when scheduled time arrives).
- `sitemap:generate`: Daily (regenerates cached XML sitemap).
- `blog:seo-audit`: Daily (audits published posts for metadata and SEO completeness).
- `blog:backup`: Daily (creates automated database snapshots in `storage/app/backups/`).
- `blog:clear-logs --days=30`: Weekly (rotates and trims activity & visitor telemetry logs).

---

## 6. Creating Clean Release Archives

Before transferring code to a production server or publishing distribution zips, execute:

```bash
php artisan deploy:clean --package
```

This generates a release archive in `dist/` that strips:
- SQLite development databases
- Development backup snapshots
- Active user session records
- Development application logs (`laravel.log`)
- `.env` secrets
