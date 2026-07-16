<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            if (! Schema::hasColumn('posts', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        DB::table('page_sections')
            ->where('key', 'news')
            ->update([
                'key' => 'latest_news',
                'type' => 'latest_news',
                'title_en' => 'Latest News',
                'title_ar' => 'آخر الأخبار',
            ]);

        $homePageId = DB::table('pages')->where('slug', 'home')->value('id');

        if (! $homePageId) {
            return;
        }

        $exists = DB::table('page_sections')
            ->where('page_id', $homePageId)
            ->where('key', 'latest_news')
            ->exists();

        if ($exists) {
            return;
        }

        $maxOrder = (int) DB::table('page_sections')
            ->where('page_id', $homePageId)
            ->max('sort_order');

        DB::table('page_sections')->insert([
            'page_id' => $homePageId,
            'key' => 'latest_news',
            'type' => 'latest_news',
            'title_en' => 'Latest News',
            'title_ar' => 'آخر الأخبار',
            'sort_order' => max(9, $maxOrder),
            'is_active' => true,
            'settings' => json_encode(['content' => config('news.defaults')]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            if (Schema::hasColumn('posts', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        DB::table('page_sections')
            ->where('key', 'latest_news')
            ->update([
                'key' => 'news',
                'type' => 'news',
            ]);
    }
};
