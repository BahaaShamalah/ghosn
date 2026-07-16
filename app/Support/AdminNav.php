<?php

namespace App\Support;

use App\Models\User;

class AdminNav
{
    /**
     * @return list<array{route: string, label: string, icon: string, permission: string}>
     */
    public static function items(): array
    {
        return [
            ['key' => 'dashboard', 'route' => 'admin.dashboard', 'label' => __('admin.nav.dashboard'), 'icon' => 'dashboard', 'permission' => 'dashboard.view'],
            ['key' => 'settings', 'route' => 'admin.settings.index', 'label' => __('admin.nav.settings'), 'icon' => 'settings', 'permission' => 'settings.manage'],
            ['key' => 'pages', 'route' => 'admin.pages.index', 'label' => __('admin.nav.pages_builder'), 'icon' => 'pages', 'permission' => 'pages.manage'],
            ['key' => 'posts', 'route' => 'admin.posts.index', 'label' => __('admin.nav.posts'), 'icon' => 'posts', 'permission' => 'posts.manage'],
            ['key' => 'contentPages', 'route' => 'admin.content-pages.index', 'label' => __('admin.nav.content_pages'), 'icon' => 'content', 'permission' => 'content_pages.manage'],
            ['key' => 'categories', 'route' => 'admin.categories.index', 'label' => __('admin.nav.categories'), 'icon' => 'categories', 'permission' => 'categories.manage'],
            ['key' => 'media', 'route' => 'admin.media.index', 'label' => __('admin.nav.media'), 'icon' => 'media', 'permission' => 'media.manage'],
            ['key' => 'campaigns', 'route' => 'admin.campaigns.index', 'label' => __('admin.nav.campaigns'), 'icon' => 'campaigns', 'permission' => 'campaigns.manage'],
            ['key' => 'donations', 'route' => 'admin.donations.index', 'label' => __('admin.nav.donations'), 'icon' => 'donations', 'permission' => 'donations.view'],
            ['key' => 'volunteers', 'route' => 'admin.volunteers.index', 'label' => __('admin.nav.volunteers'), 'icon' => 'volunteers', 'permission' => 'volunteers.manage'],
            ['key' => 'newsletter', 'route' => 'admin.newsletter.index', 'label' => __('admin.nav.newsletter'), 'icon' => 'messages', 'permission' => 'newsletter.manage'],
            ['key' => 'messages', 'route' => 'admin.messages.index', 'label' => __('admin.nav.messages'), 'icon' => 'messages', 'permission' => 'messages.manage'],
            ['key' => 'donors', 'route' => 'admin.donors.index', 'label' => __('admin.nav.donors'), 'icon' => 'donors', 'permission' => 'donors.manage'],
            ['key' => 'users', 'route' => 'admin.users.index', 'label' => __('admin.nav.users'), 'icon' => 'users', 'permission' => 'users.manage'],
            ['key' => 'roles', 'route' => 'admin.roles.index', 'label' => __('admin.nav.roles'), 'icon' => 'roles', 'permission' => 'roles.manage'],
        ];
    }

    /**
     * @return list<array{route: string, label: string, icon: string, permission: string}>
     */
    public static function visibleFor(?User $user): array
    {
        if (! $user) {
            return [];
        }

        return array_values(array_filter(
            self::items(),
            fn (array $item): bool => $user->hasPermission($item['permission']),
        ));
    }
}
