<?php

namespace App\Mail;

use App\Mail\Concerns\GhosnBrandedMail;
use App\Models\VolunteerApplication;
use App\Support\VolunteerEmailSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class VolunteerTemplatedMail extends Mailable implements ShouldQueue
{
    use GhosnBrandedMail, Queueable, SerializesModels;

    /**
     * @param  array{subject: string, heading: string, body: string}  $content
     */
    public function __construct(
        public VolunteerApplication $application,
        public string $templateType,
        public array $content,
        public string $mailLocale,
    ) {}

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        return $this->brandedEnvelope($this->content['subject']);
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.volunteers.templated',
            text: 'emails.volunteers.text.templated',
            with: [
                ...$this->brandingData($this->mailLocale),
                'application' => $this->application,
                'heading' => $this->content['heading'],
                'bodyText' => $this->content['body'],
                'showSummary' => $this->templateType === VolunteerEmailSettings::TYPE_ADMIN_ALERT,
                'locale' => $this->mailLocale,
            ],
        );
    }
}
