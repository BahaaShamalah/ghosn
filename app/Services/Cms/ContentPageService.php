<?php

namespace App\Services\Cms;

use App\Models\ContentPage;
use App\Support\CmsSlug;
use Illuminate\Support\Facades\DB;

class ContentPageService
{
    public function duplicate(ContentPage $page): ContentPage
    {
        $copy = $page->replicate(['slug', 'deleted_at']);
        $copy->slug = CmsSlug::uniqueFrom($page->slug.'-copy', new ContentPage);
        $copy->status = ContentPage::STATUS_DRAFT;
        $copy->author_id = auth()->id();
        $copy->save();

        return $copy;
    }

    /**
     * @param  list<int>  $ids
     * @return array{processed: int, skipped: int}
     */
    public function bulkPublish(array $ids): array
    {
        return $this->bulkUpdateStatus($ids, ContentPage::STATUS_PUBLISHED);
    }

    /**
     * @param  list<int>  $ids
     * @return array{processed: int, skipped: int}
     */
    public function bulkUnpublish(array $ids): array
    {
        return $this->bulkUpdateStatus($ids, ContentPage::STATUS_DRAFT);
    }

    /**
     * @param  list<int>  $ids
     * @return array{processed: int, skipped: int, duplicated: int}
     */
    public function bulkDuplicate(array $ids): array
    {
        $processed = 0;
        $duplicated = 0;

        ContentPage::query()->whereIn('id', $ids)->each(function (ContentPage $page) use (&$processed, &$duplicated): void {
            $this->duplicate($page);
            $processed++;
            $duplicated++;
        });

        return ['processed' => $processed, 'skipped' => 0, 'duplicated' => $duplicated];
    }

    /**
     * @param  list<int>  $ids
     * @return array{processed: int, skipped: int}
     */
    public function bulkDelete(array $ids): array
    {
        $processed = 0;
        $skipped = 0;

        ContentPage::query()->whereIn('id', $ids)->each(function (ContentPage $page) use (&$processed, &$skipped): void {
            if (! $page->canDelete()) {
                $skipped++;

                return;
            }

            $page->delete();
            $processed++;
        });

        return ['processed' => $processed, 'skipped' => $skipped];
    }

    /**
     * @param  list<int>  $ids
     * @return array{processed: int, skipped: int}
     */
    private function bulkUpdateStatus(array $ids, string $status): array
    {
        $processed = 0;
        $skipped = 0;

        ContentPage::query()->whereIn('id', $ids)->each(function (ContentPage $page) use ($status, &$processed, &$skipped): void {
            if ($page->isProtected() && $status !== ContentPage::STATUS_PUBLISHED && $page->status === ContentPage::STATUS_PUBLISHED) {
                $skipped++;

                return;
            }

            $page->update(['status' => $status]);
            $processed++;
        });

        return ['processed' => $processed, 'skipped' => $skipped];
    }
}
