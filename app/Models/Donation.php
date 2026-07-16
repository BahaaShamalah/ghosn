<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donation extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_REFUNDED = 'refunded';

    public const METHOD_BANK = 'bank_transfer';

    public const METHOD_STRIPE_CARD = 'stripe_card';

    public const METHOD_PAYPAL = 'paypal_business';

    public const GATEWAY_BANK = 'bank_transfer';

    public const GATEWAY_STRIPE = 'stripe';

    public const GATEWAY_PAYPAL = 'paypal';

    protected $fillable = [
        'reference',
        'campaign_id',
        'donor_id',
        'amount',
        'currency',
        'payment_method',
        'gateway',
        'status',
        'donor_name',
        'donor_email',
        'donor_phone',
        'message',
        'is_anonymous',
        'locale',
        'gateway_reference',
        'gateway_transaction_id',
        'metadata',
        'paid_at',
        'refunded_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_anonymous' => 'boolean',
            'metadata' => 'array',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function gatewayEvents(): HasMany
    {
        return $this->hasMany(PaymentGatewayEvent::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function formattedAmount(): string
    {
        $symbol = config("donations.currencies.{$this->currency}.symbol", $this->currency.' ');

        return $symbol.number_format((float) $this->amount, 2);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isGatewayPayment(): bool
    {
        return in_array($this->payment_method, [self::METHOD_STRIPE_CARD, self::METHOD_PAYPAL], true);
    }

    public function canManuallyConfirm(): bool
    {
        return $this->payment_method === self::METHOD_BANK && $this->status === self::STATUS_PENDING;
    }

    public function displayDonorName(): string
    {
        if ($this->is_anonymous) {
            return __('public.donate.anonymous_donor');
        }

        return $this->donor_name;
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeAwaitingReview(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
