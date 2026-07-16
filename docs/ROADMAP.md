# GHOSN Relief — Roadmap

## Phase 1 — Foundation & authentication ✅

- [x] Modular routes (`public`, `auth`, `admin`)
- [x] Controller and view folder skeleton
- [x] Localization foundation (`en` / `ar`, SetLocale middleware, RTL/LTR)
- [x] Public home placeholder
- [x] Admin login, session auth, protected dashboard
- [x] AdminUserSeeder (local dev credentials)
- [x] Project documentation

## Phase 2 — Assets & Vite/Tailwind ✅

- [x] Extract fonts, images, and media from `GHOSN-Relief-Landing.html` bundle
- [x] Port Tailwind theme (GHOSN colors, fonts)
- [x] Move custom CSS and animations into Vite assets
- [x] Replace CDN Tailwind with compiled assets

## Phase 3 — Landing page Blade conversion ✅

- [x] Split reference HTML into layouts, partials, and sections
- [x] Preserve visual parity and animations
- [x] Wire public home to composed sections
- [x] Port landing JavaScript interactions

## Phase 4 — Admin foundation & builder prep ✅

- [x] GHOSN-branded admin UI and login
- [x] Admin layout (sidebar, topbar)
- [x] Site Settings module (database + service)
- [x] Page builder tables (`pages`, `page_sections`, `page_section_blocks`)
- [x] Landing content seeders (homepage sections/blocks)
- [x] Pages Builder placeholder UI
- [x] Campaigns / Donations / Media placeholders

## Phase 5 — Dynamic rendering & builder editing ✅

- [x] Section edit forms in admin (section meta + block content EN/AR)
- [x] Reorder sections and blocks (up/down)
- [x] Homepage always renders from Pages Builder (no static/dynamic toggle)
- [x] Builder edits render on `/` immediately (same Blade sections + DB content)
- [x] Site Settings card UI with per-group save + validation fix
- [x] Laravel translation files (`lang/en/landing.php`, `lang/ar/landing.php`)
- [x] Locale toggle sync with session
- [x] Media library (upload, grid, delete, thumbnails)
- [x] Logo/favicon from media library
- [x] Builder media assignment (image/video blocks)
- [x] Cairo / Montserrat font settings + CSS variables

## Phase 6 — Donations & payments (in progress)

- [x] Public `/donate` checkout (bank transfer, Stripe card, PayPal when enabled)
- [x] Payment gateway layer (`StripeGateway`, `PayPalBusinessGateway`, webhooks)
- [x] Admin Settings → Payments (gateway credentials, compliant product wording)
- [x] Admin Donations list (gateway, status, manual bank confirm only)
- [ ] Campaigns module
- [ ] Contact forms
- [ ] Drag-and-drop builder

---

**Current status:** Phase 6 payment gateway backend complete — campaigns and advanced donation features planned next.
