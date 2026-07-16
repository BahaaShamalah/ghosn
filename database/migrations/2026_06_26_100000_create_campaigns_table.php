<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('slug')->unique();
            $table->string('excerpt_en', 1000)->nullable();
            $table->string('excerpt_ar', 1000)->nullable();
            $table->longText('story_en')->nullable();
            $table->longText('story_ar')->nullable();
            $table->foreignId('featured_image_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->json('gallery_media_ids')->nullable();
            $table->decimal('goal_amount', 12, 2)->default(0);
            $table->decimal('raised_amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('status', 20)->default('draft');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_featured_homepage')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('donors_count')->default(0);
            $table->string('seo_title_en')->nullable();
            $table->string('seo_title_ar')->nullable();
            $table->string('seo_description_en', 500)->nullable();
            $table->string('seo_description_ar', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'starts_at']);
            $table->index(['is_featured_homepage', 'sort_order']);
        });

        Schema::table('donations', function (Blueprint $table): void {
            $table->foreignId('campaign_id')->nullable()->after('reference')->constrained('campaigns')->nullOnDelete();
            $table->index('campaign_id');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('campaign_id');
        });

        Schema::dropIfExists('campaigns');
    }
};
