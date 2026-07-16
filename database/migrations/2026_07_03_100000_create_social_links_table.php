<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_links', function (Blueprint $table): void {
            $table->id();
            $table->string('platform', 32);
            $table->string('label_en')->nullable();
            $table->string('label_ar')->nullable();
            $table->string('url', 500);
            $table->string('icon', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        $this->seedFromLegacySettings();
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }

    private function seedFromLegacySettings(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $facebook = DB::table('settings')->where('key', 'social.facebook_url')->value('value');
        $instagram = DB::table('settings')->where('key', 'social.instagram_handle')->value('value');
        $sort = 0;

        if (filled($facebook)) {
            DB::table('social_links')->insert([
                'platform' => 'facebook',
                'label_en' => 'Facebook',
                'label_ar' => 'فيسبوك',
                'url' => $facebook,
                'is_active' => true,
                'sort_order' => $sort++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (filled($instagram)) {
            $handle = ltrim((string) $instagram, '@');

            DB::table('social_links')->insert([
                'platform' => 'instagram',
                'label_en' => '@'.$handle,
                'label_ar' => '@'.$handle,
                'url' => 'https://instagram.com/'.$handle,
                'is_active' => true,
                'sort_order' => $sort++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
