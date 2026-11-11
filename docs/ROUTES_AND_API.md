# Routes & Public API Reference - XIVIG Maison Gazette

All routes are defined in [`routes/web.php`](../routes/web.php) and [`routes/api.php`](../routes/api.php) (mounted with `/api` prefix in [`bootstrap/app.php`](../bootstrap/app.php)).

---

## 1. Public Editorial Routes (`routes/web.php`)

| Method | URI | Route Name | Purpose |
|---|---|---|---|
| `GET` | `/` (alias `/home`) | `blog.index` | Editorial homepage with sticky, featured, and category-filtered posts |
| `GET` | `/article/{slug}` | `blog.show` | Full monograph view with reading time, TOC, breadcrumbs, gallery, comments |
| `GET` | `/category/{slug}` | `blog.category` | Category archive view |
| `GET` | `/tag/{slug}` | `blog.tag` | Tag archive view |
| `GET` | `/author/{slug}` | `blog.author` | Author dossier & published articles |
| `GET` | `/search` | `blog.search` | Full editorial search |
| `GET` | `/page/{slug}` | `page.show` | Dynamic CMS static page (`/privacy`, `/terms`, `/ethics`, `/about` redirect canonically) |
| `GET` | `/contact` | `contact` | Public contact page + arithmetic CAPTCHA (`GET /contact/captcha-refresh`, `POST /contact`) |
| `GET` | `/services`, `/services/{slug}` | `services.index`, `services.show` | Bespoke services showcase & inquiry submission (`POST /services/inquire`) |
| `POST` | `/article/{id}/unlock` | `blog.unlock` | Unlock password-protected articles (throttled `10,1`) |
| `POST` | `/article/{id}/like` | `blog.like` | Increment article like counter (throttled `likes`) |
| `POST` | `/article/{id}/comments` | `blog.comments.store` | Submit article commentary (throttled `comments` + honeypot) |
| `POST` | `/newsletter/subscribe` | `newsletter.subscribe` | Subscribe email to newsletter list |
| `GET` | `/ad/click/{ad}` | `ad.click` | Track advertisement click and redirect to target URL |
| `GET` | `/sitemap.xml`, `/sitemap`, `/sitemap.xsl` | `blog.sitemap`, `blog.sitemap.html`, `blog.sitemap.xsl` | XML & HTML sitemaps with XSL formatting |
| `GET` | `/feed.xml` (aliases `/feed`, `/rss.xml`, `/rss`), `/rss.xsl` | `blog.feed`, `blog.rss.xsl` | RSS syndication feed with media enclosures |
| `GET` | `/robots.txt` | `robots.txt` | Dynamic `robots.txt` output configured via Admin SEO Studio |

---

## 2. Read-Only REST API (`routes/api.php`)

| Method | URI | Name | Controller Action | Middleware |
|---|---|---|---|---|
| `GET` | `/api/search` | `api.search` | `BlogController@liveSearch` | `throttle:search` |
| `GET` | `/api/v1/posts` | `api.v1.posts.index` | `Api\PostApiController@index` | — |
| `GET` | `/api/v1/posts/trending` | `api.v1.posts.trending` | `Api\PostApiController@trending` | — |
| `GET` | `/api/v1/posts/{slug}` | `api.v1.posts.show` | `Api\PostApiController@show` | — |
| `GET` | `/api/v1/categories` | `api.v1.categories.index` | `Api\CategoryApiController@index` | — |
| `GET` | `/api/v1/categories/{slug}` | `api.v1.categories.show` | `Api\CategoryApiController@show` | — |
| `GET` | `/api/v1/categories/{slug}/posts` | `api.v1.categories.posts` | `Api\CategoryApiController@posts` | — |

---

## 3. Admin Authentication & Security Screens

- **Login & Logout**: `GET|POST /admin/login` (alias `GET /login`), `POST /admin/logout`
- **Two-Factor Challenge**: `GET|POST /admin/two-factor`, `POST /admin/two-factor/resend`
- **Lockscreen**: `GET /admin/lockscreen`, `POST /admin/unlock`
- **Password Recovery**: `GET|POST /admin/forgot-password`, `GET /admin/reset-password/{token}`, `POST /admin/reset-password`

---

## 4. Protected Admin Suite (`/admin/*`, Middleware: `admin.auth`)

- **Overview & Search**: `admin.dashboard` (`/admin`), `admin.dashboard.view` (`/admin/dashboard`), `admin.search` (`/admin/search`)
- **Editorial & Content**:
  - `posts` (`index`, `create`, `store`, `edit`, `update`, `destroy`), `posts/{post}/preview`, `posts/{post}/revisions` & `restore`, `posts/{post}/toggle-status`, `posts/{post}/clone`, `posts/gallery-images/{image}`
  - `categories` & `tags` (`index`, `store`, `update`, `destroy`)
  - `comments` & comment replies CRUD (bulk actions, approve, spam, soft-delete, restore, force-delete)
  - `pages` (`index`, `create`, `store`, `edit`, `update`, `destroy`), `redirects`, `not-found-logs`
- **Media, Galleries & Monetization**:
  - `media` manager (upload, soft-delete, restore, force-delete, bulk operations)
  - `galleries` (`index`, `store`, `show`, `update`, `destroy`, restore, force-delete, item cover/management)
  - `ads` (`index`, `store`, `update`, `destroy`, `toggle`) & `transactions`
- **Audience, Services & SEO**:
  - `subscribers` (CRUD, bulk actions, CSV export, raw text import, broadcast)
  - `services` & `services/inquiries` (CRUD, featured/status toggles, inquiry review)
  - `analytics`, `menus`, `widgets`, `seo`, `activity-logs` & CSV export
- **Workspace Apps & Profile**:
  - `profile` (edit profile/timezone, avatar upload/remove, password change, 2FA toggle, invalidate other sessions)
  - `chat` (salon messages), `contacts` (inquiry desk & replies), `emails` (webmail inbox/compose/trash), `calendar`, `task-manager`, `file-manager`, `image-cropper`, `errors/*` showcase
- **Administrator-Only Vault (`role:admin`)**:
  - `settings` (general & maintenance toggle, integrations), `backups` (database snapshots & downloads), `seo/robots`, `seo/regenerate-sitemap`, `roles` (RBAC matrix), `users` (full CRUD + trash/restore/duplicate/force-delete), `activity-logs/clear` & `destroy`.
