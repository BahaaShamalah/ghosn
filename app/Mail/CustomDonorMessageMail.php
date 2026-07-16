<?php

namespace App\Mail;

use App\Mail\Concerns\GhosnBrandedMail;
use App\Models\Donor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class CustomDonorMessageMail extends Mailable implements ShouldQueue
{
    use GhosnBrandedMail, Queueable, SerializesModels;

    public function __construct(
        public Donor $donor,
        public string $emailSubject,
        public string $messageBody,
        public ?string $ctaText = null,
        public ?string $ctaUrl = null,
        /** @var list<string> */
        public array $imageUrls = [],
        /** @var list<array{watch_url: string, thumbnail_url: string}> */
        public array $youtubeVideos = [],
    ) {}

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        return $this->brandedEnvelope($this->emailSubject);
    }

    public function content(): Content
    {
        $locale = $this->donor->locale ?: 'en';

        return new Content(
            html: 'emails.donors.custom-message',
            text: 'emails.donors.text.custom-message',
            with: [
                ...$this->brandingData($locale),
                'emailSubject' => $this->emailSubject,
                'donorName' => $this->donor->name,
                'messageBody' => $this->messageBody,
                'ctaText' => $this->ctaText,
                'ctaUrl' => $this->ctaUrl,
                'imageUrls' => $this->imageUrls,
                'youtubeVideos' => $this->youtubeVideos,
            ],
        );
    }
}
