@php
    $googleBody = app(\App\Services\Google\GoogleIntegrationService::class)->bodyPayload();
@endphp

@if ($googleBody['gtmEnabled'])
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id={{ urlencode($googleBody['gtmContainerId']) }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe>
</noscript>
@endif

@if ($googleBody['consentEnabled'])
    @include('public.partials.consent-banner')
@endif
