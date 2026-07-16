<?php



namespace App\Support;



class PublicNavigation

{

    /**

     * @return list<array{label_en: string, label_ar: string, href: string}>

     */

    public static function links(): array

    {

        return SiteChrome::navLinks();

    }

}

