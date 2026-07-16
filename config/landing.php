<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bundled Landing HTML Source
    |--------------------------------------------------------------------------
    */

    'source' => base_path('GHOSN-Relief-Landing.html'),

    /*
    |--------------------------------------------------------------------------
    | Extracted Public Assets
    |--------------------------------------------------------------------------
    */

    'assets_path' => 'assets/landing',

    'assets' => [
        'logo' => 'assets/landing/images/logo.webp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Landing Props (from reference DCLogic component)
    |--------------------------------------------------------------------------
    */

    'default_lang' => 'en',
    'default_accent' => 'growth',
    'default_motion' => 'calm',

];
