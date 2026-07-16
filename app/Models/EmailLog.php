<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    public const STATUS_QUEUED = 'queued';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    public const TYPE_BANK_TRANSFER_INSTRUCTIONS = 'bank_transfer_instructions';

    public const TYPE_PAYMENT_CONFIRMED = 'payment_confirmed';

    public const TYPE_DONATION_RECEIPT = 'donation_receipt';

    public const TYPE_ADMIN_NEW_DONATION = 'admin_new_donation';

    public const TYPE_CUSTOM_DONOR_MESSAGE = 'custom_donor_message';

    public const TYPE_VOLUNTEER_CONFIRMATION = 'volunteer_application_confirmation';

    public const TYPE_VOLUNTEER_ADMIN_ALERT = 'volunteer_application_admin_alert';

    public const TYPE_VOLUNTEER_WELCOME = 'volunteer_application_welcome';

    public const TYPE_VOLUNTEER_REJECTED = 'volunteer_application_rejected';

    protected $fillable = [
        'donor_id',
        'donation_id',
        'type',
        'recipient',
        'subject',
        'status',
        'sent_at',
        'error_message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }
}
