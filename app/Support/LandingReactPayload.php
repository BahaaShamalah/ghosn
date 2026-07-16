<?php

namespace App\Support;

use App\Models\Campaign;
use App\Models\Post;
use App\Services\Settings\SettingsService;
use Illuminate\Support\Collection;

class LandingReactPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function build(): array
    {
        $pageContent = LandingPageContent::forReact();
        $campaignSection = self::homeSection('campaigns');
        $newsSection = self::homeSection('latest_news');
        $campaignData = CampaignContent::resolve(
            is_array($campaignSection?->settings) ? $campaignSection->settings : null,
        );
        $newsData = NewsContent::resolve(
            is_array($newsSection?->settings) ? $newsSection->settings : null,
        );
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        return array_merge([
            'locale' => app()->getLocale(),
            'localeBase' => url('/locale'),
            'csrfToken' => csrf_token(),
            'routes' => [
                'home' => route('home'),
                'donate' => route('donate'),
                'about' => route('about'),
                'contact' => route('contact'),
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
            'campaigns' => self::serializeCampaigns($campaignData['campaigns']),
            'posts' => self::serializePosts($newsData['posts']),
            'socialLinks' => SiteFooter::socialLinksForReact(),
            'newsletter' => self::serializeNewsletter($settings),
            'hero' => $pageContent['hero'],
            'about' => $pageContent['about'],
            'impact' => $pageContent['impact'],
            'howWorks' => $pageContent['howWorks'],
            'ways' => $pageContent['ways'],
            'testimonials' => $pageContent['testimonials'],
            'join' => $pageContent['join'],
            'campaignSection' => $pageContent['campaigns'],
            'blogSection' => $pageContent['blog'],
            'campaignsSectionVisible' => (bool) ($pageContent['campaigns']['visible'] ?? true),
            'blogSectionVisible' => (bool) ($pageContent['blog']['visible'] ?? true),
            'homeSectionsVisible' => self::homeSectionsVisible(),
        ], SiteChrome::reactPayload(), [
            'contact' => array_merge([
                'email' => (string) $settings->get('contact.email', 'ghosn.gaza@gmail.com'),
                'phone' => (string) $settings->get('contact.phone', ''),
            ], SiteChrome::reactPayload()['contact'] ?? []),
            'contactPage' => SiteChrome::contactPageCopy(),
        ]);
    }

    private static function homeSection(string $key): ?\App\Models\PageSection
    {
        $page = \App\Models\Page::query()->where('slug', 'home')->first();

        return $page?->sections()->where('key', $key)->first();
    }

    /**
     * @return array<string, bool>
     */
    private static function homeSectionsVisible(): array
    {
        $page = \App\Models\Page::query()->where('slug', 'home')->first();

        if (! $page) {
            return [];
        }

        return $page->sections()
            ->pluck('is_active', 'key')
            ->map(fn (mixed $isActive): bool => (bool) $isActive)
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private static function serializeNewsletter(SettingsService $settings): array
    {
        return [
            'enabled' => (bool) $settings->get('newsletter.enabled', true),
            'title' => [
                'en' => (string) $settings->get('newsletter.title_en', 'Stay close to the impact'),
                'ar' => (string) $settings->get('newsletter.title_ar', 'ابقَ قريبًا من الأثر'),
            ],
            'subtitle' => [
                'en' => (string) $settings->get('newsletter.subtitle_en', ''),
                'ar' => (string) $settings->get('newsletter.subtitle_ar', ''),
            ],
            'placeholder' => [
                'en' => (string) $settings->get('newsletter.placeholder_en', 'Your email address'),
                'ar' => (string) $settings->get('newsletter.placeholder_ar', 'بريدك الإلكتروني'),
            ],
            'button' => [
                'en' => (string) $settings->get('newsletter.button_en', 'Subscribe'),
                'ar' => (string) $settings->get('newsletter.button_ar', 'اشترك'),
            ],
            'success' => [
                'en' => (string) $settings->get('newsletter.success_en', 'You are subscribed — thank you!'),
                'ar' => (string) $settings->get('newsletter.success_ar', 'تم اشتراكك — شكرًا لك!'),
            ],
        ];
    }

    /**
     * @param  Collection<int, Campaign>  $campaigns
     * @return list<array<string, mixed>>
     */
    private static function serializeCampaigns(Collection $campaigns): array
    {
        return $campaigns->map(function (Campaign $campaign): array {
            $goal = (float) $campaign->goal_amount;
            $raised = (float) $campaign->raised_amount;
            $pct = $goal > 0 ? (int) round(($raised / $goal) * 100) : 0;

            return [
                'id' => $campaign->id,
                'slug' => $campaign->slug,
                'url' => route('campaigns.show', $campaign->slug),
                'title_en' => $campaign->title_en,
                'title_ar' => $campaign->title_ar,
                'excerpt_en' => $campaign->excerpt_en,
                'excerpt_ar' => $campaign->excerpt_ar,
                'goal' => $goal,
                'raised' => $raised,
                'currency' => $campaign->currency ?: 'USD',
                'pct' => min(100, $pct),
                'tag' => $campaign->status === Campaign::STATUS_ACTIVE && $campaign->ends_at?->isPast() === false
                    ? 'ongoing'
                    : ($pct >= 85 ? 'urgent' : 'ongoing'),
                'image' => $campaign->featuredImage?->thumbnailUrl() ?? $campaign->featuredImage?->url(),
            ];
        })->values()->all();
    }

    /**
     * @param  Collection<int, Post>  $posts
     * @return list<array<string, mixed>>
     */
    private static function serializePosts(Collection $posts): array
    {
        return $posts->map(fn (Post $post): array => [
            'id' => $post->id,
            'slug' => $post->slug,
            'url' => route('news.show', $post->slug),
            'title_en' => $post->title_en,
            'title_ar' => $post->title_ar,
            'excerpt_en' => $post->excerpt_en,
            'excerpt_ar' => $post->excerpt_ar,
            'category_en' => $post->category?->name_en,
            'category_ar' => $post->category?->name_ar,
            'date' => $post->published_at?->format('M j, Y') ?? '',
            'image' => $post->featuredImage?->thumbnailUrl() ?? $post->featuredImage?->url(),
        ])->values()->all();
    }
}
