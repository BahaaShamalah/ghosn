# GHOSN Relief Team

Production website for **GHOSN Relief Team** — relief, development, and community support.

Full project docs: [docs/README.md](docs/README.md)

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm install
php artisan landing:extract-assets
npm run build
php artisan serve
```

| URL | Purpose |
|-----|---------|
| `/` | Public homepage |
| `/admin/login` | Admin login |
| `/admin/settings` | Settings hub (category cards) |
| `/admin/settings/{group}` | Individual settings group |
| `/admin/pages` | Pages builder |
| `/admin/media` | Media library |

**Local admin:** `admin@ghosn.test` / `password` (change before production)

## Homepage (Pages Builder)

The public homepage at `/` **always** renders from the **Pages Builder** database. Blade section templates preserve the original landing HTML, Tailwind classes, and animations — only content and media are dynamic.

1. Go to **Admin → Pages Builder** (or **Site Settings → Homepage**)
2. Edit sections — saves update `/` immediately
3. Hero video, cover, and text: **Admin → Pages → Hero section**

The static `GHOSN-Relief-Landing.html` file remains the design reference; seeded builder content matches it on first install.

## Media library

- Upload path: `storage/app/public/media/` (URL via `storage:link`)
- Allowed types: jpg, jpeg, png, webp, svg, pdf, mp4
- Max size: `MEDIA_MAX_UPLOAD_KB` in `.env` (default 10240 KB / 10 MB)
- Thumbnails generated for raster images when GD is available

Logo and favicon can be selected from the media library in Site Settings.

## Typography

- **English:** Montserrat (self-hosted WOFF2 from landing bundle)
- **Arabic:** Cairo (self-hosted WOFF2)
- Configurable in **Site Settings → Typography** (`theme.font_en`, `theme.font_ar`)
- Applied via CSS variables `--ghosn-font-en` / `--ghosn-font-ar`

Fonts are **local** (not Google Fonts CDN). To switch to Google Fonts later, update `resources/css/ghosn/fonts.css` and theme settings.

## Verification

```bash
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan test
```

## Documentation

- [ROADMAP.md](docs/ROADMAP.md)
- [CHANGELOG.md](docs/CHANGELOG.md)
- [SECURITY.md](docs/SECURITY.md)
- [PAYMENTS.md](docs/PAYMENTS.md)
- [ASSETS.md](docs/ASSETS.md)

## Payments

Stripe card and PayPal Business checkout are configured in **Admin → Settings → Payments**. Bank transfer remains manual confirmation in **Admin → Donations**.

Webhook endpoints: `POST /webhooks/stripe`, `POST /webhooks/paypal`. See [PAYMENTS.md](docs/PAYMENTS.md) for setup, env vars, and compliance wording.
