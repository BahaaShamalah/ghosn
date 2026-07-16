<?php

namespace App\Services\Volunteers;

use App\Mail\VolunteerTemplatedMail;
use App\Models\EmailLog;
use App\Models\VolunteerApplication;
use App\Support\EmailSettings;
use App\Support\VolunteerEmailSettings;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VolunteerEmailService
{
    public function __construct(
        private readonly VolunteerEmailSettings $templates,
        private readonly EmailSettings $emailSettings,
    ) {}

    public function afterApplicationSubmitted(VolunteerApplication $application): void
    {
        if ($this->templates->enabled(VolunteerEmailSettings::TYPE_CONFIRMATION)) {
            $this->sendToApplicant($application, VolunteerEmailSettings::TYPE_CONFIRMATION, EmailLog::TYPE_VOLUNTEER_CONFIRMATION);
        }

        if ($this->templates->enabled(VolunteerEmailSettings::TYPE_ADMIN_ALERT) && $this->emailSettings->adminAlertsEnabled()) {
            $this->sendAdminAlert($application);
        }
    }

    public function afterStatusChanged(VolunteerApplication $application, string $previousStatus): void
    {
        if ($application->status === VolunteerApplication::STATUS_APPROVED
            && $previousStatus !== VolunteerApplication::STATUS_APPROVED
            && $this->templates->enabled(VolunteerEmailSettings::TYPE_WELCOME)
        ) {
            $this->sendToApplicant($application, VolunteerEmailSettings::TYPE_WELCOME, EmailLog::TYPE_VOLUNTEER_WELCOME);
        }

        if ($application->status === VolunteerApplication::STATUS_REJECTED
            && $previousStatus !== VolunteerApplication::STATUS_REJECTED
            && $this->templates->enabled(VolunteerEmailSettings::TYPE_REJECTED)
        ) {
            $this->sendToApplicant($application, VolunteerEmailSettings::TYPE_REJECTED, EmailLog::TYPE_VOLUNTEER_REJECTED);
        }
    }

    private function sendAdminAlert(VolunteerApplication $application): ?EmailLog
    {
        $adminEmail = $this->templates->adminRecipient();

        if (! filled($adminEmail) || ! filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        if ($this->alreadySent($application->id, EmailLog::TYPE_VOLUNTEER_ADMIN_ALERT)) {
            return null;
        }

        return $this->queueMail(
            $adminEmail,
            $application,
            VolunteerEmailSettings::TYPE_ADMIN_ALERT,
            EmailLog::TYPE_VOLUNTEER_ADMIN_ALERT,
            'en',
        );
    }

    private function sendToApplicant(VolunteerApplication $application, string $templateType, string $logType): ?EmailLog
    {
        if (! filter_var($application->email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        if ($this->alreadySent($application->id, $logType)) {
            return null;
        }

        return $this->queueMail(
            $application->email,
            $application,
            $templateType,
            $logType,
            $application->locale ?: 'en',
        );
    }

    private function queueMail(
        string $recipient,
        VolunteerApplication $application,
        string $templateType,
        string $logType,
        string $locale,
    ): ?EmailLog {
        $content = $this->templates->render($templateType, $application, $locale);

        if ($content['subject'] === '' || $content['body'] === '') {
            return null;
        }

        $mailable = new VolunteerTemplatedMail($application, $templateType, $content, $locale);

        $log = EmailLog::query()->create([
            'type' => $logType,
            'recipient' => $recipient,
            'subject' => $content['subject'],
            'status' => EmailLog::STATUS_QUEUED,
            'metadata' => [
                'volunteer_application_id' => $application->id,
                'template_type' => $templateType,
                'locale' => $locale,
            ],
        ]);

        $this->dispatchMail($recipient, $mailable, $log);

        return $log;
    }

    private function dispatchMail(string $recipient, Mailable $mailable, EmailLog $log): void
    {
        if (property_exists($mailable, 'emailLogId')) {
            $mailable->emailLogId = $log->id;
        }

        try {
            Mail::to($recipient)->queue(
                $mailable->withSymfonyMessage(function ($message) use ($log): void {
                    $message->getHeaders()->addTextHeader('X-Email-Log-Id', (string) $log->id);
                }),
            );
        } catch (\Throwable $exception) {
            $log->update([
                'status' => EmailLog::STATUS_FAILED,
                'error_message' => $exception->getMessage(),
            ]);

            Log::warning('Failed to queue volunteer email.', [
                'email_log_id' => $log->id,
                'type' => $log->type,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function alreadySent(int $applicationId, string $type): bool
    {
        return EmailLog::query()
            ->where('type', $type)
            ->whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_QUEUED])
            ->where('metadata->volunteer_application_id', $applicationId)
            ->exists();
    }
}
