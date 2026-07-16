<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ContentPage;
use App\Models\Post;
use Illuminate\Database\Seeder;

class CmsContentSeeder extends Seeder
{
    public function run(): void
    {
        // About Us lives in Pages Builder (Who We Are) + settings about.page — not Content Pages.
        ContentPage::withTrashed()->where('slug', 'about')->forceDelete();

        $category = Category::query()->updateOrCreate(
            ['slug' => 'field-updates'],
            [
                'name_en' => 'Field Updates',
                'name_ar' => 'تحديثات ميدانية',
                'type' => Category::TYPE_POST,
            ],
        );

        $posts = [
            [
                'slug' => 'winter-relief-distribution-begins',
                'title_en' => 'Winter relief distribution begins in affected communities',
                'title_ar' => 'انطلاق توزيع الإغاثة الشتوية في المجتمعات المتضررة',
                'excerpt_en' => 'GHOSN volunteers began delivering essential winter supplies to families facing urgent needs.',
                'excerpt_ar' => 'بدأ متطوعو غُصن بتوصيل مستلزمات الشتاء الأساسية للأسر التي تواجه احتياجات عاجلة.',
                'content_en' => "Our team coordinated with local partners to reach vulnerable households with blankets, heating support, and food parcels.\n\nThis response reflects GHOSN's commitment to urgent relief with dignity and transparency.",
                'content_ar' => "نسّق فريقنا مع شركاء محليين للوصول إلى الأسر الأكثر ضعفاً بالبطانيات والدعم التدفيئي وطرود الغذاء.\n\nيعكس هذا الاستجابة التزام غُصن بالإغاثة العاجلة بكرامة وشفافية.",
                'published_at' => now()->subDays(3),
            ],
            [
                'slug' => 'community-kitchens-expand-capacity',
                'title_en' => 'Community kitchens expand capacity for daily meals',
                'title_ar' => 'توسعة مطابخ مجتمعية لتوفير وجبات يومية',
                'excerpt_en' => 'New volunteer shifts helped community kitchens serve more families each day.',
                'excerpt_ar' => 'ساعدت ورديات تطوعية جديدة المطابخ المجتمعية على خدمة المزيد من الأسر يومياً.',
                'content_en' => "Volunteer cooks and logistics teams increased daily meal output while maintaining food safety standards.\n\nSupporters can help sustain this program through the donate page.",
                'content_ar' => "رفع الطباخون المتطوعون وفرق اللوجستيات عدد الوجبات اليومية مع الحفاظ على معايير سلامة الغذاء.\n\nيمكن للداعمين المساهمة في استدامة هذا البرنامج عبر صفحة التبرع.",
                'published_at' => now()->subDays(8),
            ],
            [
                'slug' => 'volunteer-training-for-field-teams',
                'title_en' => 'Volunteer training strengthens field response teams',
                'title_ar' => 'تدريب المتطوعين يعزز فرق الاستجابة الميدانية',
                'excerpt_en' => 'A new training cycle prepared volunteers for safe, coordinated relief activities.',
                'excerpt_ar' => 'أعدّت دورة تدريبية جديدة المتطوعين لأنشطة إغاثية آمنة ومنسقة.',
                'content_en' => "Training covered safety protocols, beneficiary communication, and coordination with partner organizations.\n\nGHOSN continues investing in volunteer capacity as part of long-term community resilience.",
                'content_ar' => "شمل التدريب بروتوكولات السلامة والتواصل مع المستفيدين والتنسيق مع المنظمات الشريكة.\n\nيواصل غُصن الاستثمار في قدرات المتطوعين ضمن صمود مجتمعي مستدام.",
                'published_at' => now()->subDays(14),
            ],
        ];

        foreach ($posts as $postData) {
            Post::query()->updateOrCreate(
                ['slug' => $postData['slug']],
                [
                    ...$postData,
                    'category_id' => $category->id,
                    'status' => Post::STATUS_PUBLISHED,
                ],
            );
        }

        $pages = [
            [
                'slug' => 'donation-policy',
                'title_en' => 'Donation Policy',
                'title_ar' => 'سياسة التبرعات',
                'content_en' => "GHOSN Relief Team accepts donations to fund humanitarian relief, development programs, and community support for affected families.\n\nContributions are allocated to urgent needs first, then to sustainable initiatives aligned with our mission and values.\n\nWe are committed to accountability and will share updates on how support is used as programs scale.",
                'content_ar' => "يقبل فريق غُصن للإغاثة التبرعات لتمويل الإغاثة الإنسانية وبرامج التنمية ودعم المجتمعات المتضررة.\n\nتُوجَّه المساهمات إلى الاحتياجات العاجلة أولاً، ثم إلى المبادرات المستدامة المتوافقة مع رسالتنا وقيمنا.\n\nنلتزم بالمساءلة وسنشارك تحديثات حول كيفية استخدام الدعم مع توسّع برامجنا.",
            ],
            [
                'slug' => 'join-us',
                'title_en' => 'Join Our Team',
                'title_ar' => 'انضمّ إلى فريقنا',
                'content_en' => "GHOSN is powered by passionate volunteers who give their time, skills, and energy on the ground and behind the scenes.\n\nIf you would like to volunteer, partner, or support our work in another way, we would love to hear from you through the contact section on our homepage.",
                'content_ar' => "يقود غُصن متطوعون متحمسون يمنحون وقتهم ومهاراتهم وطاقتهم على الأرض وخلف الكواليس.\n\nإذا رغبت في التطوّع أو الشراكة أو دعم عملنا بطريقة أخرى، يسعدنا التواصل معك عبر قسم التواصل في صفحتنا الرئيسية.",
            ],
        ];

        foreach ($pages as $pageData) {
            ContentPage::query()->updateOrCreate(
                ['slug' => $pageData['slug']],
                [
                    ...$pageData,
                    'status' => ContentPage::STATUS_PUBLISHED,
                    'template' => ContentPage::TEMPLATE_DEFAULT,
                ],
            );
        }
    }
}
