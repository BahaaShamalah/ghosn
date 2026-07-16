<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VolunteerApplication extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'area',
        'message',
        'status',
        'locale',
        'ip_address',
    ];

    public function initial(): string
    {
        return mb_strtoupper(mb_substr($this->name, 0, 1));
    }

    public function localizedArea(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $key = 'admin.volunteers.areas.'.$this->area;

        return __($key, [], $locale) !== $key ? __($key, [], $locale) : $this->area;
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
