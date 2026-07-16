<?php

/**
 * Admin RBAC — permission slugs, default roles, and route → permission map.
 */
return [

    'permissions' => [
        'dashboard.view' => [
            'group' => 'dashboard',
            'label_en' => 'View dashboard',
            'label_ar' => 'عرض لوحة التحكم',
        ],
        'settings.manage' => [
            'group' => 'settings',
            'label_en' => 'Manage site settings',
            'label_ar' => 'إدارة إعدادات الموقع',
        ],
        'pages.manage' => [
            'group' => 'pages',
            'label_en' => 'Manage pages builder',
            'label_ar' => 'إدارة منشئ الصفحات',
        ],
        'posts.manage' => [
            'group' => 'cms',
            'label_en' => 'Manage posts',
            'label_ar' => 'إدارة المقالات',
        ],
        'content_pages.manage' => [
            'group' => 'cms',
            'label_en' => 'Manage content pages',
            'label_ar' => 'إدارة الصفحات',
        ],
        'categories.manage' => [
            'group' => 'cms',
            'label_en' => 'Manage categories',
            'label_ar' => 'إدارة التصنيفات',
        ],
        'media.manage' => [
            'group' => 'media',
            'label_en' => 'Manage media library',
            'label_ar' => 'إدارة مكتبة الوسائط',
        ],
        'campaigns.manage' => [
            'group' => 'campaigns',
            'label_en' => 'Manage campaigns',
            'label_ar' => 'إدارة الحملات',
        ],
        'donations.view' => [
            'group' => 'donations',
            'label_en' => 'View donations',
            'label_ar' => 'عرض التبرعات',
        ],
        'donations.manage' => [
            'group' => 'donations',
            'label_en' => 'Manage donations',
            'label_ar' => 'إدارة التبرعات',
        ],
        'donors.manage' => [
            'group' => 'donors',
            'label_en' => 'Manage donors',
            'label_ar' => 'إدارة المتبرعين',
        ],
        'volunteers.manage' => [
            'group' => 'volunteers',
            'label_en' => 'Manage volunteers',
            'label_ar' => 'إدارة المتطوعين',
        ],
        'newsletter.manage' => [
            'group' => 'newsletter',
            'label_en' => 'Manage newsletter',
            'label_ar' => 'إدارة النشرة البريدية',
        ],
        'messages.manage' => [
            'group' => 'messages',
            'label_en' => 'Manage contact messages',
            'label_ar' => 'إدارة الرسائل',
        ],
        'users.manage' => [
            'group' => 'access',
            'label_en' => 'Manage admin users',
            'label_ar' => 'إدارة مستخدمي الإدارة',
        ],
        'roles.manage' => [
            'group' => 'access',
            'label_en' => 'Manage roles & permissions',
            'label_ar' => 'إدارة الأدوار والصلاحيات',
        ],
    ],

    'roles' => [
        'super-admin' => [
            'label_en' => 'Super Administrator',
            'label_ar' => 'مدير أعلى',
            'is_super' => true,
            'is_system' => true,
            'permissions' => '*',
        ],
        'administrator' => [
            'label_en' => 'Administrator',
            'label_ar' => 'مدير',
            'is_super' => false,
            'is_system' => true,
            'permissions' => '*',
        ],
        'editor' => [
            'label_en' => 'Content Editor',
            'label_ar' => 'محرّر محتوى',
            'is_super' => false,
            'is_system' => true,
            'permissions' => [
                'dashboard.view',
                'pages.manage',
                'posts.manage',
                'content_pages.manage',
                'categories.manage',
                'media.manage',
                'campaigns.manage',
                'newsletter.manage',
            ],
        ],
        'finance' => [
            'label_en' => 'Finance & Donations',
            'label_ar' => 'التبرعات والمالية',
            'is_super' => false,
            'is_system' => true,
            'permissions' => [
                'dashboard.view',
                'donations.view',
                'donations.manage',
                'donors.manage',
                'campaigns.manage',
            ],
        ],
        'viewer' => [
            'label_en' => 'Viewer',
            'label_ar' => 'مشاهد',
            'is_super' => false,
            'is_system' => true,
            'permissions' => [
                'dashboard.view',
                'donations.view',
            ],
        ],
    ],

    /*
    | Route name patterns → required permission (first match wins).
    */
    'route_permissions' => [
        'admin.dashboard' => 'dashboard.view',
        'admin.settings.*' => 'settings.manage',
        'admin.pages.*' => 'pages.manage',
        'admin.posts.*' => 'posts.manage',
        'admin.content-pages.*' => 'content_pages.manage',
        'admin.categories.*' => 'categories.manage',
        'admin.media.*' => 'media.manage',
        'admin.campaigns.*' => 'campaigns.manage',
        'admin.donations.index' => 'donations.view',
        'admin.donations.receipt.*' => 'donations.view',
        'admin.donations.confirm' => 'donations.manage',
        'admin.donors.*' => 'donors.manage',
        'admin.volunteers.*' => 'volunteers.manage',
        'admin.newsletter.*' => 'newsletter.manage',
        'admin.messages.*' => 'messages.manage',
        'admin.users.*' => 'users.manage',
        'admin.roles.*' => 'roles.manage',
    ],

];
