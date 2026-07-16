<?php



namespace App\Support;



use App\Models\Page;

use App\Models\PageSection;



class BuilderPageContent

{

    /**

     * @return array<string, PageSection>

     */

    public static function indexedSections(string $pageSlug): array

    {

        $page = Page::query()->where('slug', $pageSlug)->first();



        if (! $page) {

            return [];

        }



        return $page->sections()

            ->orderBy('sort_order')

            ->get()

            ->keyBy('key')

            ->all();

    }



    /**

     * @return array<string, mixed>|null

     */

    public static function sectionSettings(string $pageSlug, string $sectionKey): ?array

    {

        $sections = self::indexedSections($pageSlug);

        $section = $sections[$sectionKey] ?? null;



        if (! $section) {

            return null;

        }



        return is_array($section->settings) ? $section->settings : null;

    }



    public static function findPage(string $slug): ?Page

    {

        return Page::query()

            ->where('slug', $slug)

            ->where('is_active', true)

            ->first();

    }

}

