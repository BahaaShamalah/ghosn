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

class DonationReceiptMail extends Mailable implements ShouldQueue
{
    use GhosnBrandedMail, Queueable, SerializesModels;

    public function __construct(
        public Donation $donation,
        public Donor $donor,
    ) {}

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        $locale = $this->donor->locale ?: 'en';

        $subject = $this->donation->isPaid()
            ? __('emails.donation_receipt.subject_paid', ['reference' => $this->donation->reference], $locale)
            : __('emails.donation_receipt.subject_pending', ['reference' => $this->donation->reference], $locale);

        return $this->brandedEnvelope($subject);
    }

    public function content(): Content
    {
        $locale = $this->donor->locale ?: 'en';
        $donation = $this->donation->loadMissing('campaign');

        return new Content(
            html: 'emails.donors.donation-receipt',
            text: 'emails.donors.text.donation-receipt',
            with: [
                ...$this->brandingData($locale),
                'donation' => $donation,
                'donorName' => $this->donor->name,
                'campaignName' => $this->campaignName($donation, $locale),
                'statusLabel' => __('admin.donations.status_'.$donation->status, locale: $locale),
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
