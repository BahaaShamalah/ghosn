# Payments — Stripe & PayPal Business

Backend payment gateway support for GHOSN Relief checkout (`/donate`). Provider-facing labels use **neutral, accurate wording** (e.g. “GHOSN Relief Support Contribution”) — not “donation” unless your Stripe/PayPal account is officially approved for nonprofit or charitable use.

## Compliance

> **Use accurate payment descriptions. Do not misrepresent donations or charitable payments to payment providers.**

- Stripe Checkout product name/description and PayPal order item name/description are configurable in **Admin → Settings → Payments**.
- Defaults: **GHOSN Relief Support Contribution** / **Community support payment for GHOSN Relief Team activities.**
- Do not use misleading product names or fake invoice descriptions.

## Supported methods

| Method key | Gateway | Flow |
|------------|---------|------|
| `bank_transfer` | `bank_transfer` | Pending until admin marks received |
| `stripe_card` | `stripe` | Stripe Checkout Session → webhook or return verify |
| `paypal_business` | `paypal` | PayPal JS SDK popup → server capture (`POST /donate/paypal/capture-order`) + optional webhook |

## Architecture

```
app/Services/Payments/
├── Contracts/PaymentGatewayInterface.php
├── DTOs/PaymentRequestData.php
├── DTOs/PaymentResultData.php
├── DTOs/WebhookResultData.php
├── Gateways/StripeGateway.php
├── Gateways/PayPalBusinessGateway.php
├── PaymentGatewayManager.php
└── PaymentGatewayEventLogger.php
```

- **Donations table** stores `gateway`, `payment_method`, `amount`, `currency`, `status`, `gateway_transaction_id`, `gateway_reference`, donor fields, `is_anonymous`, `metadata`, `paid_at`.
- **Statuses:** `pending`, `paid`, `failed`, `cancelled`, `refunded`
- **Idempotency:** `payment_gateway_events` table records processed webhook event IDs.

## Environment variables

Add to `.env` (see `.env.example`):

```env
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=
PAYPAL_CLIENT_SECRET=
PAYPAL_WEBHOOK_ID= # optional — async verification only
```

Admin settings (`payments.*`) control enable/disable, currency limits, and compliant product wording. **API credentials are `.env` only** — never stored in the database.

## Stripe setup

1. Create a [Stripe](https://dashboard.stripe.com) account.
2. Add `STRIPE_KEY`, `STRIPE_SECRET`, and `STRIPE_WEBHOOK_SECRET` to `.env`.
3. Enable Stripe in **Admin → Settings → Payments**.
4. Create a webhook endpoint:
   - **URL:** `https://your-domain.com/webhooks/stripe`
   - **Events:** `checkout.session.completed`
5. Copy the **Signing secret** into webhook secret (admin or `STRIPE_WEBHOOK_SECRET`).

Return URL: `/donate/success?gateway=stripe&session_id={CHECKOUT_SESSION_ID}` (handled automatically).

## PayPal Business sandbox setup

1. Create a [PayPal Developer](https://developer.paypal.com) app (sandbox).
2. Add `PAYPAL_CLIENT_ID` and `PAYPAL_CLIENT_SECRET` to `.env`.
3. Enable PayPal in **Admin → Settings → Payments** and set mode to **Sandbox** until live credentials are ready.
4. Configure checkout item name/description with compliant wording.
5. **(Optional)** Create a webhook for extra async verification:
   - **URL:** `https://your-domain.com/webhooks/paypal`
   - **Events:** `PAYMENT.CAPTURE.COMPLETED`
   - Copy **Webhook ID** into `PAYPAL_WEBHOOK_ID` in `.env`.

Checkout uses the **PayPal JS SDK** on `/donate` (popup/modal). Payment is confirmed server-side via:

- `POST /donate/paypal/create-order` — creates pending donation + PayPal order
- `POST /donate/paypal/capture-order` — captures payment and marks donation paid

Success page: `/donate/complete/{reference}`

`PAYPAL_WEBHOOK_ID` is **not required** for checkout. Without it, webhooks are accepted but not verified/processed.

## Webhook URLs

| Gateway | Route name | Path |
|---------|------------|------|
| Stripe | `webhooks.stripe` | `POST /webhooks/stripe` |
| PayPal | `webhooks.paypal` | `POST /webhooks/paypal` |

Webhooks are CSRF-exempt. Invalid signatures are rejected (HTTP 403). Events are logged safely without storing full raw payloads when large.

## Admin

- **Settings → Payments:** enable gateways, currency, min/max amount, receipt email, compliant product strings.
- **Donations:** list shows gateway, status, transaction ID, amount. Bank transfers can be marked received manually; Stripe/PayPal require verified webhooks or return capture.

## Local testing

```bash
php artisan migrate
php artisan test --filter=PaymentGateway
```

Use Stripe CLI for webhook forwarding:

```bash
stripe listen --forward-to http://127.0.0.1:8000/webhooks/stripe
```

Use PayPal sandbox buyer/seller accounts from the developer dashboard for checkout tests.
