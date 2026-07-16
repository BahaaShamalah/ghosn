# Landing Assets

How GHOSN Relief extracts and serves design assets from `GHOSN-Relief-Landing.html`.

## Source file

`GHOSN-Relief-Landing.html` (project root) is a bundled export. The real page markup lives inside embedded JSON; binary assets (fonts, logo) live in a base64 manifest.

**Do not redesign or replace this file.** Phase 3 converts its markup to Blade; this document covers Phase 2 asset extraction only.

## Extraction command

```bash
php artisan landing:extract-assets
```

Options:

| Option | Description |
|--------|-------------|
| `--force` | Overwrite existing files in `public/assets/landing/` |
| `--source=` | Custom path to bundled HTML (default: project root file) |

The command:

1. Decodes the `__bundler/manifest` payload
2. Writes files under `public/assets/landing/`
3. Updates `public/assets/landing/manifest.json` (UUID → path map)
4. Regenerates `resources/css/ghosn/fonts.css` and `resources/css/ghosn/landing.css`

## Extracted layout

```
public/assets/landing/
├── manifest.json
├── fonts/
│   ├── cairo-arabic.woff2
│   ├── cairo-latin-ext.woff2
│   ├── cairo-latin.woff2
│   ├── montserrat-cyrillic-ext.woff2
│   ├── montserrat-cyrillic.woff2
│   ├── montserrat-vietnamese.woff2
│   ├── montserrat-latin-ext.woff2
│   └── montserrat-latin.woff2
├── images/
│   └── logo.webp
└── scripts/
    └── bundler-runtime.js   # reference only — not loaded in production
```

## Vite / CSS pipeline

| File | Purpose |
|------|---------|
| `resources/css/app.css` | Tailwind 4 entry, `@theme` GHOSN palette |
| `resources/css/ghosn/fonts.css` | `@font-face` rules (self-hosted WOFF2) |
| `resources/css/ghosn/landing.css` | Animations, `#ghosn-root` helpers, nav/reveal styles |

Compiled via `npm run build` or `npm run dev`. **CDN Tailwind is not used.**

## PHP helpers

```php
use App\Support\LandingAsset;

LandingAsset::url('logo'); // /assets/landing/images/logo.webp
```

Asset keys are defined in `config/landing.php`.

## Stylesheet generators

If you need to regenerate CSS without re-extracting binaries:

```bash
php scripts/generate-landing-fonts-css.php
php scripts/generate-landing-styles-css.php
```

Both scripts read `storage/app/_extracted_landing.html` (created automatically by `landing:extract-assets` if missing).

## When to re-run extraction

- After replacing `GHOSN-Relief-Landing.html` with an updated design export
- After cloning the repo if extracted assets are not committed (they should be committed for deploy simplicity)

## Phase 3 note

Blade sections will reference:

- Vite-compiled CSS/JS (not CDN Tailwind)
- `LandingAsset::url('logo')` instead of bundle UUIDs
- Tailwind utilities from `@theme` (e.g. `bg-ghosn`, `text-growth-light`, `tracking-tightish`)

## Regenerating Blade sections

If `GHOSN-Relief-Landing.html` changes:

```bash
php artisan landing:extract-assets --force   # if assets changed
php scripts/split-landing-blade.php            # re-split Blade sections
npm run build
```

Review generated diffs in `resources/views/public/` before committing.

## Typography (Phase 5)

| Role | Font | Source |
|------|------|--------|
| English (LTR) | Montserrat | Self-hosted WOFF2 in `public/assets/landing/fonts/` |
| Arabic (RTL) | Cairo | Self-hosted WOFF2 in `public/assets/landing/fonts/` |

CSS variables (from Site Settings):

- `--ghosn-font-en` — default Montserrat
- `--ghosn-font-ar` — default Cairo

Applied in `resources/css/ghosn/landing.css` on `#ghosn-root` and via `partials/theme-fonts.blade.php` on admin layouts.

**Not using Google Fonts CDN.** To use Google Fonts instead, add link tags to layouts and update `theme.font_en` / `theme.font_ar` in settings.

## Media uploads (Phase 5)

| Setting | Value |
|---------|-------|
| Disk | `public` |
| Path | `storage/app/public/media/` |
| Public URL | `/storage/media/...` (requires `php artisan storage:link`) |
| Thumbnails | `storage/app/public/media/thumbnails/` (GD, raster only) |
| Max size | `MEDIA_MAX_UPLOAD_KB` env (default 10240) |

Allowed MIME types: `config/media.php`

Logo/favicon can reference media library IDs via Site Settings (`site.logo_media_id`, `site.favicon_media_id`) or legacy path upload.
