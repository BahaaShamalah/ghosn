<?php



namespace App\Support;



use App\Models\Page;

use App\Models\PageSection;



class BuilderPageRoutes

{

    public static function publicUrl(Page $page): string

    {

        return match ($page->slug) {

            'home' => route('home'),

            'volunteer' => route('volunteer'),

            'who-we-are' => route('about'),

            'team' => route('team'),

            'contact' => route('contact'),

            default => route('home'),

        };

    }



    public static function sectionPreviewUrl(Page $page, PageSection $section): string

    {

        if ($page->slug !== 'home') {

            return self::publicUrl($page);

        }



        $anchor = match ($section->key) {

            'about' => '#about',

            'join' => '#team',

            default => '#'.$section->key,

        };



        return route('home').$anchor;

    }

}

