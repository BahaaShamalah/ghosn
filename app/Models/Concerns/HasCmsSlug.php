<?php

namespace App\Models\Concerns;

use App\Support\CmsSlug;
use Illuminate\Database\Eloquent\Model;

trait HasCmsSlug
{
    public static function bootHasCmsSlug(): void
    {
        static::saving(function (Model $model): void {
            if (! filled($model->slug) && filled($model->title_en)) {
                $model->slug = CmsSlug::uniqueFrom((string) $model->title_en, $model);
            }
        });
    }
}
