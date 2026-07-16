<?php

namespace App\Services\Donors;

use App\Mail\AdminNewDonationAlertMail;
use App\Mail\BankTransferInstructionsMail;
use App\Mail\CustomDonorMessageMail;
use App\Mail\PaymentConfirmedMail;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\EmailLog;
use App\Models\Media;
use App\Support\EmailSettings;
use App\Support\YouTubeUrl;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DonorEmailService
{
    public function __construct(
        private readonly DonorService $donors,
        private readonly EmailSettings $emailSettings,
    ) {}

    public function afterDonationCreated(Donation $donation): void
    {
        $donor = $donation->donor ?? $this->donors->syncFromDonation($donation);

        if ($donation->payment_method !== Donation::METHOD_BANK) {
            return;
        }

        $this->sendBankTransferInstructions($donation, $donor);
        $this->sendAdminNewDonationAlert($donation, $donor);
    }

    public function afterDonationPaid(Donation $donation): void
    {
        $donor = $donation->donor ?? $this->donors->syncFromDonation($donation);
        $this->donors->refreshStats($donor);

        $this->sendPaymentConfirmed($donation, $donor);

        if ($donation->isGatewayPayment()) {
            $this->sendAdminNewDonationAlert($donation, $donor);
        }
    }

    public function sendBankTransferInstructions(Donation $donation, ?Donor $donor = null): ?EmailLog
    {
        $donor ??= $donation->donor;

        if (! $donor || ! $this->canEmailDonor($donor)) {
            return null;
        }

        if ($this->alreadySent($donation->id, EmailLog::TYPE_BANK_TRANSFER_INSTRUCTIONS)) {
            return null;
        }

        return $this->queueDonorMail(
            $donor,
            $donation,
            EmailLog::TYPE_BANK_TRANSFER_INSTRUCTIONS,
            new BankTransferInstructionsMail($donation, $donor),
        );
    }

    public function sendPaymentConfirmed(Donation $donation, ?Donor $donor = null): ?EmailLog
    {
        $donor ??= $donation->donor;

        if (! $donor || ! $this->canEmailDonor($donor)) {
            return null;
        }

        if (! $this->emailSettings->donorReceiptsEnabled()) {
            return null;
        }

        if ($this->alreadySent($donation->id, EmailLog::TYPE_PAYMENT_CONFIRMED)) {
            return null;
        }

        return $this->queueDonorMail(
            $donor,
            $donation,
            EmailLog::TYPE_PAYMENT_CONFIRMED,
            new PaymentConfirmedMail($donation, $donor),
        );
    }

    public function sendAdminNewDonationAlert(Donation $donation, ?Donor $donor = null): ?EmailLog
    {
        if (! $this->emailSettings->adminAlertsEnabled()) {
            return null;
        }

        $adminEmail = $this->emailSettings->adminNotificationEmail();

        if (! filled($adminEmail)) {
            return null;
        }

        if ($this->alreadySent($donation->id, EmailLog::TYPE_ADMIN_NEW_DONATION)) {
            return null;
        }

        $donor ??= $donation->donor;

        $mailable = new AdminNewDonationAlertMail($donation, $donor);
        $subject = $mailable->envelope()->subject;

        $log = EmailLog::query()->create([
            'donor_id' => $donor?->id,
            'donation_id' => $donation->id,
            'type' => EmailLog::TYPE_ADMIN_NEW_DONATION,
            'recipient' => $adminEmail,
            'subject' => $subject,
            'status' => EmailLog::STATUS_QUEUED,
            'metadata' => ['donation_reference' => $donation->reference],
        ]);

        $this->dispatchMail($adminEmail, $mailable, $log);

        return $log;
    }

    /**
     * @param  array{
     *     subject: string,
     *     message: string,
     *     cta_text?: string|null,
     *     cta_url?: string|null,
     *     attachment_media_ids?: list<int>,
     *     youtube_urls?: list<string>
     * }  $payload
     */
    public function sendCustomMessage(Donor $donor, array $payload): ?EmailLog
    {
        if ($donor->isBlocked()) {
            return null;
        }

        $message = $this->sanitizeMessage($payload['message']);
        $imageUrls = $this->resolveAttachmentImageUrls($payload['attachment_media_ids'] ?? []);
        $youtubeVideos = YouTubeUrl::normalizeMany($payload['youtube_urls'] ?? []);

        $mailable = new CustomDonorMessageMail(
            $donor,
            $payload['subject'],
            $message,
            $payload['cta_text'] ?? null,
            $payload['cta_url'] ?? null,
            $imageUrls,
            $youtubeVideos,
        );

        $log = EmailLog::query()->create([
            'donor_id' => $donor->id,
            'type' => EmailLog::TYPE_CUSTOM_DONOR_MESSAGE,
            'recipient' => $donor->email,
            'subject' => $payload['subject'],
            'status' => EmailLog::STATUS_QUEUED,
            'metadata' => array_filter([
                'cta_text' => $payload['cta_text'] ?? null,
                'cta_url' => $payload['cta_url'] ?? null,
                'attachment_media_ids' => $payload['attachment_media_ids'] ?? [],
                'youtube_urls' => collect($payload['youtube_urls'] ?? [])->map(fn ($url) => YouTubeUrl::watchUrl($url))->filter()->values()->all(),
            ]),
        ]);

        $this->dispatchMail($donor->email, $mailable, $log);

        return $log;
    }

    /**
     * @param  list<int>  $mediaIds
     * @return list<string>
     */
    private function resolveAttachmentImageUrls(array $mediaIds): array
    {
        if ($mediaIds === []) {
            return [];
        }

        return Media::query()
            ->whereIn('id', $mediaIds)
            ->get()
            ->sortBy(fn (Media $media) => array_search($media->id, $mediaIds, true))
            ->filter(fn (Media $media) => $media->isImage())
            ->map(fn (Media $media) => url($media->url()))
            ->values()
            ->all();
    }

    private function queueDonorMail(Donor $donor, Donation $donation, string $type, Mailable $mailable): ?EmailLog
    {
        if (! $this->canEmailDonor($donor)) {
            return null;
        }

        $subject = method_exists($mailable, 'envelope')
            ? $mailable->envelope()->subject
            : '';

        $log = EmailLog::query()->create([
            'donor_id' => $donor->id,
            'donation_id' => $donation->id,
            'type' => $type,
            'recipient' => $donor->email,
            'subject' => $subject,
            'status' => EmailLog::STATUS_QUEUED,
            'metadata' => ['donation_reference' => $donation->reference],
        ]);

        $this->dispatchMail($donor->email, $mailable, $log);

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

            Log::warning('Failed to queue donor email.', [
                'email_log_id' => $log->id,
                'type' => $log->type,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function canEmailDonor(Donor $donor): bool
    {
        return ! $donor->isBlocked() && filled($donor->email) && filter_var($donor->email, FILTER_VALIDATE_EMAIL);
    }

    private function alreadySent(int $donationId, string $type): bool
    {
        return EmailLog::query()
            ->where('donation_id', $donationId)
            ->where('type', $type)
            ->whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_QUEUED])
            ->exists();
    }

    private function sanitizeMessage(string $message): string
    {
        return trim(strip_tags($message));
    }
}
