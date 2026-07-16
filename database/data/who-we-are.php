<?php



/**

 * Who We Are page — React about UI, edited via Pages Builder.

 */

return [

    'page' => [

        'slug' => 'who-we-are',

        'title_en' => 'Who We Are',

        'title_ar' => 'من نحن',

        'is_active' => true,

        'meta_title_en' => 'Who We Are — GHOSN Relief Team',

        'meta_title_ar' => 'من نحن — فريق غُصن للإغاثة',

        'meta_description_en' => 'Learn about GHOSN Relief Team — our story, mission, and the communities we serve.',

        'meta_description_ar' => 'تعرّف على فريق غُصن للإغاثة — قصتنا ورسالتنا والمجتمعات التي نخدمها.',

    ],

    'sections' => [

        [

            'key' => 'about',

            'type' => 'about',

            'title_en' => 'Who We Are',

            'title_ar' => 'من نحن',

            'sort_order' => 1,

            'settings' => ['content' => config('about.defaults')],

            'blocks' => [],

        ],

    ],

];

