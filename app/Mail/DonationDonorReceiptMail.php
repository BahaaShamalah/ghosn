<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationDonorReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Donation $donation,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->donation->isPaid()
            ? __('public.donate.email_receipt_subject_paid', ['reference' => $this->donation->reference])
            : __('public.donate.email_receipt_subject_pending', ['reference' => $this->donation->reference]);

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.donations.donor-receipt',
        );
    }
}
