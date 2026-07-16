# Changelog

All notable changes to the GHOSN Relief project are documented here.

## [Unreleased]

### Settings hub refactor (2026-06-24)

#### Security

- Payment API credentials are **`.env` only** — removed from admin UI and database (`STRIPE_*`, `PAYPAL_*` secrets)

#### Changed

- **Admin Settings** is now a dashboard hub at `/admin/settings` with icon cards for each category
- Each group has its own page: `/admin/settings/general`, `/branding`, `/colors`, etc.
- Per-group save redirects back to the same group page (setting keys unchanged)

### Payment gateways — Stripe & PayPal Business (2026-06-24)

#### Added

- Payment gateway architecture under `app/Services/Payments/` (Stripe Checkout, PayPal Orders API)
- Payment methods: `bank_transfer`, `stripe_card`, `paypal_business`
- Webhook routes: `POST /webhooks/stripe`, `POST /webhooks/paypal` with signature verification and idempotency
- Admin **Settings → Payments** (gateway enable/disable, credentials, currency limits, compliant checkout wording)
- Admin Donations list: gateway, transaction ID, status; manual confirm for bank transfer only
- `payment_gateway_events` table and donation gateway fields migration
- PayPal option on `/donate` when enabled (no page redesign)
- Documentation: [PAYMENTS.md](PAYMENTS.md)
- Tests: `tests/Feature/Payments/PaymentGatewayTest.php`

#### Compliance

- Provider-facing labels default to neutral wording (“GHOSN Relief Support Contribution”); configurable in admin settings.

### Site Settings & builder architecture (2026-06-24)

#### Changed

- **Homepage always renders from Pages Builder** — removed `site.use_dynamic_homepage` toggle and static fallback
- Site Settings redesigned into **premium cards** (General, Branding, Typography, Colors, Homepage, Social, Contact, Advanced) with per-card Save
- Settings validation fixed: bracket notation inputs flattened to dot keys before validation (fixes false "required" errors)
- `HeroContent` always merges builder settings/blocks with defaults on the public site

#### Removed

- Dynamic homepage toggle, admin notices, and related tests/docs
- `public/home/dynamic.blade.php`, `UpdateSettingsRequest` (monolithic form)

### Phase 5 — Dynamic homepage fix, media library & fonts (2025-06-24)

#### Fixed

- Dynamic homepage now renders **full landing Blade sections** with **live DB block content** (`data-en`/`data-ar` compatible with landing JS)
- Builder edits appear on `/` immediately when dynamic mode is enabled (fresh DB queries, no content cache)
- Settings cache cleared on save; admin banner shows dynamic homepage **enabled/disabled**

#### Added

**Media library**

- `media` table, `Media` model, `MediaService`, `config/media.php`
- Admin upload/grid/delete, copy URL, type filter, search
- Thumbnails for raster images (GD)
- Delete protection when file is used in settings or page blocks
- Routes in `routes/admin-media.php`

**Settings integration**

- `site.logo_media_id`, `site.favicon_media_id`
- Logo/favicon picker from media library; `SiteAsset` helper for public rendering

**Builder integration**

- Media selector on section edit forms (image/video blocks)
- `LandingBlockHelper`, `<x-landing.text>`, `<x-landing.media>` components

**Typography**

- `--ghosn-font-en` / `--ghosn-font-ar` CSS variables from Site Settings
- Cairo (AR) and Montserrat (EN) via self-hosted WOFF2

**Tests**

- `DynamicHomepageTest`, `MediaLibraryTest`; updated `HomePageTest`

### Phase 5 — Dynamic rendering & builder editing (2025-06-24)

#### Added

**Services**

- `App\Services\Pages\PageBuilderService` — section/block reordering, homepage lookup
- `App\Services\Pages\LandingContentRepository` — DB content for dynamic homepage

**Admin controllers**

- `Admin\Pages\PageSectionController` — edit, update, reorder sections
- `Admin\Pages\PageSectionBlockController` — update, reorder blocks

**Form requests**

- `Admin\Pages\UpdatePageSectionRequest`, `UpdatePageSectionBlockRequest`, `ReorderRequest`

**Settings**

- `site.use_dynamic_homepage` (boolean, default `false`) — optional DB-driven homepage preview

**Views**

- `admin/pages/sections/edit.blade.php` — section and block editing
- `public/home/dynamic.blade.php`, `public/sections/dynamic/section.blade.php`

**Translations**

- `lang/en/landing.php`, `lang/ar/landing.php` — landing string foundation

**Public**

- `HomeController` chooses static Blade or dynamic DB view based on setting
- `LocaleController` returns JSON for AJAX locale sync
- Landing JS syncs language toggle with Laravel session

**Tests**

- `PageBuilderTest`, `LocaleSyncTest`; expanded `HomePageTest`

#### Notes

- Static Blade landing remains the default public experience.
- Enable **Use dynamic homepage** in Site Settings to preview builder content on `/`.

### Phase 4 — Admin foundation & builder prep (2025-06-24)

#### Added

**Database**

- `settings` table, `Setting` model
- `pages`, `page_sections`, `page_section_blocks` tables and models
- `database/data/homepage.php` — seeded landing structure

**Services**

- `App\Services\Settings\SettingsService`

**Admin controllers**

- `Admin\Settings\SettingsController`
- `Admin\Pages\PageController`
- `Admin\Media\MediaController`
- `Admin\Campaigns\CampaignController`
- `Admin\Donations\DonationController`

**Form requests**

- `Admin\Settings\UpdateSettingsRequest`

**Seeders**

- `SettingsSeeder`, `LandingPageSeeder`

**Views**

- `admin/layouts/app.blade.php`, `admin/partials/sidebar.blade.php`, `admin/partials/topbar.blade.php`
- `admin/dashboard`, `admin/settings`, `admin/pages`, `admin/media`, `admin/campaigns`, `admin/donations`
- `auth/layouts/ghosn.blade.php` — branded login

**Config**

- `config/settings.php`

**Tests**

- `tests/Feature/Admin/AdminAccessTest.php`
- `tests/Feature/Admin/SettingsUpdateTest.php`

#### Changed

- Admin login and dashboard redesigned with GHOSN identity
- `routes/admin.php` — settings, pages, media, campaigns, donations routes
- `DatabaseSeeder` — runs settings and landing page seeders
- `AppServiceProvider` — registers `SettingsService`, admin nav composer

#### Unchanged (by design)

- Public landing still renders static Blade sections
- No dynamic frontend rendering from DB yet

### Phase 3 — Landing page Blade conversion (2025-06-24)

#### Added

**Views — layout & partials**

- `resources/views/public/layouts/app.blade.php`
- `resources/views/public/partials/header.blade.php`
- `resources/views/public/partials/footer.blade.php`

**Views — sections**

- `resources/views/public/sections/hero.blade.php` (includes trust strip)
- `resources/views/public/sections/about.blade.php`
- `resources/views/public/sections/vision.blade.php`
- `resources/views/public/sections/work.blade.php`
- `resources/views/public/sections/values.blade.php`
- `resources/views/public/sections/goals.blade.php`
- `resources/views/public/sections/groups.blade.php`
- `resources/views/public/sections/support.blade.php`

**Scripts**

- `scripts/split-landing-blade.php` — regenerates Blade sections from reference HTML

**Tests**

- `tests/Feature/Public/HomePageTest.php`

#### Changed

- `resources/views/public/home/index.blade.php` — composes landing sections
- `resources/js/public/landing.js` — full interactions (lang toggle, mobile menu, parallax, scroll reveal)
- `lang/en/public.php`, `lang/ar/public.php` — home page title strings

#### Removed

- Phase 1 placeholder content from public home view

### Phase 2 — Assets & Vite/Tailwind (2025-06-24)

#### Added

**Artisan**

- `app/Console/Commands/ExtractLandingAssetsCommand.php` — `landing:extract-assets`

**Configuration**

- `config/landing.php` — source path and asset key map

**Support**

- `app/Support/LandingAsset.php` — named asset URL helper

**Public assets** (extracted from bundle)

- `public/assets/landing/` — fonts, logo, manifest

**Styles**

- `resources/css/ghosn/fonts.css` — self-hosted Cairo & Montserrat `@font-face`
- `resources/css/ghosn/landing.css` — animations and `#ghosn-root` component styles

**Scripts**

- `scripts/generate-landing-fonts-css.php`
- `scripts/generate-landing-styles-css.php`
- `resources/js/public/landing.js` — stub for Phase 4 interactions

**Documentation**

- `docs/ASSETS.md`

#### Changed

- `resources/css/app.css` — GHOSN `@theme` tokens, imports ghosn CSS layers
- `resources/js/app.js` — imports landing JS stub
- `vite.config.js` — removed CDN/bunny Instrument Sans; Tailwind 4 only
- `composer.json` — `setup` script runs `landing:extract-assets`

#### Removed

- Instrument Sans font dependency from Vite config

#### Not included (deferred)

- Landing page Blade conversion (Phase 3)
- Full landing JavaScript behavior (Phase 4)

### Phase 1 — Foundation & authentication (2025-06-24)

#### Added

**Configuration**

- `config/locale.php` — supported locales (`en`, `ar`) and RTL list

**Middleware**

- `app/Http/Middleware/SetLocale.php` — session-based locale with whitelist

**Support**

- `app/Support/LocaleHelper.php` — direction and locale validation helpers

**Controllers**

- `app/Http/Controllers/Public/HomeController.php`
- `app/Http/Controllers/Public/LocaleController.php`
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Http/Controllers/Admin/DashboardController.php`

**Form requests**

- `app/Http/Requests/Auth/LoginRequest.php` — validation, rate limiting, authentication

**Routes**

- `routes/public.php` — home, locale switch
- `routes/auth.php` — admin login, logout
- `routes/admin.php` — protected dashboard
- Updated `routes/web.php` to load modular route files
- Updated `bootstrap/app.php` — SetLocale middleware, auth redirects

**Views**

- `resources/views/shared/layouts/base.blade.php`
- `resources/views/shared/layouts/public.blade.php`
- `resources/views/shared/layouts/guest.blade.php`
- `resources/views/shared/layouts/admin.blade.php`
- `resources/views/public/home/index.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/admin/dashboard/index.blade.php`

**Translations**

- `lang/en/public.php`, `lang/ar/public.php`
- `lang/en/admin.php`, `lang/ar/admin.php`
- `lang/en/auth.php`, `lang/ar/auth.php`

**Database**

- `database/seeders/AdminUserSeeder.php`
- Updated `database/seeders/DatabaseSeeder.php`

**Documentation**

- `docs/README.md`
- `docs/ROADMAP.md`
- `docs/CHANGELOG.md`
- `docs/SECURITY.md`

#### Changed

- `resources/css/app.css` — scan Blade views for Tailwind classes
- `.env.example` — default `APP_NAME` set to `GHOSN Relief`

#### Removed

- `resources/views/welcome.blade.php` — replaced by public home module

#### Not included (deferred)

- Landing page HTML conversion
- Asset extraction from bundle
- Registration / public signup
- Campaigns, donations, contact forms, CMS
