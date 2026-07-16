<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class AdminBreadcrumbs
{
    /**
     * @return list<array{label: string, url: string|null}>
     */
    public static function forCurrentRoute(): array
    {
        $routeName = Route::currentRouteName() ?? '';
        $items = [
            ['label' => __('admin.nav.dashboard'), 'url' => route('admin.dashboard')],
        ];

        return match (true) {
            str_starts_with($routeName, 'admin.content-pages.') => self::contentPagesTrail($routeName, $items),
            str_starts_with($routeName, 'admin.settings.') => self::append($items, __('admin.nav.settings'), route('admin.settings.index'), self::settingsLeaf($routeName)),
            str_starts_with($routeName, 'admin.donors.') => self::append($items, __('admin.nav.donors'), route('admin.donors.index'), self::donorsLeaf($routeName)),
            str_starts_with($routeName, 'admin.users.') => self::append($items, __('admin.nav.users'), route('admin.users.index')),
            str_starts_with($routeName, 'admin.roles.') => self::append($items, __('admin.nav.roles'), route('admin.roles.index')),
            str_starts_with($routeName, 'admin.donations.') => self::append($items, __('admin.nav.donations'), route('admin.donations.index')),
            str_starts_with($routeName, 'admin.campaigns.') => self::append($items, __('admin.nav.campaigns'), route('admin.campaigns.index')),
            str_starts_with($routeName, 'admin.posts.') => self::append($items, __('admin.nav.posts'), route('admin.posts.index')),
            str_starts_with($routeName, 'admin.pages.') => self::append($items, __('admin.nav.pages_builder'), route('admin.pages.index')),
            str_starts_with($routeName, 'admin.settings') => self::append($items, __('admin.nav.settings'), null),
            default => $items,
        };
    }

    /**
     * @param  list<array{label: string, url: string|null}>  $items
     * @return list<array{label: string, url: string|null}>
     */
    private static function contentPagesTrail(string $routeName, array $items): array
    {
        $items = self::append($items, __('admin.nav.content_pages'), route('admin.content-pages.index'));

        return match ($routeName) {
            'admin.content-pages.create' => self::append($items, __('admin.cms.new_page'), null),
            'admin.content-pages.edit' => self::append($items, __('admin.cms.edit_page'), null),
            'admin.content-pages.preview' => self::append($items, __('admin.cms.preview'), null),
            default => $items,
        };
    }

    /**
     * @param  list<array{label: string, url: string|null}>  $items
     * @return list<array{label: string, url: string|null}>
     */
    private static function settingsLeaf(string $routeName): ?string
    {
        if ($routeName === 'admin.settings.show') {
            $group = request()->route('group');

            return $group ? __('admin.settings.group_'.$group) : null;
        }

        return null;
    }

    private static function donorsLeaf(string $routeName): ?string
    {
        return $routeName === 'admin.donors.show' ? __('admin.donors.profile') : null;
    }

    /**
     * @param  list<array{label: string, url: string|null}>  $items
     * @return list<array{label: string, url: string|null}>
     */
    private static function append(array $items, string $label, ?string $url, ?string $leaf = null): array
    {
        $items[] = ['label' => $label, 'url' => $url];

        if ($leaf) {
            $items[] = ['label' => $leaf, 'url' => null];
        }

        return $items;
    }
}
