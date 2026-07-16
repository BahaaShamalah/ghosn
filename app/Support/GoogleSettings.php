<?php

namespace App\Support;

use App\Services\Settings\SettingsService;

class GoogleSettings
{
    public function __construct(
        private readonly SettingsService $settings,
    ) {}

    public static function make(): self
    {
        return app(self::class);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings->get($key, $default);
    }

    public function bool(string $key, bool $default = false): bool
    {
        return (bool) $this->settings->get($key, $default);
    }

    public function string(string $key, string $default = ''): string
    {
        return trim((string) $this->settings->get($key, $default));
    }

    public function analyticsEnabled(): bool
    {
        return $this->bool('google.analytics.enabled')
            && $this->measurementId() !== '';
    }

    public function measurementId(): string
    {
        return strtoupper($this->string('google.analytics.measurement_id'));
    }

    public function gtmEnabled(): bool
    {
        return $this->bool('google.gtm.enabled')
            && $this->gtmContainerId() !== '';
    }

    public function gtmContainerId(): string
    {
        return strtoupper($this->string('google.gtm.container_id'));
    }

    public function consentEnabled(): bool
    {
        return $this->bool('google.consent.enabled', true);
    }

    /**
     * @return array{analytics_storage: string, ad_storage: string, ad_user_data: string, ad_personalization: string}
     */
    public function consentDefaults(): array
    {
        $normalize = static function (string $value): string {
            return in_array($value, ['granted', 'denied'], true) ? $value : 'denied';
        };

        return [
            'analytics_storage' => $normalize($this->string('google.consent.analytics_storage', 'denied')),
            'ad_storage' => $normalize($this->string('google.consent.ad_storage', 'denied')),
            'ad_user_data' => $normalize($this->string('google.consent.ad_user_data', 'denied')),
            'ad_personalization' => $normalize($this->string('google.consent.ad_personalization', 'denied')),
        ];
    }

    public function waitForUpdate(): int
    {
        return max(0, (int) $this->settings->get('google.consent.wait_for_update', 500));
    }

    /**
     * @return list<string>
     */
    public function consentRegions(): array
    {
        $raw = $this->string('google.consent.regions');

        if ($raw === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (string $part): string => strtoupper(trim($part)),
            explode(',', $raw),
        )));
    }

    public function cookieDays(): int
    {
        return max(1, (int) $this->settings->get('google.consent.cookie_days', 365));
    }

    public function searchConsoleMeta(): string
    {
        return $this->sanitizeMetaContent($this->string('google.search_console.meta_tag'));
    }

    public function merchantMeta(): string
    {
        return $this->sanitizeMetaContent($this->string('google.merchant.meta_tag'));
    }

    public function verificationFile(): string
    {
        return basename($this->string('google.search_console.verification_file'));
    }

    public function adsenseEnabled(): bool
    {
        return $this->bool('google.adsense.enabled')
            && $this->adsensePublisherId() !== '';
    }

    public function adsensePublisherId(): string
    {
        return strtolower($this->string('google.adsense.publisher_id'));
    }

    public function mapsEnabled(): bool
    {
        return $this->bool('google.maps.enabled')
            && $this->mapsApiKey() !== '';
    }

    public function mapsApiKey(): string
    {
        return $this->string('google.maps.api_key');
    }

    public function recaptchaEnabled(): bool
    {
        return $this->bool('google.recaptcha.enabled')
            && $this->recaptchaSiteKey() !== ''
            && $this->recaptchaSecretKey() !== '';
    }

    public function recaptchaSiteKey(): string
    {
        return $this->string('google.recaptcha.site_key');
    }

    public function recaptchaSecretKey(): string
    {
        return $this->string('google.recaptcha.secret_key');
    }

    public function recaptchaScoreThreshold(): float
    {
        $value = (float) $this->string('google.recaptcha.score_threshold', '0.5');

        return max(0.0, min(1.0, $value));
    }

    public function recaptchaForContact(): bool
    {
        return $this->recaptchaEnabled() && $this->bool('google.recaptcha.contact', true);
    }

    /**
     * Public-safe payload for layouts (never includes secret_key).
     *
     * @return array<string, mixed>
     */
    public function publicConfig(): array
    {
        return [
            'consentEnabled' => $this->consentEnabled(),
            'consentDefaults' => $this->consentDefaults(),
            'waitForUpdate' => $this->waitForUpdate(),
            'regions' => $this->consentRegions(),
            'cookieDays' => $this->cookieDays(),
            'analyticsEnabled' => $this->analyticsEnabled(),
            'measurementId' => $this->measurementId(),
            'gtmEnabled' => $this->gtmEnabled(),
            'gtmContainerId' => $this->gtmContainerId(),
            'recaptchaEnabled' => $this->recaptchaEnabled(),
            'recaptchaSiteKey' => $this->recaptchaSiteKey(),
            'mapsEnabled' => $this->mapsEnabled(),
            'mapsApiKey' => $this->mapsEnabled() ? $this->mapsApiKey() : '',
            'mapsLanguage' => $this->string('google.maps.language'),
            'mapsRegion' => $this->string('google.maps.region'),
        ];
    }

    private function sanitizeMetaContent(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/content=["\']([^"\']+)["\']/i', $value, $matches)) {
            return trim($matches[1]);
        }

        return preg_replace('/[^A-Za-z0-9_\-=.]/', '', $value) ?? '';
    }
}
