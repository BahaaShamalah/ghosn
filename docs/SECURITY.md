# Security

Security notes and phase reviews for the GHOSN Relief project.

## Phase 1 review (2025-06-24)

### Authentication

| Check | Status | Notes |
|-------|--------|-------|
| CSRF on login | ✅ Pass | `@csrf` on POST `/admin/login` |
| CSRF on logout | ✅ Pass | `@csrf` on POST `/admin/logout` |
| Password hashing | ✅ Pass | `User` model casts `password` to `hashed`; seeder relies on cast |
| Session regeneration | ✅ Pass | Regenerated after login; invalidated on logout |
| Rate limiting | ✅ Pass | `LoginRequest` limits to 5 attempts per email/IP |
| Registration disabled | ✅ Pass | No register routes or views |
| Admin dashboard protected | ✅ Pass | `auth` middleware on `/admin` routes |
| Guest-only login | ✅ Pass | `guest` middleware on login routes |

### Localization

| Check | Status | Notes |
|-------|--------|-------|
| Locale whitelist | ✅ Pass | Only `en` and `ar` accepted (middleware + route constraint + controller) |
| Invalid locale | ✅ Pass | Falls back to default or returns 404 on switch route |

### Output & input

| Check | Status | Notes |
|-------|--------|-------|
| XSS — Blade escaping | ✅ Pass | User-facing strings use `{{ }}`; auth user name/email escaped on dashboard |
| Login validation | ✅ Pass | Email and password validated via `LoginRequest` |
| Unescaped output | ✅ Pass | No `{!! !!}` in Phase 1 views |

### Routes & secrets

| Check | Status | Notes |
|-------|--------|-------|
| Route organization | ✅ Pass | Split across `public`, `auth`, `admin` |
| Secrets in repo | ✅ Pass | No `.env` committed; dev password documented as local-only |
| Default credentials | ⚠️ Note | `admin@ghosn.test` / `password` — **change before production** |

### JavaScript

| Check | Status | Notes |
|-------|--------|-------|
| Unsafe inline JS | ✅ Pass | No custom inline scripts in Phase 1 views |

### Recommendations before production

1. Replace or remove the seeded admin password; use strong unique credentials.
2. Set `APP_DEBUG=false` and `APP_ENV=production`.
3. Enforce HTTPS and secure session cookies (`SESSION_SECURE_COOKIE=true`).
4. Consider admin IP allowlisting or 2FA in a later phase.
5. Review `redirectGuestsTo` if public routes later require different guest handling.

### Phase 6 — Payments (2026-06-24)

| Check | Status | Notes |
|-------|--------|-------|
| Webhook CSRF exempt | ✅ Pass | Dedicated `routes/webhooks.php` |
| Stripe signature verification | ✅ Pass | `Stripe\Webhook::constructEvent`; invalid → 403 |
| PayPal signature verification | ✅ Pass | PayPal verify-webhook-signature API |
| Webhook idempotency | ✅ Pass | `payment_gateway_events` by gateway + event_id |
| Gateway manual confirm blocked | ✅ Pass | `Donation::canManuallyConfirm()` — bank transfer only |
| Compliant product wording | ✅ Pass | Configurable; defaults avoid “donation” on provider labels |
| Secrets in repo | ✅ Pass | Keys in `.env` / admin settings, not committed |
| Webhook payload logging | ✅ Pass | Sanitized/truncated in `PaymentGatewayEventLogger` |

### Recommendations

1. Use HTTPS in production; webhook URLs must be publicly reachable.
2. Rotate Stripe/PayPal secrets if exposed; prefer admin settings or env, not both in source control.
3. Only use “donation” wording with providers when the merchant account is approved for charitable use.

### Unchanged from Phase 2

Frontend supply chain checks from Phase 2 remain valid (Vite Tailwind, self-hosted fonts, no bundler runtime in production).

### Phase 4 — Admin & settings (2025-06-24)

| Check | Status | Notes |
|-------|--------|-------|
| Admin routes protected | ✅ Pass | `auth` middleware on all `routes/admin.php` routes |
| CSRF on settings | ✅ Pass | `@csrf` + `@method('PUT')` on settings form |
| CSRF on login/logout | ✅ Pass | Unchanged from Phase 1 |
| Settings validation | ✅ Pass | `UpdateSettingsRequest` — colors, email, URLs, locale |
| File upload restrictions | ✅ Pass | Logo/favicon: images only, 1–2 MB max, `public` disk |
| Settings key whitelist | ✅ Pass | `SettingsService::setMany` ignores unknown keys |
| JSON columns | ✅ Pass | Eloquent array casts on sections/blocks; admin read-only for now |
| Public write access | ✅ Pass | No public routes modify settings or pages |
| Landing unchanged | ✅ Pass | Frontend still uses static Blade; DB is seed-only |
| Secrets in repo | ✅ Pass | No credentials in settings seed |

### Recommendations

1. Run `php artisan storage:link` before testing logo uploads locally.
2. Phase 5: validate JSON block content schemas before enabling edits.
3. Add authorization policies if multiple admin roles are introduced later.

---

## Phase 5 review — Dynamic homepage, media & fonts (2025-06-24)

### Dynamic homepage

| Check | Status | Notes |
|-------|--------|-------|
| Builder homepage | ✅ Pass | Public `/` always renders from Pages Builder (seeded landing content on first install) |
| XSS — builder text | ✅ Pass | Dynamic text uses `e()` in Blade; `data-en`/`data-ar` spans escaped |
| XSS — JSON blocks | ✅ Pass | Block content rendered via escaped Blade, not `{!! !!}` |
| Settings cache | ✅ Pass | `SettingsService::clearCache()` on settings save |
| Content cache | ✅ Pass | Landing content loaded fresh from DB (no remember cache) |
| Inactive sections | ✅ Pass | Filtered in `PageBuilderService::findHomePage()` |

### Media uploads

| Check | Status | Notes |
|-------|--------|-------|
| Auth required | ✅ Pass | All media routes behind `auth` middleware |
| CSRF | ✅ Pass | Upload/delete forms use `@csrf` |
| Extension whitelist | ✅ Pass | `config/media.php` allowed extensions |
| MIME validation | ✅ Pass | `StoreMediaRequest` + `MediaService::assertAllowed()` |
| File size limit | ✅ Pass | Configurable `MEDIA_MAX_UPLOAD_KB` (default 10 MB) |
| SVG handling | ⚠️ Note | SVG allowed for logo; can embed scripts — restrict to trusted admins only |
| Public storage | ✅ Pass | Files in `storage/app/public/media`; served via symlink only |
| Delete protection | ✅ Pass | Blocks delete when referenced in settings or page blocks |
| Path traversal | ✅ Pass | Laravel `storeAs()` with UUID filenames |

### Settings & builder

| Check | Status | Notes |
|-------|--------|-------|
| Media ID validation | ✅ Pass | `exists:media,id` on settings and block forms |
| Settings whitelist | ✅ Pass | Unknown keys ignored in `SettingsService::setMany()` |
| Block content validation | ✅ Pass | Max length on EN/AR text fields |

### Recommendations

1. Consider disabling SVG uploads in production or sanitizing SVG files.
2. Add virus scanning for uploads if accepting files from non-admin sources later.
3. Enable HTTPS and `SESSION_SECURE_COOKIE` in production.
4. Review `MEDIA_MAX_UPLOAD_KB` for server `upload_max_filesize` / `post_max_size` alignment.

---

| Check | Status | Notes |
|-------|--------|-------|
| XSS — static landing content | ✅ Pass | Section markup is static; logo URL from `LandingAsset` helper |
| XSS — user input | ✅ Pass | No user-submitted content on landing |
| External links | ✅ Pass | Social links retain `rel="noopener"` |
| Inline scripts | ✅ Pass | Behavior in Vite `landing.js`; no inline JS in Blade |
| CDN dependencies | ✅ Pass | No CDN Tailwind or external scripts on landing |
| CSRF meta | ✅ Pass | Included in landing layout for future forms |

### Recommendations

1. Phase 5: replace `data-en` / `data-ar` duplicates with Laravel translations where appropriate.
2. Phase 4/5: sync client lang toggle with `/locale/{locale}` session when toggling language.
3. Re-run `php scripts/split-landing-blade.php` after reference HTML updates (then review diffs).

---

## Phase 2 review (2025-06-24)

### Frontend supply chain

| Check | Status | Notes |
|-------|--------|-------|
| CDN Tailwind removed | ✅ Pass | Tailwind 4 compiled via Vite; no `cdn.tailwindcss.com` in app |
| Self-hosted fonts | ✅ Pass | WOFF2 served from `public/assets/landing/fonts/` |
| Bundler runtime in production | ✅ Pass | `bundler-runtime.js` extracted for reference only; not enqueued |
| Vite manifest | ✅ Pass | Assets built with `npm run build` |

### Asset extraction

| Check | Status | Notes |
|-------|--------|-------|
| Source integrity | ✅ Pass | Extraction reads local `GHOSN-Relief-Landing.html` only |
| Binary decode | ✅ Pass | Base64 + optional gzip handled in Artisan command |
| Path traversal | ✅ Pass | Output paths mapped to known keys or `{folder}/{uuid}.{ext}` under `assets/landing/` |

### Unchanged from Phase 1

Authentication, localization whitelist, CSRF, and admin route protection remain as documented in Phase 1.

### Recommendations

1. Commit extracted assets or run `landing:extract-assets` during deploy.
2. Do not load `bundler-runtime.js` in Blade layouts — Phase 4 will use `resources/js/public/landing.js`.
3. Re-run extraction after any update to `GHOSN-Relief-Landing.html`.

---

## Payment credentials (2026-06-24)

| Check | Status | Notes |
|-------|--------|-------|
| Secrets in `.env` only | ✅ Pass | `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`, `PAYPAL_CLIENT_*`, `PAYPAL_WEBHOOK_ID` |
| No secrets in database | ✅ Pass | Legacy `payments.*` secret keys removed; migration purges DB copies |
| No secrets in admin HTML | ✅ Pass | Payments settings show **Configured** / **Missing .env keys** only |
| Gateway UI gating | ✅ Pass | Stripe/PayPal checkout options require admin enable + env credentials |
| Donate form availability | ✅ Pass | `/donate` renders when donations are enabled; bank transfer works independently |

### Recommendations

1. Never commit `.env` or paste live secrets into admin forms, tickets, or chat.
2. Rotate Stripe/PayPal credentials immediately if exposed.
3. Use HTTPS in production; webhook endpoints must be publicly reachable.

---

## Reporting

Report security concerns to the project maintainers through your agreed internal channel.
