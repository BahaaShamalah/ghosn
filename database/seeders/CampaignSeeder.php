<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::query()->updateOrCreate(
            ['slug' => 'relief', 'type' => Category::TYPE_CAMPAIGN],
            [
                'name_en' => 'Relief',
                'name_ar' => 'إغاثة',
            ],
        );

        $campaigns = [
            [
                'title_en' => 'Winter Family Relief',
                'title_ar' => 'إغاثة عائلات الشتاء',
                'slug' => 'winter-family-relief',
                'category_id' => $category->id,
                'excerpt_en' => 'Warm meals, blankets, and heating support for vulnerable families this winter.',
                'excerpt_ar' => 'وجبات دافئة وبطانيات ودعم للتدفئة للأسر الضعيفة هذا الشتاء.',
                'story_en' => '<p>Help GHOSN Relief deliver urgent winter support to families facing cold weather without adequate shelter or heating.</p>',
                'story_ar' => '<p>ساعد فريق غُصن للإغاثة في تقديم دعم شتوي عاجل للأسر التي تواجه البرد دون مأوى أو تدفئة كافية.</p>',
                'goal_amount' => 25000,
                'raised_amount' => 8750,
                'donors_count' => 42,
                'status' => Campaign::STATUS_ACTIVE,
                'is_featured_homepage' => true,
                'sort_order' => 1,
                'starts_at' => now()->subWeek(),
            ],
            [
                'title_en' => 'School Supplies Drive',
                'title_ar' => 'حملة مستلزمات مدرسية',
                'slug' => 'school-supplies-drive',
                'excerpt_en' => 'Equip children with backpacks, books, and materials to keep learning.',
                'excerpt_ar' => 'تزويد الأطفال بحقائب وكتب ومواد لمواصلة التعلم.',
                'story_en' => '<p>Your gift helps students return to class prepared with the essentials they need to succeed.</p>',
                'story_ar' => '<p>تبرعك يساعد الطلاب على العودة إلى المدرسة مستعدين بالأساسيات التي يحتاجونها للنجاح.</p>',
                'goal_amount' => 12000,
                'raised_amount' => 4200,
                'donors_count' => 18,
                'status' => Campaign::STATUS_ACTIVE,
                'is_featured_homepage' => true,
                'sort_order' => 2,
                'starts_at' => now()->subDays(3),
            ],
            [
                'title_en' => 'Draft Campaign Hidden',
                'title_ar' => 'حملة مسودة مخفية',
                'slug' => 'draft-campaign-hidden',
                'excerpt_en' => 'Should not appear publicly.',
                'excerpt_ar' => 'لا ينبغي أن تظهر علناً.',
                'goal_amount' => 5000,
                'status' => Campaign::STATUS_DRAFT,
                'is_featured_homepage' => false,
                'sort_order' => 99,
            ],
        ];

        foreach ($campaigns as $data) {
            Campaign::query()->updateOrCreate(
                ['slug' => $data['slug']],
                array_merge([
                    'currency' => 'USD',
                    'raised_amount' => 0,
                    'donors_count' => 0,
                ], $data),
            );
        }
    }
}
