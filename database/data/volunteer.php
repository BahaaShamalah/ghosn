<?php

return [
    'page' => [
        'slug' => 'volunteer',
        'title_en' => 'Volunteer',
        'title_ar' => 'التطوّع',
        'is_active' => true,
        'meta_title_en' => 'Volunteer with GHOSN',
        'meta_title_ar' => 'تطوّع مع غُصن',
        'meta_description_en' => 'Join GHOSN Relief Team as a volunteer and help deliver dignified humanitarian relief.',
        'meta_description_ar' => 'انضم إلى فريق غُصن للإغاثة كمتطوّع وساهم في إغاثة إنسانية كريمة.',
    ],
    'sections' => [
        [
            'key' => 'volunteer',
            'type' => 'volunteer',
            'title_en' => 'Volunteer Page',
            'title_ar' => 'صفحة التطوّع',
            'sort_order' => 1,
            'settings' => ['content' => config('volunteer-page.defaults')],
            'blocks' => [],
        ],
    ],
];
