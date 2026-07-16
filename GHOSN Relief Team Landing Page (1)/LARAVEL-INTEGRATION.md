# GHOSN Relief Team — Laravel + React Integration Guide

This package contains the complete **GHOSN** website and admin panel as design/UI
source. Every page was authored to map 1:1 onto a React component for the Laravel
app ("Sara"). This guide explains the structure, the component map, and every API
endpoint the UI already expects.

> Stack target: **Laravel (API) + React (Vite) + Tailwind CSS**, Laravel **Sanctum**
> for auth. All colors/fonts follow the GHOSN brand (see "Design tokens" below).

---

## 1. What's in this package

| File | Purpose |
|------|---------|
| `GHOSN Landing.dc.html` | Public landing page (hero, about, impact, how-it-works, campaigns, ways-to-help, testimonials, blog, newsletter, join) |
| `About Us.dc.html` | About / Who We Are |
| `Our Team.dc.html` | Leadership, departments, culture, team |
| `Volunteer.dc.html` | Become a volunteer + application form |
| `Campaign Detail.dc.html` | Single campaign (gallery, donation panel, story, updates, donors, related) |
| `News Post.dc.html` | Single news/update article (inline donate CTA, share, related, newsletter) |
| `Official Page.dc.html` | Legal/official pages (Donation, Privacy, Terms, Refund) |
| `Contact.dc.html` | Contact form + info + donate CTA |
| `Donate.dc.html` | Donation flow (amount, fund, method, summary, thank-you) |
| `Admin Login.dc.html` | Auth: login / signed-in / signed-out |
| `Admin Dashboard.dc.html` | KPIs, charts, campaigns/donations/volunteers/messages |
| `Admin Editor.dc.html` | Create campaign / news / page |
| `assets/` | Brand logo marks (color + white monochrome) |
| `image-slot.js`, `support.js` | Runtime for the design preview (NOT needed in the React app) |

> **Note:** `.dc.html` files are self-contained design documents (open any in a browser
> to view). In the React app you re-implement each as a component using the markup +
> the isolated data objects already defined at the top of each file's logic. The
> `support.js` / `image-slot.js` runtime and the `<x-dc>`/`{{ }}` template syntax are
> preview-only — do not ship them.

---

## 2. Recommended React project structure (Sara frontend)

```
resources/js/
  main.jsx
  App.jsx                     # React Router routes
  lib/
    api.js                    # axios instance (see §5)
    i18n.js                   # EN/AR content + dir switching
  components/
    Header.jsx  Footer.jsx  LanguageSwitcher.jsx
    RevealOnScroll.jsx        # the scroll-reveal helper (see §6)
  pages/
    Landing.jsx  About.jsx  Team.jsx  Volunteer.jsx
    CampaignDetail.jsx  NewsPost.jsx  OfficialPage.jsx
    Contact.jsx  Donate.jsx
  admin/
    AuthPage.jsx  Dashboard.jsx  Editor.jsx
    AdminLayout.jsx
```

Each page's `CONTENT = { en: {...}, ar: {...} }` object (already in every `.dc.html`)
moves into `i18n.js` or a per-component `content.js`. The language-agnostic data
arrays (`CAMPAIGNS`, `POSTS_META`, `STATS`, etc.) become the shape your API returns.

---

## 3. Routes

### Public (React Router)
```
/                      -> Landing
/about                 -> About
/team                  -> Team
/volunteer             -> Volunteer
/campaigns/:slug       -> CampaignDetail
/news/:slug            -> NewsPost
/pages/:slug           -> OfficialPage   (donation-policy | privacy | terms | refund)
/contact               -> Contact
/donate                -> Donate
```

### Admin (guarded by auth + role:admin)
```
/admin/login           -> AuthPage
/admin                 -> Dashboard
/admin/create/campaign -> Editor (campaign)
/admin/create/post     -> Editor (post)
/admin/create/page     -> Editor (page)
```

---

## 4. API endpoints the UI already expects

These exact endpoints are referenced in `// TODO(Sara)` comments throughout the code.
Define them in `routes/api.php`.

### Public
| Method | Endpoint | Used by |
|--------|----------|---------|
| GET  | `/api/impact` | Landing, About (stat counters) |
| GET  | `/api/campaigns` | Landing, Campaigns grid |
| GET  | `/api/campaigns/{slug}` | Campaign Detail |
| POST | `/api/campaigns/{id}/donations` | Campaign Detail donate |
| POST | `/api/donations` | Donate page `{ amount, frequency, fund, donor, method }` |
| GET  | `/api/posts` | Landing blog, News list |
| GET  | `/api/posts/{slug}` | News Post |
| GET  | `/api/pages/{slug}` | Official Page |
| POST | `/api/volunteers` | Volunteer + landing Join form |
| POST | `/api/contact` | Contact form |
| POST | `/api/subscribers` | Newsletter (landing + news post) |

### Auth (Sanctum)
| Method | Endpoint | Notes |
|--------|----------|-------|
| GET  | `/sanctum/csrf-cookie` | call before login |
| POST | `/api/auth/login` | `{ email, password, remember }` |
| POST | `/api/auth/logout` | |
| GET  | `/api/auth/user` | current admin |

### Admin (auth:sanctum + role:admin)
| Method | Endpoint |
|--------|----------|
| GET  | `/api/admin/overview` |
| GET  | `/api/admin/campaigns` · POST `/api/admin/campaigns` |
| GET  | `/api/admin/donations` |
| GET  | `/api/admin/volunteers` · PATCH `/api/admin/volunteers/{id}` (approve/reject) |
| GET  | `/api/admin/contact-messages` |
| POST | `/api/admin/posts` |
| POST | `/api/admin/pages` |
| POST | `/api/admin/media` (multipart image upload; returns URL for image slots) |

---

## 5. Suggested Laravel resources (models / migrations)

```
Campaign            (title, slug, description, category, status, goal, raised, cover, deadline)
Donation            (campaign_id?, donor_name, email, amount, frequency, fund, method, status)
Post                (title, slug, category, author, excerpt, body, cover, published_at)
Page                (title, slug, subtitle, sections json, updated_at)
VolunteerApplication(name, age, phone, email, area, availability json, message, status)
ContactMessage      (name, email, subject, message, read_at)
Subscriber          (email)
User                (…, role)   // role: 'admin'
```

Enum hints from the UI:
- Campaign.status: `urgent | ongoing | completed`
- Donation.frequency: `once | monthly`; method: `card | paypal | wallet`; status: `completed | pending`
- VolunteerApplication.status: `pending | approved | rejected`

### axios instance (`lib/api.js`)
```js
import axios from 'axios';
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,   // e.g. http://localhost:8000
  withCredentials: true,                    // Sanctum cookie auth
  headers: { Accept: 'application/json' },
});
export async function login(creds) {
  await api.get('/sanctum/csrf-cookie');
  return api.post('/api/auth/login', creds);
}
export default api;
```
Set `VITE_API_URL` in `.env` — the UI already references a single `API_BASE_URL`
constant everywhere instead of hardcoding.

---

## 6. Front-end notes carried in the designs

- **Bilingual EN/AR + RTL** — every page has a language switcher. On Arabic, set
  `dir="rtl"` on the root and swap the font to **Cairo**; English uses **Montserrat**.
  All layouts already use `inset-inline-*` / logical properties so they mirror cleanly.
- **Forms** — all use controlled inputs + an async `handleSubmit` with client-side
  validation and loading / success / error states. Swap the mock `setTimeout` for the
  real `api.post(...)`. Never post raw card data — use Stripe/PayPal Elements and send
  only a token (`Donate.dc.html`).
- **Scroll reveals / counters / progress bars** — implemented with an
  IntersectionObserver-style helper; reproduce as a small `RevealOnScroll` component or
  a `useInView` hook. In React you won't need the re-render workarounds present here.
- **Images** — `image-slot` placeholders mark every spot that needs a real photo
  (hero, campaign covers, team portraits, article covers). In React these become
  `<img>` fed by the API/CDN or Laravel `public/` storage. Use only licensed, dignified
  humanitarian imagery (brand guideline).

---

## 7. Design tokens (brand)

```css
--ghosn-green:   #406139;  /* primary */
--growth-green:  #819562;  /* secondary */
--beige:         #BCCAA7;  /* supporting light */
--sage:          #96A791;  /* supporting mid */
--offwhite:      #F2F1EA;  /* background (never pure white) */
--ink:           #2f4327;  /* headings */
--admin-bg:      #EDEEE4;  /* admin surface */
--sidebar:       #243619;  /* admin sidebar */
```

Tailwind: add these under `theme.extend.colors.ghosn`. Fonts: `Montserrat`
(400/500/600/700) + `Cairo` (400/500/600/700) via Google Fonts.

- Headings: Montserrat SemiBold / Cairo Bold
- Body: Montserrat Regular / Cairo Regular

---

## 8. How to view the designs now

Open any `.dc.html` file directly in a browser — each is self-contained and renders
the full page, including the EN/AR toggle. Use these as the visual spec while building
the React components.
