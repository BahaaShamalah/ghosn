<?php

namespace App\Mail;

use App\Mail\Concerns\GhosnBrandedMail;
use App\Models\Donation;
use App\Models\Donor;
use App\Support\DonationSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class BankTransferInstructionsMail extends Mailable implements ShouldQueue
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
            __('emails.bank_transfer.subject', ['reference' => $this->donation->reference], $locale),
        );
    }

    public function content(): Content
    {
        $locale = $this->donor->locale ?: 'en';
        $donationSettings = app(DonationSettings::class);
        $bank = $donationSettings->bankDetails();
        $isAr = $locale === 'ar';

        return new Content(
            html: 'emails.donors.bank-transfer-instructions',
            text: 'emails.donors.text.bank-transfer-instructions',
            with: [
                ...$this->brandingData($locale),
                'donation' => $this->donation->loadMissing('campaign'),
                'donorName' => $this->donor->name,
                'instructions' => $isAr ? $bank['instructions_ar'] : $bank['instructions_en'],
                'bankDetails' => [
                    __('emails.bank_transfer.bank_name', locale: $locale) => $isAr ? $bank['bank_name_ar'] : $bank['bank_name_en'],
                    __('emails.bank_transfer.account_holder', locale: $locale) => $isAr ? $bank['account_holder_ar'] : $bank['account_holder_en'],
                    'IBAN' => $bank['iban'],
                    __('emails.bank_transfer.account_number', locale: $locale) => $bank['account_number'],
                    'SWIFT' => $bank['swift'],
                ],
            ],
        );
    }
}
