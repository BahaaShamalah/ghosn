<?php

/**
 * Landing page section defaults and admin schema — aligned with React landing (App.jsx).
 */
return [

    'impact' => [
        'label_en' => 'Impact in Numbers',
        'label_ar' => 'الأثر بالأرقام',
        'defaults' => [
            'title_en' => 'Our Impact in Numbers',
            'title_ar' => 'أثرنا بالأرقام',
            'stats' => [
                [
                    'key' => 'beneficiaries',
                    'end' => 128000,
                    'decimals' => 0,
                    'prefix' => '',
                    'suffix' => '+',
                    'label_en' => 'Beneficiaries reached',
                    'label_ar' => 'المستفيدون',
                ],
                [
                    'key' => 'campaigns',
                    'end' => 24,
                    'decimals' => 0,
                    'prefix' => '',
                    'suffix' => '',
                    'label_en' => 'Active campaigns',
                    'label_ar' => 'الحملات النشطة',
                ],
                [
                    'key' => 'volunteers',
                    'end' => 1450,
                    'decimals' => 0,
                    'prefix' => '',
                    'suffix' => '+',
                    'label_en' => 'Volunteers',
                    'label_ar' => 'المتطوعون',
                ],
                [
                    'key' => 'donations',
                    'end' => 3.2,
                    'decimals' => 1,
                    'prefix' => '$',
                    'suffix' => 'M',
                    'label_en' => 'Total donations',
                    'label_ar' => 'إجمالي التبرعات',
                ],
            ],
        ],
        'admin' => [
            [
                'title_en' => 'Impact in Numbers',
                'title_ar' => 'الأثر بالأرقام',
                'fields' => [
                    ['type' => 'bilingual', 'key' => 'title', 'input' => 'text'],
                    [
                        'type' => 'repeater',
                        'key' => 'stats',
                        'label_en' => 'Statistics',
                        'label_ar' => 'الإحصائيات',
                        'item_fields' => [
                            [
                                'key' => 'end',
                                'input' => 'number',
                                'bilingual' => false,
                                'label_en' => 'Number',
                                'label_ar' => 'الرقم',
                                'step' => 'any',
                            ],
                            [
                                'key' => 'label',
                                'input' => 'text',
                                'bilingual' => true,
                                'label_en' => 'Label',
                                'label_ar' => 'المسمى',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'how_works' => [
        'label_en' => 'How Giving Works',
        'label_ar' => 'كيف يعمل العطاء',
        'defaults' => [
            'eyebrow_en' => 'Simple & transparent',
            'eyebrow_ar' => 'بسيط وشفّاف',
            'heading_en' => 'How your giving works',
            'heading_ar' => 'كيف يعمل عطاؤك',
            'description_en' => 'Three steps from your heart to a family in need — with full transparency at every stage.',
            'description_ar' => 'ثلاث خطوات من قلبك إلى أسرةٍ محتاجة — بشفافيةٍ كاملة في كل مرحلة.',
            'steps' => [
                [
                    'title_en' => 'Choose a cause',
                    'title_ar' => 'اختر قضية',
                    'body_en' => 'Pick a campaign that speaks to you, or give where the need is greatest.',
                    'body_ar' => 'اختر حملةً تلامس قلبك، أو تبرّع حيث الحاجة أكبر.',
                ],
                [
                    'title_en' => 'Give securely',
                    'title_ar' => 'تبرّع بأمان',
                    'body_en' => 'Donate in seconds through a safe, encrypted checkout.',
                    'body_ar' => 'تبرّع في ثوانٍ عبر بوّابة دفعٍ آمنة ومشفّرة.',
                ],
                [
                    'title_en' => 'See the impact',
                    'title_ar' => 'شاهد الأثر',
                    'body_en' => 'Follow real updates and watch your generosity grow into change.',
                    'body_ar' => 'تابع التحديثات الحقيقية وشاهد عطاءك يتحوّل إلى تغيير.',
                ],
            ],
        ],
        'admin' => [
            [
                'title_en' => 'How Giving Works',
                'title_ar' => 'كيف يعمل العطاء',
                'fields' => [
                    ['type' => 'bilingual', 'key' => 'eyebrow', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'heading', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'description', 'input' => 'textarea'],
                    [
                        'type' => 'repeater',
                        'key' => 'steps',
                        'label_en' => 'Steps',
                        'label_ar' => 'الخطوات',
                        'item_fields' => [
                            ['key' => 'title', 'input' => 'text'],
                            ['key' => 'body', 'input' => 'textarea'],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'ways' => [
        'label_en' => 'Ways to Help',
        'label_ar' => 'طرق المساعدة',
        'defaults' => [
            'eyebrow_en' => 'Get involved',
            'eyebrow_ar' => 'شارك معنا',
            'heading_en' => 'Ways you can help',
            'heading_ar' => 'طرقٌ يمكنك أن تساعد بها',
            'description_en' => 'Every hand matters. Choose the way that fits you best.',
            'description_ar' => 'كل يدٍ تصنع فرقًا. اختر الطريقة التي تناسبك.',
            'cards' => [
                [
                    'title_en' => 'Donate',
                    'title_ar' => 'تبرّع',
                    'body_en' => 'Fund clean water, food, and shelter for families in need.',
                    'body_ar' => 'موّل المياه النظيفة والغذاء والمأوى للأسر المحتاجة.',
                    'cta_en' => 'Give now',
                    'cta_ar' => 'تبرّع الآن',
                ],
                [
                    'title_en' => 'Volunteer',
                    'title_ar' => 'تطوّع',
                    'body_en' => 'Lend your time and skills to relief work on the ground.',
                    'body_ar' => 'امنح وقتك ومهاراتك للعمل الإغاثي في الميدان.',
                    'cta_en' => 'Join us',
                    'cta_ar' => 'انضم إلينا',
                ],
                [
                    'title_en' => 'Partner with us',
                    'title_ar' => 'كن شريكًا',
                    'body_en' => 'Bring your organization on board to multiply the impact.',
                    'body_ar' => 'أشرك مؤسستك معنا لمضاعفة الأثر.',
                    'cta_en' => 'Get in touch',
                    'cta_ar' => 'تواصل معنا',
                ],
            ],
        ],
        'admin' => [
            [
                'title_en' => 'Ways to Help',
                'title_ar' => 'طرق المساعدة',
                'fields' => [
                    ['type' => 'bilingual', 'key' => 'eyebrow', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'heading', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'description', 'input' => 'textarea'],
                    [
                        'type' => 'repeater',
                        'key' => 'cards',
                        'label_en' => 'Cards',
                        'label_ar' => 'البطاقات',
                        'item_fields' => [
                            ['key' => 'title', 'input' => 'text'],
                            ['key' => 'body', 'input' => 'textarea'],
                            ['key' => 'cta', 'input' => 'text'],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'testimonials' => [
        'label_en' => 'Testimonials',
        'label_ar' => 'الشهادات',
        'defaults' => [
            'eyebrow_en' => 'Voices of hope',
            'eyebrow_ar' => 'أصوات الأمل',
            'heading_en' => 'Stories from our community',
            'heading_ar' => 'قصصٌ من مجتمعنا',
            'items' => [
                [
                    'quote_en' => 'The new well changed everything. My children no longer walk hours for water — they walk to school instead.',
                    'quote_ar' => 'البئر الجديد غيّر كل شيء. لم يعد أطفالي يمشون ساعاتٍ لأجل الماء — صاروا يمشون إلى المدرسة.',
                    'name_en' => 'Amina',
                    'name_ar' => 'أمينة',
                    'role_en' => 'Beneficiary, Northern Region',
                    'role_ar' => 'مستفيدة، المنطقة الشمالية',
                ],
                [
                    'quote_en' => 'Giving through GHOSN, I actually see where my donation goes. That transparency is why I keep coming back.',
                    'quote_ar' => 'حين أتبرّع عبر غُصن أرى فعلًا أين يذهب عطائي. هذه الشفافية سبب عودتي دائمًا.',
                    'name_en' => 'Khalid',
                    'name_ar' => 'خالد',
                    'role_en' => 'Monthly donor',
                    'role_ar' => 'متبرّع شهري',
                ],
                [
                    'quote_en' => 'Volunteering here gave my weekends meaning. We are a family bound by one purpose.',
                    'quote_ar' => 'التطوّع هنا منح عطلاتي معنى. صرنا عائلةً يجمعها هدفٌ واحد.',
                    'name_en' => 'Layla',
                    'name_ar' => 'ليلى',
                    'role_en' => 'Field volunteer',
                    'role_ar' => 'متطوّعة ميدانية',
                ],
            ],
        ],
        'admin' => [
            [
                'title_en' => 'Testimonials',
                'title_ar' => 'الشهادات',
                'fields' => [
                    ['type' => 'bilingual', 'key' => 'eyebrow', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'heading', 'input' => 'text'],
                    [
                        'type' => 'repeater',
                        'key' => 'items',
                        'label_en' => 'Testimonials',
                        'label_ar' => 'الشهادات',
                        'item_fields' => [
                            ['key' => 'quote', 'input' => 'textarea'],
                            ['key' => 'name', 'input' => 'text'],
                            ['key' => 'role', 'input' => 'text'],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'join' => [
        'label_en' => 'Join Our Team',
        'label_ar' => 'انضم إلى فريقنا',
        'defaults' => [
            'eyebrow_en' => 'Become a volunteer',
            'eyebrow_ar' => 'كن متطوعًا',
            'heading_en' => 'Join Our Team',
            'heading_ar' => 'انضم إلى فريقنا',
            'description_en' => 'Become part of a community that shows up when it matters. Your time, skills and heart create real change in people lives.',
            'description_ar' => 'كن جزءًا من مجتمعٍ يحضر عند الحاجة. وقتك ومهاراتك وقلبك تصنع فرقًا حقيقيًا في حياة الناس.',
            'bullets' => [
                ['text_en' => 'Flexible time commitment', 'text_ar' => 'مرونة في الوقت'],
                ['text_en' => 'Training and mentorship', 'text_ar' => 'تدريب وإرشاد'],
                ['text_en' => 'Real, visible impact', 'text_ar' => 'أثرٌ ملموس وواضح'],
            ],
            'name_label_en' => 'Full name',
            'name_label_ar' => 'الاسم الكامل',
            'name_placeholder_en' => 'Your full name',
            'name_placeholder_ar' => 'اكتب اسمك الكامل',
            'phone_label_en' => 'Phone number',
            'phone_label_ar' => 'رقم الهاتف',
            'phone_placeholder_en' => '+1 (555) 000-0000',
            'phone_placeholder_ar' => '+٩٦٦ ٥٠ ٠٠٠ ٠٠٠٠',
            'email_label_en' => 'Email',
            'email_label_ar' => 'البريد الإلكتروني',
            'email_placeholder_en' => 'you@email.com',
            'email_placeholder_ar' => 'you@email.com',
            'area_label_en' => 'Preferred volunteering area',
            'area_label_ar' => 'مجال التطوّع المفضّل',
            'area_placeholder_en' => 'Select an area',
            'area_placeholder_ar' => 'اختر مجالاً',
            'message_label_en' => 'Short message',
            'message_label_ar' => 'رسالة قصيرة',
            'message_placeholder_en' => 'Tell us how you would like to help',
            'message_placeholder_ar' => 'أخبرنا كيف تودّ المساعدة',
            'submit_en' => 'Submit application',
            'submit_ar' => 'أرسل الطلب',
            'sending_en' => 'Sending…',
            'sending_ar' => 'جارٍ الإرسال…',
            'success_en' => 'Thank you! We will reach out to you very soon.',
            'success_ar' => 'شكرًا لك! سنتواصل معك قريبًا جدًا.',
            'error_en' => 'Something went wrong. Please try again.',
            'error_ar' => 'حدث خطأ ما. حاول مرة أخرى.',
            'areas' => [
                ['value' => 'field_relief', 'label_en' => 'Field relief', 'label_ar' => 'الإغاثة الميدانية'],
                ['value' => 'fundraising', 'label_en' => 'Fundraising', 'label_ar' => 'جمع التبرعات'],
                ['value' => 'media', 'label_en' => 'Media and outreach', 'label_ar' => 'الإعلام والتواصل'],
                ['value' => 'logistics', 'label_en' => 'Logistics', 'label_ar' => 'اللوجستيات'],
                ['value' => 'education', 'label_en' => 'Education and youth', 'label_ar' => 'التعليم والشباب'],
            ],
        ],
        'admin' => [
            [
                'title_en' => 'Join Our Team',
                'title_ar' => 'انضم إلى فريقنا',
                'fields' => [
                    ['type' => 'bilingual', 'key' => 'eyebrow', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'heading', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'description', 'input' => 'textarea'],
                    [
                        'type' => 'repeater',
                        'key' => 'bullets',
                        'label_en' => 'Bullet points',
                        'label_ar' => 'النقاط',
                        'item_fields' => [
                            ['key' => 'text', 'input' => 'text'],
                        ],
                    ],
                    ['type' => 'bilingual', 'key' => 'name_label', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'name_placeholder', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'phone_label', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'phone_placeholder', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'email_label', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'email_placeholder', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'area_label', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'area_placeholder', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'message_label', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'message_placeholder', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'submit', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'sending', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'success', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'error', 'input' => 'text'],
                    [
                        'type' => 'repeater',
                        'key' => 'areas',
                        'label_en' => 'Volunteering areas',
                        'label_ar' => 'مجالات التطوّع',
                        'item_fields' => [
                            ['key' => 'value', 'input' => 'text'],
                            ['key' => 'label', 'input' => 'text'],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'campaigns' => [
        'label_en' => 'Campaigns',
        'label_ar' => 'الحملات',
        'defaults' => config('campaigns.defaults'),
        'admin' => [
            [
                'title_en' => 'Campaigns section',
                'title_ar' => 'قسم الحملات',
                'fields' => [
                    ['type' => 'toggle', 'key' => 'is_visible', 'label_en' => 'Show section on homepage', 'label_ar' => 'إظهار القسم في الصفحة الرئيسية'],
                    ['type' => 'bilingual', 'key' => 'eyebrow', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'title', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'subtitle', 'input' => 'textarea'],
                    ['type' => 'number', 'key' => 'campaigns_count', 'label_en' => 'Number of campaigns', 'label_ar' => 'عدد الحملات', 'min' => 1, 'max' => 12],
                ],
            ],
        ],
    ],

    'latest_news' => [
        'label_en' => 'Latest Updates',
        'label_ar' => 'آخر المستجدات',
        'defaults' => config('news.defaults'),
        'admin' => [
            [
                'title_en' => 'Latest Updates section',
                'title_ar' => 'قسم المستجدات',
                'fields' => [
                    ['type' => 'toggle', 'key' => 'is_visible', 'label_en' => 'Show section on homepage', 'label_ar' => 'إظهار القسم في الصفحة الرئيسية'],
                    ['type' => 'bilingual', 'key' => 'eyebrow', 'input' => 'text'],
                    ['type' => 'bilingual', 'key' => 'title', 'input' => 'text'],
                    ['type' => 'number', 'key' => 'posts_count', 'label_en' => 'Number of posts', 'label_ar' => 'عدد المقالات', 'min' => 1, 'max' => 12],
                ],
            ],
        ],
    ],

];
