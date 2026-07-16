<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $homePageId = DB::table('pages')->where('slug', 'home')->value('id');

        if (! $homePageId) {
            return;
        }

        $exists = DB::table('page_sections')
            ->where('page_id', $homePageId)
            ->where('key', 'campaigns')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('page_sections')
            ->where('page_id', $homePageId)
            ->where('sort_order', '>=', 9)
            ->increment('sort_order');

        DB::table('page_sections')->insert([
            'page_id' => $homePageId,
            'key' => 'campaigns',
            'type' => 'campaigns',
            'title_en' => 'Campaigns',
            'title_ar' => 'الحملات',
            'sort_order' => 9,
            'is_active' => true,
            'settings' => json_encode(['content' => config('campaigns.defaults')]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $homePageId = DB::table('pages')->where('slug', 'home')->value('id');

        if (! $homePageId) {
            return;
        }

        DB::table('page_sections')
            ->where('page_id', $homePageId)
            ->where('key', 'campaigns')
            ->delete();

        DB::table('page_sections')
            ->where('page_id', $homePageId)
            ->where('sort_order', '>', 9)
            ->decrement('sort_order');
    }
};
