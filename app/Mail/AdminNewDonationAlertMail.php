<?php

namespace App\Mail;

use App\Mail\Concerns\GhosnBrandedMail;
use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class AdminNewDonationAlertMail extends Mailable implements ShouldQueue
{
    use GhosnBrandedMail, Queueable, SerializesModels;

    public function __construct(
        public Donation $donation,
        public ?Donor $donor = null,
    ) {}

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        return $this->brandedEnvelope(
            __('emails.admin_alert.subject', ['reference' => $this->donation->reference], 'en'),
        );
    }

    public function content(): Content
    {
        $locale = 'en';

        return new Content(
            html: 'emails.donors.admin-new-donation',
            text: 'emails.donors.text.admin-new-donation',
            with: [
                ...$this->brandingData($locale),
                'donation' => $this->donation,
                'donorLabel' => $this->donation->is_anonymous
                    ? __('public.donate.anonymous_donor', locale: $locale)
                    : $this->donation->donor_name,
                'statusLabel' => __('admin.donations.status_'.$this->donation->status, locale: $locale),
            ],
        );
    }
}
