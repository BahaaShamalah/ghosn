<?php

namespace App\Services\Google;

use App\Support\GoogleSettings;
use App\Support\ThemeHelper;

class GoogleIntegrationService
{
    public function __construct(
        private readonly GoogleSettings $google,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function headPayload(): array
    {
        return [
            'searchConsoleMeta' => $this->google->searchConsoleMeta(),
            'merchantMeta' => $this->google->merchantMeta(),
            'consentEnabled' => $this->google->consentEnabled(),
            'consentDefaults' => $this->google->consentDefaults(),
            'waitForUpdate' => $this->google->waitForUpdate(),
            'regions' => $this->google->consentRegions(),
            'gtmEnabled' => $this->google->gtmEnabled() && $this->google->bool('google.gtm.inject_head', true),
            'gtmContainerId' => $this->google->gtmContainerId(),
            'analyticsEnabled' => $this->google->analyticsEnabled(),
            'measurementId' => $this->google->measurementId(),
            'anonymizeIp' => $this->google->bool('google.analytics.anonymize_ip', true),
            'debug' => $this->google->bool('google.analytics.debug'),
            'adsenseEnabled' => $this->google->adsenseEnabled() && $this->google->bool('google.adsense.auto_ads', true),
            'adsensePublisherId' => $this->google->adsensePublisherId(),
            'fontsCdn' => $this->fontsCdnPayload(),
            'recaptchaSiteKey' => $this->google->recaptchaEnabled() ? $this->google->recaptchaSiteKey() : '',
            'publicConfig' => $this->google->publicConfig(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function bodyPayload(): array
    {
        return [
            'gtmEnabled' => $this->google->gtmEnabled() && $this->google->bool('google.gtm.inject_body', true),
            'gtmContainerId' => $this->google->gtmContainerId(),
            'consentEnabled' => $this->google->consentEnabled(),
            'cookieDays' => $this->google->cookieDays(),
        ];
    }

    /**
     * @return array{enabled: bool, preconnect: bool, stylesheet: string|null}
     */
    private function fontsCdnPayload(): array
    {
        if (! $this->google->bool('google.fonts.enable_cdn') || $this->google->bool('google.fonts.prefer_local', true)) {
            return [
                'enabled' => false,
                'preconnect' => false,
                'stylesheet' => null,
            ];
        }

        $fonts = ThemeHelper::fonts();
        $familyEn = $this->google->string('google.fonts.family_en') ?: $fonts['en'];
        $familyAr = $this->google->string('google.fonts.family_ar') ?: $fonts['ar'];
        $display = $this->google->bool('google.fonts.display_swap', true) ? 'swap' : 'auto';

        $families = array_unique(array_filter([$familyEn, $familyAr]));
        $query = collect($families)
            ->map(static fn (string $name): string => 'family='.rawurlencode($name).':wght@400;500;600;700')
            ->implode('&');

        return [
            'enabled' => true,
            'preconnect' => $this->google->bool('google.fonts.preconnect', true),
            'stylesheet' => 'https://fonts.googleapis.com/css2?'.$query.'&display='.$display,
        ];
    }
}
