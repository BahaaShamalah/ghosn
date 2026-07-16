<?php

namespace App\Mail;

use App\Mail\Concerns\GhosnBrandedMail;
use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class ContactMessageAdminMail extends Mailable implements ShouldQueue
{
    use GhosnBrandedMail, Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
    ) {}

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        $subject = filled($this->contactMessage->subject)
            ? $this->contactMessage->subject
            : __('emails.contact.admin_alert.default_subject', locale: 'en');

        return $this->brandedEnvelope(
            __('emails.contact.admin_alert.subject', [
                'name' => $this->contactMessage->name,
                'subject' => $subject,
            ], 'en'),
        );
    }

    public function content(): Content
    {
        $locale = 'en';

        return new Content(
            html: 'emails.contact.admin-notification',
            text: 'emails.contact.text.admin-notification',
            with: [
                ...$this->brandingData($locale),
                'contactMessage' => $this->contactMessage,
                'adminUrl' => route('admin.messages.show', $this->contactMessage),
                'locale' => $locale,
            ],
        );
    }
}
