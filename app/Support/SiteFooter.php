<?php

namespace App\Support;

use App\Models\SocialLink;
use App\Services\Settings\SettingsService;
use Illuminate\Support\Collection;

class SiteFooter
{
    /**
     * @return Collection<int, SocialLink>
     */
    public static function socialLinks(): Collection
    {
        return SocialLink::query()
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * @return list<array{platform: string, url: string, label: string, iconClass: string}>
     */
    public static function socialLinksForReact(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        return self::socialLinks()
            ->map(fn (SocialLink $link): array => [
                'platform' => $link->platform,
                'url' => $link->url,
                'label' => $link->localizedLabel($locale),
                'iconClass' => $link->fontAwesomeClass(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{email: string, phone: string}
     */
    public static function contact(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        return [
            'email' => (string) $settings->get('contact.email', 'ghosn.gaza@gmail.com'),
            'phone' => (string) $settings->get('contact.phone', ''),
        ];
    }
}
