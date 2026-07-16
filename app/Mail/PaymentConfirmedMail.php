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

class PaymentConfirmedMail extends Mailable implements ShouldQueue
{
    use GhosnBrandedMail, Queueable, SerializesModels;

    public function __construct(
        public Donation $donation,
        public Donor $donor,
    ) {}

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        $locale = $this->donor->locale ?: 'en';

        return $this->brandedEnvelope(
            __('emails.payment_confirmed.subject', ['reference' => $this->donation->reference], $locale),
        );
    }

    public function content(): Content
    {
        $locale = $this->donor->locale ?: 'en';
        $donation = $this->donation->loadMissing('campaign');

        return new Content(
            html: 'emails.donors.payment-confirmed',
            text: 'emails.donors.text.payment-confirmed',
            with: [
                ...$this->brandingData($locale),
                'donation' => $donation,
                'donorName' => $this->donor->name,
                'campaignName' => $this->campaignName($donation, $locale),
            ],
        );
    }

    private function campaignName(Donation $donation, string $locale): ?string
    {
        if (! $donation->campaign) {
            return null;
        }

        return $locale === 'ar' ? $donation->campaign->title_ar : $donation->campaign->title_en;
    }
}
