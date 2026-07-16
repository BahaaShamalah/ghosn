<?php

namespace App\Support;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\ContentPage;
use App\Models\Post;
use App\Services\Settings\SettingsService;
use Illuminate\Support\Facades\Cache;

class SitemapBuilder
{
    public static function render(): string
    {
        return Cache::remember('seo.sitemap.xml', 3600, function (): string {
            $urls = self::urls();

            $xml = ['<?xml version="1.0" encoding="UTF-8"?>'];
            $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            foreach ($urls as $url) {
                $xml[] = '  <url>';
                $xml[] = '    <loc>'.e($url['loc']).'</loc>';
                if (! empty($url['lastmod'])) {
                    $xml[] = '    <lastmod>'.e($url['lastmod']).'</lastmod>';
                }
                if (! empty($url['changefreq'])) {
                    $xml[] = '    <changefreq>'.e($url['changefreq']).'</changefreq>';
                }
                if (isset($url['priority'])) {
                    $xml[] = '    <priority>'.e((string) $url['priority']).'</priority>';
                }
                $xml[] = '  </url>';
            }

            $xml[] = '</urlset>';

            return implode("\n", $xml)."\n";
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('seo.sitemap.xml');
    }

    /**
     * @return list<array{loc: string, lastmod?: string, changefreq?: string, priority?: string}>
     */
    public static function urls(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        if (! (bool) $settings->get('seo.sitemap_enabled', true)) {
            return [];
        }

        $changefreq = trim((string) $settings->get('seo.sitemap_changefreq', 'weekly')) ?: 'weekly';
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('about'), 'changefreq' => $changefreq, 'priority' => '0.8'],
            ['loc' => route('team'), 'changefreq' => $changefreq, 'priority' => '0.7'],
            ['loc' => route('contact'), 'changefreq' => $changefreq, 'priority' => '0.7'],
            ['loc' => route('volunteer'), 'changefreq' => $changefreq, 'priority' => '0.7'],
            ['loc' => route('campaigns.index'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('news.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => route('donate'), 'changefreq' => $changefreq, 'priority' => '0.9'],
        ];

        foreach (config('legal-pages.slugs', []) as $slug) {
            $urls[] = [
                'loc' => url('/'.$slug),
                'changefreq' => 'monthly',
                'priority' => '0.4',
            ];
        }

        if ((bool) $settings->get('seo.sitemap_include_campaigns', true)) {
            Campaign::query()
                ->where('status', Campaign::STATUS_ACTIVE)
                ->orderByDesc('updated_at')
                ->get(['slug', 'updated_at'])
                ->each(function (Campaign $campaign) use (&$urls, $changefreq): void {
                    $urls[] = [
                        'loc' => route('campaigns.show', $campaign->slug),
                        'lastmod' => optional($campaign->updated_at)?->toAtomString(),
                        'changefreq' => $changefreq,
                        'priority' => '0.8',
                    ];
                });
        }

        if ((bool) $settings->get('seo.sitemap_include_posts', true)) {
            Post::query()
                ->where('status', Post::STATUS_PUBLISHED)
                ->orderByDesc('published_at')
                ->get(['slug', 'updated_at', 'published_at'])
                ->each(function (Post $post) use (&$urls, $changefreq): void {
                    $urls[] = [
                        'loc' => route('news.show', $post->slug),
                        'lastmod' => optional($post->updated_at ?? $post->published_at)?->toAtomString(),
                        'changefreq' => $changefreq,
                        'priority' => '0.7',
                    ];
                });
        }

        if ((bool) $settings->get('seo.sitemap_include_pages', true)) {
            ContentPage::query()
                ->where('status', ContentPage::STATUS_PUBLISHED)
                ->orderBy('slug')
                ->get(['slug', 'updated_at'])
                ->each(function (ContentPage $page) use (&$urls, $changefreq): void {
                    $urls[] = [
                        'loc' => route('pages.show', $page->slug),
                        'lastmod' => optional($page->updated_at)?->toAtomString(),
                        'changefreq' => $changefreq,
                        'priority' => '0.6',
                    ];
                });
        }

        if ((bool) $settings->get('seo.sitemap_include_categories', true)) {
            Category::query()
                ->orderBy('slug')
                ->get(['slug', 'updated_at', 'type'])
                ->each(function (Category $category) use (&$urls): void {
                    // Categories are filter facets; include stable public index anchors when available.
                    $urls[] = [
                        'loc' => $category->type === Category::TYPE_POST
                            ? route('news.index', ['category' => $category->slug])
                            : route('campaigns.index', ['category' => $category->slug]),
                        'lastmod' => optional($category->updated_at)?->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.5',
                    ];
                });
        }

        return $urls;
    }
}
