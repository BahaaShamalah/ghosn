<?php

namespace App\Support;

use App\Models\Page;
use App\Services\Settings\SettingsService;

class BuilderReactPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function volunteer(Page $page): array
    {
        $volunteer = VolunteerPageContent::forReact(
            BuilderPageContent::sectionSettings('volunteer', 'volunteer'),
        );

        return array_merge(self::sharedChrome(), [
            'pageType' => 'volunteer',
            'pageTitle' => self::pageTitlePair($page),
            'volunteer' => $volunteer,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function whoWeAre(Page $page): array
    {
        return array_merge(self::sharedChrome(), [
            'pageType' => 'about',
            'pageTitle' => self::pageTitlePair($page),
            'aboutPage' => AboutPageContent::forReact(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function sharedChrome(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        return array_merge(SiteChrome::reactPayload(), [
            'locale' => app()->getLocale(),
            'localeBase' => url('/locale'),
            'csrfToken' => csrf_token(),
            'routes' => [
                'home' => route('home'),
                'donate' => route('donate'),
                'about' => route('about'),
                'contact' => route('contact'),
                'team' => route('team'),
                'joinUs' => route('volunteer'),
                'volunteer' => route('volunteer'),
                'campaigns' => route('campaigns.index'),
                'news' => route('news.index'),
                'localeEn' => route('locale.switch', 'en'),
                'localeAr' => route('locale.switch', 'ar'),
                'volunteerApplications' => route('volunteer-applications.store'),
                'contactMessages' => route('contact-messages.store'),
                'newsletterSubscriptions' => route('newsletter-subscriptions.store'),
            ],
            'assets' => [
                'logo' => SiteAsset::logoUrl() ?: asset('assets/landing/images/logo.webp'),
            ],
            'siteName' => [
                'en' => SiteSettings::name('en'),
                'ar' => SiteSettings::name('ar'),
            ],
            'socialLinks' => SiteFooter::socialLinksForReact(),
            'contact' => array_merge([
                'email' => (string) $settings->get('contact.email', 'ghosn.gaza@gmail.com'),
                'phone' => (string) $settings->get('contact.phone', ''),
            ], SiteChrome::reactPayload()['contact'] ?? []),
            'contactPage' => SiteChrome::contactPageCopy(),
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @deprecated Use sharedChrome().
     */
    private static function shared(): array
    {
        return self::sharedChrome();
    }

    /**
     * @return array{en: string, ar: string}
     */
    private static function pageTitlePair(Page $page): array
    {
        return [
            'en' => (string) ($page->meta_title_en ?: $page->title_en),
            'ar' => (string) ($page->meta_title_ar ?: $page->title_ar),
        ];
    }
}
