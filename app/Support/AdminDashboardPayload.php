<?php

namespace App\Support;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use App\Models\VolunteerApplication;
use App\Support\AdminNav;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;

class AdminDashboardPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function build(): array
    {
        $user = auth()->user();
        assert($user instanceof User);

        $homepage = Page::query()->where('slug', 'home')->first();

        return [
            'locale' => app()->getLocale(),
            'localeBase' => url('/admin/locale'),
            'csrfToken' => csrf_token(),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'initial' => mb_strtoupper(mb_substr($user->name, 0, 1)),
                'role_en' => $user->roleLabel('en'),
                'role_ar' => $user->roleLabel('ar'),
            ],
            'routes' => [
                'dashboard' => route('admin.dashboard'),
                'settings' => route('admin.settings.index'),
                'pages' => route('admin.pages.index'),
                'posts' => route('admin.posts.index'),
                'contentPages' => route('admin.content-pages.index'),
                'categories' => route('admin.categories.index'),
                'media' => route('admin.media.index'),
                'campaigns' => route('admin.campaigns.index'),
                'donations' => route('admin.donations.index'),
                'volunteers' => route('admin.volunteers.index'),
                'messages' => route('admin.messages.index'),
                'donors' => route('admin.donors.index'),
                'home' => route('home'),
                'logout' => route('admin.logout'),
                'homepageBuilder' => $homepage ? route('admin.pages.show', $homepage) : null,
            ],
            'assets' => [
                'logo' => SiteAsset::logoUrl(),
            ],
            'nav' => self::navItems(),
            'dashboard' => [
                'kpis' => self::kpis(),
                'trend' => self::donationTrend(),
                'campaignProgress' => self::campaignProgress(),
                'recentDonations' => self::recentDonations(),
                'recentVolunteers' => self::recentVolunteers(),
                'pagesCount' => Page::query()->count(),
                'sectionsCount' => PageSection::query()->count(),
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function navItems(): array
    {
        $user = auth()->user();
        assert($user instanceof User);

        return array_map(function (array $item) use ($user): array {
            $labelKey = match ($item['key']) {
                'pages' => 'pages_builder',
                'contentPages' => 'content_pages',
                default => $item['key'],
            };

            return [
                'key' => $item['key'],
                'route' => $item['route'],
                'icon' => $item['icon'],
                'url' => route($item['route']),
                'label_en' => Lang::get('admin.nav.'.$labelKey, [], 'en'),
                'label_ar' => Lang::get('admin.nav.'.$labelKey, [], 'ar'),
                'active' => $item['key'] === 'dashboard',
            ];
        }, AdminNav::visibleFor($user));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function kpis(): array
    {
        $totalRaised = (float) Donation::query()->paid()->sum('amount');
        $activeCampaigns = Campaign::query()->where('status', Campaign::STATUS_ACTIVE)->count();
        $pagesCount = Page::query()->count();
        $sectionsCount = PageSection::query()->count();

        return [
            [
                'key' => 'raised',
                'value' => self::formatCompactMoney($totalRaised),
                'label_en' => 'Total raised',
                'label_ar' => 'إجمالي التبرّعات',
                'trend' => null,
            ],
            [
                'key' => 'campaigns',
                'value' => (string) $activeCampaigns,
                'label_en' => 'Active campaigns',
                'label_ar' => 'الحملات النشطة',
                'trend' => null,
            ],
            [
                'key' => 'pages',
                'value' => (string) $pagesCount,
                'label_en' => 'Pages',
                'label_ar' => 'الصفحات',
                'trend' => null,
            ],
            [
                'key' => 'sections',
                'value' => (string) $sectionsCount,
                'label_en' => 'Landing sections',
                'label_ar' => 'أقسام الصفحة الرئيسية',
                'trend' => null,
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function donationTrend(): array
    {
        $months = collect(range(6, 0))->map(fn (int $i): Carbon => now()->subMonths($i)->startOfMonth());

        $amounts = $months->map(function (Carbon $month): float {
            return (float) Donation::query()
                ->paid()
                ->whereNotNull('paid_at')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->sum('amount');
        });

        $max = max(1.0, (float) $amounts->max());

        return $months->map(function (Carbon $month, int $index) use ($amounts, $max): array {
            $amount = (float) ($amounts[$index] ?? 0);

            return [
                'label_en' => $month->format('M'),
                'label_ar' => $month->locale('ar')->translatedFormat('M'),
                'amount' => $amount,
                'heightPct' => (int) round(($amount / $max) * 100),
            ];
        })->values()->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function campaignProgress(): array
    {
        return Campaign::query()
            ->where('status', '!=', Campaign::STATUS_DRAFT)
            ->ordered()
            ->limit(4)
            ->get()
            ->map(fn (Campaign $campaign): array => [
                'title_en' => $campaign->title_en,
                'title_ar' => $campaign->title_ar,
                'pct' => (int) round($campaign->progressPercent()),
                'raised' => $campaign->formattedRaised(),
                'goal' => $campaign->formattedGoal(),
                'url' => route('admin.campaigns.edit', $campaign),
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function recentDonations(): array
    {
        $colors = ['#406139', '#819562', '#6f8452', '#96A791', '#557045'];

        return Donation::query()
            ->with('campaign')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get()
            ->map(function (Donation $donation, int $index) use ($colors): array {
                $name = $donation->displayDonorName();

                return [
                    'name' => $name,
                    'initial' => mb_strtoupper(mb_substr($name, 0, 1)),
                    'color' => $colors[$index % count($colors)],
                    'campaign_en' => $donation->campaign?->title_en ?? '—',
                    'campaign_ar' => $donation->campaign?->title_ar ?? '—',
                    'amount' => $donation->formattedAmount(),
                    'status' => $donation->status,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function recentVolunteers(): array
    {
        return VolunteerApplication::query()
            ->orderByDesc('created_at')
            ->limit(4)
            ->get()
            ->map(fn (VolunteerApplication $application): array => [
                'name' => $application->name,
                'initial' => $application->initial(),
                'area_en' => $application->localizedArea('en'),
                'area_ar' => $application->localizedArea('ar'),
                'status' => $application->status,
            ])
            ->values()
            ->all();
    }

    private static function formatCompactMoney(float $amount): string
    {
        if ($amount >= 1_000_000) {
            return '$'.number_format($amount / 1_000_000, 1).'M';
        }

        if ($amount >= 1_000) {
            return '$'.number_format($amount / 1_000, 1).'K';
        }

        return '$'.number_format($amount, 0);
    }
}
