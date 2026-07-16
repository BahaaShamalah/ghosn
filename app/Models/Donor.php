<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'is_anonymous',
        'total_donated',
        'donations_count',
        'last_donation_at',
        'locale',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'total_donated' => 'decimal:2',
            'donations_count' => 'integer',
            'last_donation_at' => 'datetime',
        ];
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    public function displayName(): string
    {
        if ($this->is_anonymous) {
            return __('public.donate.anonymous_donor');
        }

        return $this->name;
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
