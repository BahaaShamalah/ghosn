@php
    $googleHead = app(\App\Services\Google\GoogleIntegrationService::class)->headPayload();
@endphp

@if ($googleHead['searchConsoleMeta'] !== '')
    <meta name="google-site-verification" content="{{ e($googleHead['searchConsoleMeta']) }}">
@endif
@if ($googleHead['merchantMeta'] !== '')
    <meta name="google-merchant-verification" content="{{ e($googleHead['merchantMeta']) }}">
@endif

@if ($googleHead['fontsCdn']['enabled'] && $googleHead['fontsCdn']['preconnect'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
@endif
@if ($googleHead['fontsCdn']['enabled'] && $googleHead['fontsCdn']['stylesheet'])
    <link rel="stylesheet" href="{{ e($googleHead['fontsCdn']['stylesheet']) }}">
@endif

<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    window.__GHOSN_GOOGLE__ = @json($googleHead['publicConfig']);
</script>

@if ($googleHead['consentEnabled'])
<script>
    gtag('consent', 'default', {
        analytics_storage: @json($googleHead['consentDefaults']['analytics_storage']),
        ad_storage: @json($googleHead['consentDefaults']['ad_storage']),
        ad_user_data: @json($googleHead['consentDefaults']['ad_user_data']),
        ad_personalization: @json($googleHead['consentDefaults']['ad_personalization']),
        wait_for_update: {{ (int) $googleHead['waitForUpdate'] }}
        @if (! empty($googleHead['regions']))
        , region: @json($googleHead['regions'])
        @endif
    });
</script>
@endif

@if ($googleHead['gtmEnabled'])
<script>
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer',@json($googleHead['gtmContainerId']));
</script>
@endif

@if ($googleHead['analyticsEnabled'] && ! $googleHead['gtmEnabled'])
<script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($googleHead['measurementId']) }}"></script>
<script>
    gtag('js', new Date());
    gtag('config', @json($googleHead['measurementId']), {
        anonymize_ip: {{ $googleHead['anonymizeIp'] ? 'true' : 'false' }},
        debug_mode: {{ $googleHead['debug'] ? 'true' : 'false' }}
    });
</script>
@endif

@if ($googleHead['adsenseEnabled'])
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ urlencode($googleHead['adsensePublisherId']) }}" crossorigin="anonymous"></script>
@endif

@if ($googleHead['recaptchaSiteKey'] !== '')
<script src="https://www.google.com/recaptcha/api.js?render={{ urlencode($googleHead['recaptchaSiteKey']) }}" async defer></script>
@endif
