<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;

class MarkEmailLogSent
{
    public function handle(MessageSent $event): void
    {
        $headers = $event->message->getHeaders();
        $header = $headers->get('X-Email-Log-Id');

        if (! $header) {
            return;
        }

        $logId = (int) $header->getBodyAsString();

        if ($logId <= 0) {
            return;
        }

        EmailLog::query()
            ->where('id', $logId)
            ->where('status', EmailLog::STATUS_QUEUED)
            ->update([
                'status' => EmailLog::STATUS_SENT,
                'sent_at' => now(),
            ]);
    }
}
