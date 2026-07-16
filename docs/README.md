# GHOSN Relief Team

Production-ready website for **GHOSN Relief Team** — relief, development, and community support.

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13 (as installed), PHP 8.3+ |
| Views | Blade |
| Frontend | Tailwind CSS 4, Vite, Alpine.js (planned), Vanilla JS |
| i18n | English (default), Arabic (RTL) |

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- SQLite (default) or MySQL

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
php artisan landing:extract-assets
npm run build
php artisan serve
```

Visit:

| URL | Purpose |
|-----|---------|
| `/` | Public landing page (GHOSN Relief) |
| `/admin/login` | Admin login |
| `/admin` | Admin dashboard (authenticated) |
| `/admin/settings` | Site settings |
| `/admin/pages` | Pages builder |
| `/admin/pages/{page}/sections/{section}/edit` | Edit section & blocks |
| `/locale/en` or `/locale/ar` | Switch locale |

## Development admin credentials

> **Local development only.** Change or remove before any shared or production deployment.

| Field | Value |
|-------|-------|
| Email | `admin@ghosn.test` |
| Password | `password` |

Seed with:

```bash
php artisan db:seed --class=AdminUserSeeder
```

## Project structure

```
app/Http/Controllers/
├── Admin/
│   ├── Settings/
│   ├── Pages/
│   ├── Media/
│   ├── Campaigns/
│   └── Donations/
├── Auth/
└── Public/

app/Services/
├── Settings/SettingsService.php
└── Pages/PageBuilderService.php, LandingContentRepository.php
app/Models/Setting.php, Page.php, PageSection.php, PageSectionBlock.php

routes/public.php, auth.php, admin.php

resources/views/admin/     # GHOSN-branded admin UI
resources/views/public/    # Static landing (source of truth on frontend)
resources/views/auth/

database/data/homepage.php # Seeded landing section structure
database/seeders/          # AdminUser, Settings, LandingPage
```

## Admin modules (Phase 5)

- **Site Settings** — bilingual site name, theme colors, fonts, logo/favicon upload, contact & social links, optional dynamic homepage toggle
- **Pages Builder** — edit section titles and block content (EN/AR), reorder sections and blocks; changes apply on the public site only when dynamic homepage is enabled
- **Media / Campaigns / Donations** — route + view placeholders for future phases

The **public homepage renders static Blade sections by default**. Enable **Use dynamic homepage** in Site Settings to preview database content on `/`.

## Design reference

The landing page design source of truth is `GHOSN-Relief-Landing.html` in the project root, converted to Blade in Phase 3. Do not redesign it.

## Documentation

- [ROADMAP.md](./ROADMAP.md) — phased delivery plan
- [ASSETS.md](./ASSETS.md) — landing asset extraction & Vite CSS pipeline
- [CHANGELOG.md](./CHANGELOG.md) — change history
- [SECURITY.md](./SECURITY.md) — security notes and reviews

## Dev workflow

```bash
composer dev
```

Runs the app server, queue listener, logs, and Vite dev server concurrently.
