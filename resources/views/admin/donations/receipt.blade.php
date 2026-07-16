@php
    $symbol = config('donations.currencies.'.$donation->currency.'.symbol', $donation->currency.' ');
    $paidAt = $donation->paid_at ?? $donation->created_at;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.donations.receipt_title', ['reference' => $donation->reference]) }}</title>
    <style>
        :root { color-scheme: light; }
        body { font-family: Montserrat, Arial, sans-serif; background: #f4eee1; color: #1f3d2b; margin: 0; padding: 32px; }
        .sheet { max-width: 720px; margin: 0 auto; background: #fff; border: 1px solid rgba(12,90,46,.12); border-radius: 24px; padding: 32px; }
        .brand { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
        .brand img { height: 56px; width: auto; }
        .brand h1 { margin: 0; font-size: 1.25rem; color: #0c5a2e; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 24px 0; }
        .meta dt { font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: rgba(31,61,43,.55); margin-bottom: 4px; }
        .meta dd { margin: 0; font-weight: 600; }
        .amount { font-size: 2rem; font-weight: 700; color: #0c5a2e; margin: 8px 0 24px; }
        .message { border-top: 1px solid rgba(12,90,46,.1); padding-top: 20px; color: rgba(31,61,43,.75); line-height: 1.6; }
        .actions { margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; border-radius: 999px; padding: 10px 18px; font-size: 14px; font-weight: 600; text-decoration: none; }
        .btn-primary { background: #0c5a2e; color: #f4eee1; }
        .btn-secondary { border: 1px solid rgba(12,90,46,.2); color: #0c5a2e; background: #fff; }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { border: none; border-radius: 0; box-shadow: none; }
            .actions { display: none !important; }
        }
    </style>
    @if ($autoPrint ?? false)
        <script>window.addEventListener('load', () => window.print());</script>
    @endif
</head>
<body>
    <div class="sheet">
        <div class="brand">
            <img src="{{ \App\Support\SiteAsset::logoUrl() }}" alt="GHOSN Relief Team">
            <div>
                <h1>GHOSN Relief Team</h1>
                <p style="margin:4px 0 0;font-size:13px;color:rgba(31,61,43,.65);">{{ __('admin.donations.receipt_subtitle') }}</p>
            </div>
        </div>

        <p style="margin:0 0 8px;font-size:13px;color:rgba(31,61,43,.6);">{{ __('admin.donations.receipt_reference') }}</p>
        <p style="margin:0 0 16px;font-size:1.25rem;font-weight:700;" dir="ltr">{{ e($donation->reference) }}</p>

        <div class="amount" dir="ltr">{{ e($donation->formattedAmount()) }}</div>

        <dl class="meta">
            <div>
                <dt>{{ __('admin.donations.receipt_donor') }}</dt>
                <dd>{{ e($donation->displayDonorName()) }}</dd>
            </div>
            <div>
                <dt>{{ __('admin.donations.receipt_email') }}</dt>
                <dd dir="ltr">{{ e($donation->donor_email) }}</dd>
            </div>
            <div>
                <dt>{{ __('admin.donations.col_gateway') }}</dt>
                <dd>{{ $donation->gateway ? __('admin.donations.gateway_'.$donation->gateway) : '—' }}</dd>
            </div>
            <div>
                <dt>{{ __('admin.donations.col_method') }}</dt>
                <dd>{{ __('admin.donations.method_'.$donation->payment_method) }}</dd>
            </div>
            <div>
                <dt>{{ __('admin.donations.col_status') }}</dt>
                <dd>{{ __('admin.donations.status_'.$donation->status) }}</dd>
            </div>
            <div>
                <dt>{{ __('admin.donations.col_transaction') }}</dt>
                <dd dir="ltr">{{ e($donation->gateway_transaction_id ?? '—') }}</dd>
            </div>
            <div>
                <dt>{{ __('admin.donations.receipt_date') }}</dt>
                <dd>{{ $paidAt?->format('Y-m-d H:i') ?? '—' }}</dd>
            </div>
            <div>
                <dt>{{ __('admin.donations.receipt_currency') }}</dt>
                <dd dir="ltr">{{ e($donation->currency) }}</dd>
            </div>
        </dl>

        <div class="message">
            {{ __('admin.donations.receipt_thank_you') }}
        </div>

        @unless ($printMode)
            <div class="actions">
                <a href="{{ route('admin.donations.receipt.print', $donation) }}" class="btn btn-primary" target="_blank" rel="noopener">{{ __('admin.donations.receipt_print') }}</a>
                <a href="{{ route('admin.donations.receipt.download', $donation) }}" class="btn btn-secondary">{{ __('admin.donations.receipt_download') }}</a>
                <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary">{{ __('admin.donations.back_to_list') }}</a>
            </div>
        @endunless
    </div>
</body>
</html>
