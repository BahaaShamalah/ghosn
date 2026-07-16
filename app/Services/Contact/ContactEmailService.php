<?php

namespace App\Services\Contact;

use App\Mail\ContactMessageAdminMail;
use App\Models\ContactMessage;
use App\Support\EmailSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactEmailService
{
    public function __construct(
        private readonly EmailSettings $emailSettings,
    ) {}

    public function afterMessageSubmitted(ContactMessage $message): void
    {
        if (! $this->emailSettings->adminAlertsEnabled()) {
            return;
        }

        $recipient = $this->emailSettings->contactInboxEmail();

        if (! filled($recipient) || ! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($recipient)->queue(new ContactMessageAdminMail($message));
        } catch (\Throwable $exception) {
            Log::warning('Failed to queue contact message admin email.', [
                'contact_message_id' => $message->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
