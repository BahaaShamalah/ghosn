<?php

namespace App\Support;

use App\Models\Campaign;
use Illuminate\Support\Collection;

class CampaignContent
{
    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    public static function resolve(?array $settings = null): array
    {
        $defaults = config('campaigns.defaults', []);
        $stored = is_array($settings) ? $settings : [];

        if (isset($stored['content']) && is_array($stored['content'])) {
            $stored = array_merge($stored, $stored['content']);
        }

        unset($stored['content']);

        $content = array_merge($defaults, $stored);
        $visible = self::isTruthy($content['is_visible'] ?? true);
        $limit = max(1, min(12, (int) ($content['campaigns_count'] ?? 3)));

        /** @var Collection<int, Campaign> $campaigns */
        $campaigns = $visible
            ? Campaign::query()
                ->featuredHomepage()
                ->with(['category', 'featuredImage'])
                ->ordered()
                ->limit($limit)
                ->get()
            : collect();

        if ($visible && $campaigns->isEmpty()) {
            $campaigns = Campaign::query()
                ->public()
                ->with(['category', 'featuredImage'])
                ->ordered()
                ->limit($limit)
                ->get();
        }

        return [
            'visible' => $visible,
            'eyebrow_en' => (string) ($content['eyebrow_en'] ?? 'Active Campaigns'),
            'eyebrow_ar' => (string) ($content['eyebrow_ar'] ?? 'الحملات النشطة'),
            'title_en' => (string) ($content['title_en'] ?? 'Support a Cause'),
            'title_ar' => (string) ($content['title_ar'] ?? 'ادعم قضية'),
            'subtitle_en' => (string) ($content['subtitle_en'] ?? ''),
            'subtitle_ar' => (string) ($content['subtitle_ar'] ?? ''),
            'campaigns_count' => $limit,
            'campaigns' => $campaigns,
        ];
    }

    private static function isTruthy(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($filtered !== null) {
            return $filtered;
        }

        return filled($value);
    }
}
