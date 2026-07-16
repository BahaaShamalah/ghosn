<?php

namespace App\Support;

use App\Models\Page;
use App\Models\PageSection;

class LandingPageContent
{
    /**
     * @return array<string, mixed>
     */
    public static function forReact(): array
    {
        $sections = BuilderPageContent::indexedSections('home');

        return [
            'hero' => HeroContent::forReact(self::settings($sections['hero'] ?? null), null),
            'about' => AboutContent::forReact(self::settings($sections['about'] ?? null), null),
            'impact' => self::impact(self::settings($sections['impact'] ?? null)),
            'howWorks' => self::howWorks(self::settings($sections['how_works'] ?? null)),
            'ways' => self::ways(self::settings($sections['ways'] ?? null)),
            'testimonials' => self::testimonials(self::settings($sections['testimonials'] ?? null)),
            'join' => self::joinSection(self::settings($sections['join'] ?? null)),
            'campaigns' => self::campaigns(self::settings($sections['campaigns'] ?? null)),
            'blog' => self::blog(self::settings($sections['latest_news'] ?? null)),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    public static function joinSection(?array $settings): array
    {
        return self::join($settings);
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function settings(?PageSection $section): ?array
    {
        if (! $section) {
            return null;
        }

        return is_array($section->settings) ? $section->settings : null;
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    private static function impact(?array $settings): array
    {
        $resolved = SectionContent::resolve('impact', $settings, null);

        return [
            'title' => self::pair($resolved, 'title'),
            'stats' => collect($resolved['stats'] ?? [])->map(fn (array $stat): array => [
                'key' => (string) ($stat['key'] ?? ''),
                'end' => (float) ($stat['end'] ?? 0),
                'decimals' => (int) ($stat['decimals'] ?? 0),
                'prefix' => (string) ($stat['prefix'] ?? ''),
                'suffix' => (string) ($stat['suffix'] ?? ''),
                'label' => self::pair($stat, 'label'),
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    private static function howWorks(?array $settings): array
    {
        $resolved = SectionContent::resolve('how_works', $settings, null);

        return [
            'eyebrow' => self::pair($resolved, 'eyebrow'),
            'title' => self::pair($resolved, 'heading'),
            'intro' => self::pair($resolved, 'description'),
            'steps' => collect($resolved['steps'] ?? [])->map(fn (array $step): array => [
                'title' => self::pair($step, 'title'),
                'body' => self::pair($step, 'body'),
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    private static function ways(?array $settings): array
    {
        $resolved = SectionContent::resolve('ways', $settings, null);

        return [
            'eyebrow' => self::pair($resolved, 'eyebrow'),
            'title' => self::pair($resolved, 'heading'),
            'intro' => self::pair($resolved, 'description'),
            'cards' => collect($resolved['cards'] ?? [])->map(fn (array $card): array => [
                'title' => self::pair($card, 'title'),
                'body' => self::pair($card, 'body'),
                'cta' => self::pair($card, 'cta'),
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    private static function testimonials(?array $settings): array
    {
        $resolved = SectionContent::resolve('testimonials', $settings, null);

        return [
            'eyebrow' => self::pair($resolved, 'eyebrow'),
            'title' => self::pair($resolved, 'heading'),
            'items' => collect($resolved['items'] ?? [])->map(fn (array $item): array => [
                'quote' => self::pair($item, 'quote'),
                'name' => self::pair($item, 'name'),
                'role' => self::pair($item, 'role'),
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    private static function join(?array $settings): array
    {
        $resolved = SectionContent::resolve('join', $settings, null);

        return [
            'eyebrow' => self::pair($resolved, 'eyebrow'),
            'title' => self::pair($resolved, 'heading'),
            'copy' => self::pair($resolved, 'description'),
            'bullets' => collect($resolved['bullets'] ?? [])->map(
                fn (array $bullet): array => self::pair($bullet, 'text'),
            )->values()->all(),
            'name' => self::pair($resolved, 'name_label'),
            'namePh' => self::pair($resolved, 'name_placeholder'),
            'phone' => self::pair($resolved, 'phone_label'),
            'phonePh' => self::pair($resolved, 'phone_placeholder'),
            'email' => self::pair($resolved, 'email_label'),
            'emailPh' => self::pair($resolved, 'email_placeholder'),
            'areaLabel' => self::pair($resolved, 'area_label'),
            'areaPh' => self::pair($resolved, 'area_placeholder'),
            'message' => self::pair($resolved, 'message_label'),
            'messagePh' => self::pair($resolved, 'message_placeholder'),
            'submit' => self::pair($resolved, 'submit'),
            'sending' => self::pair($resolved, 'sending'),
            'success' => self::pair($resolved, 'success'),
            'error' => self::pair($resolved, 'error'),
            'areas' => collect($resolved['areas'] ?? [])->map(fn (array $area): array => [
                'value' => (string) ($area['value'] ?? ''),
                'label' => self::pair($area, 'label'),
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    private static function campaigns(?array $settings): array
    {
        $resolved = CampaignContent::resolve($settings);

        return [
            'visible' => (bool) ($resolved['visible'] ?? true),
            'eyebrow' => self::pair($resolved, 'eyebrow'),
            'title' => self::pair($resolved, 'title'),
            'intro' => self::pair($resolved, 'subtitle'),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    private static function blog(?array $settings): array
    {
        $resolved = NewsContent::resolve($settings);

        return [
            'visible' => (bool) ($resolved['visible'] ?? true),
            'eyebrow' => self::pair($resolved, 'eyebrow'),
            'title' => self::pair($resolved, 'title'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{en: string, ar: string}
     */
    private static function pair(array $data, string $key): array
    {
        return [
            'en' => (string) ($data["{$key}_en"] ?? ''),
            'ar' => (string) ($data["{$key}_ar"] ?? ''),
        ];
    }
}
